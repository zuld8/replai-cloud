<template>
    <form
        id="myForm"
        @submit.prevent="handleSubmit"
        enctype="multipart/form-data"
        method="POST"
        class="col-12 col-md-10"
    >
        <div class="card-body row" style="padding: 16px 12px;">
            <div class="col-lg-7 col-sm-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <label class="form-label mb-1" style="font-weight:600;font-size:13px;color:#444;">
                            <i class="ti ti-template" style="color:#00ceec;margin-right:4px;"></i>
                            Nama Template
                        </label>
                        <input
                            class="form-control"
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="contoh: konfirmasi_pendaftaran"
                            @input="slugifyName"
                            style="font-size:13px;"
                        />
                        <small style="color:#aaa;font-size:10px;">Otomatis huruf kecil, spasi → underscore</small>
                    </div>

                    <div class="col-lg-6 col-sm-12 mb-2">
                        <label class="form-label mb-1" style="font-weight:600;font-size:13px;color:#444;">
                            <i class="ti ti-category" style="color:#00ceec;margin-right:4px;"></i>
                            Kategori
                        </label>
                        <select
                            class="form-control"
                            v-model="form.category"
                            required
                            style="font-size:13px;"
                        >
                            <option value="UTILITY">UTILITY</option>
                            <option value="MARKETING">MARKETING</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mb-2">
                        <label class="form-label mb-1" style="font-weight:600;font-size:13px;color:#444;">
                            <i class="ti ti-language" style="color:#00ceec;margin-right:4px;"></i>
                            Bahasa
                        </label>
                        <select
                            class="form-control langwaba"
                            v-model="form.lang"
                            required
                        >
                            <option value="">Choose Lang</option>
                            <option value="af">Afrikaans</option>
                            <option value="sq">Albanian</option>
                            <option value="am">Amharic</option>
                            <option value="ar">Arabic</option>
                            <option value="hy">Armenian</option>
                            <option value="az">Azerbaijani</option>
                            <option value="bn">Bengali</option>
                            <option value="bg">Bulgarian</option>
                            <option value="ca">Catalan</option>
                            <option value="zh_CN">Chinese (Simplified)</option>
                            <option value="zh_HK">Chinese (Hong Kong)</option>
                            <option value="zh_TW">Chinese (Traditional)</option>
                            <option value="hr">Croatian</option>
                            <option value="cs">Czech</option>
                            <option value="da">Danish</option>
                            <option value="nl">Dutch</option>
                            <option value="en">English</option>
                            <option value="en_GB">English (UK)</option>
                            <option value="en_US">English (US)</option>
                            <option value="et">Estonian</option>
                            <option value="fil">Filipino</option>
                            <option value="fi">Finnish</option>
                            <option value="fr">French</option>
                            <option value="ka">Georgian</option>
                            <option value="de">German</option>
                            <option value="el">Greek</option>
                            <option value="gu">Gujarati</option>
                            <option value="ha">Hausa</option>
                            <option value="he">Hebrew</option>
                            <option value="hi">Hindi</option>
                            <option value="hu">Hungarian</option>
                            <option value="is">Icelandic</option>
                            <option value="id">Indonesian</option>
                            <option value="ga">Irish</option>
                            <option value="it">Italian</option>
                            <option value="ja">Japanese</option>
                            <option value="kn">Kannada</option>
                            <option value="kk">Kazakh</option>
                            <option value="km">Khmer</option>
                            <option value="ko">Korean</option>
                            <option value="ky">Kyrgyz</option>
                            <option value="lo">Lao</option>
                            <option value="lv">Latvian</option>
                            <option value="lt">Lithuanian</option>
                            <option value="mk">Macedonian</option>
                            <option value="ms">Malay</option>
                            <option value="ml">Malayalam</option>
                            <option value="mr">Marathi</option>
                            <option value="mn">Mongolian</option>
                            <option value="ne">Nepali</option>
                            <option value="no">Norwegian</option>
                            <option value="fa">Persian</option>
                            <option value="pl">Polish</option>
                            <option value="pt_BR">Portuguese (Brazil)</option>
                            <option value="pt_PT">Portuguese (Portugal)</option>
                            <option value="pa">Punjabi</option>
                            <option value="ro">Romanian</option>
                            <option value="ru">Russian</option>
                            <option value="sr">Serbian</option>
                            <option value="si">Sinhala</option>
                            <option value="sk">Slovak</option>
                            <option value="sl">Slovenian</option>
                            <option value="es">Spanish</option>
                            <option value="es_AR">Spanish (Argentina)</option>
                            <option value="es_ES">Spanish (Spain)</option>
                            <option value="es_MX">Spanish (Mexico)</option>
                            <option value="sw">Swahili</option>
                            <option value="sv">Swedish</option>
                            <option value="ta">Tamil</option>
                            <option value="te">Telugu</option>
                            <option value="th">Thai</option>
                            <option value="tr">Turkish</option>
                            <option value="uk">Ukrainian</option>
                            <option value="ur">Urdu</option>
                            <option value="uz">Uzbek</option>
                            <option value="vi">Vietnamese</option>
                            <option value="cy">Welsh</option>
                            <option value="zu">Zulu</option>
                        </select>
                    </div>

                    <div class="col-12 mb-2">
                        <label class="form-label mb-1" style="font-weight:600;font-size:13px;color:#444;">
                            <i class="ti ti-layout-navbar" style="color:#00ceec;margin-right:4px;"></i>
                            Header <small style="color:#aaa;font-weight:400;font-size:11px;margin-left:4px;">(Opsional)</small>
                        </label>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <button type="button" v-for="t in [{v:'text',l:'📝 Teks'},{v:'image',l:'🖼️ Gambar'},{v:'video',l:'🎬 Video'},{v:'document',l:'📄 Dokumen'}]" :key="t.v"
                                @click="form.type = t.v"
                                :style="form.type == t.v ? 'background:#00ceec;color:#fff;border-color:#00ceec;' : 'background:#fff;color:#555;border-color:#ddd;'"
                                style="padding:4px 12px;border-radius:20px;border:1px solid;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;">
                                {{ t.l }}
                            </button>
                        </div>
                    </div>
                    <div class="col-12 mb-2" v-if="form.type == 'text'">
                        <input
                            class="form-control form-control-sm"
                            v-model="form.header_text"
                            placeholder="Judul header template"
                            type="text"
                            style="font-size:13px;"
                        />
                    </div>
                    <div class="col-12 mb-2" v-else-if="form.type != 'text'">
                        <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:8px 12px;display:flex;align-items:center;gap:8px;">
                            <span style="font-size:16px;">💡</span>
                            <span style="font-size:11px;color:#0369a1;">
                                Media {{ form.type == 'image' ? '(JPG/PNG)' : form.type == 'video' ? '(MP4)' : '(PDF)' }} di-upload saat kirim broadcast, bukan di sini.
                            </span>
                        </div>
                    </div>

                    <div class="col-12 mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label mb-0" style="font-weight: 600; font-size: 13px; color: #444;">
                                <i class="ti ti-message-2" style="color: #00ceec; margin-right: 4px;"></i>
                                Body Message
                                <span style="background: #dc3545; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 3px; margin-left: 4px; font-weight: 700;">WAJIB</span>
                            </label>
                            <span :style="bodyCharCount > 1024 ? 'color: #dc3545; font-weight: 700;' : 'color: #888;'" style="font-size: 12px; font-family: monospace;">
                                {{ bodyCharCount }} / <span style="opacity: 0.7;">1024</span>
                            </span>
                        </div>
                        <div style="display: flex; gap: 4px; align-items: center; margin-bottom: 0; padding: 6px 10px; background: #f8f9fe; border: 1px solid #00ceec; border-bottom: none; border-radius: 8px 8px 0 0;">
                            <button type="button" @click="wrapBody('*','*')" title="Bold" style="border: 1px solid #ddd; border-radius: 4px; background: #fff; padding: 2px 8px; cursor: pointer; font-weight: 700; font-size: 13px; color: #333;">B</button>
                            <button type="button" @click="wrapBody('_','_')" title="Italic" style="border: 1px solid #ddd; border-radius: 4px; background: #fff; padding: 2px 8px; cursor: pointer; font-style: italic; font-size: 13px; color: #333;">I</button>
                            <button type="button" @click="wrapBody('~','~')" title="Strikethrough" style="border: 1px solid #ddd; border-radius: 4px; background: #fff; padding: 2px 8px; cursor: pointer; text-decoration: line-through; font-size: 13px; color: #333;">S</button>
                            <div style="width: 1px; background: #ddd; height: 18px; margin: 0 2px;"></div>
                            <small style="color: #aaa; font-size: 11px;">*bold*&nbsp;&nbsp;_italic_&nbsp;&nbsp;~coret~</small>
                        </div>
                        <textarea
                            id="bodyMessageTextarea"
                            class="form-control"
                            :style="bodyCharCount > 1024
                                ? 'height:140px; border-color:#dc3545; border-top:none; border-radius:0 0 8px 8px; box-shadow:0 0 0 3px rgba(220,53,69,0.1); font-size:14px; resize:vertical;'
                                : 'height:140px; border-color:#00ceec; border-top:none; border-radius:0 0 8px 8px; font-size:14px; resize:vertical;'"
                            v-model="form.body_message"
                            placeholder="Ketik pesan template... contoh: Halo *{{1}}*, pesanan _{{2}}_ sudah siap!"
                            required
                        ></textarea>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small v-if="bodyCharCount > 1024" style="color: #dc3545; font-size: 11px;">
                                <i class="ti ti-alert-circle"></i> Melebihi batas {{ bodyCharCount - 1024 }} karakter
                            </small>
                            <small v-else style="color: #aaa; font-size: 11px;">Maks. 1024 karakter · Gunakan {{1}}, {{2}}, dst. untuk variabel</small>
                            <a href="javascript:void(0);" @click="addVariabled()" style="color: #00ceec; font-size: 12px; font-weight: 600; text-decoration: none; white-space: nowrap;">+ Insert Variable</a>
                        </div>
                    </div>

                    <div
                        class="col-12 mb-1"
                        v-if="form.variables.length > 0"
                        style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;"
                    >
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                            <span style="font-size:14px;">📝</span>
                            <span style="font-weight:700;font-size:12px;color:#92400e;">Contoh Variabel</span>
                            <small style="color:#a16207;font-size:10px;">· Jangan pakai data asli pelanggan</small>
                        </div>
                        <div class="row g-1">
                            <div class="col-12" v-for="(item, index) in form.variables" :key="index">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text" style="font-weight:700;font-family:monospace;font-size:12px;min-width:50px;justify-content:center;background:#fef3c7;border-color:#fde68a;color:#92400e;">{{ item.code }}</span>
                                    <input
                                        type="text"
                                        class="form-control form-control-sm text-dark"
                                        v-model="item.sample"
                                        placeholder="Contoh isi variabel"
                                        required
                                        style="font-size:12px;"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label mb-0" style="font-weight: 600; font-size: 13px; color: #444;">
                                <i class="ti ti-text-caption" style="color: #00ceec; margin-right: 4px;"></i>
                                Footer Text
                                <small style="color: #aaa; font-weight: 400; font-size: 11px; margin-left: 4px;">(Opsional)</small>
                            </label>
                            <span :style="footerCharCount > 60 ? 'color: #dc3545; font-weight: 700;' : footerCharCount > 50 ? 'color: #f39c12; font-weight: 600;' : 'color: #888;'" style="font-size: 12px; font-family: monospace;">
                                {{ footerCharCount }} / <span style="opacity: 0.7;">60</span>
                            </span>
                        </div>
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            :style="footerCharCount > 60
                                ? 'border-color:#dc3545; font-size:12px; height:30px; padding:4px 8px;'
                                : 'border-color:#dee2e6; font-size:12px; height:30px; padding:4px 8px;'"
                            v-model="form.footer_message"
                            maxlength="60"
                            placeholder="Balas STOP untuk berhenti"
                        />
                        <div class="d-flex justify-content-between mt-1">
                            <small v-if="footerCharCount > 60" style="color: #dc3545; font-size: 11px;"><i class="ti ti-alert-circle"></i> Melebihi batas 60 karakter</small>
                            <small v-else style="color: #aaa; font-size: 11px;">Maks. 60 · tampil abu-abu</small>
                        </div>
                    </div>

                    <div class="col-12 mb-2">
                        <label class="form-label mb-2" style="font-weight: 600; font-size: 13px; color: #444;">
                            <i class="ti ti-cursor-text" style="color: #00ceec; margin-right: 4px;"></i>
                            Tombol Interaktif <small style="color: #aaa; font-weight: 400; font-size: 11px; margin-left: 4px;">(Opsional · Maks. 3 tombol)</small>
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="btn-card" @click="addButton('call')">
                                    <div class="btn-card-header">
                                        <span class="btn-card-icon" style="background:#dcf5fb;color:#0097b2;">📞</span>
                                        <span class="btn-card-title">Call Action</span>
                                        <span class="btn-card-badge">Maks. 1</span>
                                    </div>
                                    <p class="btn-card-desc">Tombol langsung telepon nomor HP. Ideal untuk CS/support.</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="btn-card" @click="addButton('web')">
                                    <div class="btn-card-header">
                                        <span class="btn-card-icon" style="background:#dcf5fb;color:#0097b2;">🔗</span>
                                        <span class="btn-card-title">Visit Website</span>
                                        <span class="btn-card-badge">Maks. 2</span>
                                    </div>
                                    <p class="btn-card-desc">Buka URL. Bisa URL dinamis dengan {{1}}.</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="btn-card btn-card-green" @click="addButton('wa_call')">
                                    <div class="btn-card-header">
                                        <span class="btn-card-icon" style="background:#d4f8e0;color:#1a7a45;">📲</span>
                                        <span class="btn-card-title">Call on WhatsApp</span>
                                        <span class="btn-card-badge">Maks. 1</span>
                                    </div>
                                    <p class="btn-card-desc">Panggilan via WhatsApp ke nomor bisnis.</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="btn-card" @click="addButton('custom')">
                                    <div class="btn-card-header">
                                        <span class="btn-card-icon" style="background:#dcf5fb;color:#0097b2;">⚡</span>
                                        <span class="btn-card-title">Quick Reply</span>
                                        <span class="btn-card-badge">Maks. 3</span>
                                    </div>
                                    <p class="btn-card-desc">User klik → kirim balasan otomatis.</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="btn-card btn-card-yellow" @click="addButton('copy')">
                                    <div class="btn-card-header">
                                        <span class="btn-card-icon" style="background:#fff3cd;color:#856404;">📋</span>
                                        <span class="btn-card-title">Copy Code</span>
                                        <span class="btn-card-badge">Maks. 1</span>
                                    </div>
                                    <p class="btn-card-desc">Salin kode promo/OTP. Khusus MARKETING.</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="btn-card btn-card-disabled">
                                    <span class="btn-card-soon">SEGERA HADIR</span>
                                    <div class="btn-card-header">
                                        <span class="btn-card-icon" style="background:#e9ecef;color:#6c757d;">🔄</span>
                                        <span class="btn-card-title" style="color:#6c757d;">Complete Flow</span>
                                    </div>
                                    <p class="btn-card-desc">Form interaktif langsung di dalam WhatsApp.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 row p-0 listbutton">
                        <div
                            class="col-12 callbutton mt-1"
                            v-for="(item, index) in form.buttons"
                            :key="index"
                        >
                            <div v-if="item && item.type" style="border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;margin-bottom:2px;">
                                <!-- Header - unified gray -->
                                <div style="display:flex;align-items:center;padding:7px 12px;background:linear-gradient(135deg,#f5f6f8,#ebedf0);border-bottom:1.5px solid #ddd;gap:8px;">
                                    <span style="font-size:14px;flex-shrink:0;">{{ btnIcon(item.type) }}</span>
                                    <span style="font-weight:700;font-size:12px;color:#444;flex:1;">{{ btnLabel(item.type) }}</span>
                                    <span style="font-size:9px;font-weight:600;background:rgba(0,0,0,0.08);padding:2px 6px;border-radius:4px;color:#666;">{{ btnLimit(item.type) }}</span>
                                    <button type="button" @click="removeButton(index)" title="Hapus"
                                        style="width:24px;height:24px;border:1px solid #ddd;border-radius:5px;background:#fff;color:#dc3545;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:11px;padding:0;line-height:1;flex-shrink:0;transition:all .15s;"
                                        onmouseover="this.style.background='#dc3545';this.style.color='#fff';this.style.borderColor='#dc3545'"
                                        onmouseout="this.style.background='#fff';this.style.color='#dc3545';this.style.borderColor='#ddd'">
                                        ✕
                                    </button>
                                </div>
                                <!-- Fields - compact -->
                                <div style="padding:8px 12px;">
                                    <div class="row g-2 align-items-end">
                                        <!-- Teks Tombol -->
                                        <div :class="item.type == 'custom' ? 'col-12' : 'col-5'">
                                            <label style="font-size:11px;color:#888;font-weight:600;">Teks Tombol</label>
                                            <input class="form-control form-control-sm" type="text" maxlength="25" required
                                                v-model="item.button_name"
                                                :placeholder="item.type=='custom' ? 'Lihat Katalog' : 'Hubungi Kami'"
                                                style="font-size:12px;"/>
                                        </div>
                                        <!-- Call: +62 fixed + nomor -->
                                        <template v-if="item.type == 'call'">
                                            <div class="col-7">
                                                <label style="font-size:11px;color:#888;font-weight:600;">Nomor Telepon</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text" style="font-size:12px;font-weight:600;background:#f0f0f0;">+62</span>
                                                    <input class="form-control form-control-sm" type="text" v-model="item.phone_number" required placeholder="81234567890" style="font-size:12px;"/>
                                                </div>
                                            </div>
                                        </template>
                                        <!-- WA Call: +62 fixed + nomor -->
                                        <template v-else-if="item.type == 'wa_call'">
                                            <div class="col-7">
                                                <label style="font-size:11px;color:#888;font-weight:600;">Nomor WhatsApp</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text" style="font-size:12px;font-weight:600;background:#f0f0f0;">+62</span>
                                                    <input class="form-control form-control-sm" type="text" v-model="item.phone_number" required placeholder="81234567890" style="font-size:12px;"/>
                                                </div>
                                            </div>
                                        </template>
                                        <!-- Web: URL -->
                                        <template v-else-if="item.type == 'web'">
                                            <div class="col-7">
                                                <label style="font-size:11px;color:#888;font-weight:600;">URL Website</label>
                                                <input class="form-control form-control-sm" type="text" v-model="item.button_link" required placeholder="https://contoh.com" style="font-size:12px;"/>
                                            </div>
                                        </template>
                                        <!-- Copy: code -->
                                        <template v-else-if="item.type == 'copy'">
                                            <div class="col-7">
                                                <label style="font-size:11px;color:#888;font-weight:600;">Kode Promo</label>
                                                <input class="form-control form-control-sm" type="text" v-model="item.example" required placeholder="PROMO50" style="font-size:12px;"/>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div
                class="col-lg-5 col-sm-12 whatsapp-chat-body"
                pattern="https://res.cloudinary.com/eventbree/image/upload/v1575854793/Widgets/whatsapp-bg.png"
            >
                <div class="whatsapp-chat-bubble">
                    <div
                        class="whatsapp-chat-message-loader"
                        style="opacity: 0"
                    >
                        <div style="position: relative; display: flex">
                            <div class="ixsrax"></div>
                            <div class="dRvxoz"></div>
                            <div class="kXBtNt"></div>
                        </div>
                    </div>
                    <div class="whatsapp-chat-message" style="opacity: 1">
                        <div class="bMIBDo">John Due</div>
                        <div
                            class="iSpIQi text-center"
                            v-if="form.type != 'text'"
                            style="padding: 50px; background-color: darkgrey"
                        >
                            <i
                                class="ti ti-photo-plus text-center text-white icon-image"
                                v-if="form.type == 'image'"
                                style="font-size: 50px"
                            ></i>
                            <i
                                class="ti ti-video text-center text-white icon-video"
                                v-if="form.type == 'video'"
                                style="font-size: 50px"
                            ></i>
                            <i
                                class="ti ti-file-type-pdf text-center text-white icon-document"
                                v-if="form.type == 'document'"
                                style="font-size: 50px"
                            ></i>
                        </div>
                        <div
                            class="iSpIQi"
                            style="font-weight: 600; margin-bottom: 2px;"
                            v-if="form.type == 'text' && form.header_text"
                            v-html="formattedHeaderText"
                        ></div>
                        <div class="iSpIQi" style="word-break: break-word; line-height: 1.5;" v-html="formattedBodyText"></div>
                        <div
                            class="iSpIQd"
                            v-if="
                                form.footer_message != '' &&
                                form.footer_message != null
                            "
                            style="color:#8696a0;font-size:12px;margin-top:2px;"
                        >
                            {{ form.footer_message }}
                        </div>
                        <div class="cqCDVm">10:51</div>
                    </div>
                </div>
                <div id="listButton">
                    <div
                        class="whatsapp-chat-bubble phone-call"
                        v-for="(item, index) in form.buttons.filter(
                            (item) => item && item.type == 'call'
                        )"
                        :key="index + 'call'"
                    >
                        <div
                            class="whatsapp-chat-message-loader"
                            style="opacity: 0"
                        >
                            <div style="position: relative; display: flex">
                                <div class="ixsrax"></div>
                                <div class="dRvxoz"></div>
                                <div class="kXBtNt"></div>
                            </div>
                        </div>
                        <div
                            class="whatsapp-chat-button"
                            style="opacity: 1"
                        >
                            <div class="iSpIQi text-center" style="color:#00a884;">
                                <i class="ti ti-phone fs-16 me-2" style="color:#00a884;"></i>
                                <span style="color:#00a884;">{{ item.button_name || '...' }}</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="whatsapp-chat-bubble phone-call"
                        v-for="(item, index) in form.buttons.filter(
                            (item) => item && item.type == 'wa_call'
                        )"
                        :key="index + 'wacall'"
                    >
                        <div
                            class="whatsapp-chat-message-loader"
                            style="opacity: 0"
                        >
                            <div style="position: relative; display: flex">
                                <div class="ixsrax"></div>
                                <div class="dRvxoz"></div>
                                <div class="kXBtNt"></div>
                            </div>
                        </div>
                        <div
                            class="whatsapp-chat-button"
                            style="opacity: 1"
                        >
                            <div class="iSpIQi text-center" style="color:#00a884;">
                                <i class="ti ti-brand-whatsapp fs-16 me-2" style="color:#00a884;"></i>
                                <span style="color:#00a884;">{{ item.button_name || '...' }}</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="whatsapp-chat-bubble copy-text"
                        v-for="(item, index) in form.buttons.filter(
                            (item) => item && item.type == 'copy'
                        )"
                        :key="index + 'copy'"
                    >
                        <div
                            class="whatsapp-chat-message-loader"
                            style="opacity: 0"
                        >
                            <div style="position: relative; display: flex">
                                <div class="ixsrax"></div>
                                <div class="dRvxoz"></div>
                                <div class="kXBtNt"></div>
                            </div>
                        </div>
                        <div
                            class="whatsapp-chat-button"
                            style="opacity: 1"
                        >
                            <div class="iSpIQi text-center" style="color:#00a884;">
                                <i class="ti ti-copy fs-16 me-2" style="color:#00a884;"></i>
                                <span style="color:#00a884;">{{ item.button_name || '...' }}</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="whatsapp-chat-bubble ext-link"
                        v-for="(item, index) in form.buttons.filter(
                            (item) => item && item.type == 'web'
                        )"
                        :key="index + 'web'"
                    >
                        <div
                            class="whatsapp-chat-message-loader"
                            style="opacity: 0"
                        >
                            <div style="position: relative; display: flex">
                                <div class="ixsrax"></div>
                                <div class="dRvxoz"></div>
                                <div class="kXBtNt"></div>
                            </div>
                        </div>
                        <div
                            class="whatsapp-chat-button"
                            style="opacity: 1"
                        >
                            <div class="iSpIQi text-center" style="color:#00a884;">
                                <i class="ti ti-external-link fs-16 me-2" style="color:#00a884;"></i>
                                <span style="color:#00a884;">{{ item.button_name || '...' }}</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="whatsapp-chat-bubble cbutton"
                        v-for="(item, index) in form.buttons.filter(
                            (item) => item && item.type == 'custom'
                        )"
                        :key="index + 'custom'"
                    >
                        <div
                            class="whatsapp-chat-message-loader"
                            style="opacity: 0"
                        >
                            <div style="position: relative; display: flex">
                                <div class="ixsrax"></div>
                                <div class="dRvxoz"></div>
                                <div class="kXBtNt"></div>
                            </div>
                        </div>
                        <div
                            class="whatsapp-chat-button"
                            style="opacity: 1"
                        >
                            <div class="iSpIQi text-center" style="color:#00a884;">
                                <i class="ti ti-corner-up-right fs-16 me-2" style="color:#00a884;"></i>
                                <span style="color:#00a884;">{{ item.button_name || '...' }}</span>
                            </div>
                        </div>
                    </div>
                </div>




            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button
                type="submit"
                class="btn btn-primary"
                :disabled="loader.submit"
            >
                <i class="ti ti-device-floppy fs-16 me-1"></i
                >{{
                    loader.submit
                        ? "Loading...."
                        : type.id == null
                        ? "Add Template"
                        : "Save Changes"
                }}
            </button>
        </div>
    </form>
