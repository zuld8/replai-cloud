@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="row d-flex justify-content-center">
    <div class="col-xl-9">
        <x-validation-component></x-validation-component>
        <form action="<?= route('stores.store'); ?>" method="POST">
            @csrf
            
            <!-- Card Informasi Kontak -->
            <div class="card custom-card mb-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-user-plus me-2"></i>{{__('contact.contact_information')}}
                    </div>
                </div>
                <div class="card-body">
                    <!-- Nama Kontak -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-user me-1"></i>{{__('contact.contact_name')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-user-circle"></i>
                            </span>
                            <input type="text" class="form-control" name="name" 
                                   placeholder="{{__('contact.contact_name_placeholder')}}" 
                                   required value="<?= old('name'); ?>">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('contact.contact_name_help')}}
                        </small>
                    </div>

                    <!-- Kategori -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-category me-1"></i>{{__('contact.category')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control categories" name="category" required>
                            <option value="">{{__('contact.select_category')}}</option>
                            @foreach ($categories as $category)
                            <option value="<?= $category->id; ?>"><?= $category->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('contact.category_help')}}
                        </small>
                    </div>

                    <!-- No. WhatsApp -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bxl-whatsapp me-1"></i>{{__('contact.whatsapp_number')}}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-phone"></i>
                            </span>
                            <input type="text" class="form-control" name="phone" id="waPhone" 
                                   placeholder="{{__('contact.phone_placeholder')}}" 
                                   value="{{ old('phone') }}">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('contact.whatsapp_help')}}
                        </small>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-envelope me-1"></i>{{__('contact.email_optional')}}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-at"></i>
                            </span>
                            <input type="email" class="form-control" name="email" 
                                   placeholder="{{__('contact.email_placeholder')}}" 
                                   value="<?= old('email'); ?>">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('contact.email_help')}}
                        </small>
                    </div>

                    <!-- Label -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-purchase-tag me-1"></i>{{__('contact.label')}}
                        </label>
                        <select class="form-control" name="label">
                            <option value="">{{__('contact.select_label')}}</option>
                            @foreach ($labels as $label)
                            <option value="<?= $label->id; ?>"><?= $label->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('contact.label_help')}}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Card Regional (Opsional) -->
            <div class="card custom-card mb-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-map-alt me-2"></i>{{__('contact.regional_info')}}
                    </div>
                </div>
                <div class="card-body">
                    <!-- Provinsi -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-map me-1"></i>{{__('contact.province')}}
                        </label>
                        <select class="form-control provinces" name="province">
                            <option value="">{{__('contact.select_province')}}</option>
                            @foreach ($provinces as $province)
                            <option value="<?= $province->id; ?>"><?= $province->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('contact.province_help')}}
                        </small>
                    </div>

                    <!-- Kota/Kabupaten -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-building me-1"></i>{{__('contact.city')}}
                        </label>
                        <select class="form-control cities" name="city">
                            <option value="">{{__('contact.select_city')}}</option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('contact.city_help')}}
                        </small>
                    </div>

                    <!-- Kecamatan -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-map-pin me-1"></i>{{__('contact.district')}}
                        </label>
                        <select class="form-control districts" name="district">
                            <option value="">{{__('contact.select_district')}}</option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('contact.district_help')}}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <small class="text-muted">
                    <i class="bx bx-info-circle me-1"></i>{{__('contact.required_fields')}} <span class="text-danger">*</span> {{__('contact.must_be_filled')}}
                </small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                        <i class="bx bx-x me-1"></i>{{__('contact.cancel_button')}}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>{{__('contact.save_contact')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
 
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        // Initialize select2
        $('.provinces').select2({
            placeholder: "{{__('contact.select_province')}}",
            allowClear: true
        });
        
        $('.cities').select2({
            placeholder: "{{__('contact.select_city')}}",
            allowClear: true
        });
        
        $(".districts").select2({
            placeholder: "{{__('contact.select_district')}}",
            allowClear: true
        });
        
        $(".categories").select2({
            placeholder: "{{__('contact.select_category')}}",
            allowClear: true
        });
    });

    // Province change handler
    $(".provinces").on("change", function() {
        $(".cities").val("").trigger('change');
        $(".districts").val("").trigger('change');

        if ($(this).val()) {
            $('.cities').select2({
                placeholder: "{{__('contact.select_city')}}",
                allowClear: true,
                ajax: {
                    url: `/app/master/components/cities?province=${$(this).val()}`,
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.type + ' ' + item.name,
                                    id: item.id,
                                }
                            }),
                        }
                    },
                    cache: false,
                },
            });
        } else {
            $('.cities').select2({
                placeholder: "{{__('contact.select_city')}}",
                allowClear: true
            });
        }
    });

    // City change handler
    $(".cities").on("change", function() {
        $(".districts").val("").trigger('change');
        
        if ($(this).val()) {
            $('.districts').select2({
                placeholder: "{{__('contact.select_district')}}",
                allowClear: true,
                ajax: {
                    url: `/app/master/components/districts?city=${$(this).val()}`,
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id,
                                }
                            }),
                        }
                    },
                    cache: false,
                },
            });
        } else {
            $('.districts').select2({
                placeholder: "{{__('contact.select_district')}}",
                allowClear: true
            });
        }
    });
</script>
@endsection