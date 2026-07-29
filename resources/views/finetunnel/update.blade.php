@extends('layouts.app')

@section('styles')
<style>
/* ═══ FineTunnel Redesign — 2026-07-30 ═══ */
.ft-header-name {
    font-size:1.1rem; font-weight:700;
    background:transparent; border:none;
    border-bottom:2px solid rgba(255,255,255,.12);
    color:inherit; padding:2px 6px; min-width:180px; max-width:320px;
}
.ft-header-name:focus {
    outline:none; border-bottom-color:#2E8DE1;
    background:rgba(46,141,225,.08); border-radius:4px 4px 0 0;
}
.ft-credit-chip {
    display:inline-flex; align-items:center; gap:5px;
    background:rgba(46,141,225,.12); border:1px solid rgba(46,141,225,.28);
    border-radius:20px; padding:3px 12px; font-size:.82rem;
    color:#2E8DE1; font-weight:600; white-space:nowrap;
}
.ft-step-badge {
    display:inline-flex; align-items:center; justify-content:center;
    width:28px; height:28px; border-radius:50%;
    font-size:.8rem; font-weight:700;
    background:#2E8DE1; color:#fff; flex-shrink:0; margin-top:2px;
}
.ft-step-badge.warn { background:#F59E0B; }
.ft-step-title { display:flex; align-items:flex-start; gap:10px; margin-bottom:1rem; }
.ft-step-title h6 { margin:0; font-weight:700; font-size:.95rem; line-height:1.3; }
.ft-guard-notice {
    background:rgba(245,158,11,.12); border:1px solid rgba(245,158,11,.28);
    border-radius:8px; padding:10px 14px; font-size:.81rem;
    color:#F59E0B; margin-top:14px;
}
.ft-chip-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:6px 14px; border-radius:20px;
    border:1px solid rgba(255,255,255,.14);
    background:rgba(255,255,255,.05);
    color:#94A3B8; font-size:.82rem; cursor:pointer;
    transition:all .2s; line-height:1; text-decoration:none;
}
.ft-chip-btn:hover { background:rgba(46,141,225,.15); border-color:rgba(46,141,225,.4); color:#2E8DE1; }
.ft-chip-btn.ft-active { background:rgba(46,141,225,.2); border-color:#2E8DE1; color:#2E8DE1; }
.ft-chip-count {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:20px; height:20px; border-radius:10px;
    background:rgba(46,141,225,.3); font-size:.72rem; font-weight:700; padding:0 5px;
}
.ft-rag-bar { height:5px; border-radius:3px; background:rgba(255,255,255,.08); overflow:hidden; }
.ft-rag-fill { height:100%; background:linear-gradient(90deg,#2E8DE1,#5B3FB0); border-radius:3px; transition:width .5s; }
.ft-label-chip {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 11px; border-radius:16px; cursor:pointer;
    border:1px solid rgba(255,255,255,.12); font-size:.8rem;
    transition:all .2s; user-select:none;
}
.ft-label-chip.selected { background:rgba(91,63,176,.2); border-color:#5B3FB0; color:#a78bfa; }
.ft-adv-toggle { cursor:pointer; user-select:none; }
.ft-adv-toggle:hover .card-body { opacity:.85; }
.ft-adv-icon { transition:transform .25s; }
.ft-adv-toggle[aria-expanded="true"] .ft-adv-icon { transform:rotate(180deg); }
.ft-step3-card { border-left:3px solid #F59E0B !important; }
.ft-n-inline { width:72px; display:inline-block !important; text-align:center; }
.ft-tpl-btn {
    background:rgba(91,63,176,.1); border:1px solid rgba(91,63,176,.25);
    border-radius:8px; color:#a78bfa; font-size:.78rem;
    padding:4px 10px; cursor:pointer; transition:all .2s; white-space:nowrap;
}
.ft-tpl-btn:hover { background:rgba(91,63,176,.25); }
@media (min-width:992px) { .ft-chat-sticky { position:sticky; top:74px; } }
</style>
@endsection

@section('content')
<form id="ft-form" action="{{ route(\'finetunnel.edit\', $fineTunnel->id) }}" method="POST" enctype="multipart/form-data">
@csrf
{{-- Variable alias agar $finetunnel = $fineTunnel dari controller --}}
@php $finetunnel = $fineTunnel ?? $finetunnel ?? null; @endphp


{{-- ════ HEADER ════ --}}
<div class="d-flex align-items-start align-items-sm-center flex-column flex-sm-row justify-content-between gap-3 mb-3">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
             style="width:44px;height:44px;background:linear-gradient(135deg,#2E8DE1,#5B3FB0)">
            <i class="bx bx-bot text-white" style="font-size:1.3rem"></i>
        </div>
        <div>
            <input type="text" name="name" class="ft-header-name"
                   value="{{ $finetunnel->name }}" placeholder="Nama AI Agent" required>
            <div class="text-muted" style="font-size:.78rem;padding-left:6px;margin-top:2px">Agen AI · aktif</div>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        @isset($credit)
        <span class="ft-credit-chip">
            <i class="bx bx-coin-stack"></i>
            Kredit AI: {{ number_format($credit) }}
        </span>
        @endisset
        <button type="submit" class="btn btn-primary px-4">
            <i class="bx bx-save me-1"></i>Simpan
        </button>
    </div>
</div>
<p class="text-muted small mb-4">
    <i class="bx bx-sparkles me-1" style="color:#2E8DE1"></i>
    Cukup isi 3 hal: <strong>siapa AI-nya</strong>, apa yang dia tahu, dan kapan menyerahkan ke manusia.
</p>

<div class="row g-3">
{{-- ════ KOLOM KIRI ════ --}}
<div class="col-lg-7">

    {{-- ─── STEP 1: Siapa AI kamu ─── --}}
    <div class="card custom-card mb-3">
        <div class="card-body">
            <div class="ft-step-title">
                <span class="ft-step-badge">1</span>
                <div>
                    <h6>Siapa AI kamu</h6>
                    <span class="text-muted" style="font-size:.8rem">Sifat, gaya bicara, dan info produk yang boleh disampaikan.</span>
                </div>
            </div>

            {{-- Template presets --}}
            <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                <span class="text-muted" style="font-size:.78rem">Mulai dari template:</span>
                <button type="button" class="ft-tpl-btn" onclick="ftApplyTpl('toko')">&#x1F6D2; Toko Online</button>
                <button type="button" class="ft-tpl-btn" onclick="ftApplyTpl('kelas')">&#x1F4DA; Kelas/e-course</button>
                <button type="button" class="ft-tpl-btn" onclick="ftApplyTpl('booking')">&#x1F4C5; Reservasi</button>
            </div>

            <label class="form-label fw-semibold mb-1">Karakter &amp; info AI</label>
            <div class="text-muted mb-2" style="font-size:.8rem">Sifat, gaya bicara, harga, dan info yang boleh AI sampaikan ke pelanggan.</div>
            <textarea id="ft-description" name="description" class="form-control mb-3" rows="6"
                placeholder="Contoh: Kamu adalah CS yang ramah untuk toko Baju Anak Murah. Harga kaos mulai Rp35.000.">{{ $finetunnel->description }}</textarea>

            <label class="form-label fw-semibold mb-1">Pesan sambutan pertama</label>
            <input type="text" name="welcome_message" class="form-control mb-3"
                   value="{{ $finetunnel->welcome_message }}"
                   placeholder="Halo! Selamat datang &#x1F44B; Ada yang bisa saya bantu?">

            <label class="form-label fw-semibold mb-1">
                Gambar sambutan
                <span class="text-muted fw-normal ms-1" style="font-size:.8rem">(opsional)</span>
            </label>
            <input type="file" name="image" class="form-control mb-2" accept="image/*">
            @if($finetunnel->image)
            <div class="mb-3 d-flex align-items-center gap-3">
                <img src="{{ asset('storage/'.$finetunnel->image) }}" class="rounded"
                     style="max-height:60px;max-width:120px;object-fit:cover;opacity:.8" alt="">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" name="remove_image" id="ft-removeImg" value="1">
                    <label class="form-check-label text-muted" for="ft-removeImg" style="font-size:.8rem">Hapus gambar ini</label>
                </div>
            </div>
            @endif

            <div class="ft-guard-notice">
                <i class="bx bx-shield-alt-2 me-1"></i>
                <strong>Pengaman otomatis:</strong>
                AI tak menjanjikan harga/diskon di luar info ini &amp; menyerahkan ke agen bila ragu.
            </div>
        </div>
    </div>

    {{-- ─── STEP 2: Apa yang AI tahu ─── --}}
    <div class="card custom-card mb-3">
        <div class="card-body">
            <div class="ft-step-title">
                <span class="ft-step-badge">2</span>
                <div>
                    <h6>Apa yang AI tahu</h6>
                    <span class="text-muted" style="font-size:.8rem">Klik sumber di bawah untuk menambah pengetahuan AI.</span>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-3" id="ft-chips">
                <button type="button" class="ft-chip-btn" data-ft-tab="#documents">
                    <i class="bx bx-file-blank"></i> Dokumen
                    @if(isset($ragDocuments) && $ragDocuments->count() > 0)
                    <span class="ft-chip-count">{{ $ragDocuments->count() }}</span>
                    @endif
                </button>
                <button type="button" class="ft-chip-btn" data-ft-tab="#g-sheet">
                    <i class="bx bx-table"></i> Google Sheet
                </button>
                <button type="button" class="ft-chip-btn" data-ft-tab="#follow-ups">
                    <i class="bx bx-time-five"></i> Follow up
                    @if(isset($finetunnel->follow_ups) && $finetunnel->follow_ups->count() > 0)
                    <span class="ft-chip-count">{{ $finetunnel->follow_ups->count() }}</span>
                    @endif
                </button>
                <button type="button" class="ft-chip-btn" data-ft-tab="#courier-data">
                    <i class="bx bx-package"></i> Ongkir
                </button>
            </div>

            @php
                $ftUsed = isset($ragStorageUsed) ? $ragStorageUsed : (isset($usedStorage) ? $usedStorage : null);
                $ftMax  = isset($maxRagStorage)  ? $maxRagStorage  : (isset($maxStorage)  ? $maxStorage  : null);
                $ftPct  = ($ftMax && $ftMax > 0) ? min(100, round($ftUsed / $ftMax * 100)) : 0;
            @endphp
            @if($ftUsed !== null && $ftMax !== null)
            <div class="mb-3">
                <div class="ft-rag-bar mb-1">
                    <div class="ft-rag-fill" style="width:{{ $ftPct }}%"></div>
                </div>
                <small class="text-muted">
                    {{ round($ftUsed/1024/1024,1) }} dari {{ round($ftMax/1024/1024,1) }} MB dokumen terpakai
                </small>
            </div>
            @endif



            {{-- Tab panes (dipreserve dari blade asli) --}}
<div class="tab-content">
                                <!-- Tab AI Behaviour -->
                                <div class="tab-pane fade active show" id="general-information" role="tabpanel">
                                    <!-- Gaya Bicara dengan Character Counter -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="bx bx-message-square-edit me-1"></i>{{ __('finetunnel.speech_style') }}
                                        </label>
                                        <small class="d-block text-muted mb-2">
                                            <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.speech_style_description') }}
                                        </small>
                                        <textarea
                                            id="ai-description"
                                            class="form-control"
                                            name="description"
                                            style="height: 300px; resize: vertical;"
                                            maxlength="15000"
                                            placeholder="{{ __('finetunnel.describe_ai_character') }}">{{ old('description', $fineTunnel->description) }}</textarea>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <small class="text-muted">
                                                <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.describe_ai_character_detail') }}
                                            </small>
                                            <div class="character-counter">
                                                <span id="charCount">0</span> / 15,000 {{ __('finetunnel.character_count') }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Welcome Message -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="bx bx-message-dots me-1"></i>{{ __('finetunnel.welcome_message') }}
                                        </label>
                                        <small class="d-block text-muted mb-2">
                                            <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.welcome_message_desc') }}
                                        </small>
                                        <textarea class="form-control" id="welcomeMessage" style="height: 100px; resize: vertical;" name="welcome_message" placeholder="{{ __('finetunnel.welcome_message_placeholder') }}">{{old('welcome_message',$fineTunnel->welcome_message)}}</textarea>
                                        <small class="d-block text-muted mb-2">
                                            <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.welcome_message_name_info') }}
                                        </small>
                                    </div>

                                    <!-- Welcome Image -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="bx bx-image me-1"></i>{{ __('finetunnel.welcome_message_image') }}
                                        </label>
                                        <input class="dropify" type="file" id="image" name="image" data-default-file="{{asset($fineTunnel->welcome_image)}}" accept="image/*">
                                        <small class="text-muted d-block mt-2">
                                            <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.welcome_message_image_info') }}
                                        </small>
                                    </div>

                                    <!-- Transfer Conditions -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="bx bx-transfer me-1"></i>{{ __('finetunnel.agent_transfer_condition') }}
                                        </label>
                                        <small class="d-block text-muted mb-2">
                                            <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.agent_transfer_condition_desc') }}
                                        </small>
                                        <textarea class="form-control" style="height: 100px; resize: vertical;" name="term_condition" placeholder="{{ __('finetunnel.agent_transfer_condition_placeholder') }}">{{old('term_condition',$fineTunnel->transfer_condition)}}</textarea>
                                    </div>

                                    <!-- Pilih Human Agent -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="bx bx-group me-1"></i>{{ __('finetunnel.choose_human_agent') }}
                                        </label>
                                        <small class="d-block text-muted mb-2">
                                            <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.choose_human_agent_desc') }}
                                        </small>
                                        <select class="form-control users" name="agent[]" multiple="multiple">
                                            @foreach ($users as $user)
                                            <option value="<?= $user->id; ?>" {{ in_array($user->id, explode(',',$fineTunnel->agent)) ? 'selected' : '' }}>
                                                <?= $user->name; ?>
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Label Otomatis -->
                                    <div class="mb-3">
                                        <p class="form-label fw-semibold">
                                            <i class="bx bx-purchase-tag me-1"></i>{{ __('finetunnel.auto_label_ai') }}
                                        </p>
                                        <small class="d-block text-muted mb-2">
                                            <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.auto_label_ai_desc') }}
                                        </small>
                                        @php $selectedLabels = explode(',', $fineTunnel->label); @endphp
                                        <div class="row gy-3">
                                            @foreach ($labels as $label)
                                            <div class="col-xl-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="{{$label->id}}" name="label[]" id="label-{{$label->id}}" {{ in_array($label->id, $selectedLabels) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="label-{{$label->id}}">
                                                        {{$label->name}}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Follow Up -->
                                <div class="tab-pane fade" id="follow-ups" role="tabpanel">
                                    <div class="alert alert-success mb-3" role="alert">
                                        <div class="d-flex align-items-start">
                                            <i class="bx bx-info-circle fs-20 me-2"></i>
                                            <div>
                                                <h6 class="alert-heading mb-1">{{ __('finetunnel.follow_up_instruction') }}</h6>
                                                <p class="mb-0 small">
                                                    {{ __('finetunnel.follow_up_desc_1') }} {{ __('finetunnel.follow_up_desc_2') }}
                                                    <br><strong>{{ __('finetunnel.follow_up_warning') }}</strong> {{ __('finetunnel.follow_up_warning_text') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mb-3">
                                        <button class="btn btn-outline-primary" type="button" id="addFollowUp">
                                            <i class="bx bx-plus-circle me-1"></i>{{ __('finetunnel.add_follow_up') }}
                                        </button>
                                        <input type="hidden" id="followUpCount" value="{{ count($fineTunnel->follow_ups) }}" />
                                    </div>
                                    <div id="listFollowUps">
                                        @foreach ($fineTunnel->follow_ups as $index => $follow)
                                        <div class="card mb-3" id="followUp-{{$follow->id}}">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0"><i class="bx bx-time-five me-1"></i>{{ __('finetunnel.follow_up') }}</h6>
                                                <button type="button" class="btn btn-outline-danger btn-sm removeFollowUp" data-id="{{$follow->id}}">
                                                    <i class="bx bx-trash"></i> {{ __('finetunnel.delete') }}
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bx bx-message-square-detail me-1"></i>{{ __('finetunnel.prompt_text_follow_up') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea class="form-control" name="prompt[]" required rows="5" placeholder="{{ __('finetunnel.follow_up_message_placeholder') }}">{{ $follow->text }}</textarea>
                                                    <small class="text-muted">
                                                        <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.message_sent_by_ai') }}
                                                    </small>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bx bx-timer me-1"></i>{{ __('finetunnel.delay_limit') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="bx bx-time-five"></i>
                                                        </span>
                                                        <input type="number" class="form-control" name="delay_followups[]" required min="1" value="{{$follow->delay}}">
                                                        <span class="input-group-text">{{ __('finetunnel.minutes_unit') }}</span>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.delay_before_send') }}
                                                    </small>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="hidden" name="exact[{{$index}}]" value="no">
                                                    <input class="form-check-input" type="checkbox" @if($follow->exact == 'yes') checked @endif name="exact[{{$index}}]" id="exact-{{$follow->id}}">
                                                    <label class="form-check-label" for="exact-{{$follow->id}}">
                                                        <i class="bx bx-lock me-1"></i>{{ __('finetunnel.exact_label') }}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="hidden" name="handoff[{{$index}}]" value="no">
                                                    <input class="form-check-input" type="checkbox"
                                                        @if($follow->handoff == 'yes') checked @endif
                                                    name="handoff[{{$index}}]"
                                                    id="handoff-{{$follow->id}}">
                                                    <label class="form-check-label" for="handoff-{{$follow->id}}">
                                                        <i class="bx bx-user-check me-1"></i>{{ __('finetunnel.handoff_label') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Tab Data Training -->
                                <div class="tab-pane fade" id="documents" role="tabpanel">
                                    <div class="alert alert-info mb-3" role="alert">
                                        <div class="d-flex align-items-start">
                                            <i class="bx bx-info-circle fs-20 me-2"></i>
                                            <div>
                                                <h6 class="alert-heading mb-1">{{ __('finetunnel.rag_document_info') }}</h6>
                                                <p class="mb-0 small">
                                                    {{ __('finetunnel.rag_document_desc') }}
                                                    <br><strong>{{ __('finetunnel.supported_formats') }}</strong> PDF, Word (DOC/DOCX), Excel (XLS/XLSX), CSV
                                                    <br><strong>{{ __('finetunnel.max_file_size') }}</strong> <span id="maxFileSizeText">-</span>
                                                    <br><strong>{{ __('finetunnel.max_total_size') }}</strong> <span id="maxTotalSizeText">-</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Upload Area -->
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h6 class="mb-0">
                                                <i class="bx bx-cloud-upload me-1"></i>{{ __('finetunnel.upload_document') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="upload-area" id="uploadArea">
                                                <input type="file"
                                                    id="ragDocumentInput"
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.csv"
                                                    style="display: none;">
                                                <div class="text-center py-5" id="uploadPlaceholder">
                                                    <i class="bx bx-cloud-upload" style="font-size: 48px; color: #696cff;"></i>
                                                    <p class="mt-3 mb-2"><strong>{{ __('finetunnel.drag_drop_or_click') }}</strong></p>
                                                    <p class="text-muted small mb-3">{{ __('finetunnel.supported_formats_list') }}</p>
                                                    <button type="button" class="btn btn-primary" id="selectFileBtn">
                                                        <i class="bx bx-folder-open me-1"></i>{{ __('finetunnel.select_file') }}
                                                    </button>
                                                </div>

                                                <!-- Progress Area -->
                                                <div id="uploadProgress" style="display: none;">
                                                    <div class="text-center py-4">
                                                        <div class="spinner-border text-primary mb-3" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                        <p class="mb-1"><strong id="uploadStatusText">{{ __('finetunnel.processing') }}</strong></p>
                                                        <p class="text-muted small" id="uploadFileName"></p>
                                                        <div class="progress mt-3" style="height: 20px;">
                                                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                                id="progressBar"
                                                                role="progressbar"
                                                                style="width: 0%">
                                                                <span id="progressText">0%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Storage Info -->
                                            <div class="mt-3 p-3 bg-light rounded">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-muted small">
                                                        <i class="bx bx-hdd me-1"></i>{{ __('finetunnel.storage_used') }}
                                                    </span>
                                                    <span class="fw-bold" id="storageInfo">0 MB / 0 MB</span>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar"
                                                        id="storageBar"
                                                        role="progressbar"
                                                        style="width: 0%">
                                                    </div>
                                                </div>

                                                <!-- Package Limits Info -->
                                                <div class="mt-3 pt-3 border-top">
                                                    <div class="row g-2 small">
                                                        <div class="col-6">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-file me-2 text-primary"></i>
                                                                <div>
                                                                    <div class="text-muted">Max per File</div>
                                                                    <strong id="maxPerUploadInfo">-</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-layer me-2 text-success"></i>
                                                                <div>
                                                                    <div class="text-muted">Total Limit</div>
                                                                    <strong id="maxTotalRagInfo">-</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Documents List -->
                                    <div id="ragDocumentsList">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                </div>

                                <!-- Tab Courier -->
                                @if($courierStatus)
                                <div class="tab-pane fade" id="courier-data" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="bx bx-map me-1"></i>{{__('sidebar.state')}}
                                            </label>
                                            <select class="form-control provinces" name="province">
                                                <option value="">{{__('master.directory.choose_state')}}</option>
                                                @foreach ($provinces as $province)
                                                <option value="<?= $province->id; ?>" @if(($fineTunnel->subdistrict->district->city->province_id ?? '') == $province->id) selected @endif><?= $province->name; ?></option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">
                                                <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.origin_province_info') }}
                                            </small>
                                        </div>

                                        <div class="col-lg-6 col-sm-12 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="bx bx-building me-1"></i>{{__('sidebar.city')}}
                                            </label>
                                            <select class="form-control cities" name="city">
                                                <option value="<?= $fineTunnel->subdistrict->district->city->id ?? ''; ?>">
                                                    <?= $fineTunnel->subdistrict->district->city->type ?? __('finetunnel.select_city_option'); ?>
                                                    <?= $fineTunnel->subdistrict->district->city->name ?? ''; ?>
                                                </option>
                                            </select>
                                            <small class="text-muted">
                                                <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.origin_city_info') }}
                                            </small>
                                        </div>

                                        <div class="col-lg-6 col-sm-12 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="bx bx-map-pin me-1"></i>{{__('sidebar.district')}}
                                            </label>
                                            <select class="form-control districts" name="district">
                                                <option value="<?= $fineTunnel->subdistrict->district_id ?? ''; ?>"><?= $fineTunnel->subdistrict->district->name ?? __('master.directory.choose_district'); ?></option>
                                            </select>
                                            <small class="text-muted">
                                                <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.origin_district_info') }}
                                            </small>
                                        </div>

                                        <div class="col-lg-6 col-sm-12 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="bx bx-current-location me-1"></i>{{ __('finetunnel.origin_subdistrict_label') }}
                                            </label>
                                            <select class="form-control subdistricts" name="sub_district_id">
                                                <option value="<?= $fineTunnel->sub_district_id ?? ''; ?>"><?= $fineTunnel->subdistrict->name ?? __('finetunnel.origin_subdistrict_placeholder'); ?></option>
                                            </select>
                                            <small class="text-muted">
                                                <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.origin_subdistrict_info') }}
                                            </small>
                                        </div>

                                        <div class="col-lg-6 col-sm-12 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="bx bx-package me-1"></i>{{ __('finetunnel.input_weight') }}
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bx bx-calculator"></i>
                                                </span>
                                                <input type="text" class="form-control" placeholder="{{ __('finetunnel.input_weight_placeholder') }}" name="weight" value="{{old('weight',(int)$fineTunnel->weight)}}">
                                                <span class="input-group-text">{{ __('finetunnel.weight_unit') }}</span>
                                            </div>
                                            <small class="text-muted">
                                                <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.weight_info') }}
                                            </small>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <div class="alert alert-primary" role="alert">
                                                <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.courier_integration_note') }}
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th><i class="bx bx-package me-1"></i>{{ __('finetunnel.courier_name') }}</th>
                                                            <th class="text-center" width="100"><i class="bx bx-check-square me-1"></i>{{ __('finetunnel.courier_status') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($couriers as $courier)
                                                        <tr>
                                                            <td>
                                                                <div class="fw-semibold">{{$courier->name}} - {{$courier->service}}</div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-check d-flex justify-content-center">
                                                                    <input class="form-check-input" type="checkbox" name="couriers[]" value="<?= $courier->code; ?>" @if(check_courier($courier->code,$fineTunnel->id)) checked @endif id="courier-{{$courier->code}}">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <input type="hidden" id="sheetCount" value="{{ count($fineTunnel->gsheets) }}" />

                                <!-- Tab Google Sheet -->
                                @if($gsheet)
                                <div class="tab-pane fade" id="g-sheet" role="tabpanel">
                                    <div class="alert alert-primary mb-3" role="alert">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="alert-heading mb-0">
                                                <i class="bx bx-book-open me-1"></i>{{ __('finetunnel.gsheet_guide') }}
                                            </h6>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleGuide()">
                                                <i class="bx bx-chevron-up" id="toggleIcon"></i> <span id="toggleText">{{ __('finetunnel.gsheet_toggle') }}</span>
                                            </button>
                                        </div>
                                        <div id="guideContent">
                                            <hr>
                                            <div class="row small">
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold mt-2">{{ __('finetunnel.gsheet_step1') }}</h6>
                                                    <ul>
                                                        <li>{{ __('finetunnel.gsheet_step1_a') }}</li>
                                                        <li>{{ __('finetunnel.gsheet_step1_b') }}</li>
                                                        <li>{{ __('finetunnel.gsheet_step1_c') }}</li>
                                                    </ul>
                                                    <h6 class="fw-bold mt-2">{{ __('finetunnel.gsheet_step2') }}</h6>
                                                    <ul>
                                                        <li>{{ __('finetunnel.gsheet_step2_b') }}</li>
                                                        <li>{{ __('finetunnel.gsheet_step2_c') }}</li>
                                                        <li>{{ __('finetunnel.gsheet_step2_d') }}</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold mt-2">{{ __('finetunnel.gsheet_step3') }}</h6>
                                                    <ul>
                                                        <li>{{ __('finetunnel.gsheet_step3_a') }}</li>
                                                        <li>{{ __('finetunnel.gsheet_step3_c') }}</li>
                                                    </ul>
                                                    <h6 class="fw-bold mt-2">{{ __('finetunnel.gsheet_realtime') }}</h6>
                                                    <ul>
                                                        <li>{{ __('finetunnel.gsheet_realtime_a') }}</li>
                                                        <li>{{ __('finetunnel.gsheet_realtime_b') }}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mb-3">
                                        <button class="btn btn-outline-primary" type="button" id="addGSheet">
                                            <i class="bx bx-plus-circle me-1"></i>{{ __('finetunnel.add_gsheet') }}
                                        </button>
                                    </div>
                                    <div id="listGsheet">
                                        @foreach($fineTunnel->gsheets as $sheet)
                                        <div class="card mb-3 cardsheet" id="datasheet-{{$sheet->id}}">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0"><i class="bx bx-spreadsheet me-1"></i>{{ __('finetunnel.google_sheet') }}</h6>
                                                <button type="button" class="btn btn-outline-danger btn-sm removeSheet" data-id="{{$sheet->id}}">
                                                    <i class="bx bx-trash"></i> {{ __('finetunnel.delete') }}
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bx bx-link-alt me-1"></i>{{ __('finetunnel.google_sheet_url') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="bx bx-globe"></i>
                                                        </span>
                                                        <input type="url" class="form-control" name="url[]" value="{{$sheet->url}}" required placeholder="{{ __('finetunnel.gsheet_url_placeholder') }}">
                                                        <button type="button" class="btn btn-outline-primary" onclick="validateUrl(this)">
                                                            <i class="bx bx-check-circle"></i> {{ __('finetunnel.validate') }}
                                                        </button>
                                                    </div>
                                                    <div id="urlValidation" class="mt-2"></div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bx bx-toggle-right me-1"></i>{{ __('finetunnel.status') }}
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="bx bx-info-square"></i>
                                                        </span>
                                                        <select class="form-control" name="status_sheet[]">
                                                            <option value="yes" @if($sheet->status == 'yes') selected @endif>{{ __('finetunnel.active_status') }}</option>
                                                            <option value="no" @if($sheet->status == 'no') selected @endif>{{ __('finetunnel.inactive_status') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div id="previewContainer" class="mt-3" style="display: none;">
                                                    <h6><i class="bx bx-show me-1"></i>{{ __('finetunnel.preview_data') }}</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered" id="previewTable">
                                                            <thead class="table-light"></thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer text-end">
                                                <button type="button" class="btn btn-info btn-sm me-2" onclick="previewData(this)">
                                                    <i class="bx bx-show"></i> {{ __('finetunnel.preview') }}
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
        </div>
    </div>

    {{-- ─── STEP 3: Kapan diserahkan ke manusia ─── --}}
    <div class="card custom-card ft-step3-card mb-3">
        <div class="card-body">
            <div class="ft-step-title">
                <span class="ft-step-badge warn">3</span>
                <div>
                    <h6>Kapan diserahkan ke manusia</h6>
                    <span class="text-muted" style="font-size:.8rem">Agar AI tidak menangani terlalu lama atau kasus di luar kemampuannya.</span>
                </div>
            </div>

            <label class="form-label fw-semibold mb-1">
                Kalau pelanggan menyebut kata ini, serahkan ke agen
            </label>
            <textarea name="term_condition" class="form-control mb-1" rows="2"
                placeholder="complaint, refund, mau bicara dengan orang">{{ old('term_condition', $fineTunnel->transfer_condition) }}</textarea>
            <small class="text-muted d-block mb-4">Pisahkan dengan koma. Contoh: refund, mau bicara, komplain</small>

            <label class="form-label fw-semibold mb-2 d-flex align-items-center flex-wrap gap-2">
                Atau setelah AI balas
                <input type="number" name="message_limit"
                       class="form-control ft-n-inline" min="0" max="9999"
                       value="{{ $finetunnel->message_limit ?? 0 }}">
                kali, serahkan ke agen
            </label>
            <small class="text-muted d-block mb-4">Isi 0 = tidak ada batas otomatis</small>

            <label class="form-label fw-semibold mb-2">Agen yang dihubungi saat serah terima</label>
<select class="form-control users" name="agent[]" multiple="multiple">
                                            @foreach ($users as $user)
                                            <option value="<?= $user->id; ?>" {{ in_array($user->id, explode(',',$fineTunnel->agent)) ? 'selected' : '' }}>
                                                <?= $user->name; ?>
                                            </option>
                                            @endforeach
                                        </select>
        </div>
    </div>

    {{-- ─── AUTO LABEL ─── --}}
    @if(isset($labels) && $labels->count() > 0)
    <div class="card custom-card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="fw-semibold me-2" style="font-size:.88rem">
                    <i class="bx bx-purchase-tag-alt me-1" style="color:#5B3FB0"></i>Auto label
                </span>
@foreach ($labels as $label)
                                            <div class="col-xl-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="{{$label->id}}" name="label[]" id="label-{{$label->id}}" {{ in_array($label->id, $selectedLabels) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="label-{{$label->id}}">
                                                        {{$label->name}}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ─── PENGATURAN LANJUTAN ─── --}}
    <div class="card custom-card ft-adv-toggle mb-1"
         role="button"
         data-bs-toggle="collapse" data-bs-target="#ft-adv"
         aria-expanded="false">
        <div class="card-body py-3 d-flex justify-content-between align-items-center">
            <div>
                <span class="fw-semibold">
                    <i class="bx bx-slider-alt me-2 text-muted"></i>Pengaturan lanjutan
                </span>
                <div class="text-muted" style="font-size:.78rem;margin-top:2px">
                    Model AI, ingatan, kedalaman baca dokumen, jeda balas — sudah diatur aman
                </div>
            </div>
            <i class="bx bx-chevron-down ft-adv-icon text-muted" style="font-size:1.2rem"></i>
        </div>
    </div>
    <div class="collapse mb-3" id="ft-adv">
        <div class="card custom-card" style="border-top-left-radius:0;border-top-right-radius:0;border-top:none">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Model AI</label>
                        <select name="model_ai" class="form-select">
                            <option value="basic" {{ ($finetunnel->model_ai ?? 'basic') === 'basic' ? 'selected' : '' }}>
                                Standard — hemat kredit
                            </option>
                            <option value="advanced" {{ ($finetunnel->model_ai ?? '') === 'advanced' ? 'selected' : '' }}>
                                Advanced — lebih pintar, lebih boros
                            </option>
                        </select>
                        <small class="text-muted">Standard = hemat; Advanced = lebih pintar</small>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold mb-1 d-flex align-items-center gap-2 flex-wrap">
                            Ingat
                            <input type="number" name="history_limit"
                                   class="form-control ft-n-inline" min="1" max="100"
                                   value="{{ $finetunnel->history_limit ?? 20 }}">
                            pesan terakhir
                        </label>
                        <small class="text-muted">Lebih banyak = lebih ingat konteks (1–100)</small>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold mb-1 d-flex align-items-center gap-2 flex-wrap">
                            Kedalaman baca dokumen
                            <input type="number" name="context_limit"
                                   class="form-control ft-n-inline" min="1" max="100"
                                   value="{{ $finetunnel->context_limit ?? 10 }}">
                        </label>
                        <small class="text-muted">Seberapa dalam AI menggali info dari dokumenmu (1–100)</small>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold mb-1 d-flex align-items-center gap-2 flex-wrap">
                            Jeda balas
                            <input type="number" name="delay"
                                   class="form-control ft-n-inline" min="0" max="60"
                                   value="{{ $finetunnel->delay ?? 1 }}">
                            detik
                        </label>
                        <small class="text-muted">Waktu tunggu sebelum AI membalas (0–60 detik)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /col-lg-7 --}}

{{-- ════ KOLOM KANAN: TES CHAT ════ --}}
<div class="col-lg-5">
    <div class="ft-chat-sticky">
        <div class="chat-test-container" id="chatTestContainer">
                <div class="chat-test-box">
                    <div class="chat-header">
                        <h6 class="mb-1">
                            <i class="bx bx-message-square-dots me-1"></i> {{ __('finetunnel.test_chat_ai') }}
                        </h6>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.test_chat_ai_desc') }}
                        </small>
                    </div>
                    <div class="chat-messages" id="chatMessages">
                        <div class="text-center text-muted py-5" id="sampleData">
                            <i class="bx bx-message-dots" style="font-size: 48px;"></i>
                            <p class="mt-2">{{ __('finetunnel.test_chat_ai_empty') }}</p>
                        </div>
                    </div>
                    <div class="chat-input-area">
                        <div class="chat-input-group">
                            <input type="text" class="form-control" id="chatInput" placeholder="{{ __('finetunnel.test_chat_ai_placeholder') }}" autocomplete="off">
                            <button type="button" class="btn btn-primary" id="sendMessage">
                                <i class="bx bx-send"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bx bx-info-circle me-1"></i>{{ __('finetunnel.test_chat_ai_hint') }}
                        </small>
                        <button type="button" class="btn btn-sm btn-outline-danger mt-2 w-100" id="clearChat">
                            <i class="bx bx-trash"></i> {{ __('finetunnel.clear_chat') }}
                        </button>
                    </div>
                </div>
            </div>
    </div>
</div>

</div>{{-- /row --}}
</form>
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/dropify/js/dropify.min.js')}}"></script>
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        // Get all translation strings from hidden HTML elements

        let packageLimits = {
            maxPerUpload: 0, // in MB
            maxTotalRag: 0, // in MB
            maxPerUploadBytes: 0, // in bytes
            maxTotalRagBytes: 0 // in bytes
        };

        const trans = {
            uploading: $('#lang-uploading').text(),
            processingDocument: $('#lang-processing-document').text(),
            uploadFailed: $('#lang-upload-failed').text(),
            pleaseFillAiBehaviour: $('#lang-please-fill-ai-behaviour').text(),
            connectionError: $('#lang-connection-error').text(),
            errorTitle: $('#lang-error-title').text(),
            errorProcessingRequest: $('#lang-error-processing-request').text(),
            connectionErrorTitle: $('#lang-connection-error-title').text(),
            serverConnectionFailed: $('#lang-server-connection-failed').text(),
            clearChatConfirmTitle: $('#lang-clear-chat-confirm-title').text(),
            clearChatConfirmText: $('#lang-clear-chat-confirm-text').text(),
            yesDelete: $('#lang-yes-delete').text(),
            cancel: $('#lang-cancel').text(),
            success: $('#lang-success').text(),
            chatCleared: $('#lang-chat-cleared').text(),
            jsPreviewWelcome: $('#lang-js-preview-welcome').text(),
            jsTypeMessageStart: $('#lang-js-type-message-start').text(),
            jsStartConversation: $('#lang-js-start-conversation').text(),
            jsFillCharacterFirst: $('#lang-js-fill-character-first').text(),
            searchProductBadge: $('#lang-search-product-badge').text(),
            otherBadge: $('#lang-other-badge').text(),
            cartBadge: $('#lang-cart-badge').text(),
            checkoutBadge: $('#lang-checkout-badge').text(),
            checkShippingBadge: $('#lang-check-shipping-badge').text(),
            noDocumentsYet: $('#lang-no-documents-yet').text(),
            uploadInProgressWarning: $('#lang-upload-in-progress-warning').text(),
            enterUrlFirst: $('#lang-enter-url-first').text(),
            invalidUrlFormat: $('#lang-invalid-url-format').text(),
            loading: $('#lang-loading').text(),
            noDataToPreview: $('#lang-no-data-to-preview').text(),
            preview: $('#lang-preview').text(),
            selectHumanAgent: $('#lang-select-human-agent').text(),
            selectProvince: $('#lang-select-province').text(),
            selectCity: $('#lang-select-city').text(),
            selectDistrict: $('#lang-select-district').text(),
            selectVillage: $('#lang-select-village').text(),
            newFollowup: $('#lang-new-followup').text(),
            delete: $('#lang-delete').text(),
            promptTextFollowUp: $('#lang-prompt-text-follow-up').text(),
            followUpMessagePlaceholder: $('#lang-follow-up-message-placeholder').text(),
            messageSentByAi: $('#lang-message-sent-by-ai').text(),
            delayLimit: $('#lang-delay-limit').text(),
            minutesUnit: $('#lang-minutes-unit').text(),
            delayBeforeSend: $('#lang-delay-before-send').text(),
            exactLabel: $('#lang-exact-label').text(),
            handoffLabel: $('#lang-handoff-label').text(),
            newGsheet: $('#lang-new-gsheet').text(),
            googleSheetUrl: $('#lang-google-sheet-url').text(),
            gsheetUrlPlaceholder: $('#lang-gsheet-url-placeholder').text(),
            validate: $('#lang-validate').text(),
            status: $('#lang-status').text(),
            activeStatus: $('#lang-active-status').text(),
            inactiveStatus: $('#lang-inactive-status').text(),
            previewData: $('#lang-preview-data').text(),
            gsheetToggle: $('#lang-gsheet-toggle').text(),
            showGuide: $('#lang-show-guide').text()
        };

        let chatHistory = [];
        let productDiscussion = null;
        let isProcessing = false;
        let isUploading = false;
        let uploadedFiles = new Set();

        // Enhanced sticky detection with smooth shadow effect
        let lastScrollTop = 0;
        let ticking = false;
        loadRagDocuments();

        $('#selectFileBtn, #uploadPlaceholder').click(function() {
            if (!isUploading) {
                $('#ragDocumentInput').click();
            }
        });

        // Drag & Drop
        const uploadArea = document.getElementById('uploadArea');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            if (!isUploading) {
                uploadArea.classList.add('drag-over');
            }
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('drag-over');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');

            if (!isUploading && e.dataTransfer.files.length > 0) {
                handleFileUpload(e.dataTransfer.files[0]);
            }
        });

        // File Input Change
        $('#ragDocumentInput').change(function() {
            if (this.files.length > 0) {
                handleFileUpload(this.files[0]);
            }
        });

        // Handle File Upload
        // Handle File Upload
        function handleFileUpload(file) {
            // Check if package limits loaded
            if (packageLimits.maxPerUpload === 0 || packageLimits.maxTotalRag === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Package limits belum dimuat. Refresh halaman dan coba lagi.',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            // Validate file size against package limit
            if (file.size > packageLimits.maxPerUploadBytes) {
                const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    html: `Ukuran file melebihi batas paket Anda<br>
                   <strong>Ukuran file:</strong> ${fileSizeMB} MB<br>
                   <strong>Batas paket:</strong> ${packageLimits.maxPerUpload} MB`,
                    confirmButtonColor: '#d33'
                });
                return;
            }

            // Validate file type
            const allowedTypes = [
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
                'text/csv'
            ];

            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Tidak Didukung',
                    text: 'Hanya menerima PDF, Word, Excel, dan CSV',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            // Show confirmation
            Swal.fire({
                title: 'Upload Dokumen?',
                html: `<p>File: <strong>${file.name}</strong></p>
               <p>Ukuran: <strong>${formatBytes(file.size)}</strong></p>
               <p class="text-muted small">Dokumen akan diproses secara otomatis</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#696cff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Upload!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    uploadDocument(file);
                }
            });
        }

        // Upload Document
        function uploadDocument(file) {
            isUploading = true;
            uploadedFiles.add(file.name);

            // Show progress
            $('#uploadPlaceholder').hide();
            $('#uploadProgress').show();
            $('#uploadFileName').text(file.name);
            $('#uploadStatusText').text(trans.uploading);

            updateProgress(10);

            const formData = new FormData();
            formData.append('document', file);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: `/app/auto-reply/finetunnel/{{$fineTunnel->id}}/documents/upload`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 50);
                            updateProgress(percent);
                        }
                    });
                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        updateProgress(50);
                        $('#uploadStatusText').text(trans.processingDocument);

                        // Simulate processing progress
                        simulateProcessing(response.data);
                    } else {
                        handleUploadError(response.message);
                    }
                },
                error: function(xhr) {
                    let errorMsg = trans.uploadFailed;
                    let errorTitle = 'Upload Gagal';

                    if (xhr.status === 400) {
                        // Package/storage error
                        errorTitle = 'Limit Tercapai';
                        errorMsg = xhr.responseJSON?.message || 'Storage limit tercapai';
                    } else if (xhr.status === 422) {
                        // Validation error
                        const errors = xhr.responseJSON?.errors;
                        if (errors && errors.document) {
                            errorMsg = errors.document[0];
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.status === 413) {
                        errorMsg = 'File terlalu besar. Maksimal ' + packageLimits.maxPerUpload + 'MB per file.';
                    } else if (xhr.status === 500) {
                        errorMsg = 'Terjadi kesalahan server. Silakan coba lagi atau hubungi administrator.';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: errorTitle,
                        text: errorMsg,
                        confirmButtonColor: '#d33'
                    });

                    resetUploadArea();
                    uploadedFiles.delete(file.name);
                    isUploading = false;
                }
            });
        }

        // Simulate Processing Progress
        function simulateProcessing(data) {
            let progress = 50;
            const interval = setInterval(() => {
                progress += 10;
                if (progress >= 100) {
                    clearInterval(interval);
                    updateProgress(100);

                    setTimeout(() => {
                        showUploadSuccess(data);
                        resetUploadArea();
                        loadRagDocuments();
                        uploadedFiles.delete(data.filename);
                        isUploading = false;
                    }, 500);
                } else {
                    updateProgress(progress);
                }
            }, 300);
        }

        // Update Progress Bar
        function updateProgress(percent) {
            $('#progressBar').css('width', percent + '%');
            $('#progressText').text(percent + '%');
        }

        // Show Upload Success
        // Show Upload Success
        function showUploadSuccess(data) {
            let storageInfo = '';
            if (data.storage) {
                const remainingMB = data.storage.remaining.toFixed(2);
                storageInfo = `<p class="mt-2"><strong>Storage tersisa:</strong> ${remainingMB} MB dari ${data.storage.total} MB</p>`;
            }

            Swal.fire({
                icon: 'success',
                title: 'Upload Berhasil!',
                html: `
        <div class="text-start">
            <p><strong>File:</strong> ${data.filename}</p>
            <p><strong>Ukuran:</strong> ${data.file_size_mb} MB</p>
            <p><strong>Total Chunks:</strong> ${data.total_chunks}</p>
            <p><strong>Berhasil:</strong> <span class="text-success">${data.successful_chunks}</span></p>
            <p><strong>Gagal:</strong> <span class="text-danger">${data.failed_chunks}</span></p>
            ${data.total_images > 0 ? `<p><strong>Gambar:</strong> ${data.total_images}</p>` : ''}
            ${storageInfo}
            <p class="text-muted small mt-2">Dokumen siap digunakan untuk AI</p>
        </div>
        `,
                confirmButtonColor: '#696cff'
            });
        }

        // Handle Upload Error
        function handleUploadError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Upload Gagal',
                text: message,
                confirmButtonColor: '#d33'
            });
            resetUploadArea();
            isUploading = false;
        }

        // Reset Upload Area
        function resetUploadArea() {
            $('#uploadProgress').hide();
            $('#uploadPlaceholder').show();
            $('#ragDocumentInput').val('');
            updateProgress(0);
        }

        // Load RAG Documents
        // Load RAG Documents
        function loadRagDocuments() {
            $.ajax({
                url: `/app/auto-reply/finetunnel/{{$fineTunnel->id}}/documents`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        // Update package limits
                        if (response.limits) {
                            packageLimits.maxPerUpload = response.limits.max_per_upload || 0;
                            packageLimits.maxTotalRag = response.limits.max_total_rag || 0;
                            packageLimits.maxPerUploadBytes = packageLimits.maxPerUpload * 1024 * 1024;
                            packageLimits.maxTotalRagBytes = packageLimits.maxTotalRag * 1024 * 1024;

                            // Update UI
                            $('#maxPerUploadInfo').text(response.limits.max_per_upload_formatted);
                            $('#maxTotalRagInfo').text(response.limits.max_total_rag_formatted);
                            $('#maxFileSizeText').text(response.limits.max_per_upload_formatted + ' per file');
                            $('#maxTotalSizeText').text(response.limits.max_total_rag_formatted + ' total');
                        }

                        renderDocuments(response.documents);

                        if (response.storage) {
                            updateStorageInfo(
                                response.storage.used,
                                response.storage.used_formatted,
                                response.storage.total_formatted,
                                response.storage.percentage
                            );
                        }
                    }
                },
                error: function() {
                    console.error('Failed to load documents');
                }
            });
        }

        // Render Documents
        function renderDocuments(documents) {
            const container = $('#ragDocumentsList');

            if (documents.length === 0) {
                container.html(`
                    <div class="text-center text-muted py-5">
                        <i class="bx bx-file" style="font-size: 48px;"></i>
                        <p class="mt-2">${trans.noDocumentsYet}</p>
                    </div>
                `);
                return;
            }

            let html = '';
            documents.forEach(doc => {
                const statusBadge = getStatusBadge(doc.status);
                const iconClass = getFileIcon(doc.file_type);

                // Show image count if available
                const imageCountBadge = doc.image_count > 0 ?
                    `<span class="badge bg-label-warning">${doc.image_count} gambar</span>` :
                    '';

                html += `
                    <div class="card mb-3 document-card" data-doc-id="${doc.id}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-start flex-grow-1">
                                    <div class="me-3">
                                        <i class="${iconClass}" style="font-size: 32px; color: #696cff;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${doc.filename}</h6>
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            ${statusBadge}
                                            <span class="badge bg-label-info">${doc.file_size_formatted}</span>
                                            <span class="badge bg-label-success">${doc.total_chunks} chunks</span>
                                            ${imageCountBadge}
                                        </div>
                                        <small class="text-muted">
                                            <i class="bx bx-time me-1"></i>${doc.created_at}
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            onclick="viewDocument('${doc.file_path}')"
                                            title="Lihat">
                                        <i class="bx bx-show"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="deleteDocument('${doc.id}', '${doc.filename}')"
                                            title="Hapus">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            container.html(html);
        }

        // Update Storage Info
        function updateStorageInfo(usedMB, usedFormatted, totalFormatted, percentage) {
            $('#storageInfo').text(`${usedFormatted} / ${totalFormatted}`);
            $('#storageBar').css('width', percentage + '%');

            // Update color based on percentage
            $('#storageBar').removeClass('bg-success bg-warning bg-danger');
            if (percentage >= 90) {
                $('#storageBar').addClass('bg-danger');
            } else if (percentage >= 70) {
                $('#storageBar').addClass('bg-warning');
            } else {
                $('#storageBar').addClass('bg-success');
            }
        }

        // Get Status Badge
        function getStatusBadge(status) {
            const badges = {
                'completed': '<span class="badge bg-success">Completed</span>',
                'processing': '<span class="badge bg-warning">Processing</span>',
                'failed': '<span class="badge bg-danger">Failed</span>'
            };
            return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
        }

        // Get File Icon
        function getFileIcon(fileType) {
            const icons = {
                'pdf': 'bx bxs-file-pdf',
                'doc': 'bx bxs-file-doc',
                'docx': 'bx bxs-file-doc',
                'xls': 'bx bxs-spreadsheet',
                'xlsx': 'bx bxs-spreadsheet',
                'csv': 'bx bxs-spreadsheet'
            };
            return icons[fileType] || 'bx bxs-file';
        }

        // Format Bytes
        function formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Prevent page unload during upload
        window.addEventListener('beforeunload', function(e) {
            if (isUploading || uploadedFiles.size > 0) {
                e.preventDefault();
                e.returnValue = '';
                return trans.uploadInProgressWarning;
            }
        });

        // Make functions global
        window.viewDocument = function(url) {
            window.open(url, '_blank');
        };

        window.deleteDocument = function(id, filename) {
            Swal.fire({
                title: 'Hapus Dokumen?',
                html: `<p>Yakin ingin menghapus:</p><p><strong>${filename}</strong></p>
                   <p class="text-danger small">Data training dari dokumen ini akan hilang</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `/app/auto-reply/finetunnel/documents/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadRagDocuments();
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message || 'Gagal menghapus dokumen',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        };

        function updateStickyState() {
            const scrollTop = $(window).scrollTop();
            const chatContainer = $('#chatTestContainer');
            const containerOffset = chatContainer.offset();

            if (scrollTop > 150) {
                chatContainer.addClass('is-sticky');
            } else {
                chatContainer.removeClass('is-sticky');
            }

            lastScrollTop = scrollTop;
            ticking = false;
        }

        $(window).on('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(updateStickyState);
                ticking = true;
            }
        });

        // Character counter
        const descriptionTextarea = document.getElementById('ai-description');

        // Chat functionality
        const chatMessages = document.getElementById('chatMessages');
        const chatInput = document.getElementById('chatInput');
        const sendButton = document.getElementById('sendMessage');
        const clearButton = document.getElementById('clearChat');

        // Initialize with welcome message
        function initializeChat() {
            const welcomeMsg = document.getElementById('welcomeMessage').value;

            if (welcomeMsg) {
                chatMessages.innerHTML = `
                <div class="chat-welcome">
                    <i class="bx bx-message-square-detail me-1"></i>
                    <strong>${trans.jsPreviewWelcome}</strong><br>
                    ${escapeHtml(welcomeMsg)}
                </div>
                <div class="text-center text-muted py-3" id="sampleData">
                    <i class="bx bx-info-circle"></i>
                    <p class="mt-2 mb-1"><small>${trans.jsTypeMessageStart}</small></p>
                </div>
            `;
            } else {
                chatMessages.innerHTML = `
                <div class="text-center text-muted py-5" id="sampleData">
                    <i class="bx bx-message-dots" style="font-size: 48px;"></i>
                    <p class="mt-2 mb-1"><strong>${trans.jsStartConversation}</strong></p>
                    <small>${trans.jsFillCharacterFirst}</small>
                </div>
            `;
            }
        }

        document.getElementById('welcomeMessage').addEventListener('input', initializeChat);

        // Add user message
        function addUserMessage(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chat-message user';
            messageDiv.innerHTML = `
            <div class="message-bubble">
                ${escapeHtml(message)}
            </div>
        `;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Add bot text message
        function addBotTextMessage(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chat-message bot';
            messageDiv.innerHTML = `
            <div class="message-bubble">
                ${formatMessage(message)}
            </div>
        `;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Add bot image message
        function addBotImageMessage(imageUrl, caption = '') {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chat-message bot';
            messageDiv.innerHTML = `
            <div class="message-bubble image-message">
                <img src="${escapeHtml(imageUrl)}"
                     alt="Product Image"
                     class="chat-image"
                     onclick="window.open('${escapeHtml(imageUrl)}', '_blank')"
                     onerror="this.parentElement.innerHTML='<p class=\\'text-danger\\'><i class=\\'bx bx-error\\'></i> Gagal memuat gambar</p>'">
                ${caption ? `<p class="mt-2 mb-0 small text-muted">${escapeHtml(caption)}</p>` : ''}
            </div>
        `;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Show typing indicator
        function showTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'chat-message bot';
            typingDiv.id = 'typingIndicator';
            typingDiv.innerHTML = `
            <div class="typing-indicator" style="display: block;">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
            chatMessages.appendChild(typingDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Remove typing indicator
        function removeTypingIndicator() {
            const typingIndicator = document.getElementById('typingIndicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

        // Show metadata
        function showMetadata(metadata) {
            const existingMetadata = document.getElementById('chatMetadata');
            if (existingMetadata) {
                existingMetadata.remove();
            }

            const metadataDiv = document.createElement('div');
            metadataDiv.id = 'chatMetadata';
            metadataDiv.className = 'chat-metadata';

            const intentBadge = getIntentBadge(metadata.intent);

            metadataDiv.innerHTML = `
            <small class="text-muted">
                <i class="bx bx-info-circle"></i>
                ${intentBadge} |
                Model: ${metadata.model} |
                Tokens: ${metadata.tokens_used} |
                Credit: ${metadata.credit_used}
            </small>
        `;
            chatMessages.appendChild(metadataDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Get intent badge
        function getIntentBadge(intent) {
            const badges = {
                'search_product': `<span class="badge bg-success">${trans.searchProductBadge}</span>`,
                'other': `<span class="badge bg-info">${trans.otherBadge}</span>`,
                'add_to_cart': `<span class="badge bg-warning">${trans.cartBadge}</span>`,
                'checkout': `<span class="badge bg-primary">${trans.checkoutBadge}</span>`,
                'check_shipping': `<span class="badge bg-secondary">${trans.checkShippingBadge}</span>`
            };
            return badges[intent] || '<span class="badge bg-secondary">' + intent + '</span>';
        }

        // Format message (preserve line breaks, links)
        function formatMessage(message) {
            // Escape HTML first
            let formatted = escapeHtml(message);

            // Convert line breaks
            formatted = formatted.replace(/\n/g, '<br>');

            // Make URLs clickable
            formatted = formatted.replace(
                /(https?:\/\/[^\s<]+)/g,
                '<a href="$1" target="_blank" rel="noopener">$1</a>'
            );

            return formatted;
        }

        // Send message to API
        async function sendMessageToAPI(message) {
            if (isProcessing) {
                return;
            }

            const welcomeMessage = document.getElementById('welcomeMessage').value;
            const description = document.getElementById('ai-description').value;
            const modelAi = document.getElementById('modelAi').value;
            const fineTunnelId = '{{$fineTunnel->id}}';

            if (!description) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    text: trans.pleaseFillAiBehaviour,
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Hide sample data
            const sampleData = document.getElementById('sampleData');
            if (sampleData) {
                sampleData.classList.add('d-none');
            }

            // Add user message
            addUserMessage(message);

            // Add to history
            chatHistory.push({
                role: 'user',
                message: message
            });

            // Show typing
            showTypingIndicator();

            // Disable input
            isProcessing = true;
            chatInput.disabled = true;
            sendButton.disabled = true;

            try {
                const response = await fetch('/app/auto-reply/finetunnel/test-ai', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        welcome_message: welcomeMessage,
                        description: description,
                        message: message,
                        history: chatHistory,
                        model_ai: modelAi,
                        fine_tunnel_id: fineTunnelId
                    })
                });

                const data = await response.json();

                removeTypingIndicator();

                if (data.success && data.responses) {

                    // Add all responses
                    let lastTextMessage = null;

                    for (const resp of data.responses) {
                        if (resp.type === 'text') {
                            addBotTextMessage(resp.content);
                            lastTextMessage = resp.content;
                        } else if (resp.type === 'image') {
                            addBotImageMessage(resp.url, resp.caption);
                        }

                        // Small delay between messages
                        if (data.responses.length > 1) {
                            await new Promise(resolve => setTimeout(resolve, 300));
                        }
                    }

                    // Add last text to history
                    if (lastTextMessage) {
                        chatHistory.push({
                            role: 'assistant',
                            message: lastTextMessage
                        });
                    }

                    // Show metadata
                    if (data.metadata) {
                        showMetadata(data.metadata);
                    }

                } else {
                    addBotTextMessage(trans.connectionError);

                    Swal.fire({
                        icon: 'error',
                        title: trans.errorTitle,
                        text: data.message || trans.errorProcessingRequest,
                        confirmButtonColor: '#d33'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                removeTypingIndicator();
                addBotTextMessage(trans.connectionError);

                Swal.fire({
                    icon: 'error',
                    title: trans.connectionErrorTitle,
                    text: trans.serverConnectionFailed,
                    confirmButtonColor: '#d33'
                });
            } finally {
                isProcessing = false;
                chatInput.disabled = false;
                sendButton.disabled = false;
                chatInput.focus();
            }
        }

        // Send on button click
        sendButton.addEventListener('click', function() {
            const message = chatInput.value.trim();
            if (message && !isProcessing) {
                sendMessageToAPI(message);
                chatInput.value = '';
            }
        });

        // Send on Enter
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const message = chatInput.value.trim();
                if (message && !isProcessing) {
                    sendMessageToAPI(message);
                    chatInput.value = '';
                }
            }
        });

        // Clear chat
        clearButton.addEventListener('click', function() {
            if (isProcessing) {
                return;
            }

            Swal.fire({
                title: trans.clearChatConfirmTitle,
                text: trans.clearChatConfirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: trans.yesDelete,
                cancelButtonText: trans.cancel
            }).then((result) => {
                if (result.value) {
                    chatHistory = [];
                    initializeChat();

                    Swal.fire({
                        icon: 'success',
                        title: trans.success,
                        text: trans.chatCleared,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Initialize
        initializeChat();

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Get translation strings
        const trans = {
            selectHumanAgent: $('#lang-select-human-agent').text(),
            selectProvince: $('#lang-select-province').text(),
            selectCity: $('#lang-select-city').text(),
            selectDistrict: $('#lang-select-district').text(),
            selectVillage: $('#lang-select-village').text(),
            newFollowup: $('#lang-new-followup').text(),
            delete: $('#lang-delete').text(),
            promptTextFollowUp: $('#lang-prompt-text-follow-up').text(),
            followUpMessagePlaceholder: $('#lang-follow-up-message-placeholder').text(),
            messageSentByAi: $('#lang-message-sent-by-ai').text(),
            delayLimit: $('#lang-delay-limit').text(),
            minutesUnit: $('#lang-minutes-unit').text(),
            delayBeforeSend: $('#lang-delay-before-send').text(),
            exactLabel: $('#lang-exact-label').text(),
            handoffLabel: $('#lang-handoff-label').text(),
            newGsheet: $('#lang-new-gsheet').text(),
            googleSheetUrl: $('#lang-google-sheet-url').text(),
            gsheetUrlPlaceholder: $('#lang-gsheet-url-placeholder').text(),
            validate: $('#lang-validate').text(),
            status: $('#lang-status').text(),
            activeStatus: $('#lang-active-status').text(),
            inactiveStatus: $('#lang-inactive-status').text(),
            previewData: $('#lang-preview-data').text(),
            preview: $('#lang-preview').text(),
            gsheetToggle: $('#lang-gsheet-toggle').text(),
            showGuide: $('#lang-show-guide').text(),
            enterUrlFirst: $('#lang-enter-url-first').text(),
            invalidUrlFormat: $('#lang-invalid-url-format').text(),
            loading: $('#lang-loading').text(),
            noDataToPreview: $('#lang-no-data-to-preview').text()
        };

        $('.dropify').dropify();

        $('.users').select2({
            placeholder: trans.selectHumanAgent,
            allowClear: true
        });

        $('.provinces').select2({
            placeholder: trans.selectProvince,
            allowClear: true
        });

        $('.cities').select2({
            placeholder: trans.selectCity,
            allowClear: true
        });

        $('.districts').select2({
            placeholder: trans.selectDistrict,
            allowClear: true
        });

        $('.subdistricts').select2({
            placeholder: trans.selectVillage,
            allowClear: true
        });

        // Character Counter
        const textarea = document.getElementById('ai-description');
        const charCount = document.getElementById('charCount');
        const counterDiv = charCount.parentElement;

        function updateCharCount() {
            const length = textarea.value.length;
            charCount.textContent = length.toLocaleString();
            counterDiv.classList.remove('warning', 'danger');
            if (length > 13500) counterDiv.classList.add('danger');
            else if (length > 12000) counterDiv.classList.add('warning');
        }
        textarea.addEventListener('input', updateCharCount);
        updateCharCount();

        // Regional Selection
        $(".provinces").on("change", function() {
            $(".cities, .districts, .subdistricts").val("").trigger('change');
            if ($(this).val()) {
                $('.cities').select2({
                    placeholder: trans.selectCity,
                    allowClear: true,
                    ajax: {
                        url: `/app/master/components/cities?province=${$(this).val()}`,
                        dataType: 'json',
                        delay: 250,
                        processResults: data => ({
                            results: $.map(data, item => ({
                                text: item.type + ' ' + item.name,
                                id: item.id
                            }))
                        }),
                        cache: false
                    }
                });
            }
        });

        $(".cities").on("change", function() {
            $(".districts, .subdistricts").val("").trigger('change');
            if ($(this).val()) {
                $('.districts').select2({
                    placeholder: trans.selectDistrict,
                    allowClear: true,
                    ajax: {
                        url: `/app/master/components/districts?city=${$(this).val()}`,
                        dataType: 'json',
                        delay: 250,
                        processResults: data => ({
                            results: $.map(data, item => ({
                                text: item.name,
                                id: item.id
                            }))
                        }),
                        cache: false
                    }
                });
            }
        });

        $(".districts").on("change", function() {
            $(".subdistricts").val("").trigger('change');
            if ($(this).val()) {
                $('.subdistricts').select2({
                    placeholder: trans.selectVillage,
                    allowClear: true,
                    ajax: {
                        url: `/app/master/components/subdistricts?district=${$(this).val()}`,
                        dataType: 'json',
                        delay: 250,
                        processResults: data => ({
                            results: $.map(data, item => ({
                                text: item.name + ' ' + item.postal_code,
                                id: item.id
                            }))
                        }),
                        cache: false
                    }
                });
            }
        });

        // Dynamic Add Functions
        let followUpCount = $("#followUpCount").val();
        let sheetCount = $("#sheetCount").val();

        $("#addFollowUp").click(function() {
            followUpCount++;
            let html = `
                <div class="card mb-3" id="followUp-new-${followUpCount}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bx bx-time-five me-1"></i>${trans.newFollowup}</h6>
                        <button type="button" class="btn btn-outline-danger btn-sm removeFollowUp" data-id="new-${followUpCount}">
                            <i class="bx bx-trash"></i> ${trans.delete}
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-message-square-detail me-1"></i>${trans.promptTextFollowUp}
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" name="prompt[]" required rows="5" placeholder="${trans.followUpMessagePlaceholder}"></textarea>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>${trans.messageSentByAi}
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-timer me-1"></i>${trans.delayLimit}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-time-five"></i>
                                </span>
                                <input type="number" class="form-control" name="delay_followups[]" required min="1" value="720">
                                <span class="input-group-text">${trans.minutesUnit}</span>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>${trans.delayBeforeSend}
                            </small>
                        </div>
                        <div class="form-check mb-2">
                            <input type="hidden" name="exact[${followUpCount}]" value="no">
                            <input class="form-check-input" type="checkbox"
                                name="exact[${followUpCount}]"
                                id="exact-new-${followUpCount}">
                            <label class="form-check-label" for="exact-new-${followUpCount}">
                                <i class="bx bx-lock me-1"></i>${trans.exactLabel}
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="hidden" name="handoff[${followUpCount}]" value="no">
                            <input class="form-check-input" type="checkbox"
                                name="handoff[${followUpCount}]"
                                id="handoff-new-${followUpCount}">
                            <label class="form-check-label" for="handoff-new-${followUpCount}">
                                <i class="bx bx-user-check me-1"></i>${trans.handoffLabel}
                            </label>
                        </div>
                    </div>
                </div>`;
            $("#listFollowUps").append(html);
        });

        $("#addGSheet").click(function() {
            sheetCount++;
            let html = `
                <div class="card mb-3 cardsheet" id="datasheet-new-${sheetCount}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bx bx-spreadsheet me-1"></i>${trans.newGsheet}</h6>
                        <button type="button" class="btn btn-outline-danger btn-sm removeSheet" data-id="new-${sheetCount}">
                            <i class="bx bx-trash"></i> ${trans.delete}
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-link-alt me-1"></i>${trans.googleSheetUrl}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-globe"></i>
                                </span>
                                <input type="url" class="form-control" name="url[]" required placeholder="${trans.gsheetUrlPlaceholder}">
                                <button type="button" class="btn btn-outline-primary" onclick="validateUrl(this)">
                                    <i class="bx bx-check-circle"></i> ${trans.validate}
                                </button>
                            </div>
                            <div id="urlValidation" class="mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-toggle-right me-1"></i>${trans.status}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-info-square"></i>
                                </span>
                                <select class="form-control" name="status_sheet[]">
                                    <option value="yes">${trans.activeStatus}</option>
                                    <option value="no">${trans.inactiveStatus}</option>
                                </select>
                            </div>
                        </div>
                        <div id="previewContainer" class="mt-3" style="display: none;">
                            <h6><i class="bx bx-show me-1"></i>${trans.previewData}</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="previewTable">
                                    <thead class="table-light"></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="button" class="btn btn-info btn-sm me-2" onclick="previewData(this)">
                            <i class="bx bx-show"></i> ${trans.preview}
                        </button>
                    </div>
                </div>`;
            $("#listGsheet").append(html);
        });

        // Remove handlers
        $(document).on("click", ".removeFollowUp", function() {
            $(this).closest('.card').remove();
        });

        $(document).on("click", ".removeSheet", function() {
            $(this).closest('.card').remove();
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
    });

    // Toggle Guide
    function toggleGuide() {
        const trans = {
            gsheetToggle: $('#lang-gsheet-toggle').text(),
            showGuide: $('#lang-show-guide').text()
        };

        const content = document.getElementById('guideContent');
        const icon = document.getElementById('toggleIcon');
        const text = document.getElementById('toggleText');

        if (content.style.display === 'none') {
            content.style.display = 'block';
            icon.className = 'bx bx-chevron-up';
            text.textContent = trans.gsheetToggle;
        } else {
            content.style.display = 'none';
            icon.className = 'bx bx-chevron-down';
            text.textContent = trans.showGuide;
        }
    }

    // Validate & Preview functions
    function validateUrl(button) {
        const trans = {
            enterUrlFirst: $('#lang-enter-url-first').text(),
            invalidUrlFormat: $('#lang-invalid-url-format').text(),
            validate: $('#lang-validate').text()
        };

        const cardBody = button.closest('.cardsheet');
        const urlInput = cardBody.querySelector('input[name="url[]"]');
        const validationDiv = cardBody.querySelector('#urlValidation');
        const url = urlInput.value.trim();

        if (!url) {
            validationDiv.innerHTML = `<div class="alert alert-warning small">${trans.enterUrlFirst}</div>`;
            return;
        }

        const googleSheetsPattern = /^https:\/\/docs\.google\.com\/spreadsheets\/d\/[a-zA-Z0-9-_]+/;
        if (!googleSheetsPattern.test(url)) {
            validationDiv.innerHTML = `<div class="alert alert-warning small">${trans.invalidUrlFormat}</div>`;
            return;
        }

        button.disabled = true;
        button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Validating...';

        fetch(`/app/auto-reply/finetunnel/gsheet/validate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    url: url
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    validationDiv.innerHTML = `
                    <div class="alert alert-success small">
                        <strong>✓ Valid!</strong> ${data.message}<br>
                        <small><strong>Kolom:</strong> ${data.columns?.join(',') || 'N/A'}</small><br>
                        <small><strong>Total baris:</strong> ${data.origin_total_rows || 0}</small>
                    </div>`;
                    urlInput.classList.add('is-valid');
                    urlInput.classList.remove('is-invalid');
                } else {
                    validationDiv.innerHTML = `<div class="alert alert-danger small"><strong>✗ Error!</strong> ${data.message}</div>`;
                    urlInput.classList.add('is-invalid');
                    urlInput.classList.remove('is-valid');
                }
            })
            .catch(error => {
                validationDiv.innerHTML = `<div class="alert alert-danger small"><strong>✗ Error!</strong> ${error.message}</div>`;
                urlInput.classList.add('is-invalid');
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = `<i class="bx bx-check-circle"></i> ${trans.validate}`;
            });
    }

    function previewData(button) {
        const trans = {
            enterUrlFirst: $('#lang-enter-url-first').text(),
            loading: $('#lang-loading').text(),
            noDataToPreview: $('#lang-no-data-to-preview').text(),
            preview: $('#lang-preview').text()
        };

        const cardBody = button.closest('.cardsheet');
        const urlInput = cardBody.querySelector('input[name="url[]"]');
        const previewContainer = cardBody.querySelector('#previewContainer');
        const url = urlInput.value.trim();

        if (!url) {
            alert(trans.enterUrlFirst);
            return;
        }

        button.disabled = true;
        button.innerHTML = `<i class="bx bx-loader-alt bx-spin"></i> ${trans.loading}`;

        fetch(`/app/auto-reply/finetunnel/gsheet/preview`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    url: url
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const table = previewContainer.querySelector('#previewTable');
                    const headers = Object.keys(data.data[0]);
                    table.querySelector('thead').innerHTML = '<tr>' + headers.map(h => `<th>${h}</th>`).join('') + '</tr>';
                    table.querySelector('tbody').innerHTML = data.data.slice(0, 10).map(row =>
                        '<tr>' + headers.map(h => `<td>${row[h] || ''}</td>`).join('') + '</tr>'
                    ).join('');
                    previewContainer.style.display = 'block';
                } else {
                    alert(data.message || trans.noDataToPreview);
                    previewContainer.style.display = 'none';
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
                previewContainer.style.display = 'none';
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = `<i class="bx bx-show"></i> ${trans.preview}`;
            });
    }
</script>
<script>
/* ── FineTunnel Redesign JS ── */

// Chip → Tab trigger
document.querySelectorAll('.ft-chip-btn[data-ft-tab]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var href = this.getAttribute('data-ft-tab');
        // Deactivate semua chips
        document.querySelectorAll('.ft-chip-btn').forEach(function(b) {
            b.classList.remove('ft-active');
        });
        this.classList.add('ft-active');

        // Trigger bootstrap tab
        var trigger = document.querySelector('[data-bs-toggle="tab"][href="' + href + '"]');
        if (!trigger) {
            trigger = document.querySelector('[data-bs-toggle="tab"][data-bs-target="' + href + '"]');
        }
        if (trigger) {
            try { bootstrap.Tab.getOrCreateInstance(trigger).show(); } catch(e) { console.warn(e); }
        }
    });
});

// Advanced settings chevron rotate
var ftAdvEl = document.getElementById('ft-adv');
var ftAdvToggle = document.querySelector('.ft-adv-toggle');
if (ftAdvEl && ftAdvToggle) {
    ftAdvEl.addEventListener('show.bs.collapse', function() {
        ftAdvToggle.setAttribute('aria-expanded','true');
    });
    ftAdvEl.addEventListener('hide.bs.collapse', function() {
        ftAdvToggle.setAttribute('aria-expanded','false');
    });
}

// Label chip visual toggle
document.querySelectorAll('.ft-label-chip input[type=checkbox]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        this.closest('.ft-label-chip').classList.toggle('selected', this.checked);
    });
    if (cb.checked) cb.closest('.ft-label-chip').classList.add('selected');
});


const ftTemplates = {
    toko: {
        description: "Kamu adalah CS toko online yang ramah dan responsif.\nTugas: bantu pelanggan cari produk, cek harga, pilih ukuran/warna, dan bantu proses order.\nSelalu jawab singkat dan sopan. Gunakan emoji sesekali \u{1F60A}\nJangan pernah menjanjikan harga di luar yang tertulis di atas.",
        welcome: "Halo! Selamat datang di toko kami \u{1F44B} Ada yang bisa saya bantu hari ini?"
    },
    kelas: {
        description: "Kamu adalah asisten kelas online / e-course yang antusias dan informatif.\nTugas: bantu calon peserta memahami kurikulum, jadwal, harga, dan cara daftar.\nJawab dengan semangat. Detail teknis \u2192 alihkan ke tim kami.\nJangan menjanjikan diskon yang tidak ada di informasi di atas.",
        welcome: "Hai! Saya asisten program kami \u{1F393} Ada pertanyaan seputar kelas atau cara daftar?"
    },
    booking: {
        description: "Kamu adalah asisten reservasi yang membantu tamu merencanakan kunjungan.\nTugas: bantu cek ketersediaan, jelaskan paket, bantu isi form reservasi.\nJawab dengan hangat dan profesional. Konfirmasi final \u2192 arahkan ke admin kami.\nJangan menjanjikan ketersediaan yang belum dikonfirmasi.",
        welcome: "Halo! Saya siap bantu proses reservasi Anda \u{1F4C5} Kapan rencana kunjungan Anda?"
    }
};
function ftApplyTpl(type) {
    var t = ftTemplates[type];
    if (!t) return;
    var desc = document.getElementById('ft-description');
    var wel = document.querySelector('[name="welcome_message"]');
    if (desc) { desc.value = t.description; desc.focus(); }
    if (wel) wel.value = t.welcome;
}

</script>
@endsection
