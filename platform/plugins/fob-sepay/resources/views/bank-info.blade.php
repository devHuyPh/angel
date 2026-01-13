<style>
    .sepay.fob-container {
        margin-top: 2rem;
    }

    .sepay .fob-qr-code {
        text-align: center;
        margin-bottom: 40px;
    }

    .sepay .fob-qr-code img {
        width: 250px;
        height: auto;
        margin: 0;
        padding: 0;
    }

    .sepay .fob-qr-code figcaption {
        margin-top: 10px;
        font-size: 14px;
        color: #666;
    }

    .sepay .fob-qr-intro {
        margin-bottom: 10px;
        font-size: 16px;
    }

    .sepay .transaction-status-done {
        background-color: var(--bs-tertiary-bg);
        border: none;
        color: var(--primary-color);
    }

    .sepay .transaction-status-done .icon {
        width: 40px;
        height: 40px;
    }
</style>

@php
    $payments = $paymentData ?? [];
@endphp

<div class="sepay fob-container" data-bb-sepay-group>
    @foreach ($payments as $paymentItem)
        @php
            $payment = $paymentItem['payment'];
            $orderAmount = $paymentItem['orderAmount'];
            $chargeId = $paymentItem['chargeId'];
            $imageUrl = $paymentItem['imageUrl'];
            $formattedOrderAmount = number_format($orderAmount, 0, ',', '.') . ' ₫';
            $isCompleted = $paymentItem['isCompleted'] ?? $payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED;
        @endphp

        <div class="sepay-block mb-4" data-bb-sepay-block>
            @if (! $isCompleted)
                <div class="sepay-bank-info" data-bb-sepay-info>
                    <div class="fob-qr-intro">
                        Cách 1: Mở app ngân hàng/ Ví để <strong>quét mã QR</strong>
                    </div>
                    <div class="fob-qr-code">
                        <figure>
                            <img src="{{ $imageUrl }}" alt="QR Code">
                        </figure>
                    </div>

                    <div class="fob-qr-intro">
                        Cách 2: Chuyển khoản <strong>thủ công</strong> theo thông tin
                    </div>
                    <div class="fob-qr-information">
                        <table class="table table-hover table-striped">
                            <tr>
                                <td>Tên Ngân Hàng</td>
                                <td>
                                    <strong>{{ $bank }}</strong>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Chủ Tài Khoản</td>
                                <td>
                                    <strong>{{ $bankAccountHolder }}</strong>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Số Tài Khoản</td>
                                <td>
                                    <strong>{{ $bankAccountNumber }}</strong>
                                </td>
                                <td class="text-end" style="width: 80px;">
                                    <a href="javascript:void(0);" rel="nooper" class="ms-2" type="button" data-clipboard="{{ $bankAccountNumber }}" data-bb-toggle="copy">
                                        <x-core::icon name="ti ti-clipboard" />
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Nội Dung Chuyển Khoản</td>
                                <td>
                                    <strong>{{ $chargeId }}</strong>
                                </td>
                                <td class="text-end" style="width: 80px;">
                                    <a href="javascript:void(0);" rel="nooper" class="ms-2" type="button" data-clipboard="{{ $chargeId }}" data-bb-toggle="copy">
                                        <x-core::icon name="ti ti-clipboard" />
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td>Số Tiền Giao Dịch</td>
                                <td>
                                    <strong>{{ $formattedOrderAmount }}</strong>
                                </td>
                                <td class="text-end" style="width: 80px;">
                                    <a href="javascript:void(0);" rel="nooper" class="ms-2" type="button" data-clipboard="{{ $orderAmount }}" data-bb-toggle="copy">
                                        <x-core::icon name="ti ti-clipboard" />
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <div class="alert alert-warning">
                            <p>Vui lòng giữ nguyên nội dung chuyển khoản <strong class="text-danger">{{ $chargeId }}</strong> và nhập đúng số tiền <strong class="text-danger">{{ $formattedOrderAmount }}</strong> để được xác nhận thanh toán trực tuyến.</p>
                        </div>

                        <div class="transaction-status text-center" data-bb-toggle="sepay-transaction-status" data-url="{{ route('sepay.transactions.check') }}" data-charge-id="{{ $chargeId }}">
                            Trạng thái chờ thanh toán <img src="{{ url('vendor/core/plugins/fob-sepay/images/loading.gif') }}" width="20" height="20" alt="Loading">
                        </div>
                    </div>
                </div>
            @endif

            <div @style(['display: none' => ! $isCompleted])
                class="transaction-status-done card text-center pb-3 pt-2"
                data-bb-sepay-status-done>
                <div class="p-4">
                    <div class="mb-2">
                        <x-core::icon name="ti ti-circle-check"/>
                    </div>
                    <h4>Thanh toán thành công</h4>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const copyButtons = document.querySelectorAll('[data-bb-toggle="copy"]');

        copyButtons.forEach((button) => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                const textToCopy = this.getAttribute('data-clipboard');
                fobCopyToClipboard(textToCopy);
            })
        })
    })

    $(document).ready(function() {
        const paymentStatuses = $('[data-bb-toggle="sepay-transaction-status"]')

        if (paymentStatuses.length) {
            paymentStatuses.each(function () {
                const elm = $(this)
                const parentBlock = elm.closest('[data-bb-sepay-block]')

                let interval = null
                interval = setInterval(() => fetchPaymentStatus(elm, parentBlock, interval), 3000)
            })
        }
    })

    function fetchPaymentStatus(elm, block, interval) {
        $.ajax({
            url: elm.data('url'),
            method: 'POST',
            data: {
                charge_id: elm.data('charge-id')
            },
            success: ({ data }) => {
                if (data.status.value === 'completed') {
                    block.find('[data-bb-sepay-status-done]').show()
                    block.find('[data-bb-sepay-info]').remove()

                    let paymentStatusElement = $(document).find('span[data-bb-target="ecommerce-order-payment-status"]');

                    if (paymentStatusElement.length && data.status_html) {
                        paymentStatusElement.html(data.status_html);
                    }

                    clearInterval(interval)
                }
            }
        })
    }

    async function fobCopyToClipboard(textToCopy) {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(textToCopy);
        } else {
            fobUnsecuredCopyToClipboard(textToCopy);
        }

        MainCheckout.showSuccess('Sao chép thành công!');
    }

    function fobUnsecuredCopyToClipboard(textToCopy) {
        const textArea = document.createElement('textarea');
        textArea.value = textToCopy;
        textArea.style.position = 'absolute';
        textArea.style.left = '-999999px';
        document.body.append(textArea);
        textArea.focus();
        textArea.select();

        try {
            document.execCommand('copy');
        } catch (error) {
            console.error('Unable to copy to clipboard', error);
        }

        document.body.removeChild(textArea);
    }
</script>
