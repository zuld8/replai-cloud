<template>
    <form id="myForm" @submit.prevent="confirmAndSubmit" enctype="multipart/form-data" method="POST"
        class="col-lg-10 col-sm-12">

        <!-- ═══ CONFIRMATION MODAL ═══ -->
        <div v-if="showConfirm" class="modal-overlay" @click.self="showConfirm=false">
            <div class="modal-box">
                <div class="modal-icon"><i class="ti ti-send"></i></div>
                <h5>Mulai Broadcast?</h5>
                <p v-if="categoryCount">Pesan akan dikirim ke <strong>{{ categoryCount.toLocaleString('id-ID') }} kontak</strong>. Proses ini tidak bisa dibatalkan setelah dimulai.</p>
                <p v-else>Pesan akan dikirim ke seluruh kontak dalam kategori ini. Tidak bisa dibatalkan setelah dimulai.</p>
                <div class="modal-actions">
                    <button type="button" class="btn btn-ghost-secondary" @click="showConfirm=false">Batal</button>
                    <button type="button" class="btn btn-primary" :disabled="loader.submit" @click="doSubmit">
                        <span v-if="loader.submit"><span class="spinner-border spinner-border-sm me-2"></span>Mengirim...</span>
                        <span v-else><i class="ti ti-send me-1"></i>Ya, Mulai Broadcast</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body row">
            <div class="col-lg-7 col-sm-12">
                <div class="row">

                    <!-- ═══ SECTION 1: PESAN & AUDIENS ═══ -->
                    <div class="col-12 mb-3">
                        <div class="section-header">
                            <span class="section-num">1</span>
                            <span class="section-title">Pesan &amp; Audiens</span>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Nama Broadcast</label>
                        <input class="form-control" v-model="form.name" type="text" required placeholder="Contoh: Promo Juni 2026" />
                    </div>

                    <div class="col-lg-6 col-sm-12 mb-3">
                        <label class="form-label">Kategori Kontak</label>
                        <select class="form-control" v-model="form.category" @change="onCategoryChange" required>
                            <option value="" disabled>Pilih kategori...</option>
                            <option v-for="(category, i) in categories" :key="i" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <!-- Contact Count -->
                        <small v-if="loadingCount" class="text-muted mt-1 d-block" style="font-size:11px;">
                            <span class="spinner-border spinner-border-sm" style="width:10px;height:10px;"></span> Menghitung...
                        </small>
                        <small v-else-if="categoryCount !== null" class="mt-1 d-block" style="font-size:11px;color:#16A34A;font-weight:500;">
                            <i class="ti ti-users" style="font-size:12px;"></i> {{ categoryCount.toLocaleString('id-ID') }} kontak tertarget
                        </small>
                    </div>

                    <div class="col-lg-6 col-sm-12 mb-3">
                        <label class="form-label">Template Pesan</label>
                        <select class="form-control" @change="changeTemplate" v-model="form.template" required>
                            <option value="" disabled>Pilih template...</option>
                            <option v-for="(template, t) in templates" :key="t" :value="template.id">
                                {{ template.name }}
                            </option>
                        </select>
                        <small v-if="selectedTemplateName" class="mt-1 d-block" style="font-size:11px;">
                            <span class="badge" style="background:#FEF3C7;color:#854F0B;font-weight:600;border-radius:6px;padding:2px 8px;">{{ selectedTemplateCategory || 'Template' }}</span>
                        </small>
                    </div>

                    <!-- ====== ENHANCED MEDIA UPLOAD SECTION ====== -->
                    <div class="col-lg-12 col-sm-12 mb-3"
                        v-if="form.metadata.header.format != 'TEXT' && form.metadata.header.format != ''">

                        <!-- Label + Info Badge -->
                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                            <label class="form-label mb-0 fw-semibold">
                                <i class="ti ti-upload me-1"></i>
                                <span v-if="form.metadata.header.format == 'IMAGE'">Upload Gambar</span>
                                <span v-if="form.metadata.header.format == 'VIDEO'">Upload Video</span>
                                <span v-if="form.metadata.header.format == 'DOCUMENT'">Upload Dokumen</span>
                                <span class="text-danger ms-1">*</span>
                            </label>
                            <span v-if="form.metadata.header.format == 'IMAGE'"
                                class="badge" style="background:#EAF3FC;color:#1B5FA6;font-size:11px;">
                                <i class="ti ti-photo me-1"></i>JPG, PNG &bull; Maks 5 MB
                            </span>
                            <span v-if="form.metadata.header.format == 'VIDEO'"
                                class="badge" style="background:#F1ECFE;color:#5B3FB0;font-size:11px;">
                                <i class="ti ti-video me-1"></i>MP4, 3GP &bull; Maks 16 MB
                            </span>
                            <span v-if="form.metadata.header.format == 'DOCUMENT'"
                                class="badge" style="background:#FEF3C7;color:#B45309;font-size:11px;">
                                <i class="ti ti-file-type-pdf me-1"></i>PDF &bull; Maks 100 MB
                            </span>
                        </div>

                        <!-- Drag & Drop Zone -->
                        <div class="upload-drop-zone"
                            :class="{
                                'upload-drop-zone--dragging': isDragging,
                                'upload-drop-zone--error': fileError,
                                'upload-drop-zone--success': form.files && !fileError
                            }"
                            @dragover.prevent="isDragging = true"
                            @dragleave="isDragging = false"
                            @drop.prevent="handleDrop"
                            @click="$refs.fileInput.click()">

                            <!-- Empty state -->
                            <div v-if="!form.files" class="text-center py-2">
                                <i class="ti ti-cloud-upload" style="font-size:2.2rem;opacity:0.4;display:block;margin-bottom:4px;"></i>
                                <div class="text-muted" style="font-size:13px;">Klik atau drag &amp; drop file ke sini</div>
                                <div class="text-muted mt-1" style="font-size:11px;">
                                    <span v-if="form.metadata.header.format == 'IMAGE'">JPG, PNG &bull; Maks 5 MB</span>
                                    <span v-if="form.metadata.header.format == 'VIDEO'">MP4, 3GP &bull; Maks 16 MB</span>
                                    <span v-if="form.metadata.header.format == 'DOCUMENT'">PDF &bull; Maks 100 MB</span>
                                </div>
                            </div>

                            <!-- File selected state -->
                            <div v-if="form.files" class="d-flex align-items-center gap-3 px-3 py-2">
                                <i class="ti ti-check-circle text-success" style="font-size:1.5rem;flex-shrink:0;"></i>
                                <div style="min-width:0;flex:1;">
                                    <div class="fw-semibold text-truncate" style="font-size:13px;">{{ form.files.name }}</div>
                                    <small class="text-muted">{{ (form.files.size / 1024 / 1024).toFixed(2) }} MB ✓</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-ghost-danger px-2"
                                    @click.stop="clearFile" title="Hapus file">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Error message -->
                        <div v-if="fileError" class="text-danger mt-1" style="font-size:12px;">
                            <i class="ti ti-alert-circle me-1"></i>{{ fileError }}
                        </div>

                        <!-- Hidden real input -->
                        <input ref="fileInput" type="file" style="display:none;"
                            :accept="fileAccept"
                            @change="handleFileUpload" />
                    </div>
                    <!-- ====== END UPLOAD SECTION ====== -->

                    <div class="col-12 mb-4 alert alert-warning" v-if="form.metadata.body.parameters.length > 0">
                        <h3 class="form-label text-dark">Variabel Teks</h3>
                        <table class="table">
                            <tr v-for="(item, index) in form.metadata.body.parameters" :key="index">
                                <td>
                                    <input type="text" class="form-control text-dark mt-2" v-model="item.value"
                                        required />
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- ═══ SECTION 2: PENGIRIM & JADWAL ═══ -->
                    <div class="col-12 mb-3 mt-2">
                        <div class="section-header">
                            <span class="section-num">2</span>
                            <span class="section-title">Pengirim &amp; Jadwal</span>
                        </div>
                    </div>

                    <!-- Dikirim dari: auto-select WABA number -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Dikirim dari <small class="text-muted">(nomor WABA terdaftar)</small></label>
                        <div v-if="devices.length === 0" class="text-muted d-flex align-items-center gap-2" style="font-size:13px;padding:10px 14px;background:#f8f9fa;border-radius:8px;border:1px solid #e0e0e0;">
                            <span class="spinner-border spinner-border-sm"></span> Memuat nomor WABA...
                        </div>
                        <div v-else class="waba-sender-card" v-for="(device, d) in devices.slice(0,1)" :key="d">
                            <div class="waba-sender-icon"><i class="ti ti-brand-whatsapp"></i></div>
                            <div class="waba-sender-info">
                                <div class="waba-sender-name">{{ device.name || 'Nomor WABA' }}</div>
                                <div class="waba-sender-phone">+{{ device.phone }}</div>
                            </div>
                            <span class="badge" style="background:#DCFCE7;color:#16A34A;font-weight:600;border:1px solid #C9EAD7;border-radius:6px;padding:3px 9px;">Aktif</span>
                        </div>
                        <!-- If multiple devices: show select -->
                        <select v-if="devices.length > 1" class="form-control mt-2" v-model="selectedDeviceId" @change="onDeviceChange">
                            <option v-for="(device, d) in devices" :key="d" :value="device.id">
                                {{ device.name || device.phone }} — +{{ device.phone }}
                            </option>
                        </select>
                    </div>

                    <!-- Waktu Kirim toggle -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Waktu Kirim</label>
                        <div class="send-mode-toggle mb-2">
                            <button type="button"
                                :class="['toggle-btn', sendMode === 'now' ? 'active' : '']"
                                @click="setSendMode('now')">
                                <i class="ti ti-bolt me-1"></i>Kirim Sekarang
                            </button>
                            <button type="button"
                                :class="['toggle-btn', 'schedule-btn', sendMode === 'schedule' ? 'active' : '']"
                                @click="setSendMode('schedule')">
                                <i class="ti ti-calendar-event me-1"></i>Jadwalkan
                            </button>
                        </div>
                        <div v-if="sendMode === 'schedule'">
                            <input class="form-control" v-model="form.schedule" type="datetime-local" required />
                            <small style="font-size:11px;color:#94A3B8;">WIB (UTC+7)</small>
                        </div>
                        <div v-else class="text-muted" style="font-size:12px;padding:6px 0;">
                            <i class="ti ti-check me-1" style="color:#16A34A;"></i>Broadcast akan masuk antrian segera setelah dikonfirmasi.
                        </div>
                    </div>

                    <!-- Pengaturan Lanjutan (collapsible) -->
                    <div class="col-12 mb-3">
                        <div class="advanced-toggle" @click="advancedOpen = !advancedOpen">
                            <i :class="['ti', advancedOpen ? 'ti-chevron-down' : 'ti-chevron-right', 'me-1']" style="font-size:13px;"></i>
                            <span style="font-size:13px;color:#64748B;">Pengaturan lanjutan</span>
                            <span style="font-size:11px;color:#94A3B8;margin-left:6px;">(jeda antar pesan — opsional, biasanya gak perlu utk WABA)</span>
                        </div>
                        <div v-show="advancedOpen" class="advanced-panel mt-2">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12 mb-2">
                                    <label class="form-label form-label-sm">Jeda antar pesan (dtk)</label>
                                    <input class="form-control form-control-sm" v-model="form.delay" type="number" min="0" placeholder="0" />
                                    <small class="text-muted" style="font-size:10px;">delay = jeda tiap pesan</small>
                                </div>
                                <div class="col-lg-4 col-sm-12 mb-2">
                                    <label class="form-label form-label-sm">Istirahat setiap (N pesan)</label>
                                    <input class="form-control form-control-sm" v-model="form.stop_sending" type="number" min="0" placeholder="0" />
                                    <small class="text-muted" style="font-size:10px;">stop_sending = ukuran batch</small>
                                </div>
                                <div class="col-lg-4 col-sm-12 mb-2">
                                    <label class="form-label form-label-sm">Lama istirahat (dtk)</label>
                                    <input class="form-control form-control-sm" v-model="form.rest_sending" type="number" min="0" placeholder="0" />
                                    <small class="text-muted" style="font-size:10px;">rest_sending = jeda antar batch</small>
                                </div>
                            </div>
                            <small style="font-size:11px;color:#94A3B8;">
                                <i class="ti ti-info-circle me-1"></i>
                                Contoh: jeda 30 dtk · istirahat 5 menit setiap 100 pesan. WABA jarang perlu ini — Meta sudah atur rate limit.
                            </small>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ═══ RIGHT COLUMN: WhatsApp Preview ═══ -->
            <div class="col-lg-5 col-sm-12 whatsapp-chat-body"
                pattern="https://res.cloudinary.com/eventbree/image/upload/v1575854793/Widgets/whatsapp-bg.png">
                <div class="whatsapp-chat-bubble">
                    <div class="whatsapp-chat-message-loader" style="opacity: 0">
                        <div style="position: relative; display: flex">
                            <div class="ixsrax"></div>
                            <div class="dRvxoz"></div>
                            <div class="kXBtNt"></div>
                        </div>
                    </div>
                    <div class="whatsapp-chat-message" style="opacity: 1">
                        <div class="bMIBDo">John Due</div>
                        <div class="iSpIQi text-center"
                            v-if="form.metadata.header.format != 'TEXT' && form.metadata.header.format != ''"
                            style="padding: 50px; background-color: darkgrey">
                            <i class="ti ti-photo-plus text-center text-white icon-image"
                                v-if="form.metadata.header.format == 'IMAGE'" style="font-size: 50px"></i>
                            <i class="ti ti-video text-center text-white icon-video"
                                v-if="form.metadata.header.format == 'VIDEO'" style="font-size: 50px"></i>
                            <i class="ti ti-file-type-pdf text-center text-white icon-document"
                                v-if="form.metadata.header.format == 'DOCUMENT'" style="font-size: 50px"></i>
                        </div>
                        <div class="iSpIQi fw-bold" v-if="
                            form.metadata.header.format == 'TEXT' &&
                            form.metadata.header.text != '' &&
                            form.metadata.header.text != null
                        " v-html="formattedHeaderText" style="font-size:14px;"></div>
                        <div class="iSpIQi" v-html="formattedBodyText" style="white-space:normal;word-break:break-word;"></div>
                        <div class="iSpIQd" v-if="
                            form.metadata.footer.text != '' &&
                            form.metadata.footer.text != null
                        ">
                            {{ form.metadata.footer.text }}
                        </div>
                        <div class="cqCDVm">10:51</div>
                    </div>
                </div>
                <div id="listButton">
                    <div class="whatsapp-chat-bubble phone-call" v-for="(item, index) in form.metadata.buttons.filter(
                        (item) => item.type == 'BUTTON'
                    )">
                        <div class="whatsapp-chat-message-loader" style="opacity: 0">
                            <div style="position: relative; display: flex">
                                <div class="ixsrax"></div>
                                <div class="dRvxoz"></div>
                                <div class="kXBtNt"></div>
                            </div>
                        </div>
                        <div class="whatsapp-chat-button text-info" style="opacity: 1">
                            <div class="iSpIQi text-center">
                                <i class="ti ti-phone fs-16 me-2"></i>
                                <span>{{ item.text }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="whatsapp-chat-bubble copy-text" v-for="(item, index) in form.metadata.buttons.filter(
                        (item) => item.type == 'COPY_CODE'
                    )" :key="index + 'revies'">
                        <div class="whatsapp-chat-message-loader" style="opacity: 0">
                            <div style="position: relative; display: flex">
                                <div class="ixsrax"></div>
                                <div class="dRvxoz"></div>
                                <div class="kXBtNt"></div>
                            </div>
                        </div>
                        <div class="whatsapp-chat-button text-info" style="opacity: 1">
                            <div class="iSpIQi text-center">
                                <i class="ti ti-copy fs-16 me-2"></i>
                                <span>{{ item.text }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="whatsapp-chat-bubble ext-link" v-for="(item, index) in form.metadata.buttons.filter(
                        (item) => item.type == 'URL'
                    )">
                        <div class="whatsapp-chat-message-loader" style="opacity: 0">
                            <div style="position: relative; display: flex">
                                <div class="ixsrax"></div>
                                <div class="dRvxoz"></div>
                                <div class="kXBtNt"></div>
                            </div>
                        </div>
                        <div class="whatsapp-chat-button text-info" style="opacity: 1">
                            <div class="iSpIQi text-center">
                                <i class="ti ti-external-link fs-16 me-2"></i>
                                <span>{{ item.text }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="whatsapp-chat-bubble cbutton" v-for="(item, index) in form.metadata.buttons.filter(
                        (item) => item.type == 'QUICK_REPLY'
                    )">
                        <div class="whatsapp-chat-message-loader" style="opacity: 0">
                            <div style="position: relative; display: flex">
                                <div class="ixsrax"></div>
                                <div class="dRvxoz"></div>
                                <div class="kXBtNt"></div>
                            </div>
                        </div>
                        <div class="whatsapp-chat-button text-info" style="opacity: 1">
                            <div class="iSpIQi text-center">
                                <i class="ti ti-share-3 fs-16 me-2"></i>
                                <span>{{ item.text }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ FOOTER: Kirim Tes + Mulai Broadcast ═══ -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                <small style="font-size:11px;color:#94A3B8;">
                    Versi WABA-native: "Dikirim dari" auto-pilih nomor WABA &bull; Throttle disembunyikan di Pengaturan lanjutan.
                </small>
            </div>
            <div class="d-flex gap-2">
                <!-- Kirim Tes -->
                <button type="button" class="btn btn-ghost-secondary"
                    :disabled="!form.template || sendingTest"
                    @click="sendTest"
                    title="Kirim 1 pesan ke nomor admin kamu sebagai tes">
                    <span v-if="sendingTest"><span class="spinner-border spinner-border-sm me-1"></span>Mengirim tes...</span>
                    <span v-else><i class="ti ti-test-pipe me-1"></i>Kirim Tes</span>
                </button>
                <!-- Mulai Broadcast -->
                <button type="submit" class="btn btn-primary"
                    :disabled="!isFormValid || loader.submit"
                    :class="{ 'opacity-50': !isFormValid && !loader.submit }">
                    <span v-if="loader.submit">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>Mengirim...
                    </span>
                    <span v-else-if="!isFormValid">
                        <i class="ti ti-lock fs-16 me-1"></i>Lengkapi Form
                    </span>
                    <span v-else>
                        <i class="ti ti-send fs-16 me-1"></i>
                        {{ type.id == null ? "Mulai Broadcast" : "Simpan Perubahan" }}
                    </span>
                </button>
            </div>
        </div>
    </form>
</template>

<style scoped>
/* ══════════════════════════════════════════
   BRAND PALETTE
   Primary  : #2E8DE1  soft #EAF3FC  dark #1B5FA6
   Success  : #16A34A  soft #DCFCE7
   Amber    : #B45309  soft #FEF3C7
   Danger   : #DC2626  soft #FEECEC
   Navy txt : #1E2A4A  muted #64748B  border #E4EAF2
   Page bg  : #F5F8FC  WA-green #25D366 (unchanged)
══════════════════════════════════════════ */

/* ── Global input focus ring ── */
.form-control:focus,
.form-select:focus {
    border-color: #2E8DE1 !important;
    box-shadow: 0 0 0 3px rgba(46,141,225,0.15) !important;
    outline: none;
}
.form-control,
.form-select {
    border-color: #E4EAF2;
    border-radius: 7px;
    color: #1E2A4A;
}

/* ── Section headers ── */
.section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 8px;
    border-bottom: 2px solid #EEF2F7;
}
.section-num {
    width: 26px; height: 26px;
    background: #2E8DE1;           /* brand primary — was #206bc4 */
    color: #fff;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; flex-shrink: 0;
}
.section-title {
    font-weight: 600;
    font-size: 14px;
    color: #1E2A4A;
    letter-spacing: 0.3px;
}

