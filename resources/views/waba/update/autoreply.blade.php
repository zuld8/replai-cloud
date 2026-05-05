@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('waba')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('master.device.back_to_device_list')}}
    </a>
    <a href="{{route('waba')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('master.device.back_to_device_list')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card">
            <div class="row g-0">
                <x-waba-sidebar-update-component idwaba="{{$meta->id}}"></x-waba-sidebar-update-component>
                <form action="{{route('waba.autoreply.update',$device->id)}}" method="POST" class="col-12 col-md-10 d-flex flex-column">
                    @csrf
                    <div class="card-body">
                        <h2 class="mb-4">{{__("waba.autoreply_message")}}</h2>

                        <div class="row g-3 mt-4">

                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">Pilih Pengguna</label>
                                <select class="form-control users" name="agent[]" multiple="multiple" required>
                                    @foreach ($users as $user)
                                    <option value="<?= $user->id; ?>"
                                        {{ in_array($user->id, explode(',',$device->agent)) ? 'selected' : '' }}>
                                        <?= $user->name; ?>
                                    </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">{{__('master.device.auto_reply_method')}}</label>
                                <select class="form-control methodreply" name="method" required>
                                    <option value="all" @if($device->auto_reply_method == 'all') selected @endif >Semua </option>
                                    <option value="chatbot" @if($device->auto_reply_method == 'chatbot') selected @endif >Chatbot auto Reply ( Manual ) </option>
                                    <option value="ai" @if($device->auto_reply_method == 'ai') selected @endif>Ai</option>
                                </select>
                            </div>

                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">{{__('master.device.auto_reply_option')}} </label>
                                <select class="form-control" name="auto_reply_option" required>
                                    <option value="all" @if($device->auto_reply_option == 'all') selected @endif >{{__('general.all')}} </option>
                                    <option value="personal" @if($device->auto_reply_option == 'personal') selected @endif>{{__('general.personal')}}</option>
                                    <option value="group" @if($device->auto_reply_option == 'group') selected @endif>{{__('general.group')}}</option>
                                </select>
                            </div>

                            <div class="col-lg-6 col-sm-12 mt-3 d-none">
                                <label class="form-label">{{__('master.device.auto_read')}}</label>
                                <select class="form-control" name="auto_read_chatbot" required>
                                    <option value="yes" @if($device->auto_read_before_autorespon == 'yes') selected @endif>{{__('general.yes')}} </option>
                                    <option value="no" @if($device->auto_read_before_autorespon == 'no') selected @endif>{{__('general.no')}}</option>
                                </select>
                            </div>


                            <div class="col-lg-6 mt-3 finetunneldata @if($device->auto_reply_method != 'ai') d-none @endif ">
                                <label class="form-label">{{__('master.device.ai_training')}} ( Fine Tunnel ) </label>
                                <select class="form-control" name="tunnel">
                                    <option value="">{{__('master.device.choose_ai_training')}}</option>
                                    @foreach ($fineTunnels as $t)
                                    <option value="{{$t->id}}" @if($device->fine_tunnel_id == $t->id) selected @endif>{{$t->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">{{__('master.device.just_certain_day')}}</label>
                                <select class="form-control certain_day" name="certain_day" required>
                                    <option value="no" @if($device->auto_reply_certain_day == 'no') selected @endif>{{__('master.device.certain_day_no')}}</option>
                                    <option value="yes" @if($device->auto_reply_certain_day == 'yes') selected @endif>{{__('master.device.certain_day_yes')}} </option>
                                </select>
                            </div>

                            <div class="col-lg-6 col-sm-12 mt-3 @if($device->auto_reply_certain_day == 'no') d-none @endif" id="certain_day">
                                <label class="form-label">{{__('master.device.choose_day')}}</label>
                                <select class="form-control days" name="days[]" multiple>
                                    <option value="mon" {{ in_array('mon', explode(',',$device->days)) ? 'selected' : '' }}>{{__('master.device.monday')}} </option>
                                    <option value="tue" {{ in_array('tue', explode(',',$device->days)) ? 'selected' : '' }}>{{__('master.device.tuesday')}}</option>
                                    <option value="wed" {{ in_array('wed', explode(',',$device->days)) ? 'selected' : '' }}>{{__('master.device.wednesday')}}</option>
                                    <option value="thu" {{ in_array('thu', explode(',',$device->days)) ? 'selected' : '' }}>{{__('master.device.thursday')}}</option>
                                    <option value="fri" {{ in_array('fri', explode(',',$device->days)) ? 'selected' : '' }}>{{__('master.device.friday')}}</option>
                                    <option value="sat" {{ in_array('sat', explode(',',$device->days)) ? 'selected' : '' }}>{{__('master.device.saturday')}}</option>
                                    <option value="sun" {{ in_array('sun', explode(',',$device->days)) ? 'selected' : '' }}>{{__('master.device.sunday')}}</option>
                                </select>
                            </div>


                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">{{__('master.device.just_certain_time')}}</label>
                                <select class="form-control certain_time" name="certain_time" required>
                                    <option value="no" @if($device->auto_reply_certain_time == 'no') selected @endif>{{__('master.device.certain_time_no')}}</option>
                                    <option value="yes" @if($device->auto_reply_certain_time == 'yes') selected @endif>{{__('master.device.certain_time_yes')}} </option>
                                </select>
                            </div>

                            <div class="col-lg-6 col-sm-12 mt-3 @if($device->auto_reply_certain_time == 'no') d-none @endif" id="start_time">
                                <label class="form-label">{{__('master.device.start_time')}}</label>
                                <input class="form-control" name="start_time" type="time" value="<?= $device->start_time; ?>">
                            </div>

                            <div class="col-lg-6 col-sm-12 mt-3 @if($device->auto_reply_certain_time == 'no') d-none @endif" id="end_time">
                                <label class="form-label">{{__('master.device.end_time')}}</label>
                                <input class="form-control" name="end_time" type="time" value="<?= $device->end_time; ?>">
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-transparent mt-auto p-4">
                        <div class="btn-list d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.days').select2();
        $('.users').select2();
    });

    $(".methodreply").on("change", function() {
        if ($(this).val() == 'ai' || (this).val() == 'all') {
            $(".finetunneldata").removeClass('d-none');
        } else {
            $(".finetunneldata").addClass('d-none');
        }
    });

    $(".certain_day").on("change", function() {
        if ($(this).val() == 'yes') {
            $("#certain_day").removeClass('d-none');
        } else {
            $("#certain_day").addClass('d-none');
        }
    })

    $(".certain_time").on("change", function() {
        if ($(this).val() == 'yes') {
            $("#start_time").removeClass('d-none');
            $("#end_time").removeClass('d-none');
        } else {
            $("#start_time").addClass('d-none');
            $("#end_time").addClass('d-none');
        }
    })
</script>
@endsection