document.addEventListener("DOMContentLoaded", function () {
    // let isCouponProcessing = false;

    function calculateShippingFee() {

        let fromDistrictId = document.getElementById("from_city")?.value;
        let fromWardCode = document.getElementById("from_ward")?.value;
        let toDistrictId = localStorage.getItem('to_city') || document.getElementById("to_city")?.value;
        let toWardCode = localStorage.getItem('to_ward') || document.getElementById("to_ward")?.value;
        let rawOrderAmount = document.getElementById("total_amount").getAttribute("data-price");
        // let discountPercentageDowline = parseFloat(document.getElementById("discount_percentage_dowline")?.value) || 0;
        let discountInput = document.getElementById("discount_percentage_dowline");
        let discountValue = parseFloat(discountInput?.value) || 0;
        let discountDiv = document.getElementById("p_discount_percentage_dowline");
        let discountText = document.getElementById("discountper");
        
        let discountPoinDiv = document.getElementById("p_discount_poin");
        let discountPoinText = document.getElementById("poin_discount");
        let walletCheckbox = document.getElementById("use_wallet_2");
        let walletPoints = parseFloat(walletCheckbox.value, 10) || 0;

        // console.log(`disscount ${discountPercentageDowline}`);

        console.log(`From dc: ${fromDistrictId}`);
        console.log(`From wc: ${fromWardCode}`);

        console.log(`To dc: ${toDistrictId}`);
        console.log(`To wc: ${toWardCode}`);
        
        let couponValue = document.getElementById("couponValue")?.value || localStorage.getItem("couponValue") || 0;
        let couponType = document.getElementById("couponType")?.value || localStorage.getItem("couponType") || "";

        if (!toWardCode) {
            console.warn("⚠️ Chưa chọn phường/xã, không thể tính phí vận chuyển!");
            return;
        }

        if (!fromDistrictId || !fromWardCode || !toDistrictId) {
            console.log("Thiếu thông tin địa chỉ để tính phí vận chuyển.");
            return;
        }

        let products = [];
        document.querySelectorAll("[id$='_name']").forEach((nameInput) => {
            let productId = nameInput.id.replace("_name", "").replace(/\D/g, '');
            if (!productId) return;

            let quantityInput = document.getElementById(`${productId}_quantity`) || 1;
            let lengthInput = document.getElementById(`${productId}_length`);
            let widthInput = document.getElementById(`${productId}_width`);
            let heightInput = document.getElementById(`${productId}_height`);
            let weightInput = document.getElementById(`${productId}_weight`);

            if (!quantityInput || !lengthInput || !widthInput || !heightInput || !weightInput) {
                console.warn(`⚠️ Thiếu thông tin sản phẩm cho ID: ${productId}`);
                return;
            }

            let product = {
                name: nameInput.value,
                quantity: parseInt(quantityInput.value) || 1,
                length: parseInt(lengthInput.value) || 0,
                width: parseInt(widthInput.value) || 0,
                height: parseInt(heightInput.value) || 0,
                weight: parseInt(weightInput.value) || 0,
            };
            products.push(product);
        });

        if (products.length === 0) {
            console.warn("⚠️ Không có sản phẩm hợp lệ để tính phí vận chuyển.");
            return;
        }

        let maxLength = 0, maxWidth = 0, totalHeight = 0, totalWeight = 0;
        products.forEach((product) => {
            maxLength = Math.max(maxLength, product.length);
            maxWidth = Math.max(maxWidth, product.width);
            totalHeight += product.height;
            totalWeight += product.weight;
        });

        let convertedWeight = (maxLength * maxWidth * totalHeight) / 5;
        let chargeableWeight = Math.max(totalWeight, convertedWeight);

        let requestData = {
            service_type_id: 2,
            from_district_id: parseInt(fromDistrictId),
            from_ward_code: fromWardCode,
            to_district_id: parseInt(toDistrictId),
            to_ward_code: toWardCode,
            length: maxLength,
            width: maxWidth,
            height: totalHeight,
            weight: Math.ceil(chargeableWeight),
            insurance_value: 0,
            coupon: null,
            items: products,
        };

        console.log("🚀 Request Data:", requestData);

        fetch("https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Token": "2c2e62dc-ee72-11ef-a3aa-e2c95c1f5bee",
                "ShopId": "5643397",
            },
            body: JSON.stringify(requestData),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data && data.data && data.data.total) {
                    let shippingFee = data.data.total;
                    let shippingFeeNew = shippingFee;

                    rawOrderAmount = parseInt(rawOrderAmount, 10) || 0;
                    rawOrderAmount += shippingFee;
                    discountAmount = Math.ceil((discountValue / 100) * rawOrderAmount);
                    rawOrderAmount -= discountAmount;
                    rawOrderAmount = Math.ceil(rawOrderAmount);

                    if (discountValue > 0) {
                        discountDiv.style.display = "block"; // Hiển thị div
                        discountText.textContent = `- (${discountValue}%) ${formatPrice(discountAmount)}`; // Gán giá trị vào <p id="discountper">
                    }

                    document.querySelectorAll("[id^='apply_']").forEach((button) => {
                        let dataCode = button.getAttribute("data-code"); // Lấy giá trị data-code
                    
                        if (dataCode) {
                            let [value, type] = dataCode.split("','").map(item => item.trim()); // Tách giá trị và loại mã giảm giá
                    
                            value = parseFloat(value.replace(/[^0-9.-]+/g, "")); // Chuyển đổi thành số
                    
                            if (type === "shipping" && value <= shippingFee) {
                                let couponItem = button.closest(".checkout__coupon-item"); // Tìm item cha của nút apply
                                if (couponItem) {
                                    couponItem.style.display = "none";
                                }
                            }
                        }
                    });
                    

                    if (couponValue != '' && couponType == 'shipping') {
                        rawOrderAmount -= parseFloat(shippingFee);
                        shippingFeeNew = parseFloat(shippingFee) - shippingFee; 

                    } else {
                        
                        let couponDiscountAmount = document.getElementById("couponDiscountAmount")?.getAttribute("data-code") || 0;
                        rawOrderAmount -= parseFloat(couponDiscountAmount);
                    }
                    console.log(`phi ship:${formatPrice(shippingFee)}`);
                    
                    if(shippingFeeNew == 0){
                        document.getElementById("shipping_amount").innerHTML = formatPriceDiscount(shippingFee);
                    }else{
                        document.getElementById("shipping_amount").innerText = formatPrice(shippingFee);
                    }

                    if (walletCheckbox.checked && walletPoints > 0) {
                        if (walletPoints <= rawOrderAmount) {
                            rawOrderAmount -= walletPoints;
                            console.log(`Đã trừ: ${rawOrderAmount}`)
                            discountPoinDiv.style.display = "block";
                            discountPoinText.textContent = `- ${formatPrice(walletPoints)}`;
                        } else {
                            console.warn("⚠️ Số điểm sử dụng lớn hơn tổng tiền đơn hàng!");
                        }
                    }else{
                        discountPoinDiv.style.display = "none";
                        walletPoints = 0;
                    }
                    
                    document.getElementById("total_amount").innerText = formatPrice(rawOrderAmount);
                    // document.getElementById("poin_discount").innerText = formatPrice(walletPoints);
                    document.getElementById("shipping_amount_inp").value = shippingFeeNew;
                    document.getElementById("discount_poin").value = walletPoints;
                    document.getElementById("discount_value").value = discountAmount;
                    document.getElementById("total_amount_ipn").value = rawOrderAmount;

                    document.getElementById("couponValue").value = couponValue;
                    document.getElementById("couponType").value = couponType;
                } else {
                    console.error("❌ Lỗi khi lấy dữ liệu từ API GHN:", data);
                }
            })
            .catch((error) => {
                console.error("❌ Lỗi khi gọi API GHN:", error);
            });
    }
    calculateShippingFee()

    document.getElementById("address_id")?.addEventListener("change", function () {
        console.log('Sửa address_id');
        let selectedOption = this.options[this.selectedIndex];
        let dataCode = selectedOption.getAttribute("data-code"); // Lấy dữ liệu từ data-code
        if (!dataCode) {
            localStorage.removeItem('to_city');
            localStorage.removeItem('to_ward');
            localStorage.removeItem('to_wardName');

            let selectedValue = document.getElementById('address_ward').value;
            console.log(selectedValue);
            let wardCode = selectedValue.split(".")[0];
            let wardName = selectedValue.split(".")[1];

            localStorage.setItem('to_ward', wardCode);
            localStorage.setItem('to_wardName', wardName);
            // console.warn("⚠️ Không có data-code cho địa chỉ được chọn.");
            // return;
            calculateShippingFee();
        }else{
            let [toDistrictId, toWardCode] = dataCode.split(", ").map(item => item.trim());

            console.log("📍 Cập nhật toDistrictId & toWardCode:", { toDistrictId, toWardCode });

            localStorage.removeItem('to_city');
            localStorage.removeItem('to_ward');
            localStorage.removeItem('to_wardName');

            document.getElementById("to_city").value = toDistrictId;
            document.getElementById("to_ward").value = toWardCode;

            
            initialToWardValue = toWardCode;

            calculateShippingFee();
        }

    });