/* ── Upload drop zone ── */
.upload-drop-zone {
    border: 2px dashed #E4EAF2;    /* was #dee2e6 */
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    min-height: 85px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F5F8FC;           /* was #fafafa */
    user-select: none;
}
.upload-drop-zone:hover {
    border-color: #2E8DE1;
    background: #EAF3FC;           /* brand soft blue */
}
.upload-drop-zone--dragging {
    border-color: #2E8DE1;
    background: rgba(46,141,225,0.08);
    transform: scale(1.01);
}
.upload-drop-zone--success {
    border-color: #16A34A;
    background: #DCFCE7;           /* brand soft green */
    border-style: solid;
}
.upload-drop-zone--error {
    border-color: #DC2626;
    background: #FEECEC;           /* brand soft red */
}

/* ── WABA sender card ── */
.waba-sender-card {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 14px;
    background: #F0FBF4;           /* subtle green bg */
    border: 1.5px solid #C9EAD7;  /* soft green border */
    border-radius: 10px;
}
.waba-sender-icon {
    width: 38px; height: 38px; border-radius: 50%;
    background: #25D366;           /* WA green — UNCHANGED */
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.waba-sender-name {
    font-weight: 600; font-size: 13px;
    color: #1E2A4A;                /* navy */
}
.waba-sender-phone {
    font-size: 12px;
    color: #64748B;                /* muted */
}

/* ── Send-mode toggle ── */
.send-mode-toggle {
    display: flex; gap: 8px;
}
.toggle-btn {
    padding: 7px 16px; border-radius: 8px;
    border: 1.5px solid #E4EAF2;  /* brand border */
    background: #fff;
    color: #64748B;                /* muted text */
    font-size: 13px; cursor: pointer;
    transition: all 0.15s ease;
}
.toggle-btn:hover {
    border-color: #2E8DE1;
    color: #2E8DE1;
}
.toggle-btn.active {
    background: #2E8DE1;           /* brand primary */
    border-color: #2E8DE1;
    color: #fff;
    font-weight: 600;
}
/* "Jadwalkan" selected state → soft blue highlight */
.toggle-btn.active.schedule-btn {
    background: #EAF3FC;
    color: #1B5FA6;
    border-color: #2E8DE1;
}

/* ── Advanced (lanjutan) panel ── */
.advanced-toggle {
    display: flex; align-items: center;
    cursor: pointer; padding: 6px 0;
}
.advanced-toggle:hover span {
    color: #1E2A4A !important;
}
.advanced-panel {
    background: #F5F8FC;           /* page bg — was #f8f9fa */
    border-radius: 8px;
    padding: 12px 14px;
    border: 1px solid #E4EAF2;    /* brand border */
}

/* ── Confirmation modal ── */
.modal-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(30,42,74,0.45);  /* navy overlay */
    display: flex; align-items: center; justify-content: center;
}
.modal-box {
    background: #fff; border-radius: 16px;
    padding: 32px 28px; max-width: 420px; width: 90%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(30,42,74,0.18);
}
.modal-icon {
    width: 60px; height: 60px; border-radius: 50%;
    background: #EAF3FC;           /* brand soft blue */
    color: #2E8DE1;                /* brand primary */
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; margin: 0 auto 16px;
}
.modal-box h5 {
    font-weight: 700;
    color: #1E2A4A;
    margin-bottom: 8px;
}
.modal-box p {
    color: #64748B;
    font-size: 14px;
    margin-bottom: 20px;
}
.modal-actions {
    display: flex; gap: 10px; justify-content: center;
}

