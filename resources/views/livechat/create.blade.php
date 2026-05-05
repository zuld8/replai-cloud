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
<form action="<?= route('livechat.store'); ?>" enctype="multipart/form-data" method="POST" class="row">
    @csrf
    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>

    <div class="col-lg-4">
        <div class="card custom-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-cog me-2"></i>{{ __('livechat.widget_detail') }}
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <!-- Nama Widget -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bx bx-tag me-1"></i>{{__('general.insert_name')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bx bx-text"></i>
                                    </span>
                                    <input class="form-control" name="name" value="<?= old('name'); ?>" type="text" placeholder="{{ __('livechat.widget_name_placeholder') }}" required>
                                </div>
                                <small class="text-muted">
                                    <i class="bx bx-info-circle me-1"></i>{{ __('livechat.widget_name_help') }}
                                </small>
                            </div>

                            <!-- Human Agent -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bx bx-user me-1"></i>{{ __('livechat.select_agent') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control users" name="agent[]" multiple="multiple" required>
                                    @foreach ($users as $user)
                                    <option value="<?= $user->id; ?>"><?= $user->name; ?></option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    <i class="bx bx-info-circle me-1"></i>{{ __('livechat.select_agent_help') }}
                                </small>
                            </div>

                            <!-- Auto Reply Method -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bx bx-bot me-1"></i>{{__('master.device.auto_reply_method')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bx bx-message-dots"></i>
                                    </span>
                                    <select class="form-control methodreply" name="type" required>
                                        <option value="all">{{__('general.all')}} </option>
                                        <option value="chatbot">{{ __('livechat.auto_reply_manual') }}</option>
                                        <option value="ai">{{ __('livechat.chatbot_ai_auto') }}</option>
                                    </select>
                                </div>
                                <small class="text-muted">
                                    <i class="bx bx-info-circle me-1"></i>{{ __('livechat.auto_reply_help') }}
                                </small>
                            </div>

                            <!-- AI Training -->
                            <div class="col-12 mb-3 finetunneldata">
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
                                        <option value="{{$t->id}}">{{$t->name}}</option>
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
                                    <textarea class="form-control" name="description" rows="3" placeholder="{{ __('livechat.notes_placeholder') }}">{{old('description')}}</textarea>
                                </div>
                                <small class="text-muted">
                                    <i class="bx bx-info-circle me-1"></i>{{ __('livechat.notes_help') }}
                                </small>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.add_data')}}
                </button>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Upload Gambar -->
        <div class="card custom-card mb-3">
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-image me-2"></i>{{ __('livechat.upload_image_widget') }}
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-camera me-1"></i>{{ __('livechat.image_logo') }}
                        </label>
                        <input class="dropify" type="file" id="image" name="image" data-default-file="">
                        <small class="text-muted d-block mt-2">
                            <i class="bx bx-info-circle me-1"></i>{{ __('livechat.upload_image_help') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="card custom-card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <i class="bx bx-help-circle me-2"></i>{{ __('livechat.faq_full') }}
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm addfaq">
                    <i class="bx bx-plus-circle me-1"></i>{{ __('livechat.add_faq') }}
                </button>
            </div>
            <div class="card-body">
                <small class="text-muted d-block mb-3">
                    <i class="bx bx-info-circle me-1"></i>{{ __('livechat.faq_help') }}
                </small>
                <div class="table-responsive">
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
                            <tr class="text-center">
                                <td colspan="2" class="text-muted">
                                    <i class="bx bx-info-circle me-1"></i>{{ __('livechat.no_faq_yet') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Social Media Links -->
        <div class="card custom-card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <i class="bx bx-link me-2"></i>{{ __('livechat.social_media_links') }}
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm addsosmed">
                    <i class="bx bx-plus-circle me-1"></i>{{ __('livechat.add_social_link') }}
                </button>
            </div>
            <div class="card-body">
                <small class="text-muted d-block mb-3">
                    <i class="bx bx-info-circle me-1"></i>{{ __('livechat.social_media_help') }}
                </small>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="40%">
                                    <i class="bx bx-link-alt me-1"></i>{{ __('livechat.link_url') }}
                                </th>
                                <th width="25%">
                                    <i class="bx bx-text me-1"></i>{{ __('livechat.label') }}
                                </th>
                                <th width="20%">
                                    <i class="bx bx-image me-1"></i>{{ __('livechat.icon') }}
                                </th>
                                <th width="15%" class="text-center">
                                    <i class="bx bx-cog me-1"></i>{{ __('livechat.action') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="listSosmed">
                            <tr class="text-center">
                                <td colspan="4" class="text-muted">
                                    <i class="bx bx-info-circle me-1"></i>{{ __('livechat.no_social_yet') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
        whatsapp: "{{ __('livechat.whatsapp') }}"
    };

    $(document).ready(function() {
        $('.dropify').dropify();
        $('.users').select2({
            placeholder: translations.selectStaffPlaceholder,
            allowClear: true
        });

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