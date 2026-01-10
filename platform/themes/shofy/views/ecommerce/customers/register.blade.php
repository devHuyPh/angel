<style>
    #referral_tooltip {
        display: none;
        width: 100%;
        position: absolute;
        background: #228822;
        color: #ffffff;
        padding: 10px 15px;
        border-radius: 8px;
        top: -88%;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10000;
        white-space: nowrap;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        transition: opacity 0.2s ease;
    }



    #referral_code:focus+#referral_tooltip,
    #referral_code:hover+#referral_tooltip {
        display: block;
    }
</style>

@php
    Theme::set('breadcrumbHeight', 100);
    Theme::set('pageTitle', __('Register'));
@endphp

{{-- Duong Css --}}
<style>
    .custom-dropdown {
        position: relative;
        display: inline-block;
        /* border-right: none; */
        /* width: 200px; */

    }

    .custom-dropdown-toggle {
        background: #f1f1f1;
        border: 1px solid #ccc;
        border-right: none;
        padding: 8px 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    .custom-dropdown-toggle img {
        /* margin-right: 8px; */
        height: 16px;
    }

    .input_phone,
    .custom-dropdown {
        display: inline-block !important;
        vertical-align: middle !important;
    }

    .input_phone {
        /* margin-right: 10px; */
    }

    .custom-dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: white;
        border: 1px solid #ccc;
        z-index: 1000;
        display: none;
        max-height: 200px;
        overflow-y: auto;
    }

    .custom-dropdown-menu li {
        list-style: none;
    }

    .custom-dropdown-menu a {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        text-decoration: none;
        color: #000;
    }

    .custom-dropdown-menu a:hover {
        background: #f0f0f0;
    }

    .custom-dropdown-menu a img {
        /* margin-right: 8px; */
        height: 16px;
    }

    .input_phone,
    .custom-dropdown {
        display: inline-block;
        position: relative;
    }

    /* Di chuyển custom-dropdown về trước input */
    .custom-dropdown {
        position: absolute;
        left: 5rem;
        top: 0;
        transform: translateX(-110%);
        /* Di chuyển sang trái khỏi vị trí gốc */
        z-index: 2;
    }

    /* Tạo khoảng trống bên trái cho input để tránh bị đè */
    .input_phone {
        width: 449px !important;
        left: 4.2rem;
    }

    #phone {
        padding: 0 20px !important;
    }

    @media (max-width: 1280px) {
        .custom-dropdown {

            left: 0 !important;
            top: 0 !important;
            transform: none !important; 
            width: 80px !important; 
            height: 42px; 
        }

        .custom-dropdown-toggle {
            width: 100%;
            justify-content: center; 
            padding: 8px 4px; 
        }

        .input_phone {
            left: 0 !important;
            margin-left: 80px !important; 
            width: calc(100% - 80px) !important; 
        }
    }
    
    #dropdownMenu {
        position: relative;
        /* hoặc absolute/fixed tùy layout của bạn */
        z-index: 9999;
    }

    .auth-card form .auth-input-icon {
        z-index: 1 !important;
    }

    #password,
    #password_confirmation {
        z-index: 0 !important;
    }
</style>
{{-- end Duong Css --}}

{!! $form->bannerDirection('horizontal')->renderForm() !!}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referralCode = document.getElementById('referral_code');
        const tooltip = document.getElementById('referral_tooltip');
        const showTooltip = () => {
            tooltip.style.display = 'block';
            //    setTimeout(() => {
            //  tooltip.style.display = 'none';
            // }, 2000);
        };
        if (document.activeElement == referralCode) {
            referralCode.addEventListener('click', showTooltip);
            referralCode.addEventListener('focus', showTooltip);
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy ref_code từ query string
    const params = new URLSearchParams(window.location.search);
    const refCode = params.get('ref_code'); // lấy giá trị của ref_code

    // Tìm input referral
    const referralField = document.getElementById('referral_code');
			console.log('params: ', params);
			console.log('refCode: ', refCode);
			console.log('referralField: ', referralField);
    if (referralField) {
        if (refCode && refCode.trim() !== '') {
            referralField.value = refCode;
						referralField.setAttribute('readonly',true);
        } else {
            referralField.value = ''; 
        }
    }
		console.log('referralField: ', referralField);
    });
