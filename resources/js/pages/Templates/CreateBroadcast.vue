<template>
    <form id="myForm" @submit.prevent="handleSubmit" enctype="multipart/form-data" method="POST"
        class="col-lg-10 col-sm-12">
        <div class="card-body row">
            <div class="col-lg-7 col-sm-12">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Broadcast Name</label>
                        <input class="form-control" v-model="form.name" type="text" required />
                    </div>

                    <div class="col-lg-6 col-sm-12 mb-3">
                        <label class="form-label">Category Contact</label>
                        <select class="form-control" v-model="form.category" @change="onCategoryChange" required>
                            <option v-for="(category, i) in categories" :key="i" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <!-- Contact Count -->
                        <small v-if="loadingCount" class="text-muted mt-1 d-block" style="font-size:11px;">
                            <span class="spinner-border spinner-border-sm" style="width:10px;height:10px;"></span> Menghitung...
                        </small>
                        <small v-else-if="categoryCount !== null" class="mt-1 d-block" style="font-size:11px;color:#16a34a;">
                            <i class="ti ti-users" style="font-size:12px;"></i> {{ categoryCount.toLocaleString('id-ID') }} kontak tertarget
                        </small>
                    </div>

                    <div class="col-lg-6 col-sm-12 mb-3">
                        <label class="form-label">Choose Template</label>
                        <select class="form-control" @change="changeTemplate" v-model="form.template" required>
                            <option v-for="(template, t) in templates" :key="t" :value="template.id">
                                {{ template.name }}
                            </option>
                        </select>
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
                                class="badge" style="background:#e8f0fe;color:#1d6fb8;font-size:11px;">
                                <i class="ti ti-photo me-1"></i>JPG, PNG &bull; Maks 5 MB
                            </span>
                            <span v-if="form.metadata.header.format == 'VIDEO'"
                                class="badge" style="background:#f3e8ff;color:#7c3aed;font-size:11px;">
                                <i class="ti ti-video me-1"></i>MP4, 3GP &bull; Maks 16 MB
                            </span>
                            <span v-if="form.metadata.header.format == 'DOCUMENT'"
                                class="badge" style="background:#fff3e0;color:#d4820a;font-size:11px;">
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
                                    <small class="text-muted">{{ (form.files.size / 1024 / 1024).toFixed(2) }} MB</small>
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
                        <h3 class="form-label text-dark">Body Variable</h3>
                        <table class="table">
                            <tr v-for="(item, index) in form.metadata.body
                                .parameters" :key="index">
                                <td>
                                    <input type="text" class="form-control text-dark mt-2" v-model="item.value"
                                        required />
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-lg-6 col-sm-12 mb-3">
                        <label class="form-label">Schedule Campaign</label>
                        <input class="form-control" v-model="form.schedule" type="datetime-local" required />
                    </div> 

                    <div class="col-lg-6 col-sm-12 mb-3">
                        <label class="form-label">Pilih Device</label>
                        <select class="form-control devices" multiple v-model="form.devices" ref="deviceSelect">
                            <option v-for="(device, d) in devices" :key="d" :value="device.id">
                                {{ device.phone }}
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mb-3">
                        <label class="form-label">Opsi Penggunaan Device</label>
                        <select class="form-control" v-model="form.whatsapp_sender" required>
                            <option value="sequence">Single Device</option>
                            <option value="spin">AI Choose (Spin)</option>
                            <option value="random">Random (Acak)</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mb-3">
                        <label class="form-label">Delay Per Message</label>
                        <input class="form-control" v-model="form.delay" type="number" required />
                    </div>

                    <div class="col-lg-6 col-sm-12 mb-3">
                        <label class="form-label">Stop Sending After</label>
                        <input class="form-control" v-model="form.stop_sending" type="number" required />
                    </div>

                    <div class="col-lg-6 col-sm-12 mb-3">
                        <label class="form-label">Jeda Antar Pengriman (Detik)</label>
                        <input class="form-control" v-model="form.rest_sending" type="number" required />
                    </div>


                </div>
            </div>
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
        <div class="card-footer d-flex justify-content-end">
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
    </form>
</template>

