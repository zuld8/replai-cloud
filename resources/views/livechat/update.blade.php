@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/libs/dropify/css/dropify.min.css')}}">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{ route('livechats') }}" class="btn btn-primary">
        <i class="bx bx-chevron-left"></i>
        {{ __('livechat.back_to_list') }}
    </a>
</div>
@endsection

@section('content')
<form action="<?= route('livechat.edit', $chat->id); ?>" enctype="multipart/form-data" method="POST" class="row">
    @csrf
    <!-- User Sidebar -->
    <div class="col-xl-4 col-lg-5 col-md-5">
        <div class="card custom-card">
            <div class="card-body">
                <div class="text-center">
                    <span class="avatar avatar-xxxl rounded">
                        <img src="{{asset($chat->image_data)}}" alt="" class="rounded-circle">
                    </span>
                </div>
                <div class="d-flex text-center justify-content-between mt-1 mb-3">
                    <div class="flex-fill">
                        <p class="mb-0 fw-semibold fs-16 text-truncate mx-auto">
                            <a href="#">{{$chat->name}}</a>
                        </p>
                    </div>
                </div>
                <div class="btn-list text-center">
                    <div class="btn-list">
                        <button class="btn btn-sm btn-info-light btn-wave waves-effect waves-light" type="button">
                            <i class="bx bx-brain me-1"></i>{{$chat->finetunnel->name ?? __('livechat.no_ai_training')}}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-footer border-block-start-dashed text-center p-0">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="d-flex p-3 border-end">
                        <div class="text-center">
                            <p class="fw-semibold mb-0">{{ __('dashboard.open') }} <i class="bx bx-envelope-open"></i></p>
                            <span class="text-muted fs-12">{{number_format($chat->history->where('status','open')->count())}}</span>
                        </div>
                    </div>
                    <div class="d-flex p-3">
                        <div class="text-center">
                            <p class="fw-semibold mb-0">{{ __('dashboard.resolved') }} <i class="bx bx-check"></i></p>
                            <span class="text-muted fs-12">{{number_format($chat->history->where('status','resolved')->count())}}</span>
                        </div>
                    </div>
                </div>
                <div class="text-center p-3 border-top">
                    <button class="btn btn-primary" type="submit">
                        <i class="ti ti-device-floppy me-1"></i>{{ __('livechat.save_changes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--/ User Sidebar -->

    <!-- Tab -->
    <div class="col-lg-8 col-sm-12">
        <div class="card custom-card">
            <div class="card-body p-0">
                <div class="border-block-end-dashed bg-white rounded-2 p-2">
                    <div>
                        <ul class="nav nav-pills nav-justified gx-3 tab-style-6 d-sm-flex d-block" id="myTab" role="tablist">
                            <li class="nav-item rounded" role="presentation">
                                <a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#general-information" aria-controls="general-information" aria-selected="true">
                                    <i class="bx bx-id-card me-1"></i>{{ __('livechat.general_information') }}
                                </a>
                            </li>
                            <li class="nav-item rounded" role="presentation">
                                <a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#faq-information" aria-controls="faq-information" aria-selected="false">
                                    <i class="bx bx-help-circle me-1"></i>{{ __('livechat.faq') }}
                                </a>
                            </li>
                            <li class="nav-item rounded" role="presentation">
                                <a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#sosmed-information" aria-controls="sosmed-information" aria-selected="false">
                                    <i class="bx bx-link me-1"></i>{{ __('livechat.social_media') }}
                                </a>
                            </li>
                            <li class="nav-item rounded" role="presentation">
                                <a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#code-embed" aria-controls="code-embed" aria-selected="false">
                                    <i class="bx bx-code-alt me-1"></i>{{ __('livechat.code_embed') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="p-4">
                    <div class="tab-content" id="myTabContent">
                        <!-- General Information Tab -->
                        <div class="tab-pane fade active show" id="general-information" role="tabpanel">
                            <div class="row">
                                <!-- Logo Upload -->
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-image me-1"></i>{{ __('livechat.logo_chat') }}
                                    </label>
                                    <input class="dropify" type="file" id="image" name="image" data-default-file="{{asset($chat->image_data)}}">
                                    <small class="text-muted d-block mt-2">
                                        <i class="bx bx-info-circle me-1"></i>{{ __('livechat.upload_image_help') }}
                                    </small>
                                </div>

                                <!-- Nama Widget -->
                                <div class="col-lg-6 col-sm-12 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-tag me-1"></i>{{__('general.insert_name')}}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bx bx-text"></i>
                                        </span>
                                        <input class="form-control" name="name" value="<?= old('name', $chat->name); ?>" type="text" placeholder="{{ __('livechat.widget_name_placeholder') }}" required>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bx bx-info-circle me-1"></i>{{ __('livechat.widget_name_help') }}
                                    </small>
                                </div>

                                <!-- Human Agent -->
                                <div class="col-lg-6 col-sm-12 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-group me-1"></i>{{ __('livechat.select_agent') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control users" name="agent[]" multiple="multiple" required>
                                        @foreach ($users as $user)
                                        <option value="<?= $user->id; ?>"
                                            {{ in_array($user->id, explode(',',$chat->agent)) ? 'selected' : '' }}>
                                            <?= $user->name; ?>
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">
                                        <i class="bx bx-info-circle me-1"></i>{{ __('livechat.select_agent_help') }}
                                    </small>
                                </div>

                                <!-- Auto Reply Method -->
                                <div class="col-lg-6 col-sm-12 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-bot me-1"></i>{{__('master.device.auto_reply_method')}}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bx bx-message-dots"></i>
                                        </span>
                                        <select class="form-control methodreply" name="type" required>
                                            <option value="all" @if($chat->type == 'all') selected @endif>{{__('general.all')}}</option>
                                            <option value="chatbot" @if($chat->type == 'chatbot') selected @endif>{{ __('livechat.auto_reply_manual') }}</option>
                                            <option value="ai" @if($chat->type == 'ai') selected @endif>{{ __('livechat.chatbot_ai_auto') }}</option>
                                        </select>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bx bx-info-circle me-1"></i>{{ __('livechat.auto_reply_help') }}
                                    </small>
                                </div>

                                <!-- AI Training -->
                                <div class="col-lg-6 col-sm-12 mb-3 finetunneldata @if($chat->type == 'chatbot') d-none @endif">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-brain me-1"></i>{{__('master.device.ai_training')}} (Fine Tunnel)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bx bx-chip"></i>
                                        </span>
                                        <select class="form-control" name="tunnel">
                                            <option value="">{{__('master.device.choose_ai_training')}}</option>
                                            @foreach ($fineTunnels as $t)
                                            <option value="{{$t->id}}" @if($chat->finetunnel_id == $t->id) selected @endif>{{$t->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bx bx-info-circle me-1"></i>{{ __('livechat.ai_training_help') }}
                                    </small>
                                </div>

                                <!-- Deskripsi -->
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-note me-1"></i>{{ __('livechat.notes_description') }}
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text align-items-start pt-2">
                                            <i class="bx bx-message-square-detail"></i>
                                        </span>
                                        <textarea class="form-control" name="description" rows="3" placeholder="{{ __('livechat.notes_placeholder') }}">{{old('description',$chat->description)}}</textarea>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bx bx-info-circle me-1"></i>{{ __('livechat.notes_help') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Tab -->
                        <div class="tab-pane fade" id="faq-information" role="tabpanel">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="bx bx-help-circle me-1"></i>{{ __('livechat.faq_list') }}
                                            </h6>
                                            <small class="text-muted">
                                                <i class="bx bx-info-circle me-1"></i>{{ __('livechat.faq_help') }}
                                            </small>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm addfaq">
                                            <i class="bx bx-plus-circle me-1"></i>{{ __('livechat.add_faq') }}
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="80%">
                                                    <i class="bx bx-question-mark me-1"></i>{{ __('livechat.question') }}
                                                </th>
                                                <th width="20%" class="text-center">
                                                    <i class="bx bx-cog me-1"></i>{{ __('livechat.action') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="listQuestion">
                                            @forelse ($faqs as $f => $value)
                                            <tr>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="bx bx-question-mark"></i>
                                                        </span>
                                                        <input class="form-control" name="question[]" value="{{$value}}" placeholder="{{ __('livechat.question_placeholder') }}" required>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm delete-faq" title="{{ __('livechat.delete_faq') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr class="text-center">
                                                <td colspan="2" class="text-muted">
                                                    <i class="bx bx-info-circle me-1"></i>{{ __('livechat.no_faq_yet') }}
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Tab -->
                        <div class="tab-pane fade" id="sosmed-information" role="tabpanel">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="bx bx-link me-1"></i>{{ __('livechat.social_list') }}
                                            </h6>
                                            <small class="text-muted">
                                                <i class="bx bx-info-circle me-1"></i>{{ __('livechat.social_media_help') }}
                                            </small>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm addsosmed">
                                            <i class="bx bx-plus-circle me-1"></i>{{ __('livechat.add_social_link') }}
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="35%">
                                                    <i class="bx bx-link-alt me-1"></i>{{ __('livechat.link_url') }}
                                                </th>
                                                <th width="25%">
                                                    <i class="bx bx-text me-1"></i>{{ __('livechat.label') }}
                                                </th>
                                                <th width="25%">
                                                    <i class="bx bx-image me-1"></i>{{ __('livechat.icon') }}
                                                </th>
                                                <th width="15%" class="text-center">
                                                    <i class="bx bx-cog me-1"></i>{{ __('livechat.action') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="listSosmed">
                                            @forelse ($sosmed as $social)
                                            <tr>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">
                                                            <i class="bx bx-link-alt"></i>
                                                        </span>
                                                        <input class="form-control" name="url[]" value="{{$social['link']}}" placeholder="{{ __('livechat.url_placeholder') }}" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">
                                                            <i class="bx bx-text"></i>
                                                        </span>
                                                        <input class="form-control" name="label[]" value="{{$social['label']}}" placeholder="{{ __('livechat.label_placeholder') }}" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">
                                                            <i class="bx bx-image"></i>
                                                        </span>
                                                        <select class="form-control" name="icon[]" required>
                                                            <option value="web" @if($social['icon_link']=='web' ) selected @endif>{{ __('livechat.website') }}</option>
                                                            <option value="facebook" @if($social['icon_link']=='facebook' ) selected @endif>{{ __('livechat.facebook') }}</option>
                                                            <option value="twitter" @if($social['icon_link']=='twitter' ) selected @endif>{{ __('livechat.twitter') }}</option>
                                                            <option value="instagram" @if($social['icon_link']=='instagram' ) selected @endif>{{ __('livechat.instagram') }}</option>
                                                            <option value="youtube" @if($social['icon_link']=='youtube' ) selected @endif>{{ __('livechat.youtube') }}</option>
                                                            <option value="whatsapp" @if($social['icon_link']=='whatsapp' ) selected @endif>{{ __('livechat.whatsapp') }}</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm delete-sosmed" title="{{ __('livechat.delete_link') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr class="text-center">
                                                <td colspan="4" class="text-muted">
                                                    <i class="bx bx-info-circle me-1"></i>{{ __('livechat.no_social_yet') }}
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Code Embed Tab -->
                        <div class="tab-pane fade" id="code-embed" role="tabpanel">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h6 class="mb-1">
                                        <i class="bx bx-code-alt me-1"></i>{{ __('livechat.embed_code_title') }}
                                    </h6>
                                    <small class="text-muted">
                                        <i class="bx bx-info-circle me-1"></i>{{ __('livechat.embed_code_help') }}
                                    </small>
                                </div>
                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-body">
                                            <pre class="mb-0"><code id="codeBlock">&lt;script type="text/javascript"&gt;
    window.mychat = window.mychat || {};
    window.mychat.server = '{{config('app.url')}}/assets/js/livechat-widget.js';
    window.mychat.appUrl = '{{config('app.url')}}';
    window.mychat.socketUrl = '{{config('custom.socket_url')}}'; 
    window.mychat.iframeWidth = '400px';
    window.mychat.iframeHeight = '700px';
    window.mychat.tokenKey = '{{$chat->id}}';
    (function() {
        var mychat = document.createElement('script');
        mychat.type = 'text/javascript';
        mychat.async = true;
        mychat.src = window.mychat.server;
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(mychat, s);
    })();
&lt;/script&gt;</code></pre>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-end">
                                    <button class="btn btn-outline-info" type="button" id="copyButton">
                                        <i class='bx bx-clipboard me-1'></i>{{ __('livechat.copy_code') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Tab -->
</form>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script src="{{ asset('assets/libs/dropify/js/dropify.min.js')}}"></script>
<script>
    // Pass Laravel translations to JavaScript
    const translations = {
        selectStaffPlaceholder: "{{ __('livechat.select_staff_placeholder') }}",
        questionPlaceholder: "{{ __('livechat.question_input_placeholder') }}",
        deleteFaq: "{{ __('livechat.delete_faq') }}",
        noFaqYet: "{{ __('livechat.no_faq_yet') }}",
        urlPlaceholder: "{{ __('livechat.url_placeholder') }}",
        labelPlaceholder: "{{ __('livechat.label_placeholder') }}",
        deleteLink: "{{ __('livechat.delete_link') }}",
        noSocialYet: "{{ __('livechat.no_social_yet') }}",
        website: "{{ __('livechat.website') }}",
        facebook: "{{ __('livechat.facebook') }}",
        twitter: "{{ __('livechat.twitter') }}",
        instagram: "{{ __('livechat.instagram') }}",
        youtube: "{{ __('livechat.youtube') }}",
        whatsapp: "{{ __('livechat.whatsapp') }}",
        copySuccess: "{{ __('livechat.copy_success') }}"
    };

    $(document).ready(function() {
        $('.users').select2({
            placeholder: translations.selectStaffPlaceholder,
            allowClear: true
        });
        $('.dropify').dropify();

        // Menambahkan FAQ baru
        $(document).on('click', '.addfaq', function() {
            // Hapus pesan "Belum ada FAQ" jika ada
            $('#listQuestion tr.text-center').remove();

            let newRow = `
            <tr>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-question-mark"></i>
                        </span>
                        <input class="form-control" name="question[]" placeholder="${translations.questionPlaceholder}" required>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm delete-faq" title="${translations.deleteFaq}">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `;
            $('#listQuestion').append(newRow);
        });

        // Menghapus FAQ
        $(document).on('click', '.delete-faq', function() {
            $(this).closest('tr').remove();

            // Tampilkan pesan jika tidak ada FAQ
            if ($('#listQuestion tr').length === 0) {
                $('#listQuestion').html(`
                    <tr class="text-center">
                        <td colspan="2" class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>${translations.noFaqYet}
                        </td>
                    </tr>
                `);
            }
        });

        // Menambahkan Social Media Link
        $(document).on('click', '.addsosmed', function() {
            // Hapus pesan "Belum ada link" jika ada
            $('#listSosmed tr.text-center').remove();

            let newRow = `
            <tr>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="bx bx-link-alt"></i>
                        </span>
                        <input class="form-control" name="url[]" placeholder="${translations.urlPlaceholder}" required>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="bx bx-text"></i>
                        </span>
                        <input class="form-control" name="label[]" placeholder="${translations.labelPlaceholder}" required>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="bx bx-image"></i>
                        </span>
                        <select class="form-control" name="icon[]" required>
                            <option value="web">${translations.website}</option>
                            <option value="facebook">${translations.facebook}</option>
                            <option value="twitter">${translations.twitter}</option>
                            <option value="instagram">${translations.instagram}</option>
                            <option value="youtube">${translations.youtube}</option>
                            <option value="whatsapp">${translations.whatsapp}</option>
                        </select>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm delete-sosmed" title="${translations.deleteLink}">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `;
            $('#listSosmed').append(newRow);
        });

        // Menghapus Social Link
        $(document).on('click', '.delete-sosmed', function() {
            $(this).closest('tr').remove();

            // Tampilkan pesan jika tidak ada link
            if ($('#listSosmed tr').length === 0) {
                $('#listSosmed').html(`
                    <tr class="text-center">
                        <td colspan="4" class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>${translations.noSocialYet}
                        </td>
                    </tr>
                `);
            }
        });
    });

    // Copy Code Button
    document.getElementById("copyButton").addEventListener("click", function() {
        var codeBlock = document.getElementById("codeBlock").innerText;

        var tempTextArea = document.createElement("textarea");
        tempTextArea.value = codeBlock;
        document.body.appendChild(tempTextArea);

        tempTextArea.select();
        document.execCommand("copy");
        document.body.removeChild(tempTextArea);

        toastr.success(translations.copySuccess, {
            timeOut: 5e3,
            closeButton: !0,
            debug: !1,
            newestOnTop: !0,
            progressBar: !0,
            positionClass: 'toast-top-right',
            preventDuplicates: !0,
            onclick: null,
            showDuration: '100',
            hideDuration: '1000',
            extendedTimeOut: '1000',
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut',
            tapToDismiss: !1,
        })
    });

    // Method Reply Logic
    $(".methodreply").on("change", function() {
        if ($(this).val() == 'ai' || $(this).val() == 'all') {
            $(".finetunneldata").removeClass('d-none');
        } else if ($(this).val() == 'chatbot') {
            $(".finetunneldata").addClass('d-none');
        }
    });
</script>
@endsection