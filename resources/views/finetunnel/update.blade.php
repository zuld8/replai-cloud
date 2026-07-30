@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
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
/* Step 2 panes — HANYA yang aktif tampil, kalahkan CSS apapun */
#ft-know-panes > .tab-pane { display: none !important; }
#ft-know-panes > .tab-pane.ft-pane-on { display: block !important; }
.pb-chip{font-size:11.5px;padding:3px 10px;border:1px solid #d1d5db;border-radius:20px;cursor:pointer;color:#475569;user-select:none;display:inline-block;transition:all .18s}
.pb-chip:hover{border-color:#2E8DE1}
.pb-chip.on{background:#e0edff;border-color:#2E8DE1;color:#185FA5}
/* ---- Step 2 ringkas ---- */
#ft-know-panes .alert{padding:.55rem .8rem;font-size:.76rem;margin-bottom:.6rem}
#ft-know-panes .alert .alert-heading{font-size:.8rem;margin-bottom:.1rem}
#ft-know-panes .card{margin-bottom:.5rem}
#ft-know-panes .card-header{padding:.45rem .9rem}
#ft-know-panes .card-header h6{font-size:.83rem}
#documents #uploadPlaceholder{padding:.7rem 0 !important}
#documents #uploadPlaceholder .bx-cloud-upload{font-size:30px !important}
#documents #uploadPlaceholder .mt-3{margin-top:.3rem !important}
#documents #uploadPlaceholder .mb-3{margin-bottom:.4rem !important}
#documents .card-body{padding:.65rem}
#courier-data .col-lg-6,#courier-data .col-sm-12{margin-bottom:.4rem !important}
#courier-data .form-label{font-size:.8rem;margin-bottom:.12rem}
#follow-ups .card-body{padding:.65rem}
/* storage box tipis + sembunyiin baris Max/Total (sudah ada di info RAG atas) */
#documents .upload-area .bg-light.rounded{padding:.5rem .7rem !important;margin-top:.5rem !important}
#documents .upload-area .bg-light.rounded .border-top{display:none !important}
/* dropzone lebih pendek */
#documents #uploadPlaceholder{padding:.4rem 0 !important}
#documents #uploadPlaceholder .bx-cloud-upload{font-size:24px !important}
#documents #uploadPlaceholder p{margin-bottom:.25rem !important}
</style>
@endsection

@section('content')
<form id="ft-form" action="{{ route('finetunnel.edit', $fineTunnel->id) }}" method="POST" enctype="multipart/form-data">
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

            {{-- Template presets (9 chip) + Builder persona --}}
            <div class="mb-1" style="font-size:12px;color:#6c757d;">Mulai dari template <span class="text-muted">(klik &rarr; karakter keisi)</span></div>
            <div class="d-flex flex-wrap gap-2 mb-3" id="ft-templates">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-tpl="toko"><i class="bx bx-cart"></i> Toko Online</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-tpl="ecourse"><i class="bx bx-book"></i> Kelas / e-Course</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-tpl="reservasi"><i class="bx bx-calendar-check"></i> Reservasi</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-tpl="cs"><i class="bx bx-headphone"></i> CS / Bantuan</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-tpl="donasi"><i class="bx bx-donate-heart"></i> Donasi</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-tpl="properti"><i class="bx bx-home"></i> Properti</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-tpl="klinik"><i class="bx bx-plus-medical"></i> Klinik</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-tpl="fnb"><i class="bx bx-restaurant"></i> Resto / F&amp;B</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-tpl="jasa"><i class="bx bx-briefcase"></i> Jasa</button>
            </div>

            {{-- Builder persona ringkas --}}
            <div class="row g-2 mb-2">
              <div class="col-md-6"><label class="fs-12 text-muted">Nama / panggilan AI</label>
                <input type="text" id="pb-nama" class="form-control form-control-sm" placeholder="Kak Rina"></div>
              <div class="col-md-6"><label class="fs-12 text-muted">Perannya sebagai</label>
                <input type="text" id="pb-peran" class="form-control form-control-sm" placeholder="CS &amp; admin"></div>
            </div>
            <div class="d-flex flex-wrap gap-3 mb-2">
              <div><div class="fs-12 text-muted mb-1">Gaya bicara</div>
                <div id="pb-gaya" class="d-flex flex-wrap gap-1">
                  <span class="pb-chip" data-v="ramah">Ramah</span><span class="pb-chip" data-v="ringkas">Ringkas</span>
                  <span class="pb-chip" data-v="formal">Formal</span><span class="pb-chip" data-v="santai">Santai</span>
                  <span class="pb-chip" data-v="emoji">Pakai emoji</span>
                </div></div>
              <div><div class="fs-12 text-muted mb-1">Panggil customer</div>
                <div id="pb-panggil" class="d-flex gap-1">
                  <span class="pb-chip pb-radio" data-v="Kak">Kak</span><span class="pb-chip pb-radio" data-v="Bapak/Ibu">Bapak/Ibu</span><span class="pb-chip pb-radio" data-v="Kamu">Kamu</span>
                </div></div>
              <div class="align-self-end"><button type="button" id="pb-apply" class="btn btn-sm btn-outline-primary"><i class="bx bx-refresh"></i> Susun karakter</button></div>
            </div>

            <label class="form-label fw-semibold mb-1">Karakter AI</label>
            <div class="text-muted mb-2" style="font-size:.8rem">Cara AI membalas: gaya bicara, sifat, nada. (Fakta/harga/jadwal taruh di &ldquo;Pengetahuan&rdquo; Step 2.)</div>
            <textarea id="ft-description" name="description" class="form-control mb-3" rows="6"
                placeholder="Contoh: Kamu adalah CS yang ramah untuk toko Baju Anak Murah. Harga kaos mulai Rp35.000.">{{ $finetunnel->description }}</textarea>
            <div class="text-end text-muted mb-3" style="font-size:.75rem"><span id="charCount">0</span> / 15.000</div>

            <label class="form-label fw-semibold mb-1">Pesan sambutan pertama</label>
            <input type="text" id="welcomeMessage" name="welcome_message" class="form-control mb-3"
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

            <label class="form-label fw-semibold mb-1">Pengetahuan (ketik manual)</label>
            <div class="text-muted mb-2" style="font-size:.8rem">Tulis fakta, FAQ, harga, atau jadwal langsung. AI pakai ini untuk menjawab tanpa perlu upload file.</div>
            <textarea name="knowledge_manual" class="form-control mb-3" rows="6"
                placeholder="Contoh:&#10;- Harga e-course Rp299.000&#10;- Kelas mulai tiap Sabtu 09.00&#10;- Sertifikat dikirim H+3">{{ $finetunnel->knowledge_manual }}</textarea>

            <label class="form-label fw-semibold mb-1 mt-2">Sumber file &amp; data</label>
            <div class="text-muted mb-2" style="font-size:.8rem">Upload dokumen atau sambungkan sumber data eksternal.</div>
            <div class="d-flex flex-wrap gap-2 mb-3" id="ft-chips">
                <button type="button" class="ft-chip-btn ft-active" data-ft-tab="#documents">
                    <i class="bx bx-file-blank"></i> Dokumen
                    @if(isset($ragDocuments) && $ragDocuments->count() > 0)
                    <span class="ft-chip-count">{{ $ragDocuments->count() }}</span>
                    @endif
                </button>
                <button type="button" class="ft-chip-btn" data-ft-tab="#g-sheet">
                    <i class="bx bx-table"></i> Google Sheet @if(!$gsheet)<i class="bx bx-lock-alt text-warning ms-1" title="Fitur paket — belum aktif"></i>@endif
                </button>
                <button type="button" class="ft-chip-btn" data-ft-tab="#follow-ups">
                    <i class="bx bx-time-five"></i> Follow up
                    @if(isset($finetunnel->follow_ups) && $finetunnel->follow_ups->count() > 0)
                    <span class="ft-chip-count">{{ $finetunnel->follow_ups->count() }}</span>
                    @endif
                </button>
                <button type="button" class="ft-chip-btn" data-ft-tab="#courier-data">
                    <i class="bx bx-package"></i> Ongkir @if(!$courierStatus)<i class="bx bx-lock-alt text-warning ms-1" title="Fitur paket — belum aktif"></i>@endif
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
<div class="tab-content" id="ft-know-panes">
                                <!-- Tab Follow Up -->
                                <div class="tab-pane fade" id="follow-ups" role="tabpanel" style="display:none">
                                    <details class="mb-2" style="font-size:.76rem">
                                        <summary style="cursor:pointer;color:#0f9349;font-weight:600"><i class="bx bx-info-circle me-1"></i>Panduan Follow Up</summary>
                                        <div class="alert alert-success mt-2 mb-0" role="alert">
                                            <p class="mb-0 small">{{ __('finetunnel.follow_up_desc_1')}} {{ __('finetunnel.follow_up_desc_2')}}</p>
                                            <p class="mb-0 small"><strong>{{ __('finetunnel.follow_up_warning')}}</strong> {{ __('finetunnel.follow_up_warning_text')}}</p>
                                        </div>
                                    </details>
                                    <div class="d-flex justify-content-end mb-3">
                                        <button class="btn btn-outline-primary" type="button" id="addFollowUp">
                                            <i class="bx bx-plus-circle me-1"></i>{{ __('finetunnel.add_follow_up') }}
                                        </button>
                                        <input type="hidden" id="followUpCount" value="{{ count($fineTunnel->follow_ups) }}" />
                                    </div>
                                    <div id="listFollowUps">
                                        @foreach ($fineTunnel->follow_ups as $index => $follow)
                                        <div class="card mb-2" id="followUp-{{$follow->id}}">
                                          <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                              <span class="fw-semibold" style="font-size:.85rem"><i class="bx bx-time-five me-1 text-primary"></i>Follow up</span>
                                              <button type="button" class="btn btn-outline-danger btn-sm removeFollowUp" data-id="{{$follow->id}}" style="padding:.1rem .45rem"><i class="bx bx-trash"></i></button>
                                            </div>
                                            <textarea class="form-control mb-2" name="prompt[]" required rows="2" placeholder="Pesan follow up, mis. Halo Kak, masih minat? &#x1F60A;">{{ $follow->text }}</textarea>
                                            <div class="d-flex align-items-center flex-wrap gap-3" style="font-size:.8rem">
                                              <span class="d-inline-flex align-items-center gap-1">Kirim setelah
                                                <input type="number" class="form-control form-control-sm" name="delay_followups[]" required min="1" value="{{ $follow->delay }}" style="width:78px"> menit</span>
                                              <label class="d-inline-flex align-items-center gap-1 mb-0" style="cursor:pointer">
                                                <input type="hidden" name="exact[{{$index}}]" value="no">
                                                <input class="form-check-input mt-0" type="checkbox" @if($follow->exact=='yes') checked @endif name="exact[{{$index}}]" id="exact-{{$follow->id}}"> Kirim apa adanya</label>
                                              <label class="d-inline-flex align-items-center gap-1 mb-0" style="cursor:pointer">
                                                <input type="hidden" name="handoff[{{$index}}]" value="no">
                                                <input class="form-check-input mt-0" type="checkbox" @if($follow->handoff=='yes') checked @endif name="handoff[{{$index}}]" id="handoff-{{$follow->id}}"> Alihkan ke agen</label>
                                            </div>
                                          </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Tab Data Training -->
                                <div class="tab-pane fade show active ft-pane-on" id="documents" role="tabpanel">
                                    <div class="upload-area" id="uploadArea">
                                        <input type="file" id="ragDocumentInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv" style="display:none">
                                        <div id="uploadPlaceholder" class="d-flex align-items-center gap-2 p-2 rounded" style="background:var(--bs-light,#f5f5f9)">
                                            <i class="bx bx-cloud-upload" style="font-size:22px;color:#696cff"></i>
                                            <div class="flex-grow-1" style="line-height:1.2">
                                                <div class="fw-semibold" style="font-size:.85rem">Tarik file / klik untuk upload</div>
                                                <div class="text-muted" style="font-size:.72rem">PDF·Word·Excel·CSV · maks <span id="maxFileSizeText">-</span>/file, total <span id="maxTotalSizeText">-</span></div>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="selectFileBtn"><i class="bx bx-folder-open me-1"></i>Pilih file</button>
                                        </div>
                                        <div id="uploadProgress" style="display:none">
                                            <div class="d-flex align-items-center gap-2 p-2">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading…</span></div>
                                                <div class="flex-grow-1">
                                                    <div style="font-size:.8rem"><strong id="uploadStatusText">Memproses…</strong> <span class="text-muted" id="uploadFileName"></span></div>
                                                    <div class="progress mt-1" style="height:6px"><div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" role="progressbar" style="width:0%"><span id="progressText" class="visually-hidden">0%</span></div></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <span class="text-muted" style="font-size:.72rem"><i class="bx bx-hdd"></i> Storage</span>
                                        <div class="progress flex-grow-1" style="height:5px"><div class="progress-bar" id="storageBar" role="progressbar" style="width:0%"></div></div>
                                        <span class="fw-semibold" style="font-size:.72rem" id="storageInfo">0 / 0</span>
                                    </div>
                                    <div id="ragDocumentsList" class="mt-2"></div>
                                </div>

                                <!-- Tab Courier -->
                                @if($courierStatus)
                                <div class="tab-pane fade" id="courier-data" role="tabpanel" style="display:none">
                                    <div class="text-muted mb-2" style="font-size:.8rem"><i class="bx bx-map-pin me-1"></i>Alamat asal pengiriman — buat AI hitung ongkir otomatis.</div>
                                    <div class="row g-2">
                                        <div class="col-lg-6 col-sm-12 mb-2">
                                            <label class="form-label fw-semibold mb-1" style="font-size:.8rem"><i class="bx bx-map me-1"></i>Provinsi</label>
                                            <select class="form-control provinces" name="province">
                                                <option value="">Pilih provinsi</option>
                                                @foreach ($provinces as $province)
                                                <option value="{{ $province->id }}" @if(($fineTunnel->subdistrict->district->city->province_id ?? '') == $province->id) selected @endif>{{ $province->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-sm-12 mb-2">
                                            <label class="form-label fw-semibold mb-1" style="font-size:.8rem"><i class="bx bx-building me-1"></i>Kota</label>
                                            <select class="form-control cities" name="city">
                                                <option value="<?= $fineTunnel->subdistrict->district->city->id ?? ''; ?>">
                                                    <?= ($fineTunnel->subdistrict->district->city->type ?? '') . ' ' . ($fineTunnel->subdistrict->district->city->name ?? __("finetunnel.select_city_option")); ?>
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-sm-12 mb-2">
                                            <label class="form-label fw-semibold mb-1" style="font-size:.8rem"><i class="bx bx-map-pin me-1"></i>Kecamatan</label>
                                            <select class="form-control districts" name="district">
                                                <option value="<?= $fineTunnel->subdistrict->district_id ?? ''; ?>"><?= $fineTunnel->subdistrict->district->name ?? __("master.directory.choose_district"); ?></option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-sm-12 mb-2">
                                            <label class="form-label fw-semibold mb-1" style="font-size:.8rem"><i class="bx bx-current-location me-1"></i>Kelurahan / Desa</label>
                                            <select class="form-control subdistricts" name="sub_district_id">
                                                <option value="<?= $fineTunnel->sub_district_id ?? ''; ?>"><?= $fineTunnel->subdistrict->name ?? __("finetunnel.origin_subdistrict_placeholder"); ?></option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-sm-12 mb-2">
                                            <label class="form-label fw-semibold mb-1" style="font-size:.8rem"><i class="bx bx-package me-1"></i>Berat default (gram)</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><i class="bx bx-calculator"></i></span>
                                                <input type="text" class="form-control" placeholder="{{ __("finetunnel.input_weight_placeholder") }}" name="weight" value="{{old("weight",(int)$fineTunnel->weight)}}">
                                                <span class="input-group-text">{{ __("finetunnel.weight_unit") }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 mb-2" style="font-size:.8rem"><i class="bx bx-info-circle me-1 text-primary"></i>Centang kurir yang mau diaktifkan:</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($couriers as $courier)
                                        <label class="d-inline-flex align-items-center gap-2 mb-0" style="border:1px solid #e0e0e0;border-radius:8px;padding:.35rem .7rem;cursor:pointer;font-size:.82rem">
                                            <input class="form-check-input mt-0" type="checkbox" name="couriers[]" value="<?= $courier->code; ?>" @if(check_courier($courier->code,$fineTunnel->id)) checked @endif id="courier-{{$courier->code}}">
                                            <span class="fw-semibold">{{$courier->name}} - {{$courier->service}}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @else
                                <div class="tab-pane fade" id="courier-data" role="tabpanel" style="display:none">
                                    <div class="text-center py-4">
                                        <i class="bx bx-lock-alt text-warning" style="font-size:34px"></i>
                                        <p class="fw-semibold mb-1 mt-2">Fitur Cek Ongkir belum aktif</p>
                                        <p class="text-muted mb-0" style="font-size:.85rem">Fitur ini belum termasuk paket langgananmu.<br>Upgrade paket untuk mengaktifkan hitung ongkir otomatis.</p>
                                    </div>
                                </div>
                                @endif

                                <input type="hidden" id="sheetCount" value="{{ count($fineTunnel->gsheets) }}" />

                                <!-- Tab Google Sheet -->
                                @if($gsheet)
                                <div class="tab-pane fade" id="g-sheet" role="tabpanel" style="display:none">
                                    <div class="d-flex gap-2 mb-2">
                                        <input type="text" class="form-control form-control-sm" id="newSheetUrl" placeholder="Tempel link Google Sheet (Publish to web)…">
                                        <button class="btn btn-primary btn-sm" type="button" id="addGSheet"><i class="bx bx-plus"></i> Tambah</button>
                                    </div>
                                    <details class="mb-2" style="font-size:.76rem">
                                        <summary style="cursor:pointer;color:#2E8DE1;font-weight:600"><i class="bx bx-help-circle me-1"></i>Lihat cara publish sheet</summary>
                                        <div class="alert alert-light border mt-2 mb-0 small">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="fw-bold">{{ __("finetunnel.gsheet_step1") }}</div>
                                                    <ul class="mb-2"><li>{{ __("finetunnel.gsheet_step1_a") }}</li><li>{{ __("finetunnel.gsheet_step1_b") }}</li><li>{{ __("finetunnel.gsheet_step1_c") }}</li></ul>
                                                    <div class="fw-bold">{{ __("finetunnel.gsheet_step2") }}</div>
                                                    <ul class="mb-0"><li>{{ __("finetunnel.gsheet_step2_b") }}</li><li>{{ __("finetunnel.gsheet_step2_c") }}</li><li>{{ __("finetunnel.gsheet_step2_d") }}</li></ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="fw-bold">{{ __("finetunnel.gsheet_step3") }}</div>
                                                    <ul class="mb-2"><li>{{ __("finetunnel.gsheet_step3_a") }}</li><li>{{ __("finetunnel.gsheet_step3_c") }}</li></ul>
                                                    <div class="fw-bold">{{ __("finetunnel.gsheet_realtime") }}</div>
                                                    <ul class="mb-0"><li>{{ __("finetunnel.gsheet_realtime_a") }}</li><li>{{ __("finetunnel.gsheet_realtime_b") }}</li></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </details>
                                    <div id="listGsheet">
                                        @foreach($fineTunnel->gsheets as $sheet)
                                        <div class="cardsheet d-flex align-items-center gap-2 p-2 rounded mb-1" id="datasheet-{{$sheet->id}}" style="background:var(--bs-light,#f5f5f9)">
                                            <i class="bx bx-table" style="font-size:20px;color:#0f9349"></i>
                                            <input type="url" class="form-control form-control-sm border-0 bg-transparent flex-grow-1 p-0" name="url[]" value="{{$sheet->url}}" required placeholder="{{ __('finetunnel.gsheet_url_placeholder') }}" style="min-width:0">
                                            <select class="form-select form-select-sm" name="status_sheet[]" style="width:auto">
                                                <option value="yes" @if($sheet->status=='yes') selected @endif>Aktif</option>
                                                <option value="no" @if($sheet->status=='no') selected @endif>Nonaktif</option>
                                            </select>
                                            <button type="button" class="btn btn-sm btn-outline-danger removeSheet" data-id="{{$sheet->id}}"><i class="bx bx-trash"></i></button>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @else
                                <div class="tab-pane fade" id="g-sheet" role="tabpanel" style="display:none">
                                    <div class="text-center py-4">
                                        <i class="bx bx-lock-alt text-warning" style="font-size:34px"></i>
                                        <p class="fw-semibold mb-1 mt-2">Fitur Google Sheet belum aktif</p>
                                        <p class="text-muted mb-0" style="font-size:.85rem">Fitur ini belum termasuk paket langgananmu.<br>Upgrade paket untuk menyambungkan Google Sheet.</p>
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
@php $selectedLabels = explode(',', $finetunnel->label ?? ''); @endphp
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
                        <select name="model_ai" id="modelAi" class="form-select">
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
        const descriptionTextarea = document.getElementById('ft-description');

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
            const description = document.getElementById('ft-description').value;
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
            allowClear: true,
            width: '100%'
        });

        $('.provinces').select2({
            placeholder: trans.selectProvince,
            allowClear: true,
            width: '100%'
        });

        $('.cities').select2({
            placeholder: trans.selectCity,
            allowClear: true,
            width: '100%'
        });

        $('.districts').select2({
            placeholder: trans.selectDistrict,
            allowClear: true,
            width: '100%'
        });

        $('.subdistricts').select2({
            placeholder: trans.selectVillage,
            allowClear: true,
            width: '100%'
        });

        // Character Counter
        const textarea = document.getElementById('ft-description');
        const charCount = document.getElementById('charCount');
        if (textarea && charCount) {
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
        }

        // Regional Selection
        $(".provinces").on("change", function() {
            $(".cities, .districts, .subdistricts").val("").trigger('change');
            if ($(this).val()) {
                $('.cities').select2({
                    placeholder: trans.selectCity,
                    allowClear: true,
                    width: '100%'
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
                    width: '100%'
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
                    width: '100%'
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
<div class="card mb-2" id="followUp-new-${followUpCount}">
  <div class="card-body p-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <span class="fw-semibold" style="font-size:.85rem"><i class="bx bx-time-five me-1 text-primary"></i>Follow up</span>
      <button type="button" class="btn btn-outline-danger btn-sm removeFollowUp" data-id="new-${followUpCount}" style="padding:.1rem .45rem"><i class="bx bx-trash"></i></button>
    </div>
    <textarea class="form-control mb-2" name="prompt[]" required rows="2" placeholder="Pesan follow up, mis. Halo Kak, masih minat? 😊"></textarea>
    <div class="d-flex align-items-center flex-wrap gap-3" style="font-size:.8rem">
      <span class="d-inline-flex align-items-center gap-1">Kirim setelah
        <input type="number" class="form-control form-control-sm" name="delay_followups[]" required min="1" value="720" style="width:78px"> menit</span>
      <label class="d-inline-flex align-items-center gap-1 mb-0" style="cursor:pointer">
        <input type="hidden" name="exact[${followUpCount}]" value="no">
        <input class="form-check-input mt-0" type="checkbox" name="exact[${followUpCount}]" id="exact-new-${followUpCount}"> Kirim apa adanya</label>
      <label class="d-inline-flex align-items-center gap-1 mb-0" style="cursor:pointer">
        <input type="hidden" name="handoff[${followUpCount}]" value="no">
        <input class="form-check-input mt-0" type="checkbox" name="handoff[${followUpCount}]" id="handoff-new-${followUpCount}"> Alihkan ke agen</label>
    </div>
  </div>
</div>`;
            $("#listFollowUps").append(html);
        });

        $("#addGSheet").click(function () {
            var url = ($("#newSheetUrl").val() || '').trim();
            sheetCount++;
            var html = `
                <div class="cardsheet d-flex align-items-center gap-2 p-2 rounded mb-1" id="datasheet-new-${sheetCount}" style="background:var(--bs-light,#f5f5f9)">
                    <i class="bx bx-table" style="font-size:20px;color:#0f9349"></i>
                    <input type="url" class="form-control form-control-sm border-0 bg-transparent flex-grow-1 p-0" name="url[]" value="${url}" required placeholder="Link Google Sheet…" style="min-width:0">
                    <select class="form-select form-select-sm" name="status_sheet[]" style="width:auto"><option value="yes">Aktif</option><option value="no">Nonaktif</option></select>
                    <button type="button" class="btn btn-sm btn-outline-danger removeSheet" data-id="new-${sheetCount}"><i class="bx bx-trash"></i></button>
                </div>`;
            $("#listGsheet").append(html);
            $("#newSheetUrl").val('');
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

// Step 2 chip handler — bulletproof: pakai class ft-pane-on + CSS !important
(function () {
    var chips = document.querySelectorAll('.ft-chip-btn[data-ft-tab]');
    var wrap  = document.getElementById('ft-know-panes');
    if (!wrap) return;

    function showPane(href) {
        wrap.querySelectorAll(':scope > .tab-pane').forEach(function (p) {
            p.classList.remove('ft-pane-on', 'show', 'active');
        });
        var pane = wrap.querySelector(href);
        if (pane) pane.classList.add('ft-pane-on', 'show', 'active');
    }

    chips.forEach(function (btn) {
        btn.addEventListener('click', function () {
            chips.forEach(function (b) { b.classList.remove('ft-active'); });
            this.classList.add('ft-active');
            showPane(this.getAttribute('data-ft-tab'));
        });
    });

    // default saat load: Dokumen
    document.querySelectorAll('.ft-chip-btn').forEach(function (b) { b.classList.remove('ft-active'); });
    var docsBtn = document.querySelector('.ft-chip-btn[data-ft-tab="#documents"]') || chips[0];
    if (docsBtn) docsBtn.classList.add('ft-active');
    showPane('#documents');
})();

// Advanced
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
    welcome: "Halo! Selamat datang \ud83d\udc4b Ada yang bisa dibantu, Kak?",
    character: "Kamu adalah {NAMA}, CS untuk [Nama Toko]. Gaya bicara ramah, ringkas, boleh pakai emoji. Panggil pelanggan \"Kak\".\nBalas selalu mengikuti bahasa yang dipakai pelanggan (Indonesia/Melayu/Inggris).\nKalau pelanggan pertama kali chat, sapa + tanya namanya, lalu tanya butuh produk apa.\nJawab HANYA dari info di \"Pengetahuan\"/dokumen. Kalau info harga/stok tidak ada atau ragu, JANGAN mengarang \u2014 sambungkan ke agen.\nSaat pelanggan mau beli, kirim format order:\nNama:\nNo. WA:\nAlamat lengkap (kecamatan, kota, provinsi):\nPesanan:\nSetelah lengkap, sampaikan pesanan akan ditotal (harga + ongkir) lalu arahkan pembayaran sesuai info di Pengetahuan.\nSelipkan tawaran/upsell yang relevan bila ada kesempatan, tapi jangan memaksa.\nAkhiri dengan pertanyaan lanjutan sesuai konteks."
  },
  ecourse: {
    welcome: "Halo! Selamat datang di [Nama Bisnis] \ud83d\udc4b Mau tanya kelas yang mana, Kak?",
    character: "Kamu adalah {NAMA}, admin pendaftaran [Nama Bisnis]. Ramah, ringkas, boleh emoji. Panggil \"Kak\".\nBalas mengikuti bahasa pelanggan (Indonesia/Melayu/Inggris).\nKalau pelanggan baru chat, tanya nama + kelas/topik yang diminati.\nJelaskan jadwal, harga, dan benefit HANYA dari info di Pengetahuan. Kalau tidak ada/ragu, sambungkan ke agen \u2014 jangan mengarang harga/jadwal.\nSaat mau daftar, kirim format:\nNama:\nNo. WA:\nKelas dipilih:\nMetode pembayaran:\nLalu arahkan pembayaran sesuai info Pengetahuan & minta kirim bukti.\nAkhiri dengan ajakan lanjut daftar."
  },
  reservasi: {
    welcome: "Halo! Mau reservasi untuk tanggal berapa ya, Kak? \ud83d\ude0a",
    character: "Kamu adalah {NAMA}, admin reservasi [Nama Bisnis]. Ramah, ringkas. Panggil \"Kak\".\nBalas mengikuti bahasa pelanggan.\nTanya: tanggal, jam, jumlah orang/jenis layanan. Cek ketersediaan HANYA dari info Pengetahuan.\nKalau slot/jadwal tidak ada di data atau ragu, sambungkan ke agen \u2014 jangan menjanjikan slot yang tak pasti.\nSetelah data lengkap, konfirmasi ulang detail reservasi (tanggal/jam/nama/kontak) sebelum dianggap fix.\nAkhiri dengan konfirmasi & langkah berikutnya."
  },
  cs: {
    welcome: "Halo! Ada yang bisa kami bantu, Kak? \ud83d\ude0a",
    character: "Kamu adalah {NAMA}, customer service [Nama Bisnis]. Ramah, jelas, ringkas. Panggil \"Kak\".\nBalas mengikuti bahasa pelanggan.\nJawab pertanyaan/keluhan HANYA dari info di Pengetahuan. Jangan menjawab hal di luar [Nama Bisnis].\nKalau info tidak tersedia, keluhan sensitif, atau pelanggan minta manusia \u2014 sambungkan ke agen dengan sopan.\nJangan menjanjikan refund/kompensasi di luar kebijakan yang tertulis.\nAkhiri dengan menanyakan apakah masih ada yang bisa dibantu."
  },
  donasi: {
    welcome: "Halo, Kak \ud83d\ude4f Terima kasih sudah menghubungi [Nama Lembaga]. Ada yang bisa kami bantu?",
    character: "Kamu adalah {NAMA}, admin [Nama Lembaga] (lembaga donasi/amal). Hangat, sopan, empatik. Panggil \"Kak\".\nBalas mengikuti bahasa donatur.\nJelaskan program & cara berdonasi HANYA dari info Pengetahuan. Kalau ragu/di luar data, sambungkan ke agen.\nSaat donatur mau berdonasi, sampaikan rekening/QRIS resmi sesuai info Pengetahuan, lalu minta kirim bukti transfer.\nUcapkan terima kasih dengan tulus. Jangan menekan/menuntut nominal tertentu."
  },
  properti: {
    welcome: "Halo, Kak! Sedang cari properti seperti apa? \ud83d\ude0a",
    character: "Kamu adalah {NAMA}, marketing [Nama Bisnis] (properti). Ramah, profesional, ringkas. Panggil \"Kak\".\nBalas mengikuti bahasa pelanggan.\nKualifikasi calon secara natural: kebutuhan (rumah/investasi), lokasi diinginkan, kisaran budget, waktu rencana.\nBeri info unit/harga HANYA dari Pengetahuan. Kalau tidak ada/ragu, sambungkan ke agen.\nTawarkan jadwal survei/site visit bila calon serius. Jangan menjanjikan harga/diskon di luar data."
  },
  klinik: {
    welcome: "Halo, Kak \ud83d\ude0a Ada yang bisa kami bantu terkait layanan/janji temu?",
    character: "Kamu adalah {NAMA}, admin [Nama Klinik]. Ramah, sopan, menenangkan. Panggil \"Kak\".\nBalas mengikuti bahasa pasien.\nBantu info layanan, jam praktik, dan janji temu HANYA dari info Pengetahuan.\nJANGAN memberi diagnosis/saran medis. Untuk keluhan medis, arahkan konsultasi ke dokter/agen.\nSaat buat janji, tanya: nama, layanan/dokter, tanggal & jam. Konfirmasi ulang sebelum fix."
  },
  fnb: {
    welcome: "Halo, Kak! Mau pesan atau reservasi meja? \ud83d\ude0b",
    character: "Kamu adalah {NAMA}, admin [Nama Resto]. Ramah, ceria, ringkas. Panggil \"Kak\".\nBalas mengikuti bahasa pelanggan.\nBeri info menu/harga & jam buka HANYA dari Pengetahuan. Kalau tidak ada/ragu, sambungkan ke agen.\nUntuk pesan antar, kirim format:\nNama:\nNo. WA:\nAlamat:\nPesanan:\nUntuk reservasi meja, tanya tanggal/jam/jumlah orang lalu konfirmasi. Sarankan menu favorit bila relevan."
  },
  jasa: {
    welcome: "Halo, Kak! Butuh bantuan layanan apa? \ud83d\ude0a",
    character: "Kamu adalah {NAMA}, admin [Nama Bisnis] (jasa/layanan). Ramah, solutif, ringkas. Panggil \"Kak\".\nBalas mengikuti bahasa pelanggan.\nTanya kebutuhan pelanggan secara detail dulu (jenis layanan, lokasi, waktu).\nBeri estimasi/penawaran HANYA dari info Pengetahuan. Kalau butuh survei/harga custom atau ragu, sambungkan ke agen.\nSetelah sepakat, jadwalkan & konfirmasi detail. Jangan menjanjikan harga pasti di luar data."
  }
};

function ftFill(desc, welcome) {
  var d = document.getElementById('ft-description');
  var w = document.querySelector('[name="welcome_message"]');
  var nama = (document.getElementById('pb-nama')||{}).value || 'Asisten';
  if (d) { d.value = desc.replace(/\{NAMA\}/g, nama); d.dispatchEvent(new Event('input')); }
  if (w && welcome) w.value = welcome;
}

// backward compat
function ftApplyTpl(type) {
  var t = ftTemplates[type];
  if (!t) return;
  ftFill(t.character || t.description || '', t.welcome);
}

// klik chip template baru (data-tpl)
document.querySelectorAll('#ft-templates [data-tpl]').forEach(function(btn){
  btn.addEventListener('click', function(){
    var t = ftTemplates[this.getAttribute('data-tpl')];
    if (!t) return;
    var d = document.getElementById('ft-description');
    if (d && d.value.trim() && !confirm('Ganti isi Karakter dengan template ini?')) return;
    ftFill(t.character, t.welcome);
  });
});

// builder chips — gaya (multi)
document.querySelectorAll('#pb-gaya .pb-chip').forEach(function(c){
  c.addEventListener('click', function(){ this.classList.toggle('on'); });
});
// builder chips — panggil (single/radio)
document.querySelectorAll('#pb-panggil .pb-chip').forEach(function(c){
  c.addEventListener('click', function(){
    document.querySelectorAll('#pb-panggil .pb-chip').forEach(function(x){ x.classList.remove('on'); });
    this.classList.add('on');
  });
});

// Susun karakter dari builder
var pbApplyBtn = document.getElementById('pb-apply');
if (pbApplyBtn) {
  pbApplyBtn.addEventListener('click', function(){
    var nama    = document.getElementById('pb-nama').value || 'Asisten';
    var peran   = document.getElementById('pb-peran').value || 'customer service';
    var gayaMap = {ramah:'ramah',ringkas:'ringkas',formal:'formal',santai:'santai',emoji:'boleh pakai emoji'};
    var gaya    = Array.from(document.querySelectorAll('#pb-gaya .pb-chip.on')).map(function(x){ return gayaMap[x.getAttribute('data-v')] || x.getAttribute('data-v'); });
    var gayaTxt = gaya.length ? gaya.join(', ') : 'ramah';
    var panggil = (document.querySelector('#pb-panggil .pb-chip.on') || {getAttribute:function(){return 'Kak';}}).getAttribute('data-v');
    var txt = 'Kamu adalah ' + nama + ', ' + peran + ' untuk [Nama Bisnis]. Gaya bicara ' + gayaTxt + '. Panggil pelanggan "' + panggil + '".\n'
            + 'Balas selalu mengikuti bahasa yang dipakai pelanggan (Indonesia/Melayu/Inggris).\n'
            + 'Kalau pelanggan baru chat, sapa & tanya namanya + kebutuhannya.\n'
            + 'Jawab HANYA dari info di "Pengetahuan"/dokumen. Kalau ragu atau info tak ada, sambungkan ke agen \u2014 jangan mengarang.';
    var d = document.getElementById('ft-description');
    if (d && d.value.trim() && !confirm('Ganti isi Karakter dengan hasil builder?')) return;
    if (d) { d.value = txt; d.dispatchEvent(new Event('input')); }
  });
}

</script>
@endsection
