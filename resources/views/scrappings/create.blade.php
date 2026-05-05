@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('scrappings')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{ __('scraping.maps.back_to') }}
    </a>
    <a href="{{route('scrappings')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{ __('scraping.maps.back_to') }}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<form action="<?= route('scrappings.store'); ?>" method="POST" class="row d-flex justify-content-center">
    @csrf
    <div class="col-xl-8 col-sm-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card">

            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-info-square me-2"></i>
                    {{ __('scraping.maps.scraping_info') }}
                </div>
            </div>

            <div class="card-body">

                <div class="row">
                    <!-- Category -->
                    <div class="col-lg-12 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-category text-warning me-1"></i>
                            {{ __('sidebar.category') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control categories" name="category" required>
                            <option value="">{{ __('scraping.maps.choose_category') }}</option>
                            @foreach ($categories as $category)
                            <option value="<?= $category->id; ?>"><?= $category->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('scraping.maps.category_help') }}
                        </small>
                    </div>

                    <!-- Name -->
                    <div class="col-lg-12 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-purchase-tag text-primary me-1"></i>
                            {{ __('general.insert_name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-rename"></i>
                            </span>
                            <input type="text"
                                class="form-control"
                                name="name"
                                required
                                value="<?= old('name'); ?>"
                                placeholder="{{ __('scraping.maps.name_placeholder') }}">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('scraping.maps.name_help') }}
                        </small>
                    </div>

                    <!-- Schedule -->
                    <div class="col-lg-12 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-calendar text-danger me-1"></i>
                            {{ __('scraping.maps.schedule') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-time-five"></i>
                            </span>
                            <input type="datetime-local"
                                class="form-control"
                                name="schedule"
                                required
                                value="<?= old('schedule'); ?>">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('scraping.maps.schedule_help') }}
                        </small>
                    </div>
                </div>
 
            </div> 
        </div>
    </div>

    <div class="col-xl-8 col-sm-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card">

            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-map-pin me-2"></i>
                    {{ __('scraping.maps.location_target') }}
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Province -->
                    <div class="col-lg-12 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-map text-primary me-1"></i>
                            {{ __('sidebar.state') }}
                            <span class="badge bg-label-secondary ms-1">{{ __('scraping.maps.optional') }}</span>
                        </label>
                        <select class="form-control provinces" name="province">
                            <option value="">{{ __('master.directory.choose_state') }}</option>
                            @foreach ($provinces as $province)
                            <option value="<?= $province->id; ?>"><?= $province->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('scraping.maps.province_help') }}
                        </small>
                    </div>

                    <!-- City -->
                    <div class="col-lg-12 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-building text-success me-1"></i>
                            {{ __('sidebar.city') }}
                            <span class="badge bg-label-secondary ms-1">{{ __('scraping.maps.optional') }}</span>
                        </label>
                        <select class="form-control cities" name="city">
                            <option value="">{{ __('master.directory.choose_city') }}</option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('scraping.maps.city_help') }}
                        </small>
                    </div>

                    <!-- District -->
                    <div class="col-lg-12 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-current-location text-info me-1"></i>
                            {{ __('sidebar.district') }}
                            <span class="badge bg-label-secondary ms-1">{{ __('scraping.maps.optional') }}</span>
                        </label>
                        <select class="form-control districts" name="district">
                            <option value="">{{ __('master.directory.choose_district') }}</option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('scraping.maps.district_help') }}
                        </small>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Info Alert -->
                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="bx bx-info-circle me-2 fs-5"></i>
                    <div>
                        <strong>{{ __('scraping.maps.tips_title') }}</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>{{ __('scraping.maps.tip_location') }}</li>
                            <li>{{ __('scraping.maps.tip_category') }}</li>
                            <li>{{ __('scraping.maps.tip_schedule') }}</li>
                            <li>{{ __('scraping.maps.tip_auto') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-info-circle"></i>
                    <span class="text-danger">*</span> {{ __('scraping.maps.required_field') }}
                </small>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-search-alt me-1"></i>
                    {{ __('general.add_data') }}
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 with placeholders
        $('.provinces').select2({
            placeholder: '{{ __("master.directory.choose_state") }}',
            allowClear: true
        });

        $('.cities').select2({
            placeholder: '{{ __("master.directory.choose_city") }}',
            allowClear: true
        });

        $(".districts").select2({
            placeholder: '{{ __("master.directory.choose_district") }}',
            allowClear: true
        });

        $(".categories").select2({
            placeholder: '{{ __("scraping.maps.choose_category") }}'
        });
    });

    // Province Change - Load Cities
    $(".provinces").on("change", function() {
        $(".cities").val("").trigger('change');
        $(".districts").val("").trigger('change');

        if ($(this).val()) {
            $('.cities').select2({
                placeholder: '{{ __("master.directory.choose_city") }}',
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
        }
    });

    // City Change - Load Districts
    $(".cities").on("change", function() {
        $(".districts").val("").trigger('change');

        if ($(this).val()) {
            $('.districts').select2({
                placeholder: '{{ __("master.directory.choose_district") }}',
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
        }
    });
</script>
@endsection