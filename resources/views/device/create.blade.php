@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="row d-flex justify-content-center">
    <div class="col-xl-9">
        <x-validation-component></x-validation-component>
        <form action="<?= route('device.store'); ?>" enctype="multipart/form-data" method="POST">
            @csrf
            
            <!-- Card Informasi Perangkat -->
            <div class="card custom-card mb-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-mobile-alt me-2"></i>{{__('platform.whatsapp.device_info')}}
                    </div>
                </div>
                <div class="card-body">
                    <!-- Nama Perangkat -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-device-phone me-1"></i>{{__('platform.whatsapp.device_name')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-rename"></i>
                            </span>
                            <input class="form-control" name="name" value="<?= old('name'); ?>" type="text" placeholder="{{__('platform.whatsapp.device_name_placeholder')}}" required>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.whatsapp.device_name_hint')}}
                        </small>
                    </div>

                    <!-- No. WhatsApp -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bxl-whatsapp me-1"></i>{{__('platform.whatsapp.wa_number')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-phone"></i>
                            </span>
                            <input class="form-control" name="phone" id="waPhone" value="<?= old('phone'); ?>" type="text" placeholder="{{__('platform.whatsapp.wa_number_placeholder')}}" required>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.whatsapp.wa_number_hint')}}
                        </small>
                    </div>

                    <!-- Team Member -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-group me-1"></i>{{__('platform.whatsapp.team_member')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control users" name="agent[]" multiple="multiple" required>
                            @foreach ($users as $user)
                            <option value="<?= $user->id; ?>"><?= $user->name; ?></option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.whatsapp.team_member_hint')}}
                        </small>
                    </div>

                    <!-- Toggle Notifikasi Perangkat -->
                    <div class="mb-3">
                        <div class="toggle-wrapper">
                            <div>
                                <label class="form-label mb-0 fw-semibold">
                                    <i class="bx bx-bell me-1"></i>{{__('platform.whatsapp.device_notification')}}
                                </label>
                                <small class="text-muted d-block">{{__('platform.whatsapp.device_notification_hint')}}</small>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="notification" value="yes" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Toggle Simpan Riwayat Pesan -->
                    <div class="mb-3">
                        <div class="toggle-wrapper">
                            <div>
                                <label class="form-label mb-0 fw-semibold">
                                    <i class="bx bx-history me-1"></i>{{__('platform.whatsapp.save_chat_history')}}
                                </label>
                                <small class="text-muted d-block">{{__('platform.whatsapp.save_chat_history_hint')}}</small>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="chat_history" value="yes" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Toggle Auto Read Chatbot -->
                    <div class="mb-3">
                        <div class="toggle-wrapper">
                            <div>
                                <label class="form-label mb-0 fw-semibold">
                                    <i class="bx bx-message-square-check me-1"></i>{{__('platform.whatsapp.auto_read_before_reply')}}
                                </label>
                                <small class="text-muted d-block">{{__('platform.whatsapp.auto_read_before_reply_hint')}}</small>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="auto_read_chatbot" value="yes" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- WebHook Url -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-link-alt me-1"></i>{{__('platform.whatsapp.webhook_url')}}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-globe"></i>
                            </span>
                            <input class="form-control" name="webhook" value="<?= old('webhook'); ?>" type="url" placeholder="{{__('platform.whatsapp.webhook_url_placeholder')}}">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.whatsapp.webhook_url_hint')}}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Card AI Agent -->
            <div class="card custom-card mb-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-bot me-2"></i>{{__('platform.whatsapp.ai_config')}}
                    </div>
                </div>
                <div class="card-body">
                    <!-- Metode Chatbot -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-message-dots me-1"></i>{{__('platform.whatsapp.chatbot_method')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-cog"></i>
                            </span>
                            <select class="form-control methodreply" name="method" required>
                                <option value="all" selected>{{__('platform.whatsapp.method_all')}}</option>
                                <option value="chatbot">{{__('platform.whatsapp.method_chatbot')}}</option>
                                <option value="ai">{{__('platform.whatsapp.method_ai')}}</option>
                            </select>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.whatsapp.chatbot_method_hint')}}
                        </small>
                    </div>

                    <!-- AI Training (Fine Tunnel) -->
                    <div class="mb-3 finetunneldata">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-brain me-1"></i>{{__('platform.whatsapp.ai_training')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-data"></i>
                            </span>
                            <select class="form-control" name="tunnel">
                                <option value="">{{__('platform.whatsapp.select_ai_training')}}</option>
                                @foreach ($fineTunnels as $fine)
                                <option value="<?= $fine->id; ?>"><?= $fine->name; ?></option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.whatsapp.ai_training_hint')}}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Card Jadwal & Batasan -->
            <div class="card custom-card mb-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-time-five me-2"></i>{{__('platform.whatsapp.schedule_limits')}}
                    </div>
                </div>
                <div class="card-body">
                    <!-- Toggle Hari Tertentu -->
                    <div class="mb-3">
                        <div class="toggle-wrapper">
                            <div>
                                <label class="form-label mb-0 fw-semibold">
                                    <i class="bx bx-calendar-x me-1"></i>{{__('platform.whatsapp.inactive_certain_day')}}
                                </label>
                                <small class="text-muted d-block">{{__('platform.whatsapp.inactive_certain_day_hint')}}</small>
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
                            <i class="bx bx-calendar-check me-1"></i>{{__('platform.whatsapp.select_days')}}
                        </label>
                        <select class="form-control days" name="day[]" multiple="multiple">
                            <option value="monday">{{__('platform.whatsapp.day_monday')}}</option>
                            <option value="tuesday">{{__('platform.whatsapp.day_tuesday')}}</option>
                            <option value="wednesday">{{__('platform.whatsapp.day_wednesday')}}</option>
                            <option value="thursday">{{__('platform.whatsapp.day_thursday')}}</option>
                            <option value="friday">{{__('platform.whatsapp.day_friday')}}</option>
                            <option value="saturday">{{__('platform.whatsapp.day_saturday')}}</option>
                            <option value="sunday">{{__('platform.whatsapp.day_sunday')}}</option>
                        </select>
                    </div>

                    <!-- Toggle Jam Tertentu -->
                    <div class="mb-3">
                        <div class="toggle-wrapper">
                            <div>
                                <label class="form-label mb-0 fw-semibold">
                                    <i class="bx bx-time me-1"></i>{{__('platform.whatsapp.inactive_certain_time')}}
                                </label>
                                <small class="text-muted d-block">{{__('platform.whatsapp.inactive_certain_time_hint')}}</small>
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
                                <i class="bx bx-time-five me-1"></i>{{__('platform.whatsapp.start_time')}}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-play-circle"></i>
                                </span>
                                <input class="form-control" name="start_time" type="time">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>{{__('platform.whatsapp.start_time_hint')}}
                            </small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-time-five me-1"></i>{{__('platform.whatsapp.end_time')}}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-stop-circle"></i>
                                </span>
                                <input class="form-control" name="end_time" type="time">
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>{{__('platform.whatsapp.end_time_hint')}}
                            </small>
                        </div>
                    </div>

                    <!-- Toggle Batasan Harian -->
                    <div class="mb-3">
                        <div class="toggle-wrapper">
                            <div>
                                <label class="form-label mb-0 fw-semibold">
                                    <i class="bx bx-message-square-minus me-1"></i>{{__('platform.whatsapp.daily_broadcast_limit')}}
                                </label>
                                <small class="text-muted d-block">{{__('platform.whatsapp.daily_broadcast_limit_hint')}}</small>
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
                            <i class="bx bx-calculator me-1"></i>{{__('platform.whatsapp.enter_daily_limit')}}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-hash"></i>
                            </span>
                            <input class="form-control" name="limit" value="<?= old('limit'); ?>" type="number" placeholder="{{__('platform.whatsapp.daily_limit_placeholder')}}" >
                            <span class="input-group-text">{{__('platform.whatsapp.daily_limit_suffix')}}</span>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('platform.whatsapp.daily_limit_hint')}}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <small class="text-muted">
                    <i class="bx bx-info-circle me-1"></i>{{__('platform.whatsapp.required_fields')}}
                </small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                        <i class="bx bx-x me-1"></i>{{__('platform.whatsapp.cancel')}}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>{{__('platform.whatsapp.save_device')}}
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
            placeholder: "{{__('platform.whatsapp.select_days_placeholder')}}",
            allowClear: true
        });
        
        $('.users').select2({
            placeholder: "{{__('platform.whatsapp.team_member_placeholder')}}",
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
        // Convert toggles to yes/no values
        $('input[type="checkbox"]').each(function() {
            if ($(this).attr('name') && !$(this).hasClass('certain_day_toggle') && !$(this).hasClass('certain_time_toggle') && !$(this).hasClass('daily_limit_toggle')) {
                var hiddenInput = $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', $(this).attr('name'))
                    .val($(this).is(':checked') ? 'yes' : 'no');
                $(this).after(hiddenInput);
                $(this).removeAttr('name');
            }
        });
    });
</script>
@endsection