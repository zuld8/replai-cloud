@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{ route('broadcast') }}" class="btn btn-primary">
        <i class="bx bx-chevron-left"></i>
        {{ __('broadcast.upselling.back_to_campaign') }}
    </a>
</div>
@endsection

@section('content')
<form action="<?= route('broadcast.store'); ?>" method="POST" class="row d-flex justify-content-center">
    @csrf
    <div class="col-xl-8 col-sm-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card mb-2">

            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-info-circle me-2"></i>
                    {{ __('broadcast.upselling.basic_info') }}
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Campaign Title -->
                    <div class="col-lg-8 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-purchase-tag text-primary me-1"></i>
                            {{ __('broadcast.upselling.campaign_title') }}
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
                                placeholder="{{ __('broadcast.upselling.name_placeholder') }}">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.name_help') }}
                        </small>
                    </div>

                    <!-- Delay -->
                    <div class="col-lg-4 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-timer text-warning me-1"></i>
                            {{ __('broadcast.upselling.delay') }}
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
                                value="<?= old('delay', 60); ?>"
                                min="1">
                            <span class="input-group-text">{{ __('broadcast.upselling.seconds') }}</span>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.delay_help') }}
                        </small>
                    </div>

                    <!-- Devices -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-devices text-success me-1"></i>
                            {{ __('broadcast.upselling.devices') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control devices" name="devices[]" multiple required>
                            @foreach ($devices as $device)
                            <option value="<?= $device->id; ?>"><?= $device->name; ?> - <?= $device->phone; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.devices_help') }}
                        </small>
                    </div>

                    <!-- Device Usage Option -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-shuffle text-info me-1"></i>
                            {{ __('broadcast.upselling.device_option') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="whatsapp_sender_notif" required>
                            <option value="sequence">{{ __('broadcast.upselling.device_sequence') }}</option>
                            <option value="spin" selected>{{ __('broadcast.upselling.device_spin') }}</option>
                            <option value="random">{{ __('broadcast.upselling.device_random') }}</option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.device_option_help') }}
                        </small>
                    </div>

                    <!-- Category -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-category text-warning me-1"></i>
                            {{ __('broadcast.upselling.contact_category') }}
                            <span class="badge bg-label-secondary ms-1">{{ __('broadcast.upselling.optional') }}</span>
                        </label>
                        <select class="form-control categories" name="category">
                            <option value="">{{__('scrapp.choose_category')}}</option>
                            @foreach ($categories as $category)
                            <option value="<?= $category->id; ?>"><?= $category->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.category_help') }}
                        </small>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-purchase-tag-alt text-info me-1"></i>
                            {{ __('broadcast.upselling.contact_labels') }}
                            <span class="badge bg-label-secondary ms-1">{{ __('broadcast.upselling.optional') }}</span>
                        </label>
                        <div class="row gy-2 mb-2">
                            @foreach ($labels as $label)
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        value="{{$label->id}}"
                                        name="label[]"
                                        id="label-{{$label->id}}">
                                    <label class="form-check-label" for="label-{{$label->id}}">
                                        <span class="fw-semibold">{{$label->name}}</span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.labels_help') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card custom-card">

            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-calendar-event me-2"></i>
                    {{ __('broadcast.upselling.schedule_config') }}
                </div>
            </div>

            <div class="card-body">

                <div class="row">
                    <!-- Schedule Frequency -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-repeat text-primary me-1"></i>
                            {{ __('broadcast.upselling.schedule_frequency') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="schedule_frequency" id="scheduleFrequency" required>
                            <option value="once" @if(old('schedule_frequency')=='once' ) selected @endif>{{ __('broadcast.upselling.freq_once') }}</option>
                            <option value="daily" @if(old('schedule_frequency')=='daily' ) selected @endif>{{ __('broadcast.upselling.freq_daily') }}</option>
                            <option value="monthly" @if(old('schedule_frequency')=='monthly' ) selected @endif>{{ __('broadcast.upselling.freq_monthly') }}</option>
                            <option value="yearly" @if(old('schedule_frequency')=='yearly' ) selected @endif>{{ __('broadcast.upselling.freq_yearly') }}</option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.frequency_help') }}
                        </small>
                    </div>

                    <!-- Daily Options -->
                    <div class="col-lg-6 mb-4 daily-options @if(old('schedule_frequency','once') != 'daily') d-none @endif">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-calendar-check text-success me-1"></i>
                            {{ __('broadcast.upselling.select_days') }}
                        </label>
                        <select class="form-control days-select" name="schedule_days[]" multiple>
                            <option value="monday">{{ __('broadcast.upselling.monday') }}</option>
                            <option value="tuesday">{{ __('broadcast.upselling.tuesday') }}</option>
                            <option value="wednesday">{{ __('broadcast.upselling.wednesday') }}</option>
                            <option value="thursday">{{ __('broadcast.upselling.thursday') }}</option>
                            <option value="friday">{{ __('broadcast.upselling.friday') }}</option>
                            <option value="saturday">{{ __('broadcast.upselling.saturday') }}</option>
                            <option value="sunday">{{ __('broadcast.upselling.sunday') }}</option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.days_help') }}
                        </small>
                    </div>

                    <!-- Monthly Options -->
                    <div class="col-lg-6 mb-4 monthly-options @if(old('schedule_frequency','once') != 'monthly') d-none @endif">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-calendar text-warning me-1"></i>
                            {{ __('broadcast.upselling.date_in_month') }}
                        </label>
                        <select class="form-control" name="schedule_date">
                            @for($i = 1; $i <= 31; $i++)
                                <option value="{{$i}}">{{$i}}</option>
                                @endfor
                                <option value="last">{{ __('broadcast.upselling.last_day') }}</option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.date_help') }}
                        </small>
                    </div>

                    <!-- Yearly Options -->
                    <div class="col-lg-6 mb-4 yearly-options @if(old('schedule_frequency','once') != 'yearly') d-none @endif">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-calendar-star text-danger me-1"></i>
                            {{ __('broadcast.upselling.specific_date') }}
                        </label>
                        <div class="row">
                            <div class="col-6">
                                <select class="form-control" name="schedule_month">
                                    <option value="1">{{ __('broadcast.upselling.january') }}</option>
                                    <option value="2">{{ __('broadcast.upselling.february') }}</option>
                                    <option value="3">{{ __('broadcast.upselling.march') }}</option>
                                    <option value="4">{{ __('broadcast.upselling.april') }}</option>
                                    <option value="5">{{ __('broadcast.upselling.may') }}</option>
                                    <option value="6">{{ __('broadcast.upselling.june') }}</option>
                                    <option value="7">{{ __('broadcast.upselling.july') }}</option>
                                    <option value="8">{{ __('broadcast.upselling.august') }}</option>
                                    <option value="9">{{ __('broadcast.upselling.september') }}</option>
                                    <option value="10">{{ __('broadcast.upselling.october') }}</option>
                                    <option value="11">{{ __('broadcast.upselling.november') }}</option>
                                    <option value="12">{{ __('broadcast.upselling.december') }}</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <select class="form-control" name="schedule_yearly_date">
                                    @for($i = 1; $i <= 31; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.yearly_help') }}
                        </small>
                    </div>

                    <div class="col-lg-4 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-time text-info me-1"></i>
                            {{ __('broadcast.upselling.sending_time') }}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-time-five"></i>
                            </span>
                            <input type="time"
                                class="form-control"
                                name="schedule_time"
                                value="<?= old('schedule_time', '09:00'); ?>">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.time_help') }}
                        </small>
                    </div>

                    <div class="col-lg-4 mb-4 start-date-field @if(old('schedule_frequency','once') == 'once') d-none @endif">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-calendar-plus text-success me-1"></i>
                            {{ __('broadcast.upselling.start_date') }}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-calendar"></i>
                            </span>
                            <input type="date"
                                class="form-control"
                                name="start_date"
                                value="<?= old('start_date', date('Y-m-d')); ?>">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.start_date_help') }}
                        </small>
                    </div>

                    <div class="col-lg-4 mb-4 end-date-field @if(old('schedule_frequency','once') == 'once') d-none @endif">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-calendar-x text-danger me-1"></i>
                            {{ __('broadcast.upselling.end_date') }}
                            <span class="badge bg-label-secondary ms-1">{{ __('broadcast.upselling.optional') }}</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-calendar"></i>
                            </span>
                            <input type="date"
                                class="form-control"
                                name="end_date"
                                value="<?= old('end_date'); ?>">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.end_date_help') }}
                        </small>
                    </div>

                    <!-- Broadcast Method -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-code-alt text-primary me-1"></i>
                            {{ __('broadcast.upselling.broadcast_method') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="broadcast_method" required id="formMethod">
                            <option value="template" @if(old('broadcast_method')=='template' ) selected @endif>{{ __('broadcast.upselling.method_template') }}</option>
                            <option value="ai" @if(old('broadcast_method')=='ai' ) selected @endif>{{ __('broadcast.upselling.method_ai') }}</option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.method_help') }}
                        </small>
                    </div>

                    <!-- AI Method -->
                    <div class="col-12 mb-4 aimethod @if(old('broadcast_method','template') == 'template') d-none @endif">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-brain text-danger me-1"></i>
                            {{ __('broadcast.upselling.ai_prompt') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text align-items-start pt-2">
                                <i class="bx bx-edit"></i>
                            </span>
                            <textarea class="form-control"
                                name="ai_prompt"
                                rows="4"
                                placeholder="{{ __('broadcast.upselling.ai_prompt_placeholder') }}">{{old('ai_prompt')}}</textarea>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.ai_prompt_help') }}
                        </small>
                    </div>

                    <!-- Template Method -->
                    <div class="col-lg-6 mb-4 templatemethod @if(old('broadcast_method','template') == 'ai') d-none @endif">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-file-blank text-info me-1"></i>
                            {{ __('broadcast.upselling.template_message') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control templates" name="template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($templates as $template)
                            <option value="<?= $template->id; ?>"><?= $template->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('broadcast.upselling.template_help') }}
                        </small>
                    </div>

                </div>


                <!-- Info Alert -->
                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="bx bx-info-circle me-2 fs-5"></i>
                    <div>
                        <strong>{{ __('broadcast.upselling.campaign_tips') }}</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>{{ __('broadcast.upselling.tip_ai_prompt') }}</li>
                            <li>{{ __('broadcast.upselling.tip_optimal_time') }}</li>
                            <li>{{ __('broadcast.upselling.tip_use_labels') }}</li>
                            <li>{{ __('broadcast.upselling.tip_frequency') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-info-circle"></i>
                    <span class="text-danger">*</span> {{ __('broadcast.upselling.required_field') }}
                </small>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-send me-1"></i>
                    {{ __('broadcast.upselling.create_campaign') }}
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
        // Initialize Select2
        $(".categories").select2({
            placeholder: "{{ __('broadcast.upselling.select_category') }}",
            allowClear: true
        });

        $(".templates").select2({
            placeholder: "{{ __('broadcast.upselling.select_template') }}",
            allowClear: true
        });

        $(".devices").select2({
            placeholder: "{{ __('broadcast.upselling.select_device') }}"
        });

        $(".days-select").select2({
            placeholder: "{{ __('broadcast.upselling.select_day') }}",
            allowClear: true
        });
    });

    // Handle broadcast method change
    $("#formMethod").on("change", function() {
        var value = $(this).val();

        if (value == 'ai') {
            $(".aimethod").removeClass('d-none');
            $(".templatemethod").addClass('d-none');
            $('textarea[name="ai_prompt"]').attr('required', true);
            $('select[name="template"]').attr('required', false);
        } else {
            $(".templatemethod").removeClass('d-none');
            $(".aimethod").addClass('d-none');
            $('select[name="template"]').attr('required', true);
            $('textarea[name="ai_prompt"]').attr('required', false);
        }
    });

    // Handle schedule frequency change
    $("#scheduleFrequency").on("change", function() {
        var value = $(this).val();

        // Hide all conditional fields first
        $(".daily-options, .monthly-options, .yearly-options, .start-date-field, .end-date-field").addClass('d-none');

        // Remove required attributes
        $('select[name="schedule_days[]"], select[name="schedule_date"], select[name="schedule_month"], select[name="schedule_yearly_date"]').attr('required', false);

        // Show relevant fields based on selection
        if (value == 'once') {
            // No additional fields needed
        } else if (value == 'daily') {
            $(".daily-options, .start-date-field, .end-date-field").removeClass('d-none');
            $('select[name="schedule_days[]"]').attr('required', true);
        } else if (value == 'monthly') {
            $(".monthly-options, .start-date-field, .end-date-field").removeClass('d-none');
            $('select[name="schedule_date"]').attr('required', true);
        } else if (value == 'yearly') {
            $(".yearly-options, .start-date-field, .end-date-field").removeClass('d-none');
            $('select[name="schedule_month"], select[name="schedule_yearly_date"]').attr('required', true);
        }
    });

    // Trigger initial state
    $("#formMethod").trigger('change');
    $("#scheduleFrequency").trigger('change');
</script>
@endsection