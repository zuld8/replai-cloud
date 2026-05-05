@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{ route('blash') }}" class="btn btn-primary d-flex align-items-center">
        <i class="bx bx-chevron-left"></i>
        <span class="ms-1">{{ __('blash.back_to') }}</span>
    </a>
</div>
@endsection

@section('content')
<form action="<?= route('blash.store'); ?>" method="POST" class="row d-flex justify-content-center">
    @csrf
    <div class="col-xl-8 col-sm-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card mb-2">

            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-info-square me-2"></i>
                    {{ __('broadcast.wa.broadcast_info') }}
                </div>
            </div>

            <div class="card-body">

                <div class="row">
                    <!-- Title -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-purchase-tag text-primary me-1"></i>
                            {{__('blash.title')}}
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
                                placeholder="{{ __('broadcast.wa.name_placeholder') }}">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.wa.name_help') }}
                        </small>
                    </div>

                    <!-- Schedule -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-calendar text-danger me-1"></i>
                            {{__('scrapp.schedule')}}
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
                            {{ __('broadcast.wa.schedule_help') }}
                        </small>
                    </div>

                    <!-- Template -->
                    <div class="col-lg-4 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-file-blank text-info me-1"></i>
                            {{__('sidebar.message_template')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control templates" name="template" required>
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($templates as $template)
                            <option value="<?= $template->id; ?>"><?= $template->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.wa.template_help') }}
                        </small>
                    </div>

                    <!-- Devices -->
                    <div class="col-lg-4 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-devices text-success me-1"></i>
                            {{ __('broadcast.wa.devices') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control devices" name="devices[]" multiple required>
                            @foreach ($devices as $device)
                            <option value="<?= $device->id; ?>"><?= $device->name; ?> - <?= $device->phone; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.wa.devices_help') }}
                        </small>
                    </div>

                    <!-- WhatsApp Sender Option -->
                    <div class="col-lg-4 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-shuffle text-warning me-1"></i>
                            {{ __('broadcast.wa.device_option') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="whatsapp_sender_notif" required>
                            <option value="sequence">{{ __('broadcast.wa.device_sequence') }}</option>
                            <option value="spin" selected>{{ __('broadcast.wa.device_spin') }}</option>
                            <option value="random">{{ __('broadcast.wa.device_random') }}</option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.wa.device_option_help') }}
                        </small>
                    </div>

                    <!-- Delay -->
                    <div class="col-lg-4 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-timer text-primary me-1"></i>
                            {{ __('broadcast.wa.delay') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-stopwatch"></i>
                            </span>
                            <input type="number"
                                class="form-control"
                                name="delay"
                                required
                                min="10"
                                max="300"
                                value="<?= old('delay') ?? 60; ?>">
                            <span class="input-group-text">{{ __('broadcast.wa.seconds') }}</span>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-error-circle text-warning"></i>
                            {{ __('broadcast.wa.delay_help') }}
                        </small>
                    </div>

                    <!-- Stop Sending -->
                    <div class="col-lg-4 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-stop-circle text-danger me-1"></i>
                            {{ __('broadcast.wa.stop_sending') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-hash"></i>
                            </span>
                            <input type="number"
                                class="form-control"
                                name="stop_sending"
                                required
                                min="10"
                                max="300"
                                value="<?= old('stop_sending') ?? 20; ?>">
                            <span class="input-group-text">{{ __('broadcast.wa.numbers') }}</span>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.wa.stop_sending_help') }}
                        </small>
                    </div>

                    <!-- Rest Sending -->
                    <div class="col-lg-4 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-pause-circle text-info me-1"></i>
                            {{ __('broadcast.wa.rest_sending') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-time"></i>
                            </span>
                            <input type="number"
                                class="form-control"
                                name="rest_sending"
                                required
                                min="10"
                                max="300"
                                value="<?= old('rest_sending') ?? 90; ?>">
                            <span class="input-group-text">{{ __('broadcast.wa.seconds') }}</span>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.wa.rest_sending_help') }}
                        </small>
                    </div>

                </div>

            </div>
        </div>
        <div class="card custom-card">

            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-target-lock me-2"></i>
                    {{ __('broadcast.wa.target_template') }}
                </div>
            </div>

            <div class="card-body">

                <div class="row">
                    <!-- Category -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-category text-warning me-1"></i>
                            {{__('sidebar.category')}}
                            <span class="badge bg-label-secondary ms-1">{{ __('broadcast.wa.optional') }}</span>
                        </label>
                        <select class="form-control categories" name="category">
                            <option value="">{{__('scrapp.choose_category')}}</option>
                            @foreach ($categories as $category)
                            <option value="<?= $category->id; ?>"><?= $category->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.wa.category_help') }}
                        </small>
                    </div>

                    <!-- WhatsApp Group -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bxl-whatsapp text-success me-1"></i>
                            {{ __('broadcast.wa.whatsapp_group') }}
                            <span class="badge bg-label-secondary ms-1">{{ __('broadcast.wa.optional') }}</span>
                        </label>
                        <select class="form-control groups" name="group">
                            <option value="">{{ __('broadcast.wa.choose_group') }}</option>
                            @foreach ($groups as $group)
                            <option value="<?= $group->id; ?>"><?= $group->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.wa.whatsapp_group_help') }}
                        </small>
                    </div>


                </div>

                <hr class="my-4">

                <!-- Section: Lokasi Target -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bx bx-map-pin me-1"></i>
                        {{ __('broadcast.wa.location_target') }}
                    </h6>
                    <div class="row">

                        <!-- Province -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-map text-primary me-1"></i>
                                {{__('sidebar.state')}}
                                <span class="badge bg-label-secondary ms-1">{{ __('broadcast.wa.optional') }}</span>
                            </label>
                            <select class="form-control provinces" name="province">
                                <option value="">{{__('master.directory.choose_state')}}</option>
                                @foreach ($provinces as $province)
                                <option value="<?= $province->id; ?>"><?= $province->name; ?></option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{ __('broadcast.wa.province_help') }}
                            </small>
                        </div>

                        <!-- City -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-building text-success me-1"></i>
                                {{__('sidebar.city')}}
                                <span class="badge bg-label-secondary ms-1">{{ __('broadcast.wa.optional') }}</span>
                            </label>
                            <select class="form-control cities" name="city">
                                <option value="">{{__('master.directory.choose_city')}}</option>
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{ __('broadcast.wa.city_help') }}
                            </small>
                        </div>

                        <!-- District -->
                        <div class="col-lg-4 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-current-location text-info me-1"></i>
                                {{__('sidebar.district')}}
                                <span class="badge bg-label-secondary ms-1">{{ __('broadcast.wa.optional') }}</span>
                            </label>
                            <select class="form-control districts" name="district">
                                <option value="">{{__('master.directory.choose_district')}}</option>
                            </select>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{ __('broadcast.wa.district_help') }}
                            </small>
                        </div>
                    </div>
                </div>


                <hr class="my-4">


                <!-- Info Card -->
                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="bx bx-info-circle me-2 fs-5"></i>
                    <div>
                        <strong>{{ __('broadcast.wa.safe_sending_tips') }}</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>{{ __('broadcast.wa.tip_delay') }}</li>
                            <li>{{ __('broadcast.wa.tip_batch') }}</li>
                            <li>{{ __('broadcast.wa.tip_rest') }}</li>
                            <li>{{ __('broadcast.wa.tip_multiple') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-info-circle"></i>
                    <span class="text-danger">*</span> {{ __('broadcast.wa.required_field') }}
                </small>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-send me-1"></i>
                    {{__('general.add_data')}}
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
            placeholder: '{{__("master.directory.choose_state")}}',
            allowClear: true
        });

        $('.cities').select2({
            placeholder: '{{__("master.directory.choose_city")}}',
            allowClear: true
        });

        $(".districts").select2({
            placeholder: '{{__("master.directory.choose_district")}}',
            allowClear: true
        });

        $(".categories").select2({
            placeholder: '{{__("scrapp.choose_category")}}',
            allowClear: true
        });

        $(".templates").select2({
            placeholder: '{{__("blash.choose_template")}}'
        });

        $(".groups").select2({
            placeholder: '{{ __("broadcast.wa.choose_group") }}',
            allowClear: true
        });

        $(".devices").select2({
            placeholder: '{{ __("broadcast.wa.choose_device") }}'
        });
    });

    // Province Change - Load Cities
    $(".provinces").on("change", function() {
        $(".cities").val("").trigger('change');
        $(".districts").val("").trigger('change');

        if ($(this).val()) {
            $('.cities').select2({
                placeholder: '{{__("master.directory.choose_city")}}',
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
                placeholder: '{{__("master.directory.choose_district")}}',
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