</script>

{{-- Duong Js --}}
<script>
    fetch('/vendor/core/core/json/countries.json')
        .then(response => response.json())
        .then(countries => {
            const dropdownMenu = document.getElementById("dropdownMenu");

            countries.forEach(country => {
                const li = document.createElement("li");
                li.innerHTML = `
                    <a href="#" class="text-dark"
                        onclick="selectCountry(event, '${country.name}', '${country.flag}', '${country.code}', '${country.dialCode}')">
                        <img src="${country.flag}" width="20"> ${country.dialCode}
                    </a>
                `;
                dropdownMenu.appendChild(li);
            });
        })
        .catch(error => {
            console.error('Lỗi khi tải countries.json:', error);
        });

    function toggleDropdown() {
        const menu = document.getElementById("dropdownMenu");
        menu.style.display = menu.style.display === "none" ? "block" : "none";
    }

    function selectCountry(event, name, flagUrl, code, dialCode) {
        event.preventDefault();
        document.getElementById("selectedFlag").src = flagUrl;
        document.querySelector(".custom-dropdown-toggle").innerHTML = `
            <img src="${flagUrl}" id="selectedFlag" width="20"> ${dialCode}
        `;
        document.getElementById("countryInput").value = code;
        toggleDropdown();
    }

    // Đóng dropdown khi click ra ngoài
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById("dropdownMenu");
        const toggleBtn = document.querySelector(".custom-dropdown-toggle");

        if (dropdown.style.display === "block" &&
            !dropdown.contains(event.target) &&
            !toggleBtn.contains(event.target)) {
            dropdown.style.display = "none";
        }
    });
</script>
{{-- Hiển thị tt người giới thiệu qua uu_id --}}
<script>
    let timeout = null;

    function fetchReferral(uuid) {
        if (!uuid) {
            $('#referral_code').text('');
            return;
        }

        $.ajax({
            url: '/api/customer/search-by-uuid', 
            type: 'GET',
            data: {
                uuid: uuid
            },
            success: function(response) {
                $('#referral_code').html(
                    `<strong>${response.name}</strong> - (${response.phone})`
                );
            },
            error: function() {
                console.log('Lỗi khi tìm kiếm người dùng');
            }
        });
    }

    $(document).ready(function() {
        // ✅ Nếu input đã có sẵn giá trị, gọi ngay
        const initialValue = $('#referral_code').val();
        if (initialValue) {
            fetchReferral(initialValue);
        }

        // ✅ Gọi khi người dùng nhập
        $('#referral_code').on('input', function() {
            clearTimeout(timeout);

            timeout = setTimeout(function() {
                const uuid = $('#referral_code').val();
                fetchReferral(uuid);
            }, 500);
        });
    });
</script>

{{-- Hiển thị tt người giới thiệu qua sdt --}}
<script>
    let timeoutPhone = null;

    function fetchReferral(phone) {
        if (!phone) {
            $('#result-phone').text('');
            return;
        }

        $.ajax({
            url: '/api/customer/search-by-phone', 
            type: 'GET',
            data: {
                phone: phone
            },
            success: function(response) {
                $('#result-phone').val(
                    `<strong>${response.name}</strong> - (${response.phone})`
                );
            },
            error: function() {
                // $('#result-phone').text('Lỗi khi tìm kiếm người dùng');
                console.log('Lỗi khi tìm kiếm người dùng');
            }
        });
    }

    $(document).ready(function() {
        const initialValue = $('#referral_code').val();
        if (initialValue) {
            fetchReferral(initialValue);
        }

        $('#referral_code').on('input', function() {
            clearTimeout(timeoutPhone);

            timeoutPhone = setTimeout(function() {
                const phone = $('#referral_code').val();
                fetchReferral(phone);
            }, 1000);
        });
    });
</script>

{{-- End Duong Js --}}
