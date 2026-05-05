@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{ route('chatbot') }}" class="btn btn-primary">
        <i class="bx bx-chevron-left"></i>
        {{ __('chatbot.back_to') }}
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <form action="<?= route('chatbot.edit', $bot->id); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">{{__('page.chatbot.edit')}}</div>

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mt-3">
                        <label class="form-label">{{__('chatbot.insert_keyword')}} </label>
                        <input type="text" class="form-control" name="keyword" required value="<?= old('keyword', $bot->keyword); ?>">
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('chatbot.choose_device')}}</label>
                        <select class="form-control devices" name="device[]" multiple="multiple">
                            @foreach ($devices as $device)
                            <option value="<?= $device->id; ?>"
                                {{ in_array($device->id, explode(',',$bot->select_device)) ? 'selected' : '' }}>
                                <?= $device->phone; ?>
                            </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">Livechat</label>
                        <select class="form-control livechats" name="livechats[]" multiple="multiple">
                            @foreach ($livechats as $livechat)
                            <option value="<?= $livechat->id; ?>"
                                {{ in_array($livechat->id, explode(',',$bot->select_livechat)) ? 'selected' : '' }}>
                                <?= $livechat->name; ?>
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('chatbot.method_reply')}}</label>
                        <select class="form-control methodreply" name="method" required>
                            <option value="template" @if($bot->reply_method == 'template') selected @endif >{{__('general.template')}}</option>
                            <option value="text" @if($bot->reply_method == 'text') selected @endif>{{__('general.text')}}</option>
                            <option value="image" @if($bot->reply_method == 'image') selected @endif>{{__('chatbot.image')}}</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3 templateform @if($bot->reply_method != 'template') d-none @endif ">
                        <label class="form-label">{{__('sidebar.message_template')}}</label>
                        <select class="form-control templates" name="template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($templates as $template)
                            <option value="<?= $template->id; ?>" @if($template->id == $bot->template_id) selected @endif ><?= $template->name; ?></option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 mt-3 messageform @if($bot->reply_method != 'text') d-none @endif">
                        <label class="form-label">{{__('master.device.whatsapp_messanger')}}</label>
                        <textarea class="form-control" style="height: 300px;" name="message">{{old('message',$bot->message)}}</textarea>
                    </div>

                    <div class="mt-4 class-12 table-responsive listImage @if($bot->reply_method != 'image') d-none @endif ">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{__('cms.url')}}</th>
                                    <th>{{__('chatbot.caption')}}</th>
                                    <th>{{__('general.action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($bot->details->count() > 0)
                                @foreach ($bot->details as $detail)
                                <tr id="imageItem">
                                    <td>
                                        <input class="form-control" name="url[]" value="{{$detail->url}}" required>
                                    </td>
                                    <td>
                                        <input class="form-control" name="name[]" value="{{$detail->name}}">
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-danger btn-icon fs-16 deleteItem" type="button">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr id="imageItem">

                                </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.devices').select2();
        $('.livechats').select2();
    });

    $('.methodreply').on("change", function() {
        if ($(this).val() == 'text') {
            $(".templateform").addClass('d-none');
            $(".listImage").addClass('d-none');
            $(".buttonforimage").addClass('d-none');
            $(".messageform").removeClass('d-none');
        } else if ($(this).val() == 'template') {
            $(".messageform").addClass('d-none');
            $(".listImage").addClass('d-none');
            $(".buttonforimage").addClass('d-none');
            $(".templateform").removeClass('d-none');
        } else if ($(this).val() == 'image') {
            $(".messageform").addClass('d-none');
            $(".templateform").addClass('d-none');
            $(".listImage").removeClass('d-none');
            $(".buttonforimage").removeClass('d-none');
        }
    });

    $("#addData").on("click", function() {
        var newItem = `<tr id="imageItem">
                            <td>
                                <input class="form-control" name="url[]" required>
                            </td>
                            <td>
                                <input class="form-control" name="name[]">
                            </td>
                            <td>
                                <button class="btn btn-outline-danger btn-icon fs-16 deleteItem" type="button">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>`;

        $("#imageItem").after(newItem);
    })

    $("body").on("click", ".deleteItem", function() {
        $(this).parents("#imageItem").remove();
    });
</script>
@endsection