// 17/05/2025
    // document.getElementById("address_city").addEventListener("change", function () {
    //     let selectedValue = this.value; // Lấy giá trị của select
    //     localStorage.setItem('to_city', selectedValue)
    // });

    $(document).on('change', '#address_city', function () {
        // const districtId = $(this).val();
        // fetchWards(districtId);
        // console.log("City changed to:", districtId);
        let selectedValue = this.value; // Lấy giá trị của select
        localStorage.setItem('to_city', selectedValue)
    });

    // document.getElementById("address_ward").addEventListener("change", function () {
    //     let selectedValue = this.value; // Lấy giá trị từ select
    //     let wardCode = selectedValue.split(".")[0];
    //     let wardName = selectedValue.split(".")[1];

    //     localStorage.setItem('to_ward', wardCode)
    //     localStorage.setItem('to_wardName', wardName)

    //     calculateShippingFee();
    // });

    $(document).on('change', '#address_ward', function () {
        const address_ward = $(this).val();
        let selectedValue = this.value;
        let wardCode = selectedValue.split(".")[0];
        let wardName = selectedValue.split(".")[1];

        localStorage.setItem('to_ward', wardCode);
        localStorage.setItem('to_wardName', wardName);
        // fetchWards(districtId);
        console.log("Ward changed to:", address_ward);
        calculateShippingFee();
    });
