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
                <div class="card-title">
                    <i class="bx bx-edit me-2"></i>
                    {{__('page.chatbot.edit')}}
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <!-- Keyword -->
                    <div class="col-12 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-key text-primary me-1"></i>
                            {{__('chatbot.insert_keyword')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-hash"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   name="keyword" 
                                   required 
                                   value="<?= old('keyword', $bot->keyword); ?>"
                                   placeholder="{{__('chatbot.image_caption_example')}}">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{__('chatbot.trigger_keyword')}}
                        </small>
                    </div>

                    <!-- Device -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-devices text-success me-1"></i>
                            {{__('chatbot.choose_device')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control devices" name="device[]" multiple="multiple" >
                            @foreach ($devices as $device)
                            <option value="<?= $device->id; ?>"
                                {{ in_array($device->id, explode(',',$bot->select_device)) ? 'selected' : '' }}>
                                <?= $device->phone; ?>
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                                {{__('chatbot.choose_whatsapp_device')}}
                        </small>
                    </div>

                    <!-- Livechat -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-chat text-info me-1"></i>
                            {{__('chatbot.choose_livechat')}}
                            <span class="badge bg-label-secondary ms-1">{{__('chatbot.optional')}}</span>
                        </label>
                        <select class="form-control livechats" name="livechats[]" multiple="multiple">
                            @foreach ($livechats as $livechat)
                            <option value="<?= $livechat->id; ?>"
                                {{ in_array($livechat->id, explode(',',$bot->select_livechat)) ? 'selected' : '' }}>
                                <?= $livechat->name; ?>
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{__('chatbot.integrate_livechat')}}
                        </small>
                    </div>

                    <!-- Telegram -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bxl-telegram text-primary me-1"></i>
                            {{__('chatbot.choose_telegram')}}
                            <span class="badge bg-label-secondary ms-1">{{__('chatbot.optional')}}</span>
                        </label>
                        <select class="form-control telegrams" name="telegrams[]" multiple="multiple">
                            @foreach ($telegrams as $telegram)
                            <option value="<?= $telegram->id; ?>"
                                {{ in_array($telegram->id, explode(',',$bot->select_telegram)) ? 'selected' : '' }}>
                                <?= $telegram->name; ?>
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{__('chatbot.choose_bot_telegram')}}
                        </small>
                    </div>

                     <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bxl-telegram text-primary me-1"></i>
                            {{__('chatbot.choose_instagram')}}
                            <span class="badge bg-label-secondary ms-1">{{__('chatbot.optional')}}</span>
                        </label>
                         <select class="form-control instagrams" name="instagrams[]" multiple="multiple">
                                @foreach ($instagrams as $instagram)
                              <option value="<?= $instagram->id; ?>"
                                {{ in_array($instagram->id, explode(',',$bot->select_instagram)) ? 'selected' : '' }}>
                                <?= $instagram->name; ?>
                            </option>
                                @endforeach
                            </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{__('chatbot.choose_bot_instagram')}}
                        </small>
                    </div>

                      <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bxl-telegram text-primary me-1"></i>
                            {{__('chatbot.choose_messenger')}}
                            <span class="badge bg-label-secondary ms-1">{{__('chatbot.optional')}}</span>
                        </label>
                         <select class="form-control messengers" name="messengers[]" multiple="multiple">
                                @foreach ($messengers as $messenger)
                                <option value="<?= $messenger->id; ?>"
                                {{ in_array($messenger->id, explode(',',$bot->select_messanger)) ? 'selected' : '' }}>
                                <?= $messenger->page_name; ?>
                            </option>
                                @endforeach
                            </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{__('chatbot.choose_bot_messenger')}}
                        </small>
                    </div>

                    <!-- Method Reply -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-message-square-detail text-warning me-1"></i>
                            {{__('chatbot.method_reply')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control methodreply" name="method" required>
                            <option value="template" @if($bot->reply_method == 'template') selected @endif>
                                {{__('general.template')}}
                            </option>
                            <option value="text" @if($bot->reply_method == 'text') selected @endif>
                                {{__('general.text')}}
                            </option>
                            <option value="image" @if($bot->reply_method == 'image') selected @endif>
                                {{__('chatbot.image')}}
                            </option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{__('chatbot.response_format')}}
                        </small>
                    </div>

                    <!-- Template Form -->
                    <div class="col-lg-12 mb-4 templateform @if($bot->reply_method != 'template') d-none @endif">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-file-blank text-info me-1"></i>
                            {{__('sidebar.message_template')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control templates" name="template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($templates as $template)
                            <option value="<?= $template->id; ?>" @if($template->id == $bot->template_id) selected @endif>
                                <?= $template->name; ?>
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{__('chatbot.choose_message_template')}}
                        </small>
                    </div>

                    <!-- Message Form -->
                    <div class="col-12 mb-4 messageform @if($bot->reply_method != 'text') d-none @endif">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-message-alt-detail text-success me-1"></i>
                            {{__('master.device.whatsapp_messanger')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text align-items-start pt-2">
                                <i class="bx bx-edit"></i>
                            </span>
                            <textarea class="form-control" 
                                      style="min-height: 200px;" 
                                      name="message"
                                      placeholder="{{__('chatbot.write_your_message_here')}}">{{old('message', $bot->message)}}</textarea>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{__('chatbot.write_your_message_here')}}
                        </small>
                    </div>

                    <!-- Image List -->
                    <div class="col-12 listImage @if($bot->reply_method != 'image') d-none @endif">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold mb-0">
                                <i class="bx bx-images text-danger me-1"></i>
                                {{__('chatbot.image_list')}}
                            </label>
                            <button type="button" id="addData" class="btn btn-sm btn-primary">
                                <i class="bx bx-plus-circle"></i>
                                <span class="d-none d-md-inline ms-1">{{__('chatbot.add_image')}}</span>
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="45%">
                                            <i class="bx bx-link me-1"></i>
                                            {{__('cms.url')}}
                                        </th>
                                        <th width="45%">
                                            <i class="bx bx-text me-1"></i>
                                            {{__('chatbot.caption')}}
                                        </th>
                                        <th width="10%" class="text-center">
                                            <i class="bx bx-cog"></i>
                                            {{__('general.action')}}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($bot->details->count() > 0)
                                        @foreach ($bot->details as $detail)
                                        <tr>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">
                                                        <i class="bx bx-link-external"></i>
                                                    </span>
                                                    <input class="form-control" 
                                                           name="url[]" 
                                                           value="{{$detail->url}}"
                                                           placeholder="https://example.com/image.jpg"
                                                           >
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">
                                                        <i class="bx bx-message-square"></i>
                                                    </span>
                                                    <input class="form-control" 
                                                           name="name[]"
                                                           value="{{$detail->name}}"
                                                           placeholder="Caption untuk gambar">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-danger deleteItem" type="button">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr id="imageItem">
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">
                                                        <i class="bx bx-link-external"></i>
                                                    </span>
                                                    <input class="form-control" 
                                                           name="url[]" 
                                                           placeholder="https://example.com/image.jpg"
                                                           >
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">
                                                        <i class="bx bx-message-square"></i>
                                                    </span>
                                                    <input class="form-control" 
                                                           name="name[]"
                                                           placeholder="{{__('chatbot.image_caption')}}">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-danger deleteItem" type="button">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{__('chatbot.input_image_url')}}
                        </small>
                    </div>

                </div>
            </div>
            
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-info-circle"></i>
                    <span class="text-danger">*</span> {{__('chatbot.required')}}
                </small>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i>
                    {{__('general.save_change')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.devices').select2({
            placeholder: '{{__('chatbot.choose_device')}}',
            allowClear: true
        });
        
        $('.livechats').select2({
            placeholder: '{{__('chatbot.choose_livechat')}}',
            allowClear: true
        });
        
        $('.telegrams').select2({
            placeholder: '{{__('chatbot.choose_telegram')}}',
            allowClear: true
        });

         $('.messengers').select2({
            placeholder: '{{__('chatbot.choose_messenger_select')}}',
            allowClear: true
        });

        $('.instagrams').select2({
            placeholder: '{{__('chatbot.choose_instagram_select')}}',
            allowClear: true
        });
    });

    // Method Reply Change Handler
    $('.methodreply').on("change", function() {
        if ($(this).val() == 'text') {
            $(".templateform").addClass('d-none');
            $(".listImage").addClass('d-none');
            $(".messageform").removeClass('d-none');
        } else if ($(this).val() == 'template') {
            $(".messageform").addClass('d-none');
            $(".listImage").addClass('d-none');
            $(".templateform").removeClass('d-none');
        } else if ($(this).val() == 'image') {
            $(".messageform").addClass('d-none');
            $(".templateform").addClass('d-none');
            $(".listImage").removeClass('d-none');
        }
    });

    // Add Image Row
    $("#addData").on("click", function() {
        var newItem = `<tr>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i class="bx bx-link-external"></i>
                                    </span>
                                    <input class="form-control" 
                                           name="url[]" 
                                           placeholder="https://example.com/image.jpg"
                                           required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i class="bx bx-message-square"></i>
                                    </span>
                                    <input class="form-control" 
                                           name="name[]"
                                           placeholder="{{__('chatbot.image_caption')}}">
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger deleteItem" type="button">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>`;

        $("tbody").append(newItem);
    });

    // Delete Image Row
    $("body").on("click", ".deleteItem", function() {
        if ($('tbody tr').length > 1) {
            $(this).parents("tr").remove();
        } else {
            alert('{{__('chatbot.minimum_one_image')}}');
        }
    });
</script>
@endsection