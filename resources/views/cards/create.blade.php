@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-credit-card me-2"></i>{{ __('core/base::layouts.create_title') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data" id="cardForm">
                            @csrf
                            <div class="row">
                                <!-- Card Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-tag me-1"></i>{{ __('core/base::layouts.name_card') }} <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name') }}"
                                        placeholder="{{ __('core/base::layouts.name_placeholder') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Card Number -->
                                <div class="col-md-6 mb-3">
                                    <label for="number" class="form-label">
                                        <i class="fas fa-hashtag me-1"></i>{{ __('core/base::layouts.number') }} <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('number') is-invalid @enderror"
                                        id="number" name="number" value="{{ old('number') }}"
                                        placeholder="{{ __('core/base::layouts.number_placeholder') }}" maxlength="20"
                                        required>
                                    @error('number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Cashback -->
                                <div class="col-md-6 mb-3">
                                    <label for="cashback" class="form-label">
                                        <i class="fas fa-percentage me-1"></i>{{ __('core/base::layouts.cashback') }}
                                    </label>
                                    <div class="input-group">
                                        <input type="float" class="form-control @error('cashback') is-invalid @enderror"
                                            id="cashback" name="cashback" value="{{ old('cashback') }}"
                                            placeholder="{{ __('core/base::layouts.cashback_placeholder') }}" step="0.01"
                                            min="0" max="100">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('cashback')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Value -->
                                <div class="col-md-6 mb-3">
                                    <label for="value" class="form-label">
                                        <i class="fas fa-dollar-sign me-1"></i>{{ __('core/base::layouts.value_card') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('value') is-invalid @enderror"
                                            id="value" name="value" value="{{ old('value') }}"
                                            placeholder="{{ __('core/base::layouts.value_placeholder') }}" step="0.01"
                                            min="0" required>
                                    </div>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Expiration Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="expiration_date" class="form-label">
                                        <i
                                            class="fas fa-calendar-alt me-1"></i>{{ __('core/base::layouts.expiration_date') }}
                                    </label>
                                    <input type="month" class="form-control @error('expiration_date') is-invalid @enderror"
                                        id="expiration_date" name="expiration_date"
                                        value="{{ old('expiration_date', isset($item) ? \Carbon\Carbon::parse($item->expiration_date)->format('Y-m') : '') }}"
                                        min="{{ date('Y-m') }}">
                                    @error('expiration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Gift Description -->
                                <div class="col-md-6 mb-3">
                                    <label for="gift" class="form-label">
                                        <i class="fas fa-gift me-1"></i>{{ __('core/base::layouts.gift_description') }}
                                    </label>
                                    <textarea class="form-control @error('gift_description') is-invalid @enderror"
                                        id="gift_description" name="gift_description" rows="3"
                                        placeholder="{{ __('core/base::layouts.gift_description_placeholder') }}"
                                        maxlength="500">{{ old('gift_description') }}</textarea>
                                    <div class="form-text">
                                        <span id="giftCounter">0</span>/500 {{ __('core/base::layouts.characters') }}
                                    </div>
                                    @error('gift_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Image Upload with Preview -->
                                <div class="col-12 mb-4">
                                    <label for="image" class="form-label">
                                        <i class="fas fa-image me-1"></i>{{ __('core/base::layouts.image_card') }}
                                    </label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                                        name="image" accept="image/jpeg,image/png,image/jpg,image/gif"
                                        onchange="previewImage(event)">
                                    <div class="form-text">
                                        {{ __('core/base::layouts.image_supported') }}
                                    </div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <!-- Image Preview -->
                                    <div class="mt-3" id="imagePreviewContainer" style="display: none;">
                                        <label class="form-label">{{ __('core/base::layouts.image_preview') }}:</label>
                                        <div class="border rounded p-3 bg-light" style="max-width: 350px;">
                                            <img id="imagePreview" src="/placeholder.svg"
                                                alt="{{ __('core/base::layouts.image_preview_alt') }}"
                                                class="img-fluid rounded shadow-sm"
                                                style="max-height: 250px; width: auto; display: block; margin: 0 auto;">
                                            <div class="mt-3 text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="removeImage()">
                                                    <i class="fas fa-trash me-1"></i>
                                                    {{ __('core/base::layouts.remove_image') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-1"></i>
                                            {{ __('core/base::layouts.back_to_list') }}
                                        </a>
                                        <div>
                                            <button type="reset" class="btn btn-outline-warning me-2" onclick="resetForm()">
                                                <i class="fas fa-undo me-1"></i> {{ __('core/base::layouts.reset_form') }}
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                                <i class="fas fa-save me-1"></i>
                                                {{ __('core/base::layouts.create_button') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');

            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('{{ __('core/base::layouts.file_size_error') }}');
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                    previewContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        }

        function removeImage() {
            const imageInput = document.getElementById('image');
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');

            imageInput.value = '';
            preview.src = '';
            previewContainer.style.display = 'none';
        }

        function resetForm() {
            document.getElementById('cardForm').reset();
            removeImage();
            updateGiftCounter();
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        }

        function updateGiftCounter() {
            const giftTextarea = document.getElementById('gift');
            const counter = document.getElementById('giftCounter');
            counter.textContent = giftTextarea.value.length;
        }

        document.getElementById('cardForm').addEventListener('submit', function (e) {
            const submitBtn = document.getElementById('submitBtn');
            const requiredFields = ['name', 'number', 'value'];
            let isValid = true;

            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            requiredFields.forEach(function (fieldName) {
                const field = document.getElementById(fieldName);
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });

            const expirationDate = document.getElementById('expiration_date');
            if (expirationDate.value && new Date(expirationDate.value) < new Date()) {
                expirationDate.classList.add('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                alert('{{ __('core/base::validation.required') }}');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> {{ __('core/base::layouts.creating') }}';
        });

        document.querySelectorAll('input[required]').forEach(function (input) {
            input.addEventListener('blur', function () {
                if (!this.value.trim()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            input.addEventListener('input', function () {
                if (this.classList.contains('is-invalid') && this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        });

        document.getElementById('gift').addEventListener('input', updateGiftCounter);

        document.addEventListener('DOMContentLoaded', function () {
            updateGiftCounter();
        });

        document.getElementById('number').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
        });
    </script>

    <style>
        .card {
            border: none;
            border-radius: 10px;
        }

        .card-header {
            border-radius: 10px 10px 0 0 !important;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-label i {
            color: #6c757d;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        #imagePreviewContainer {
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #ced4da;
            color: #6c757d;
            font-weight: 500;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.875rem;
        }

        .form-text {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