// 17/05/2025
    function getOldWard() {
        let old_toWard = localStorage.getItem('to_ward'); // Lấy ward code từ localStorage
        let old_toWardName = localStorage.getItem('to_wardName'); // Lấy ward name từ localStorage
    
        if (old_toWard && old_toWard !== '') {
            let selectElement = document.getElementById("address_ward");
    
            if (selectElement) {
                // Kiểm tra xem option có tồn tại hay không
                let existingOption = selectElement.querySelector(`option[value='${old_toWard}']`);
                
                if (!existingOption) {
                    // Nếu chưa có, thêm option mới
                    let newOption = document.createElement("option");
                    newOption.value = old_toWard;
                    newOption.textContent = old_toWardName || `Phường/Xã ${old_toWard}`;
                    selectElement.appendChild(newOption);
                }
    
                // Chọn lại option đã lưu
                selectElement.value = old_toWard;
                // console.log(`✅ Đã chọn lại Phường/Xã: ${old_toWardName} (${old_toWard})`);
            } else {
                console.warn("❌ Không tìm thấy select #to_ward!");
            }
        } else {
            // console.log('⚠️ Không có Phường/Xã đã chọn trong LocalStorage.');
        }
    }    
    getOldWard();

    function formatPrice(price) {
        return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "₫";
    }

    function formatPriceDiscount(price) {
        let formattedPrice = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "₫";
        return `<s>${formattedPrice}</s>`; // Thêm thẻ <s> để gạch ngang số tiền
    }

    function handleCouponUpdate(actionType, discountCode, discountValue, discountType, isSuccess) {
        if (isSuccess) {
            // console.log(`🎉 ${actionType} mã giảm giá: ${discountCode}, Giá trị: ${discountValue}, Loại: ${discountType}`);
            
            setTimeout(() => {
                document.getElementById("couponValue").value = discountValue;
                document.getElementById("couponType").value = discountType;
    
                // ✅ Lưu vào LocalStorage
                localStorage.setItem("couponValue", discountValue);
                localStorage.setItem("couponType", discountType);
    
                calculateShippingFee();
            }, 1500);
        } else {
            console.warn(`❌ ${actionType} mã giảm giá thất bại: ${discountCode}`);
        }
    }
    
    $(document).on("click", "#use_wallet_2", function () {
        calculateShippingFee();
    });
    
    $(document).on("click", "[id^='apply_']", function () {
        let discountId = $(this).attr("id").replace("apply_", "");
        let discountCode = $(this).data("discount-code") || "";
        let discountData = $(this).data("code"); // Lấy từ data-code
    
        if (discountData) {
            let [discountValue, discountType] = discountData.split("','").map(item => item.replace(/['"]/g, "").trim());
    
            // console.log(`🛒 Đang áp dụng mã: ${discountCode}, Giá trị: ${discountValue}, Loại: ${discountType}`);
    
            // **Lắng nghe phản hồi AJAX để kiểm tra thành công/thất bại**
            $(document).ajaxComplete(function (event, xhr, settings) {
                if (settings.url.includes("coupon/apply")) {
                    try {
                        let response = JSON.parse(xhr.responseText);
    
                        if (response.error) {
                            console.error(`❌ Mã giảm giá ${discountCode} bị lỗi: ${response.message}`);
                            handleCouponUpdate("Áp dụng", discountCode, "", "", false);
                        } else {
                            // console.log(`🎉 Mã giảm giá ${discountCode} đã được áp dụng thành công!`);
                            localStorage.setItem("discountId", discountId);
                            handleCouponUpdate("Áp dụng", discountCode, discountValue, discountType, true);
                            setTimeout(() => {
                                getOldWard()
                                calculateShippingFee();
                            }, 1500);
                        }
                    } catch (e) {
                        console.error("❌ Lỗi xử lý phản hồi AJAX:", e);
                    }
                }
            });
        }
    });
    
    $(document).on("click", "[id^='remove_']", function () {
        let discountId = $(this).attr("id").replace("remove_", "");
        // console.log(`❌ Đang xóa mã giảm giá ID: ${discountId}`);
    
        $(document).ajaxComplete(function (event, xhr, settings) {
            if (settings.url.includes("coupon/remove")) {
                try {
                    let response = JSON.parse(xhr.responseText);
    
                    if (response.error) {
                        console.error("❌ Xóa mã giảm giá thất bại:", response.message);
                    } else {
                        // console.log("✅ Mã giảm giá đã được xóa thành công!");
                        handleCouponUpdate("Xóa", "", "", "", true);
                        setTimeout(() => {
                            calculateShippingFee();
                        }, 1500);
                    }
                } catch (e) {
                    console.error("❌ Lỗi khi xử lý phản hồi AJAX:", e);
                }
            }
        });
    });
    //-------
    $(document).ajaxComplete(function (event, xhr, settings) {
        if (
            settings.url.includes("cart/update") ||
            settings.url.includes("checkout/update")   
        ) {
            calculateShippingFee();
            // getOldWard();
        }
    });
    $(document).ajaxComplete(function (event, xhr, settings) {
        if (settings.url.includes("coupon/apply") || settings.url.includes("coupon/remove")) {
            // console.log("🛒 Coupon Updated! Waiting for final update...");
    
            // isCouponProcessing = true;
            
            setTimeout(() => {
                // isCouponProcessing = false;
                getOldWard();
                calculateShippingFee();
            }, 1500);
        }
    });


    document.addEventListener("click", function (event) {
        let btn = event.target.closest("#btn_checkout");
        
        if (btn) {
            handleCheckout();
        }
    });
    // console.log();
    
    function handleCheckout() {
        localStorage.removeItem('couponType');
        localStorage.removeItem('couponValue');
        localStorage.removeItem('discountId');
        localStorage.removeItem('to_ward');
        localStorage.removeItem('to_wardName');
        localStorage.removeItem('to_city');
    
        let path = window.location.pathname;
        let token = path.split("/tien-hanh-thanh-toan/")[1];
    
        let shippingAmount = document.getElementById("shipping_amount_inp").value || 0;
        let totalAmount = document.getElementById("total_amount_ipn").value || 0;
        let discountValue = document.getElementById("discount_value").value || 0;
        let discountPoin = document.getElementById("discount_poin").value || 0;
    
        // console.log("🚀 Sending checkout data:", { token, shippingAmount, totalAmount });
    
        fetch("/ghn/update-session", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                token: token,
                shipping_amount_inp: shippingAmount,
                discount_amount_ipn: discountValue,
                total_amount_ipn: totalAmount,
                discount_poin: discountPoin
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
            } else {
                console.error("❌ Error updating order:", data);
                alert("Failed to update order. Please try again!");
            }
        })
        .catch(error => {
            console.error("❌ AJAX Error:", error);
            // alert("There was an error processing your request.");
        });
    }

    $(document).ready(function () {
        let uniqueStoreIds = new Set(); // Tạo tập hợp Set để lưu store_id duy nhất
    
        $("input[id$='_store_id']").each(function () {
            let storeId = $(this).attr("id").split("_")[0]; // Lấy store_id từ id
            uniqueStoreIds.add(storeId); // Thêm vào Set (Set tự động loại bỏ trùng lặp)
        });
    
    });    
    
});