<style scoped>
.upload-drop-zone {
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    min-height: 85px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fafafa;
    user-select: none;
}
.upload-drop-zone:hover {
    border-color: #206bc4;
    background: rgba(32, 107, 196, 0.04);
}
.upload-drop-zone--dragging {
    border-color: #206bc4;
    background: rgba(32, 107, 196, 0.08);
    transform: scale(1.01);
}
.upload-drop-zone--success {
    border-color: #2fb344;
    background: rgba(47, 179, 68, 0.05);
    border-style: solid;
}
.upload-drop-zone--error {
    border-color: #d63939;
    background: rgba(214, 57, 57, 0.04);
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
                whatsapp_sender: 'random',
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
            file: null,
            isDragging: false,
            fileError: null,
            categoryCount: null,
            loadingCount: false,
            loader: {
                submit: false,
            },
        };
    },
    computed: {
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
            // Core required fields
            if (!f.name || !f.name.trim())           return false;
            if (!f.category)                          return false;
            if (!f.template)                          return false;
            if (!f.schedule)                          return false;
            if (!f.devices || f.devices.length === 0) return false;
            // Media file required if template has media header
            if (this.isMediaRequired && !f.files)    return false;
            // No file error
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
                    if (details.example != undefined && details.example.body_text && details.example.body_text[0] && details.example.body_text[0].length > 0) {
                        // Use example values from Meta (preferred)
                        var bodyVar = details.example.body_text[0];
                        parameters = bodyVar.map((value) => ({
                            type: "text",
                            selection: "static",
                            value: value,
                        }));
                    } else if (details.text) {
                        // Fallback: parse unique {{n}} placeholders from body text
                        var matches = details.text.match(/\{\{(\d+)\}\}/g) || [];
                        var uniqueNums = [...new Set(matches.map(m => m.replace(/[{}]/g, '')))];
                        uniqueNums.sort((a, b) => parseInt(a) - parseInt(b));
                        parameters = uniqueNums.map((num) => ({
                            type: "text",
                            selection: "static",
                            value: "",
                        }));
                    }

                    body = {
                        text: details.text,
                        parameters: parameters,
                    };
                }

                if (details.type == "FOOTER") {
                    footer = {
                        text: details.text,
                    };
                }

                if (details.type == "BUTTONS") {
                    for (var x in details.buttons) {
                        var buttondetail = details.buttons[x];
                        if (buttondetail.type == "QUICK_REPLY") {
                            buttons.push({
                                type: "QUICK_REPLY",
                                text: buttondetail.text,
                                value: "",
                            });
                        }

                        if (buttondetail.type == "URL") {
                            buttons.push({
                                type: buttondetail.type,
                                text: buttondetail.text,
                                value: buttondetail.url,
                            });
                        }

                        if (buttondetail.type == "PHONE") {
                            buttons.push({
                                type: buttondetail.type,
                                text: buttondetail.text,
                                value: buttondetail.phone,
                            });
                        }

                        if (buttondetail.type == "COPY_CODE") {
                            buttons.push({
                                type: buttondetail.type,
                                text: buttondetail.text,
                                value: buttondetail.sample,
                            });
                        }
                    }
                }
            }

            // Assemble the final structure
            const transformedData = {
                header,
                body,
                footer,
                buttons,
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
            } catch (error) {
                this.$showToast(error.response.data.message, "error", 3000);
            }
        },

        async handleSubmit() {
            // Validate: media header requires file upload
            const headerFormat = this.form.metadata.header.format;
            if (['IMAGE', 'VIDEO', 'DOCUMENT'].includes(headerFormat) && !this.form.files) {
                this.$showToast(`Harap upload file ${headerFormat.toLowerCase()} untuk header template ini`, 'error', 4000);
                return;
            }
            const formData = new FormData();

            for (const key in this.form) {
                if (key === "files" && this.form[key]) {
                    // Jika field adalah file
                    formData.append(key, this.form[key]);
                } else if (Array.isArray(this.form[key])) {
                    // Jika field adalah array (misalnya `variables` atau `buttons`)
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
                    // Pastikan metadata dikonversi menjadi JSON string
                    formData.append(key, JSON.stringify(this.form[key]));
                } else {
                    // Field lainnya
                    formData.append(key, this.form[key]);
                }
            }

            try {
                this.loader.submit = true;
                // Mendapatkan path lengkap dari URL
                const path = window.location.pathname;

                // Memecah path berdasarkan "/"
                const segments = path.split("/");

                // Mengambil segmen terakhir yang bukan string kosong
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

        initSelect2() {
            if (this.$refs.deviceSelect) {
                // Hapus Select2 sebelumnya jika sudah terpasang
                $(this.$refs.deviceSelect).off().select2('destroy');

                $(this.$refs.deviceSelect).select2({
                    placeholder: 'Pilih Device',
                    width: '100%',
                });

                // Sinkronkan ke form.devices
                $(this.$refs.deviceSelect).on('change', (e) => {
                    this.form.devices = $(e.target).val(); // akan jadi array of string
                });
            }
        },

        async getDevices() {
            try {
                const response = await this.$axios.get(`/waba/devices/${this.type.device}`);
                this.devices = response.data.data;
            } catch (error) {
                this.$showToast(error.response.data.message, "error", 3000);
            }
        },
    },
    beforeDestroy() { },
    updated() { },
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

        this.getCategories();
        this.getTemplates();
        this.getDevices();
    },
    watch: {
        devices(newVal) {
            this.$nextTick(() => {
                this.initSelect2();
            });
        },
    },
};
</script>
