@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Add a new address'))

@section('content')
    <style>
        @media (max-width: 767.98px) {
            .profile__tab-content {
                padding: 0 !important;

            }

            .form-control {
                font-size: 16px !important;
            }
        }
    </style>
    <div class="header d-flex d-md-none align-items-center mb-3 bg-white py-2 px-3"
        style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
        <a href="{{ route('customer.address') }}" class="back-btn text-success">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="header-title text-success">{{ __('Thêm địa chỉ') }}</h1>
    </div>
    <div class="container pb-3">
        {!! Form::open(['route' => 'customer.address.create']) !!}
        @include(EcommerceHelper::viewPath('customers.address.form'), [
            'address' => new Botble\Ecommerce\Models\Address(),
            'form',
        ])
        {!! Form::close() !!}
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const citySelect = document.querySelector('[data-type="city"]');
            const wardSelect = document.querySelector('[data-type="ward"]');
            const wardNameInput = document.getElementById('ward_name');
            const selectedWard = @json(old('ward'));
            const selectedWardName = @json(old('ward_name'));
            const apiToken = "2c2e62dc-ee72-11ef-a3aa-e2c95c1f5bee";

            const resetWard = (placeholder = "Chọn phường/xã...") => {
                if (!wardSelect) {
                    return;
                }

                wardSelect.innerHTML = `<option value="">${placeholder}</option>`;
                wardSelect.setAttribute("disabled", "true");

                if (wardNameInput) {
                    wardNameInput.value = "";
                }
            };

            const syncWardName = () => {
                if (!wardSelect || !wardNameInput) {
                    return;
                }

                const option = wardSelect.options[wardSelect.selectedIndex];
                wardNameInput.value = option && option.value ? option.textContent.trim() : "";
            };

            const loadWards = async (districtId, wardValue = "", wardLabel = "") => {
                if (!wardSelect || !districtId) {
                    resetWard();
                    return;
                }

                resetWard();

                try {
                    const response = await fetch(
                        `https://online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id=${districtId}`,
                        {
                            method: "GET",
                            headers: {
                                "Content-Type": "application/json",
                                "Token": apiToken,
                            },
                        }
                    );

                    const data = await response.json();

                    if (data.code === 200 && Array.isArray(data.data)) {
                        data.data.forEach((ward) => {
                            const option = document.createElement("option");
                            option.value = ward.WardCode;
                            option.textContent = ward.WardName;

                            if (wardValue && String(ward.WardCode) === String(wardValue)) {
                                option.selected = true;
                            }

                            wardSelect.appendChild(option);
                        });

                        wardSelect.removeAttribute("disabled");

                        if (wardValue && wardNameInput) {
                            wardNameInput.value = wardLabel || wardSelect.options[wardSelect.selectedIndex]?.textContent.trim() || "";
                        }
                    } else {
                        resetWard("Không có dữ liệu...");
                    }
                } catch (error) {
                    console.error("Lỗi khi tải danh sách phường/xã:", error);
                    resetWard("Lỗi tải dữ liệu...");
                }
            };

            if (wardSelect) {
                wardSelect.addEventListener("change", syncWardName);
            }

            if (citySelect) {
                citySelect.addEventListener("change", function(event) {
                    const districtId = event.target.value;
                    if (!districtId) {
                        resetWard();
                        return;
                    }

                    loadWards(districtId);
                });

                if (citySelect.value) {
                    loadWards(citySelect.value, selectedWard, selectedWardName);
                }
            }
        });
    </script>
@endsection
