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
                <form action="<?= route('waba.greeting.update', $device->id); ?>" enctype="multipart/form-data" method="POST" class="col-12 col-md-10 d-flex flex-column">
                    @csrf
                    <div class="card-body">
                        <h2 class="mb-4">{{__('waba.autoreply_message')}}</h2>

                        <div class="row g-3 mt-4">
                            <div class="col-12 col-sm-12 mt-3">
                                <label class="form-label">{{__('master.device.enable_greeting_reply')}}</label>
                                <input type="hidden" name="reply_method" value="text">
                                <select class="form-control" name="reply_chat" required>
                                    <option value="yes" @if($device->reply_any_chat == 'yes') selected @endif >{{__('general.yes')}}</option>
                                    <option value="no" @if($device->reply_any_chat == 'no') selected @endif>{{__('general.no')}}</option>
                                </select>
                            </div>

                            <div class="col-lg-6 col-sm-12 mt-3 d-none">
                                <label class="form-label">{{__('chatbot.method_reply')}}</label>
                                <select class="form-control methodreply" name="reply_method" required>
                                    <option value="template" @if($device->reply_method == 'template') selected @endif >{{__('general.template')}}</option>
                                    <option value="text" @if($device->reply_method == 'text') selected @endif>{{__('general.text')}}</option>
                                </select>
                            </div>

                            <div class="col-12 mt-3 templateform d-none ">
                                <label class="form-label">{{__('sidebar.message_template')}}</label>
                                <select class="form-control templates" name="reply_template">
                                    <option value="">{{__('blash.choose_template')}}</option>
                                    @foreach ($templates as $template)
                                    <option value="<?= $template->id; ?>" @if($template->id == $device->template_id) selected @endif ><?= $template->name; ?></option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mt-3 messageform @if($device->reply_method != 'text') d-none @endif">
                                <label class="form-label">{{__('master.device.whatsapp_messanger')}}</label>
                                <textarea class="form-control" style="height: 300px;" name="reply_text">{{old('reply_text',$device->reply_text)}}</textarea>
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
        $('.templates').select2();
    });

    $('.methodreply').on("change", function() {
        if ($(this).val() == 'text') {
            $(".templateform").addClass('d-none');
            $(".messageform").removeClass('d-none');
        } else {
            $(".messageform").addClass('d-none');
            $(".templateform").removeClass('d-none');
        }
    })
</script>
@endsection