/* ── Disabled "Lengkapi Form" button: abu, bukan biru ── */
.btn-primary:disabled,
.btn-primary.disabled,
.btn-primary[disabled] {
    background-color: #E4EAF2 !important;
    border-color: #E4EAF2 !important;
    color: #94A3B8 !important;
    opacity: 1 !important;
    cursor: not-allowed;
}

/* ── Primary button (active) ── */
.btn-primary {
    background-color: #2E8DE1;
    border-color: #2E8DE1;
}
.btn-primary:hover:not(:disabled) {
    background-color: #1B6FB8;
    border-color: #1B6FB8;
}
.btn-primary:focus {
    box-shadow: 0 0 0 3px rgba(46,141,225,0.25);
}
</style>

<script>
export default {
    components: {},
    data() {
        return {
            categories: [],
            templates: [],
            devices: [],
            type: {
                form: "create",
                device: "",
                id: null,
            },
            form: {
                whatsapp_sender: 'sequence',   // WABA = always sequence (1 official number)
                name: "",
                category: "",
                template: "",
                schedule: "",
                delay: 0,
                stop_sending: 0,
                rest_sending: 0,
                files: null,
                devices: [],
                metadata: {
                    header: {
                        format: "TEXT",
                        text: "",
                    },
                    body: {
                        text: "",
                        type: "",
                        parameters: [],
                    },
                    footer: {},
                    buttons: [],
                    media: null,
                },
            },
            // UI state
            sendMode: 'now',           // 'now' | 'schedule'
            advancedOpen: false,       // collapsible throttle
            showConfirm: false,        // confirmation modal
            sendingTest: false,        // test message loader
            testNumber: '',            // remembered test phone number
            selectedDeviceId: null,    // for multi-device dropdown
            // Media upload
            file: null,
            isDragging: false,
            fileError: null,
            // Contact count
            categoryCount: null,
            loadingCount: false,
            loader: {
                submit: false,
            },
        };
    },
    computed: {
        selectedTemplateName() {
            if (!this.form.template) return '';
            const t = this.templates.find(t => t.id === this.form.template);
            return t ? t.name : '';
        },
        selectedTemplateCategory() {
            if (!this.form.template) return '';
            const t = this.templates.find(t => t.id === this.form.template);
            return t ? (t.category || '') : '';
        },
        fileAccept() {
            const fmt = this.form.metadata.header.format;
            if (fmt === 'IMAGE')    return 'image/jpeg,image/png,image/webp';
            if (fmt === 'VIDEO')    return 'video/mp4,video/3gpp';
            if (fmt === 'DOCUMENT') return 'application/pdf';
            return '*/*';
        },
        fileSizeLimitMB() {
            const fmt = this.form.metadata.header.format;
            if (fmt === 'IMAGE')    return 5;
            if (fmt === 'VIDEO')    return 16;
            if (fmt === 'DOCUMENT') return 100;
            return 10;
        },
        isMediaRequired() {
            const fmt = this.form.metadata.header.format;
            return ['IMAGE', 'VIDEO', 'DOCUMENT'].includes(fmt);
        },
        isFormValid() {
            const f = this.form;
            if (!f.name || !f.name.trim())           return false;
            if (!f.category)                          return false;
            if (!f.template)                          return false;
            if (!f.schedule)                          return false;
            if (!f.devices || f.devices.length === 0) return false;
            if (this.isMediaRequired && !f.files)    return false;
            if (this.fileError)                       return false;
            return true;
        },
        formattedBodyText() {
            let text = this.form.metadata.body.text || '';
            if (!text) return '';
            text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            text = text.replace(/\*(.*?)\*/g, '<strong>$1</strong>');
            text = text.replace(/_(.*?)_/g, '<em>$1</em>');
            text = text.replace(/~(.*?)~/g, '<del>$1</del>');
            text = text.split('\\r\\n').join('<br>');
            text = text.split('\\n').join('<br>');
            text = text.split('\n').join('<br>');
            return text;
        },
        formattedHeaderText() {
            let text = this.form.metadata.header.text || '';
            text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            text = text.replace(/\*(.*?)\*/g, '<strong>$1</strong>');
            return text;
        },
    },
    methods: {
        async onCategoryChange() {
            if (!this.form.category) {
                this.categoryCount = null;
                return;
            }
            this.loadingCount = true;
            this.categoryCount = null;
            try {
                const response = await this.$axios.get(
                    `/master/components/categories/count?category_id=${this.form.category}`
                );
                this.categoryCount = response.data.count;
            } catch (error) {
                this.categoryCount = 0;
                console.error('Error fetching category count:', error);
            } finally {
                this.loadingCount = false;
            }
        },

        // Helper: jam WIB sekarang (Intl-based, browser-TZ agnostic)
        nowWIB() {
            const f = new Intl.DateTimeFormat('en-CA', {
                timeZone: 'Asia/Jakarta',
                year: 'numeric', month: '2-digit', day: '2-digit',
                hour: '2-digit', minute: '2-digit', hour12: false
            }).formatToParts(new Date());
            const g = t => f.find(p => p.type === t).value;
            // Returns "YYYY-MM-DDTHH:MM" in WIB — no UTC offset double-count
            return `${g('year')}-${g('month')}-${g('day')}T${g('hour')}:${g('minute')}`;
        },

        setSendMode(mode) {
            this.sendMode = mode;
            if (mode === 'now') {
                this.form.schedule = this.nowWIB();
            }
        },

        onDeviceChange() {
            const dev = this.devices.find(d => d.id === this.selectedDeviceId);
            if (dev) this.form.devices = [dev.id];
        },

        changeTemplate() {
            var templateDetail = this.templates.filter(
                (item) => item.id === this.form.template
            );

            templateDetail = templateDetail[0].data;
            this.transformTemplate(templateDetail);
        },

        transformTemplate(detail) {

            // Transform header
            var header = {
                format: "",
                parameters: [],
            };

            // Transform body
            var body = {
                text: "",
                parameters: [],
            };

            // Transform footer
            var footer = {
                text: "",
            };

            // Transform buttons
            var buttons = [];
            var media = null;

            for (var i in detail.components) {
                var details = detail.components[i];
                if (details.type == "HEADER") {
                    if (details.format == "TEXT") {
                        header = {
                            format: "TEXT",
                            text: details.text,
                            parameters: [],
                        };
                    } else {
                        media = 1;
                        header = {
                            format: details.format,
                            parameters: [],
                        };
                    }
                }

                if (details.type == "BODY") {
                    var parameters = [];
                    var matches = details.text
                        ? details.text.match(/{{(\d+)}}/g)
                        : null;

                    if (matches) {
                        parameters = matches.map((match) => ({
                            type: "text",
                            value: "",
                        }));
                    }

                    body = {
                        text: details.text ?? "",
                        parameters: parameters,
                    };
                }

                if (details.type == "FOOTER") {
                    footer = {
                        text: details.text,
                    };
                }

                if (details.type == "BUTTONS") {
                    buttons = details.buttons.map((item) => ({
                        type: item.type == "QUICK_REPLY" ? "QUICK_REPLY"
                            : item.type == "PHONE_NUMBER" ? "BUTTON"
                            : item.type == "COPY_CODE" ? "COPY_CODE"
                            : "URL",
                        text: item.text,
                        url: item.url ?? null,
                    }));
                }
            }

            var transformedData = {
                header: header,
                body: body,
                footer: footer,
                buttons: buttons,
                media: media,
            };

            this.form.metadata = transformedData;
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            this.validateAndSetFile(file);
        },

        handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            this.validateAndSetFile(file);
        },

        validateAndSetFile(file) {
            this.fileError = null;
            if (!file) return;
            const maxBytes = this.fileSizeLimitMB * 1024 * 1024;
            if (file.size > maxBytes) {
                this.fileError = `Ukuran file terlalu besar. Maks ${this.fileSizeLimitMB} MB, file kamu ${(file.size / 1024 / 1024).toFixed(1)} MB.`;
                this.form.files = null;
                if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                return;
            }
            this.form.files = file;
        },

        clearFile() {
            this.form.files = null;
            this.fileError = null;
            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
        },

        async getCategories() {
            try {
                const response = await this.$axios.get(
                    `/master/components/categories`
                );
                this.categories = response.data;
            } catch (error) {
                this.$showToast(error.response.data.message, "error", 3000);
            }
        },

        async getTemplates() {
            try {
                const response = await this.$axios.get(
                    `/master/components/templates?waba=${this.type.device}&status=APPROVED`
                );
                this.templates = response.data;
            } catch (error) {
                this.$showToast(error.response.data.message, "error", 3000);
            }
        },

        async getDetails() {
            try {
                const response = await this.$axios.get(
                    `/waba/broadcast/details/${this.type.device}/${this.type.id}`
                );

                var data = response.data;
                console.log(data);
                this.form = data.detail;
                // Restore sendMode based on schedule
                if (this.form.schedule) {
                    this.sendMode = 'schedule';
                }
            } catch (error) {
                this.$showToast(error.response.data.message, "error", 3000);
            }
        },

        // ─── Kirim Tes ───────────────────────────────────
        async sendTest() {
            if (!this.form.template) {
                this.$showToast('Pilih template terlebih dahulu', 'error', 3000);
                return;
            }
            // Prompt admin for test phone number
            const testNumber = window.prompt(
                'Masukkan nomor HP tujuan tes (format internasional, contoh: 628123456789):',
                this.testNumber || ''
            );
            if (!testNumber || !testNumber.trim()) return; // user cancelled

            this.testNumber  = testNumber.trim(); // remember for next time
            this.sendingTest = true;
            try {
                const res = await this.$axios.post(`/waba/broadcast/test/${this.type.device}`, {
                    template:    this.form.template,
                    test_number: testNumber.trim(),
                    metadata:    JSON.stringify(this.form.metadata),
                });
                this.$showToast(res.data.message || 'Pesan tes terkirim!', 'success', 4000);
            } catch (error) {
                const msg = error.response?.data?.message || 'Gagal mengirim tes';
                this.$showToast(msg, 'error', 4000);
            } finally {
                this.sendingTest = false;
            }
        },

        // ─── Confirm before broadcast ────────────────────
        confirmAndSubmit() {
            if (!this.isFormValid) return;
            this.showConfirm = true;
        },

        async doSubmit() {
            this.showConfirm = false;

            // Validate: media header requires file upload
            const headerFormat = this.form.metadata.header.format;
            if (['IMAGE', 'VIDEO', 'DOCUMENT'].includes(headerFormat) && !this.form.files) {
                this.$showToast(`Harap upload file ${headerFormat.toLowerCase()} untuk header template ini`, 'error', 4000);
                return;
            }
            const formData = new FormData();

            for (const key in this.form) {
                if (key === "files" && this.form[key]) {
                    formData.append(key, this.form[key]);
                } else if (Array.isArray(this.form[key])) {
                    if (key == 'devices') {
                        formData.append(key, this.form[key]);
                    } else {
                        this.form[key].forEach((item, index) => {
                            formData.append(
                                `${key}[${index}]`,
                                JSON.stringify(item)
                            );
                        });
                    }

                } else if (key === "metadata") {
                    formData.append(key, JSON.stringify(this.form[key]));
                } else {
                    formData.append(key, this.form[key]);
                }
            }

            try {
                this.loader.submit = true;
                const path = window.location.pathname;
                const segments = path.split("/");
                const lastSegment = segments
                    .filter((segment) => segment !== "")
                    .pop();

                var url = `/waba/broadcast/store/${lastSegment}`;

                if (this.type.form == "update") {
                    var url = `/waba/broadcast/edit/${this.type.device}/${this.type.id}`;
                }
                const response = await this.$axios.post(url, formData);

                this.$showToast(response.data.message, "info", 3000);

                setTimeout(() => {
                    window.location.href = `/app/waba/broadcast/${this.type.device}`;
                }, 3000);
            } catch (error) {
                this.loader.submit = false;
                this.$showToast(error.response.data.message, "error", 3000);
            }
        },

        async getDevices() {
            try {
                const response = await this.$axios.get(`/waba/devices/${this.type.device}`);
                this.devices = response.data.data;
                // Auto-select first device
                if (this.devices && this.devices.length > 0) {
                    this.selectedDeviceId = this.devices[0].id;
                    this.form.devices = [this.devices[0].id];
                    this.form.whatsapp_sender = 'sequence';
                }
            } catch (error) {
                this.$showToast(error.response?.data?.message || 'Gagal memuat device', "error", 3000);
            }
        },
    },
    beforeDestroy() { },
    updated() { },
    mounted() {
        const path = window.location.pathname;
        const segments = path.split("/");
        const lastSegment = segments.filter((segment) => segment !== "").pop();

        if (segments.length == 6) {
            this.type = {
                form: "create",
                device: lastSegment,
                id: null,
            };
        } else {
            this.type = {
                form: "update",
                device: segments[5],
                id: segments[6],
            };

            this.getDetails();
        }

        this.getCategories();
        this.getTemplates();
        this.getDevices();

        // Set default schedule to now (WIB)
        this.setSendMode('now');
    },
    watch: {},
};
</script>
