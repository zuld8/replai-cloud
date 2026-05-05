@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{ route('telegrams') }}" class="btn btn-outline-secondary">
        <i class="bx bx-chevron-left"></i>
        {{__('platform.telegram.back_to_list')}}
    </a>
</div>
@endsection

@section('content')
<div class="row d-flex justify-content-center">
    <div class="col-xl-9">
        <x-validation-component></x-validation-component>
        <form action="<?= route('telegram.store'); ?>" enctype="multipart/form-data" method="POST">
            @csrf
            
            <!-- Card Informasi Perangkat -->
            <div class="card custom-card mb-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bxl-telegram me-2"></i>{{__('platform.telegram.device_info')}}
                    </div>
                </div>
                <div class="card-body">
                    <!-- Nama Perangkat -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-device-phone me-1"></i>{{__('platform.telegram.device_name')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-rename"></i>
                            </span>
                            <input class="form-control" name="name" value="<?= old('name'); ?>" type="text" placeholder="{{__('platform.telegram.device_name_placeholder')}}" required>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.telegram.device_name_hint')}}
                        </small>
                    </div>

                    <!-- Bot Token -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-key me-1"></i>{{__('platform.telegram.bot_token')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-lock-alt"></i>
                            </span>
                            <input class="form-control" name="token" value="<?= old('token'); ?>" type="text" placeholder="{{__('platform.telegram.bot_token_placeholder')}}" required>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.telegram.bot_token_hint')}}
                        </small>
                    </div>

                    <!-- Team Member -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-group me-1"></i>{{__('platform.telegram.team_member')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control users" name="agent[]" multiple="multiple" required>
                            @foreach ($users as $user)
                            <option value="<?= $user->id; ?>"><?= $user->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.telegram.team_member_hint')}}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Card AI Agent -->
            <div class="card custom-card mb-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-bot me-2"></i>{{__('platform.telegram.ai_config')}}
                    </div>
                </div>
                <div class="card-body">
                    <!-- Metode AutoReply -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-message-dots me-1"></i>{{__('platform.telegram.auto_reply_method')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-cog"></i>
                            </span>
                            <select class="form-control methodreply" name="method" required>
                                <option value="all" selected>{{__('platform.telegram.method_all')}}</option>
                                <option value="chatbot">{{__('platform.telegram.method_chatbot')}}</option>
                                <option value="ai">{{__('platform.telegram.method_ai')}}</option>
                            </select>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.telegram.auto_reply_method_hint')}}
                        </small>
                    </div>

                    <!-- AI Training (Fine Tunnel) -->
                    <div class="mb-3 finetunneldata">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-brain me-1"></i>{{__('platform.telegram.ai_training')}}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-data"></i>
                            </span>
                            <select class="form-control" name="tunnel">
                                <option value="">{{__('platform.telegram.choose_ai_training')}}</option>
                                @foreach ($fineTunnels as $fine)
                                <option value="<?= $fine->id; ?>"><?= $fine->name; ?></option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.telegram.ai_training_hint')}}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Card Jadwal & Batasan -->
            <div class="card custom-card mb-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-time-five me-2"></i>{{__('platform.telegram.schedule_limits')}}
                    </div>
                </div>
                <div class="card-body">
                    <!-- Toggle Hari Tertentu -->
                    <div class="mb-3">
                        <div class="toggle-wrapper">
                            <div>
                                <label class="form-label mb-0 fw-semibold">
                                    <i class="bx bx-calendar-x me-1"></i>{{__('platform.telegram.inactive_certain_day')}}
                                </label>
                                <small class="text-muted d-block">{{__('platform.telegram.inactive_certain_day_hint')}}</small>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" class="certain_day_toggle" name="certain_day" value="yes">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Pilihan Hari -->
                    <div class="mb-3 d-none" id="certain_day_select">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-calendar-check me-1"></i>{{__('platform.telegram.select_days')}}
                        </label>
                        <select class="form-control days" name="days[]" multiple="multiple">
                            <option value="mon">{{__('platform.telegram.day_monday')}}</option>
                            <option value="tue">{{__('platform.telegram.day_tuesday')}}</option>
                            <option value="wed">{{__('platform.telegram.day_wednesday')}}</option>
                            <option value="thu">{{__('platform.telegram.day_thursday')}}</option>
                            <option value="fri">{{__('platform.telegram.day_friday')}}</option>
                            <option value="sat">{{__('platform.telegram.day_saturday')}}</option>
                            <option value="sun">{{__('platform.telegram.day_sunday')}}</option>
                        </select>
                    </div>

                    <!-- Toggle Jam Tertentu -->
                    <div class="mb-3">
                        <div class="toggle-wrapper">
                            <div>
                                <label class="form-label mb-0 fw-semibold">
                                    <i class="bx bx-time me-1"></i>{{__('platform.telegram.inactive_certain_time')}}
                                </label>
                                <small class="text-muted d-block">{{__('platform.telegram.inactive_certain_time_hint')}}</small>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" class="certain_time_toggle" name="certain_time" value="yes">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Jam Mulai & Selesai -->
                    <div class="row d-none" id="time_inputs">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-time-five me-1"></i>{{__('platform.telegram.start_time')}}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-play-circle"></i>
                                </span>
                                <input class="form-control" name="start_time" type="time">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>{{__('platform.telegram.start_time_hint')}}
                            </small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-time-five me-1"></i>{{__('platform.telegram.end_time')}}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-stop-circle"></i>
                                </span>
                                <input class="form-control" name="end_time" type="time">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>{{__('platform.telegram.end_time_hint')}}
                            </small>
                        </div>
                    </div>

                    <!-- Toggle Batasan Harian -->
                    <div class="mb-3">
                        <div class="toggle-wrapper">
                            <div>
                                <label class="form-label mb-0 fw-semibold">
                                    <i class="bx bx-message-square-minus me-1"></i>{{__('platform.telegram.daily_limit')}}
                                </label>
                                <small class="text-muted d-block">{{__('platform.telegram.daily_limit_hint')}}</small>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" class="daily_limit_toggle" name="daily_limit" value="yes">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Batas Harian -->
                    <div class="mb-3 d-none" id="daily_limit_input">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-calculator me-1"></i>{{__('platform.telegram.enter_daily_limit')}}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-hash"></i>
                            </span>
                            <input class="form-control" name="limit" value="<?= old('limit'); ?>" type="number" placeholder="{{__('platform.telegram.daily_limit_placeholder')}}">
                            <span class="input-group-text">{{__('platform.telegram.daily_limit_suffix')}}</span>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.telegram.daily_limit_hint_input')}}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <small class="text-muted">
                    <i class="bx bx-info-circle me-1"></i>{{__('platform.telegram.required_fields')}}
                </small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                        <i class="bx bx-x me-1"></i>{{__('platform.telegram.cancel')}}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>{{__('platform.telegram.save_device')}}
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
        $('.days').select2({
            placeholder: "{{__('platform.telegram.select_days_placeholder')}}",
            allowClear: true
        });
        
        $('.users').select2({
            placeholder: "{{__('platform.telegram.team_member_placeholder')}}",
            allowClear: true
        });
    });

    // Method reply toggle
    $(".methodreply").on("change", function() {
        if ($(this).val() == 'ai' || $(this).val() == 'all') {
            $(".finetunneldata").removeClass('d-none');
        } else if ($(this).val() == 'chatbot') {
            $(".finetunneldata").addClass('d-none');
        }
    });

    // Daily limit toggle
    $(".daily_limit_toggle").on("change", function() {
        if ($(this).is(':checked')) {
            $("#daily_limit_input").removeClass('d-none');
        } else {
            $("#daily_limit_input").addClass('d-none');
        }
    });

    // Certain day toggle
    $(".certain_day_toggle").on("change", function() {
        if ($(this).is(':checked')) {
            $("#certain_day_select").removeClass('d-none');
        } else {
            $("#certain_day_select").addClass('d-none');
        }
    });

    // Certain time toggle
    $(".certain_time_toggle").on("change", function() {
        if ($(this).is(':checked')) {
            $("#time_inputs").removeClass('d-none');
        } else {
            $("#time_inputs").addClass('d-none');
        }
    });

    // Handle form submission for checkboxes
    $('form').on('submit', function() {
        // Handle certain_day toggle
        if ($('.certain_day_toggle').length) {
            var hiddenInput = $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'certain_day')
                .val($('.certain_day_toggle').is(':checked') ? 'yes' : 'no');
            $('.certain_day_toggle').after(hiddenInput);
            $('.certain_day_toggle').removeAttr('name');
        }

        // Handle certain_time toggle
        if ($('.certain_time_toggle').length) {
            var hiddenInput = $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'certain_time')
                .val($('.certain_time_toggle').is(':checked') ? 'yes' : 'no');
            $('.certain_time_toggle').after(hiddenInput);
            $('.certain_time_toggle').removeAttr('name');
        }

        // Handle daily_limit toggle
        if ($('.daily_limit_toggle').length) {
            var hiddenInput = $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'daily_limit')
                .val($('.daily_limit_toggle').is(':checked') ? 'yes' : 'no');
            $('.daily_limit_toggle').after(hiddenInput);
            $('.daily_limit_toggle').removeAttr('name');
        }
    });
</script>
@endsection