</template>

<script>
export default {
    components: {},
    data() {
        return {
            type: {
                form: "create",
                device: "",
                id: null,
            },
            form: {
                name: "",
                category: "UTILITY",
                lang: "",
                type: "text",
                header_text: "",
                body_message: "",
                footer_message: "",
                files: null,
                variables: [],
                buttons: [],
            },
            file: null,
            maxform: {
                call: 1,
                web: 2,
                copy: 1,
                custom: 3,
                wa_call: 1,
            },
            loader: {
                submit: false,
            },
        };
    },
    computed: {
        formattedBodyText() {
            if (!this.form.body_message) return '';
            let text = this.form.body_message;
            text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            text = text.replace(/\*(.*?)\*/g, '<strong>$1</strong>');
            text = text.replace(/_(.*?)_/g, '<em>$1</em>');
            text = text.replace(/~(.*?)~/g, '<del>$1</del>');
            text = text.replace(/```([\s\S]*?)```/g, '<code>$1</code>');
            text = text.replace(/\r\n/g, '<br>').replace(/\n/g, '<br>').replace(/\r/g, '<br>');
            return text;
        },
        formattedHeaderText() {
            if (!this.form.header_text) return '';
            let text = this.form.header_text;
            text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            text = text.replace(/\*(.*?)\*/g, '<strong>$1</strong>');
            text = text.replace(/_(.*?)_/g, '<em>$1</em>');
            return text;
        },
        bodyCharCount() {
            return this.form.body_message ? this.form.body_message.length : 0;
        },
        footerCharCount() {
            return this.form.footer_message ? this.form.footer_message.length : 0;
        }
    },
    methods: {
        slugifyName() {
            this.form.name = this.form.name
                .toLowerCase()
                .replace(/\s+/g, '_')
                .replace(/[^a-z0-9_]/g, '');
        },
        wrapBody(before, after) {
            const ta = document.getElementById('bodyMessageTextarea');
            if (!ta) return;
            const start = ta.selectionStart;
            const end   = ta.selectionEnd;
            const sel   = this.form.body_message.substring(start, end) || 'teks';
            this.form.body_message =
                this.form.body_message.substring(0, start) +
                before + sel + after +
                this.form.body_message.substring(end);
            this.$nextTick(() => {
                ta.focus();
                ta.setSelectionRange(start + before.length, start + before.length + sel.length);
            });
        },
        addVariabled() {
            const ta = document.getElementById('bodyMessageTextarea');
            if (!ta) return;

            // Always use length+1 for next variable (since watch now tracks correctly)
            const nextNum = this.form.variables.length + 1;
            const nextVariable = `{{${nextNum}}}`;
            
            const currentBody = this.form.body_message || '';
            
            // Insert at cursor position (like wrapBody)
            const start = ta.selectionStart !== undefined ? ta.selectionStart : currentBody.length;
            const end   = ta.selectionEnd !== undefined ? ta.selectionEnd : currentBody.length;
            
            // Add space before variable if needed
            const prefix = start > 0 && currentBody[start - 1] !== ' ' ? ' ' : '';
            
            this.form.body_message = 
                currentBody.substring(0, start) +
                prefix + nextVariable +
                currentBody.substring(end);

            this.$nextTick(() => {
                ta.focus();
                const newPos = start + prefix.length + nextVariable.length;
                ta.setSelectionRange(newPos, newPos);
            });
        },

        addButton(type) {
            // === Total max 3 buttons ===
            if (this.form.buttons.length >= 3) {
                alert('⚠️ Maksimal 3 tombol untuk satu template WhatsApp.');
                return false;
            }

            var max = this.maxform;
            const typeLabels = {
                call: 'Call Action',
                web: 'Visit Website',
                copy: 'Copy Code',
                custom: 'Quick Reply',
                wa_call: 'WA Call Number',
            };

            if (type == "call") {
                max = this.maxform.call;
            }

            if (type == "web") {
                max = this.maxform.web;
            }

            if (type == "copy") {
                max = this.maxform.copy;
            }

            if (type == "custom") {
                max = this.maxform.custom;
            }

            if (type == "wa_call") {
                max = this.maxform.wa_call;
            }

            var totalButton = this.form.buttons.filter(
                (item) => item.type === type
            ).length;

            if (totalButton >= max) {
                alert(`⚠️ Tombol "${typeLabels[type] || type}" maksimal ${max} untuk satu template.`);
                return false;
            }

            this.form.buttons.push({
                type: type,
                button_name: "",
                button_link: "",
                country: "",
                phone: "",
                text: "",
            });
        },

        removeButton(index) {
            this.form.buttons.splice(index, 1);
        },

        // === Button card helpers ===
        btnIcon(type) {
            if (!type) return '🔘';
            return { call: '📞', web: '🔗', wa_call: '📲', copy: '📋', custom: '⚡' }[type] || '🔘';
        },
        btnLabel(type) {
            if (!type) return '';
            return {
                call: 'Call Action — Telepon',
                web: 'Visit Website — URL',
                wa_call: 'WA Call Number',
                copy: 'Copy Code — Kode Promo',
                custom: 'Quick Reply'
            }[type] || type;
        },
        btnLimit(type) {
            if (!type) return '';
            return { call: 'Maks. 1', web: 'Maks. 2', wa_call: 'Maks. 1', copy: 'Maks. 1', custom: 'Maks. 3' }[type] || '';
        },
        btnHeaderStyle(type) {
            if (!type) return '';
            const s = {
                call:    'background:#e8f4ff; border-bottom:2.5px solid #0097b2; color:#005080;',
                web:     'background:#e8fff6; border-bottom:2.5px solid #20c997; color:#0d6e4a;',
                wa_call: 'background:#e8ffed; border-bottom:2.5px solid #25D366; color:#1a5e35;',
                copy:    'background:#fffbe8; border-bottom:2.5px solid #f59e0b; color:#a05000;',
                custom:  'background:#f0effb; border-bottom:2.5px solid #6366f1; color:#3730a3;',
            };
            return s[type] || '';
        },

        async handleSubmit() {
            // Validate buttons before submit (form.buttons are objects, not strings)
            try {
                for (const btn of this.form.buttons || []) {
                    if (btn.type === 'web' && !btn.button_link) {
                        this.showToast('URL tombol Visit Website tidak boleh kosong', 'warning');
                        return;
                    }
                    if ((btn.type === 'call' || btn.type === 'wa_call') && !btn.phone) {
                        this.showToast('Nomor telepon tombol tidak boleh kosong', 'warning');
                        return;
                    }
                    if (!btn.button_name) {
                        this.showToast('Label tombol tidak boleh kosong', 'warning');
                        return;
                    }
                }
            } catch(e) {
                // If validation fails unexpectedly, still allow submit
                console.warn('Button validation error:', e);
            }

            const formData = new FormData();

            for (const key in this.form) {
                if (key === "files" && this.form[key]) {
                    // Jika field adalah file
                    formData.append(key, this.form[key]);
                } else if (Array.isArray(this.form[key])) {
                    // Jika field adalah array (misalnya `variables` atau `buttons`)
                    this.form[key].forEach((item, index) => {
                        formData.append(
                            `${key}[${index}]`,
                            JSON.stringify(item)
                        );
                    });
                } else {
                    // Field lainnya
                    formData.append(key, this.form[key]);
                }
            }

            try {
                // Mendapatkan path lengkap dari URL
                this.loader.submit = true;
                const path = window.location.pathname;

                // Memecah path berdasarkan "/"
                const segments = path.split("/");

                // Mengambil segmen terakhir yang bukan string kosong
                const lastSegment = segments
                    .filter((segment) => segment !== "")
                    .pop();

                var url = `/waba/templates/store/${lastSegment}`;

                if (this.type.form == "update") {
                    var url = `/waba/templates/edit/${this.type.device}/${this.type.id}`;
                }
                const response = await this.$axios.post(url, formData);

                this.$showToast(response.data.message, "info", 3000);

                setTimeout(() => {
                    window.location.href = `/app/waba/templates/${this.type.device}`;
                }, 3000);
            } catch (error) {
                this.loader.submit = false;
                this.$showToast(error.response.data.message, "error", 3000);
            }
        },

        handleFileUpload(event) {
            this.form.files = event.target.files[0];
        },

        async getDetails() {
            try {
                const response = await this.$axios.get(
                    `/waba/templates/details/${this.type.device}/${this.type.id}`
                );

                var data = response.data;
                var params = data.details.components;
                this.form.name = data.meta.name;
                this.form.category = data.meta.category;
                this.form.lang = data.meta.lang;

                for (var i in params) {
                    var details = params[i];
                    if (details.type == "HEADER") {
                        if (details.format == "IMAGE") {
                            this.form.type = "image";
                        }

                        if (details.format == "VIDEO") {
                            this.form.type = "video";
                        }

                        if (details.format == "TEXT") {
                            this.form.type = "text";
                            this.form.header_text = details.text;
                        }

                        if (details.format == "DOCUMENT") {
                            this.form.type = "document";
                        }
                    }

                    if (details.type == "BODY") {
                        this.form.body_message = details.text;
                    }

                    if (details.type == "FOOTER") {
                        this.form.footer_message = details.text;
                    }

                    if (details.type == "BUTTONS") {
                        for (var x in details.buttons) {
                            var buttondetail = details.buttons[x];
                            if (buttondetail.type == "QUICK_REPLY") {
                                this.form.buttons.push({
                                    type: "custom",
                                    button_name: buttondetail.text,
                                    button_link: "",
                                    country: "",
                                    phone: "",
                                    text: "",
                                });
                            }

                            if (buttondetail.type == "URL") {
                                this.form.buttons.push({
                                    type: "web",
                                    button_name: buttondetail.text,
                                    url: buttondetail.url,
                                    button_link: buttondetail.url,
                                    country: "",
                                    phone: "",
                                    text: "",
                                });
                            }

                            if (buttondetail.type == "PHONE") {
                                this.form.buttons.push({
                                    type: "call",
                                    button_name: buttondetail.text,
                                    button_link: "",
                                    country_code: buttondetail.country || "62",
                                    phone_number: buttondetail.phone || "",
                                    country: buttondetail.country,
                                    phone: buttondetail.phone,
                                    text: "",
                                });
                            }

                            if (buttondetail.type == "COPY_CODE") {
                                this.form.buttons.push({
                                    type: "copy",
                                    button_name: buttondetail.text,
                                    example: buttondetail.example || "",
                                    button_link: "",
                                    country: "",
                                    phone: "",
                                    text: buttondetail.sample,
                                });
                            }
                        }
                    }
                }
            } catch (error) {
                this.$showToast(error.response.data.message, "error", 3000);
            }
        },
    },
    beforeDestroy() {},
    updated() {},
    mounted() {
        // Mendapatkan path lengkap dari URL
        const path = window.location.pathname;

        // Memecah path berdasarkan "/"
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
    },
    watch: {
        "form.body_message"(newValue) {
            // Ekstrak semua variabel dari body_message
            const variablePattern = /{{\d+}}/g;
            const extractedVariables = newValue.match(variablePattern) || [];

            // Filter variabel agar hanya menyimpan yang unik
            const uniqueVariables = [...new Set(extractedVariables)];

            // Update daftar variabel berdasarkan body_message
            this.form.variables = uniqueVariables.map((variable) => {
                const code = variable;
                const existingVariable = this.form.variables.find(
                    (v) => v.code === code
                );
                return {
                    code,
                    sample: existingVariable ? existingVariable.sample : "", // Pertahankan sample jika sudah ada
                };
            });
        },
        "form.category"(newValue) {
            if (newValue == "UTILITY") {
                this.form.buttons = this.form.buttons.filter(
                    (item) => item.type !== "copy"
                );
            }
        },
    },
};
</script>
<style>
/* === Button Cards - CreateTemplate === */
.btn-card {
    border: 1.5px solid #dee2e6;
    border-radius: 10px;
    padding: 10px 12px;
    background: #f8f9ff;
    cursor: pointer;
    transition: border-color 0.18s, background 0.18s, box-shadow 0.18s;
    position: relative;
    height: 100%;
    min-height: 80px;
}
.btn-card:hover {
    border-color: #00ceec;
    background: #eaf9ff;
    box-shadow: 0 2px 10px rgba(0,206,236,0.15);
}
.btn-card-green { border-color: #b7f5d0; background: #f0fff7; }
.btn-card-green:hover { border-color: #25D366 !important; background: #e0fff0 !important; box-shadow: 0 2px 10px rgba(37,211,102,0.15); }
.btn-card-yellow { border-color: #fde68a; background: #fffbf0; }
.btn-card-yellow:hover { border-color: #f39c12 !important; background: #fff8e1 !important; box-shadow: 0 2px 10px rgba(243,156,18,0.15); }
.btn-card-disabled {
    border: 1.5px dashed #ccc !important;
    background: #f5f5f5 !important;
    cursor: not-allowed !important;
    opacity: 0.6;
}
.btn-card-disabled:hover {
    border-color: #ccc !important;
    background: #f5f5f5 !important;
    box-shadow: none !important;
}
.btn-card-header {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 5px;
}
.btn-card-icon {
    border-radius: 6px;
    padding: 3px 7px;
    font-size: 12px;
    flex-shrink: 0;
}
.btn-card-title {
    font-weight: 700;
    font-size: 12px;
    color: #333;
    flex: 1;
}
.btn-card-badge {
    font-size: 10px;
    color: #888;
    background: #e9ecef;
    border-radius: 4px;
    padding: 1px 6px;
    white-space: nowrap;
}
.btn-card-desc {
    font-size: 10.5px;
    color: #777;
    margin: 0;
    line-height: 1.45;
}
.btn-card-soon {
    position: absolute;
    top: 7px;
    right: 8px;
    background: #6c757d;
    color: #fff;
    font-size: 9px;
    padding: 2px 7px;
    border-radius: 10px;
    font-weight: 700;
    letter-spacing: 0.3px;
}

/* === Button Detail Cards === */
.btn-detail-card {
    border: 1.5px solid #dee2e6;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 10px;
    background: #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.btn-detail-header {
    display: flex;
    align-items: center;
    padding: 9px 14px;
    gap: 8px;
    flex-wrap: nowrap;
}
.btn-detail-icon { font-size: 16px; flex-shrink: 0; }
.btn-detail-name { font-weight: 700; font-size: 12.5px; flex: 1; }
.btn-detail-limit {
    font-size: 10px; font-weight: 600;
    background: rgba(0,0,0,0.1); padding: 2px 8px;
    border-radius: 10px; white-space: nowrap;
}
.btn-detail-remove {
    background: transparent !important;
    background-color: transparent !important;
    border: 2px solid #dc3545 !important;
    color: #dc3545 !important;
    border-radius: 6px !important;
    padding: 3px 9px !important;
    cursor: pointer !important;
    line-height: 1 !important;
    font-size: 13px !important;
    transition: all 0.15s !important;
    flex-shrink: 0;
    box-shadow: none !important;
}
.btn-detail-remove:hover {
    background: #dc3545 !important;
    background-color: #dc3545 !important;
    color: #fff !important;
    border-color: #dc3545 !important;
}
.btn-detail-body { padding: 8px 12px; }
.btn-field-group { margin-bottom: 0; }
.btn-field-label {
    font-size: 11.5px; font-weight: 600;
    color: #444; margin-bottom: 4px;
    display: flex; align-items: center; gap: 4px;
}
.btn-field-hint { font-weight: 400; color: #888; font-size: 10.5px; }
.btn-field-input { font-size: 12.5px; height: 34px; padding: 4px 10px; }
.btn-field-counter { text-align: right; font-size: 10px; margin-top: 2px; }
.btn-field-hint-text { color: #888; font-size: 10.5px; margin-top: 3px; display: block; }
</style>
