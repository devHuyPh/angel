<?php

namespace App\Http\Controllers\Client;
// app\Http\Controllers\Client\WalletTransferController.php
use App\Models\CustomerNotification;
use App\Models\WalletTransfer;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Models\Customer;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WalletTransferController extends BaseController
{
    public function __construct()
    {
        $version = get_cms_version();

        Theme::asset()
            ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css', ['bootstrap-css'], version: $version);

        Theme::asset()
            ->add('front-ecommerce-css', 'vendor/core/plugins/ecommerce/css/front-ecommerce.css', version: $version);

        Theme::asset()
            ->container('footer')
            ->add('ecommerce-utilities-js', 'vendor/core/plugins/ecommerce/js/utilities.js', ['jquery'], version: $version)
            ->add('cropper-js', 'vendor/core/plugins/ecommerce/libraries/cropper.js', ['jquery'], version: $version)
            ->add('avatar-js', 'vendor/core/plugins/ecommerce/js/avatar.js', ['jquery'], version: $version);
    }

    protected function generateRecipientCode(Customer $customer): string
    {
        $payload = implode('|', ['withdraw_wallet', $customer->getKey(), $customer->email]);

        return base64_encode($payload);
    }

    protected function decodeRecipientCode(?string $code): ?array
    {
        if (! $code) {
            return null;
        }

        $decoded = base64_decode($code, true);

        if (! $decoded) {
            return null;
        }

        $parts = explode('|', $decoded);

        if (count($parts) < 3 || $parts[0] !== 'withdraw_wallet') {
            return null;
        }

        return [
            'id' => (int) $parts[1],
            'email' => $parts[2],
        ];
    }

    protected function generateReference(): string
    {
        do {
            $reference = 'TF' . Str::upper(Str::random(10));
        } while (WalletTransfer::where('reference', $reference)->exists());

        return $reference;
    }

    public function index(Request $request)
    {
        $customer = auth('customer')->user();

        if (! $customer) {
            return redirect()->route('customer.login');
        }

        $prefillFromCode = $this->decodeRecipientCode($request->query('code'));
        $prefillEmail = $request->query('email') ?? ($prefillFromCode['email'] ?? null);

        $recipient = null;
        if ($prefillEmail) {
            $recipient = Customer::where('email', $prefillEmail)->first();
        }

        $prefillRecipient = null;
        if ($recipient) {
            $prefillRecipient = [
                'id' => $recipient->getKey(),
                'name' => $recipient->name,
                'email' => $recipient->email,
                'code' => $this->generateRecipientCode($recipient),
            ];
        }

        $myCode = $this->generateRecipientCode($customer);
        $myQrSvg = QrCode::format('svg')->size(220)->generate($myCode);

        SeoHelper::setTitle(__('Chuyển tiền'));

        return Theme::scope(
            'banking.transfer',
            compact('customer', 'recipient', 'prefillEmail', 'myCode', 'myQrSvg', 'prefillRecipient'),
            'banking.transfer'
        )->render();
    }

    public function recipient(Request $request): JsonResponse
    {
        $customerId = auth('customer')->id();

        if (! $customerId) {
            return response()->json(['message' => 'Vui lòng đăng nhập'], 401);
        }

        $email = $request->query('email');
        $code = $request->query('code');

        if (! $email && ! $code) {
            return response()->json(['message' => 'Email hoặc mã người nhận là bắt buộc.'], 422);
        }

        if (! $email && $code) {
            $decoded = $this->decodeRecipientCode($code);
            $email = $decoded['email'] ?? null;
        }

        if (! $email) {
            return response()->json(['message' => 'Mã người nhận không hợp lệ.'], 422);
        }

        $recipient = Customer::where('email', $email)->first();

        if (! $recipient) {
            return response()->json(['message' => 'Không tìm thấy người nhận.'], 404);
        }

        if ($recipient->getKey() === $customerId) {
            return response()->json(['message' => 'Bạn không thể chuyển cho chính mình.'], 422);
        }

        return response()->json([
            'data' => [
                'id' => $recipient->getKey(),
                'name' => $recipient->name,
                'email' => $recipient->email,
                'code' => $this->generateRecipientCode($recipient),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $customer = auth('customer')->user();

        if (! $customer) {
            return $this->httpResponse()->setError()->setMessage('Vui lòng đăng nhập.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
            'email' => ['nullable', 'email'],
            'code' => ['nullable', 'string'],
            'password' => ['required', 'string'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $recipientEmail = $validated['email'] ?? null;

        if (! $recipientEmail && ! empty($validated['code'])) {
            $decoded = $this->decodeRecipientCode($validated['code']);
            $recipientEmail = $decoded['email'] ?? null;
        }

        if (! $recipientEmail) {
            return $this->httpResponse()->setError()->setMessage('Vui lòng nhập email người nhận hoặc mã từ QR.');
        }

        $recipient = Customer::where('email', $recipientEmail)->first();

        if (! $recipient) {
            return $this->httpResponse()->setError()->setMessage('Không tìm thấy người nhận với email này.');
        }

        if ($recipient->getKey() === $customer->getKey()) {
            return $this->httpResponse()->setError()->setMessage('Bạn không thể tự chuyển cho chính mình.');
        }

        if (! Hash::check($validated['password'], $customer->password)) {
            return $this->httpResponse()->setError()->setMessage('Mật khẩu không chính xác.');
        }

        $amount = (float) $validated['amount'];

        $reference = $this->generateReference();
        $codeUsed = $validated['code'] ?? null;

        try {
            DB::transaction(function () use ($customer, $recipient, $amount, $reference, $codeUsed, $validated): void {
                $ids = [$customer->getKey(), $recipient->getKey()];
                sort($ids);

                $firstLock = Customer::whereKey($ids[0])->lockForUpdate()->first();
                $secondLock = Customer::whereKey($ids[1])->lockForUpdate()->first();

                $sender = $firstLock->getKey() === $customer->getKey() ? $firstLock : $secondLock;
                $receiver = $sender->getKey() === $firstLock->getKey() ? $secondLock : $firstLock;

                if ((float) $sender->walet_1 < $amount) {
                    throw new \RuntimeException('Số dư không đủ để chuyển.');
                }

                $sender->walet_1 = (float) $sender->walet_1 - $amount;
                $receiver->walet_1 = (float) $receiver->walet_1 + $amount;

                $sender->save();
                $receiver->save();

                WalletTransfer::create([
                    'from_customer_id' => $customer->getKey(),
                    'to_customer_id' => $recipient->getKey(),
                    'amount' => $amount,
                    'reference' => $reference,
                    'status' => 'completed',
                    'note' => $validated['note'] ?? null,
                    'code_used' => $codeUsed,
                    'meta' => [
                        'sender_email' => $customer->email,
                        'recipient_email' => $recipient->email,
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ],
                ]);

                $note = $validated['note'] ?? null;

                CustomerNotification::create([
                    'title' => 'Chuyển tiền ví rút',
                    'dessription' => 'transfer_out_wallet1',
                    'variables' => json_encode([
                        'amount' => $amount,
                        'email' => $recipient->email,
                        'reference' => $reference,
                        'note' => $note,
                    ]),
                    'customer_id' => $customer->getKey(),
                    'url' => '/marketing/wallet-history',
                ]);

                CustomerNotification::create([
                    'title' => 'Nhận tiền ví rút',
                    'dessription' => 'transfer_in_wallet1',
                    'variables' => json_encode([
                        'amount' => $amount,
                        'email' => $customer->email,
                        'reference' => $reference,
                        'note' => $note,
                    ]),
                    'customer_id' => $recipient->getKey(),
                    'url' => '/marketing/wallet-history',
                ]);
            });
        } catch (\Throwable $exception) {
            return $this->httpResponse()->setError()->setMessage($exception->getMessage());
        }

        return $this
            ->httpResponse()
            ->setMessage('Chuyển tiền thành công.')
            ->setNextRoute('wallet.transfer');
    }
}
