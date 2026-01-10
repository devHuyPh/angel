function renderGroupedProducts() {
    const container = document.getElementById('list-pro-new');
    if (!container) return;

    container.innerHTML = '';

    const cartTextareas = document.querySelectorAll('textarea[id^="car-item-"]');
    const grouped = {};

    cartTextareas.forEach(textarea => {
        try {
            const cartItem = JSON.parse(textarea.textContent.trim()); // ✅
            const productId = cartItem.id;

            const productTextarea = document.querySelector(`#product-\\[${productId}\\]`);

            if (!productTextarea) {
                console.warn(`Không tìm thấy textarea với id: product-${productId}`);
                return;
            }

            const productData = JSON.parse(productTextarea.textContent.trim()); // ✅
            console.log(cartItem, productData); // ✅ Bây giờ không còn lỗi

            const key = cartItem.name;

            if (!grouped[key]) {
                grouped[key] = {
                    rowId: cartItem.rowId,
                    id: cartItem.id,
                    name: cartItem.name,
                    qty: cartItem.qty,
                    qtyReal:cartItem.qty,
                    image: textarea.dataset.image || productData.image || '/default.jpg',
                    price: cartItem.priceFormat,
                    variation_attributes: cartItem.options?.attributes || '',
                    store_id: productData.store_id,
                    max: productData.quantity,
                };
            } else {
                grouped[key].qty += cartItem.qty;
            }

        } catch (err) {
            console.error('Lỗi phân tích JSON:', err);
        }
    });

    Object.values(grouped).forEach(product => {
        const html = `
            <div class="row cart-item">
                <div class="col-3">
                    <div class="checkout-product-img-wrapper">
                        <img class="item-thumb img-thumbnail img-rounded"
                                src="${product.image}"
                                alt="${product.name}">
                        <span class="checkout-quantity">${product.qty}</span>
                    </div>
                </div>
                <div class="col">
                    <p class="mb-0">${product.name}</p>
                    ${product.variation_attributes ? `<p class="mb-0"><small>${product.variation_attributes}</small></p>` : ''}
                    <div class="ec-checkout-quantity" data-url="/gio-hang/update"
                        data-row-id="${product.rowId}">
                        <button type="button" id="${product.id}_minus" class="ec-checkout-quantity-control ec-checkout-quantity-minus"
                            data-bb-toggle="decrease-qty">
                            <svg class="icon  svg-icon-ti-ti-minus" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 12l14 0"></path>
                            </svg>
                        </button>
                        <input type="number" class="d-none" style="display: none !important;" name="items[${product.rowId}][values][qty]" value="${product.qtyReal}"
                            min="1" max="${product.max}" data-bb-toggle="update-cart" id="${product.id}_qty" readonly="">

                        <input type="number" value="${product.qty}" min="1"
                            max="${product.max}" readonly="">
                        <button type="button" id="${product.id}_plus" class="ec-checkout-quantity-control ec-checkout-quantity-plus"
                            data-bb-toggle="increase-qty">
                            <svg class="icon  svg-icon-ti-ti-plus" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 5l0 14"></path>
                                <path d="M5 12l14 0"></path>
                            </svg> 
                        </button>
                    </div>
                </div>
                <div class="col-auto text-end">
                    <p>${product.price}</p>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    });
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

function renderShippingNew() {
    const storeSelect = document.getElementById('selected_store');
    const shippingTarget = document.getElementById('shipping-method-new');

    if (!storeSelect || !shippingTarget) return;

    function renderShippingMethod(storeId) {
        const html = `
            <div class="shipping-method-wrapper py-3">
                <div class="payment-checkout-form">
                    <h6>Phương thức vận chuyển:</h6>

                    <input name="shipping_option_new" type="hidden" value="4">

                    <div id="shipping-method-new">
                        <ul class="list-group list_payment_method">
                            <li class="list-group-item">
                                <input id="shipping-method-new-default-4" name="shipping_method[new]"
                                    class="magic-radio shipping_method_input" checked="checked" data-id="new"
                                    data-option="4" type="radio" value="default">
                                <label for="shipping-method-new-default-4">
                                    <div>
                                        <span>
                                            Giao từ kho gần nhất -
                                            <strong>Miễn phí vận chuyển</strong>
                                        </span>
                                    </div>
                                    <div></div>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="payment-info-loading loading-spinner" style="display: none;"></div>
            </div>
        `;
        shippingTarget.innerHTML = html;
    }

    // Bắt sự kiện Select2 chọn option
    $(storeSelect).on('select2:select', function () {
        const storeId = $(this).val();
        if (storeId) {
            renderShippingMethod(storeId);
        } else {
            shippingTarget.innerHTML = '';
        }
    });

    // Nếu đã chọn sẵn khi tải trang
    if (storeSelect.value) {
        renderShippingMethod(storeSelect.value);
    }
}

function handleStoreSelection() {
    const selectStore = document.getElementById('selected_store');
    const storeNew = document.getElementById('store-new');
    const storeListTextarea = document.getElementById('list-store-render');

    if (!selectStore || !storeNew || !storeListTextarea) return;

    let storeData;
    try {
        const json = JSON.parse(storeListTextarea.value);
        storeData = json.data || [];
    } catch (e) {
        console.error("Dữ liệu JSON không hợp lệ:", e);
        return;
    }

    $(selectStore).on('change select2:select', function () {
        const selectedId = parseInt(this.value);
        const store = storeData.find(item => item.id === selectedId);
        if (!store) {
            storeNew.innerHTML = '';
            return;
        }

        const logo = store.logo_square || store.logo || 'https://chichi.hisotechgroup.com/vendor/core/core/base/images/placeholder.png';
        const avgStar = parseFloat(store.avg_star || 0);
        const reviewCount = parseInt(store.review_count || 0);

        const starPercent = Math.min(100, Math.max(0, avgStar * 20)); // 0–100%

        const html = `
            <div class="p-2" style="background: antiquewhite;">
                <img class="img-fluid rounded"
                    src="${logo}" alt="${store.name}"
                    style="max-width: 30px; margin-inline-end: 3px;">
                <span class="font-weight-bold" data-storeid="${store.id}">${store.name}</span>
                <div class="d-flex align-items-center gap-2">
                    <div class="bb-product-rating" style="--bb-rating-size: 70px">
                        <span style="width: ${starPercent}% !important;"></span>
                    </div>
                    <span class="small text-muted">
                        (${reviewCount === 1 ? '1 Review' : `${reviewCount} Reviews`})
                    </span>
                </div>
            </div>
        `;

        storeNew.innerHTML = html;
    });

    if (selectStore.value) {
        $(selectStore).trigger('change');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    renderGroupedProducts();
    renderShippingNew();
    handleStoreSelection();
});

$(document).ajaxComplete(function (event, xhr, settings) {
    if (
        settings.url.includes("cart/update") ||
        settings.url.includes("checkout/update")
    ) {
        renderGroupedProducts();
        renderShippingNew();
        handleStoreSelection();
    }
});
