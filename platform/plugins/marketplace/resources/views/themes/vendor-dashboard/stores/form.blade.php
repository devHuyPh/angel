@php
    $layout = MarketplaceHelper::viewPath('vendor-dashboard.layouts.master');
@endphp

@extends('plugins/marketplace::stores.form')

@section('content')
    @parent

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        document.addEventListener("change", function (event) {
          if (event.target.matches('[data-type="city"]')) {
            let districtId = event.target.value; // Lấy giá trị quận/huyện đã chọn
            let wardSelect = document.querySelector('[data-type="ward"]'); // Chọn dropdown ward

            if (!districtId) {
              wardSelect.innerHTML = `<option value="">Chọn phường/xã...</option>`;
              wardSelect.setAttribute("disabled", "true");
              return;
            }

            let apiUrl = `https://online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id=${districtId}`;
            let token = "2c2e62dc-ee72-11ef-a3aa-e2c95c1f5bee"; // Token GHN của bạn

            // Gọi API lấy danh sách phường/xã
            fetch(apiUrl, {
              method: "GET",
              headers: {
                "Content-Type": "application/json",
                "Token": token
              }
            })
              .then(response => response.json())
              .then(data => {
                wardSelect.innerHTML = `<option value="">Chọn phường/xã...</option>`; // Reset danh sách

                if (data.code === 200 && Array.isArray(data.data)) {
                  data.data.forEach(ward => {
                    let option = document.createElement("option");
                    option.value = ward.WardCode;
                    option.textContent = ward.WardName;
                    wardSelect.appendChild(option);
                  });
                  wardSelect.removeAttribute("disabled"); // Bật dropdown khi có dữ liệu
                } else {
                  wardSelect.innerHTML = `<option value="">Không có dữ liệu...</option>`;
                }
              })
              .catch(error => {
                console.error("Lỗi khi tải danh sách phường/xã:", error);
                wardSelect.innerHTML = `<option value="">Lỗi tải dữ liệu...</option>`;
                wardSelect.setAttribute("disabled", "true");
              });
          }
        });
      });
    </script>
@stop
