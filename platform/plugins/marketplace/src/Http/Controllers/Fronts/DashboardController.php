<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Marketplace\Enums\RevenueTypeEnum;
use Botble\Marketplace\Enums\WithdrawalStatusEnum;
use Botble\Marketplace\Facades\MarketplaceHelper;
use Botble\Marketplace\Models\Revenue;
use Botble\Marketplace\Models\Withdrawal;
use Botble\Media\Chunks\Exceptions\UploadMissingFileException;
use Botble\Media\Chunks\Handler\DropZoneUploadHandler;
use Botble\Media\Chunks\Receiver\FileReceiver;
use Botble\Media\Facades\RvMedia;
use Botble\Theme\Facades\Theme;
use Botble\Payment\Enums\PaymentStatusEnum;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends BaseController
{
  public function __construct()
  {
    $version = get_cms_version();

    Theme::asset()
      ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css', ['bootstrap-css'], version: $version);

    Theme::asset()
      ->container('footer')
      ->add('ecommerce-utilities-js', 'vendor/core/plugins/ecommerce/js/utilities.js', ['jquery'], version: $version)
      ->add('cropper-js', 'vendor/core/plugins/ecommerce/libraries/cropper.js', ['jquery'], version: $version)
      ->add('avatar-js', 'vendor/core/plugins/ecommerce/js/avatar.js', ['jquery'], version: $version);
  }

  public function index(Request $request)
  {
    $this->pageTitle(__('Dashboard'));

    Assets::addScriptsDirectly([
      'vendor/core/plugins/ecommerce/libraries/daterangepicker/daterangepicker.js',
      'vendor/core/plugins/ecommerce/libraries/apexcharts-bundle/dist/apexcharts.min.js',
      'vendor/core/plugins/ecommerce/js/report.js',
    ])
      ->addStylesDirectly([
        'vendor/core/plugins/ecommerce/libraries/daterangepicker/daterangepicker.css',
        'vendor/core/plugins/ecommerce/libraries/apexcharts-bundle/dist/apexcharts.css',
        'vendor/core/plugins/ecommerce/css/report.css',
      ])
      ->addScripts(['moment']);

    Assets::usingVueJS();

    [$startDate, $endDate, $predefinedRange] = EcommerceHelper::getDateRangeInReport($request);

    $user = auth('customer')->user();
    // dd(auth('customer'));
    $store = $user->store;
    $storeId = $store->getKey();
    $data = compact('startDate', 'endDate', 'predefinedRange');

    $selectedOrderStatus = $request->input('order_status') ?: null;

    $selectedPaymentStatus = $request->input('payment_status') ?: null;

    $keyword = $request->input('keyword');

    $revenue = Revenue::query()
      ->selectRaw(
        'SUM(CASE WHEN type IS NULL OR type = ? THEN sub_amount WHEN type = ? THEN sub_amount * -1 ELSE 0 END) as sub_amount,
                SUM(CASE WHEN type IS NULL OR type = ? THEN amount WHEN type = ? THEN amount * -1 ELSE 0 END) as amount,
                SUM(fee) as fee',
        [RevenueTypeEnum::ADD_AMOUNT, RevenueTypeEnum::SUBTRACT_AMOUNT, RevenueTypeEnum::ADD_AMOUNT, RevenueTypeEnum::SUBTRACT_AMOUNT]
      )
      ->where('customer_id', $user->getKey())
      ->where(function ($query) use ($startDate, $endDate): void {
        $query->whereDate('created_at', '>=', $startDate)
          ->whereDate('created_at', '<=', $endDate);
      })
      ->groupBy('customer_id')
      ->first();

    $withdrawal = Withdrawal::query()
      ->select([
        DB::raw('SUM(mp_customer_withdrawals.amount) as amount'),
        DB::raw('SUM(mp_customer_withdrawals.fee)'),
      ])
      ->where('mp_customer_withdrawals.customer_id', $user->getKey())
      ->whereIn('mp_customer_withdrawals.status', [
        WithdrawalStatusEnum::COMPLETED,
        WithdrawalStatusEnum::PENDING,
        WithdrawalStatusEnum::PROCESSING,
      ])
      ->where(function ($query) use ($startDate, $endDate): void {
        $query->whereDate('mp_customer_withdrawals.created_at', '>=', $startDate)
          ->whereDate('mp_customer_withdrawals.created_at', '<=', $endDate);
      })
      ->groupBy('mp_customer_withdrawals.customer_id')
      ->first();

    $orderQuery = Order::query()
      ->where('is_finished', 1)
      ->when($storeId, fn($query) => $query->where('store_id', $storeId), fn($query) => $query->whereRaw('1 = 0'))
      ->when($selectedOrderStatus, fn($query) => $query->where('status', $selectedOrderStatus))
      ->when($selectedPaymentStatus, fn($query) => $query->whereHas('payment', fn($payment) => $payment->where('status', $selectedPaymentStatus)))
      ->when($keyword, function ($query) use ($keyword) {
        $query->where(function ($subQuery) use ($keyword) {
          $subQuery
            ->where('code', 'LIKE', '%' . $keyword . '%')
            ->orWhere('id', $keyword)
            ->orWhereHas('user', fn($userQuery) => $userQuery->where('name', 'LIKE', '%' . $keyword . '%')->orWhere('email', 'LIKE', '%' . $keyword . '%'));
        });
      })
      ->whereDate('created_at', '>=', $startDate)
      ->whereDate('created_at', '<=', $endDate);

    $orderTotals = (clone $orderQuery)
      ->selectRaw('SUM(amount) as total_amount, SUM(tax_amount) as total_tax_amount, SUM(shipping_amount) as total_shipping_amount')
      ->first();

    if (! $revenue && $orderTotals) {
      $orderSubAmount = ($orderTotals->total_amount ?? 0) - ($orderTotals->total_shipping_amount ?? 0) - ($orderTotals->total_tax_amount ?? 0);
      $orderFee = $orderSubAmount * (MarketplaceHelper::getSetting('fee_per_order', 0) / 100);

      $revenue = (object) [
        'sub_amount' => $orderSubAmount,
        'fee' => $orderFee,
        'amount' => $orderSubAmount - $orderFee,
      ];
    }

    $revenues = collect([
      'amount' => $revenue ? $revenue->amount : 0,
      'fee' => ($revenue ? $revenue->fee : 0) + ($withdrawal ? $withdrawal->fee : 0),
      'sub_amount' => $revenue ? $revenue->sub_amount : 0,
      'withdrawal' => $withdrawal ? $withdrawal->amount : 0,
    ]);

    $data['revenue'] = $revenues;

    $data['orders'] = (clone $orderQuery)
      ->select([
        'id',
        'status',
        'user_id',
        'created_at',
        'amount',
        'tax_amount',
        'shipping_amount',
        'payment_id',
      ])
      ->with(['user', 'payment'])
      ->orderByDesc('created_at')
      ->limit(10)
      ->get();

    $productQuery = Product::query()
      ->whereDate('created_at', '>=', $startDate)
      ->whereDate('created_at', '<=', $endDate)
      ->where('is_variation', false)
      ->when($storeId, fn($query) => $query->where('store_id', $storeId), fn($query) => $query->whereRaw('1 = 0'))
      ->wherePublished();

    $data['products'] = (clone $productQuery)
      ->select([
        'id',
        'name',
        'order',
        'created_at',
        'status',
        'sku',
        'images',
        'price',
        'sale_price',
        'sale_type',
        'start_date',
        'end_date',
        'quantity',
        'with_storehouse_management',
      ])
      ->limit(10)
      ->get();

    $orderIds = (clone $orderQuery)->pluck('id');
    $soldQuantity = $orderIds->isNotEmpty()
      ? OrderProduct::query()->whereIn('order_id', $orderIds)->sum('qty')
      : 0;

    $soldProducts = $orderIds->isNotEmpty()
      ? OrderProduct::query()
        ->selectRaw('product_id, product_name, SUM(qty) as sold_qty')
        ->whereIn('order_id', $orderIds)
        ->groupBy('product_id', 'product_name')
        ->with([
          'product' => fn($query) => $query->select([
            'id',
            'name',
            'quantity',
            'with_storehouse_management',
            'store_id',
          ]),
        ])
        ->orderByDesc('sold_qty')
        ->limit(10)
        ->get()
      : collect();

    $data['sold_quantity'] = $soldQuantity;
    $data['sold_products'] = $soldProducts;

    $totalProducts = (clone $productQuery)->count();
    $totalOrders = (clone $orderQuery)->count();
    $filterOptions = [
      'order_statuses' => OrderStatusEnum::labels(),
      'payment_statuses' => PaymentStatusEnum::labels(),
    ];
    $filterValues = [
      'order_status' => $selectedOrderStatus,
      'payment_status' => $selectedPaymentStatus,
      'keyword' => $keyword,
    ];
    $compact = compact('user', 'store', 'data', 'totalProducts', 'totalOrders', 'filterOptions', 'filterValues', 'soldQuantity');

    if ($request->ajax()) {
      return $this
        ->httpResponse()
        ->setData([
          'html' => MarketplaceHelper::view('vendor-dashboard.partials.dashboard-content', $compact)->render(),
        ]);
    }

    return MarketplaceHelper::view('vendor-dashboard.index', $compact);
  }

  public function postUpload(Request $request)
  {
    $customer = auth('customer')->user();

    $uploadFolder = $customer->store?->upload_folder ?: $customer->upload_folder;

    if (!RvMedia::isChunkUploadEnabled()) {
      $validator = Validator::make($request->all(), [
        'file.0' => ['required', 'image', 'mimes:jpg,jpeg,png'],
      ]);

      if ($validator->fails()) {
        return $this
          ->httpResponse()
          ->setError()
          ->setMessage($validator->getMessageBag()->first());
      }

      $result = RvMedia::handleUpload(Arr::first($request->file('file')), 0, $uploadFolder);

      if ($result['error']) {
        return $this
          ->httpResponse()
          ->setError()
          ->setMessage($result['message']);
      }

      return $this
        ->httpResponse()
        ->setData($result['data']);
    }

    try {
      // Create the file receiver
      $receiver = new FileReceiver('file', $request, DropZoneUploadHandler::class);
      // Check if the upload is success, throw exception or return response you need
      if ($receiver->isUploaded() === false) {
        throw new UploadMissingFileException();
      }
      // Receive the file
      $save = $receiver->receive();
      // Check if the upload has finished (in chunk mode it will send smaller files)
      if ($save->isFinished()) {
        $result = RvMedia::handleUpload($save->getFile(), 0, $uploadFolder);

        if (!$result['error']) {
          return $this
            ->httpResponse()
            ->setData($result['data']);
        }

        return $this
          ->httpResponse()
          ->setError()
          ->setMessage($result['message']);
      }
      // We are in chunk mode, lets send the current progress
      $handler = $save->handler();

      return response()->json([
        'done' => $handler->getPercentageDone(),
        'status' => true,
      ]);
    } catch (Exception $exception) {
      return $this
        ->httpResponse()
        ->setError()
        ->setMessage($exception->getMessage());
    }
  }

  public function postUploadFromEditor(Request $request)
  {
    $customer = auth('customer')->user();

    $uploadFolder = $customer->store?->upload_folder ?: $customer->upload_folder;

    return RvMedia::uploadFromEditor($request, 0, $uploadFolder);
  }
}
