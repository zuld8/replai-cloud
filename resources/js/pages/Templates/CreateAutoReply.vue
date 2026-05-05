<template>
    <form id="myForm" @submit.prevent="handleSubmit" enctype="multipart/form-data" method="POST"
        class="col-lg-10 col-sm-12">
        <div class="card-body row">
            <div class="col-lg-7 col-sm-12">
                <div class="row"> 
                    <div class="col-12 mb-3">
                        <label class="form-label">Masukkan Kata Kunci (Pisahkan dengan Koma)</label>
                        <input class="form-control" v-model="form.keyword" type="text" required />
                    </div>

                    <div class="col-12 mb-3" >
                        <label class="form-label">Pilih Device</label>
                        <select class="form-control devices" multiple v-model="form.devices" ref="deviceSelect">
                            <option v-for="(device, d) in devices" :key="d" :value="device.id">
                                {{ device.phone }}
                            </option>
                        </select>
                    </div>

                    <!-- <div class="col-12 mb-3">
                        <label class="form-label">Metode Balasan Chat</label>
                        <select class="form-control" @change="changeMethode" v-model="form.method" required>
                            <option value="text">Pesan Text</option>
                            <option value="template">Template Pesan</option>
                        </select>
                    </div> 

                    <div class="col-12 mb-3" v-if="form.method == 'template'">
                        <label class="form-label">Choose Template</label>
                        <select class="form-control" @change="changeTemplate" v-model="form.template" required>
                            <option v-for="(template, t) in templates" :key="t" :value="template.id">
                                {{ template.name }}
                            </option>
                        </select>
                    </div> -->

                    <div class="col-12 mb-3" v-if="form.method == 'text'">
                        <label class="form-label">Pesan WhatsApp</label>
                        <textarea class="form-control" v-model="form.metadata.body.text"></textarea>
                    </div>


                    <div class="col-lg-6 col-sm-12 mb-3" v-if="form.metadata.header.format != 'TEXT'">
                        <label class="form-label" v-if="form.metadata.header.format == 'IMAGE'">Upload Image ( Jpg | Png
                            Only)
                        </label>
                        <label class="form-label" v-if="form.metadata.header.format == 'VIDEO'">Upload Video ( mp4 Only
                            )
                        </label>
                        <label class="form-label" v-if="form.metadata.header.format == 'DOCUMENT'">Upload Pdf
                            File</label>
                        <input class="form-control" type="file" @change="handleFileUpload" id="fileInput" />
                    </div>

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
                        <div class="iSpIQi text-center" v-if="form.metadata.header.format != 'TEXT'"
                            style="padding: 50px; background-color: darkgrey">
                            <i class="ti ti-photo-plus text-center text-white icon-image"
                                v-if="form.metadata.header.format == 'IMAGE'" style="font-size: 50px"></i>
                            <i class="ti ti-video text-center text-white icon-video"
                                v-if="form.metadata.header.format == 'VIDEO'" style="font-size: 50px"></i>
                            <i class="ti ti-file-type-pdf text-center text-white icon-document"
                                v-if="form.metadata.header.format == 'DOCUMENT'" style="font-size: 50px"></i>
                        </div>
                        <div class="iSpIQi" v-if="
                            form.metadata.header.format == 'TEXT' &&
                            form.metadata.header.text != '' &&
                            form.metadata.header.text != null
                        ">
                            {{ form.metadata.header.text }}
                        </div>
                        <div class="iSpIQi">{{ form.metadata.body.text }}</div>
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
            <button type="submit" class="btn btn-primary" :disabled="loader.submit">
                <i class="ti ti-device-floppy fs-16 me-1"></i>{{
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
            templates: [],
            devices: [],
            type: {
                form: "create",
                device: "",
                id: null,
            },
            form: {
                keyword: "",
                method: "text",
                template: "",
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
            loader: {
                submit: false,
            },
        };
    },
    computed: {},
    methods: {

        changeMethode() {
            if (this.form.method == 'text') {
                this.form.metadata = {
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
                }
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
                    if (details.example != undefined) {
                        var bodyVar = details.example.body_text?.[0] || [];

                        parameters = bodyVar.map((value) => ({
                            type: "text",
                            selection: "static",
                            value: value,
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
            this.form.files = event.target.files[0];
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

        async getDevices() {
            try {


                const response = await this.$axios.get(`/waba/devices/${this.type.device}`);
                this.devices = response.data.data;
            } catch (error) {
                this.$showToast(error.response.data.message, "error", 3000);
            }
        },

        async getDetails() {
            try {
                const response = await this.$axios.get(
                    `/waba/chatbot/details/${this.type.device}/${this.type.id}`
                );

                var data = response.data;
                this.form = data.detail;
            } catch (error) {
                this.$showToast(error.response.data.message, "error", 3000);
            }
        },

        async handleSubmit() {
            const formData = new FormData();

            for (const key in this.form) {
                if (key === "files" && this.form[key]) {
                    // Jika field adalah file
                    formData.append(key, this.form[key]);
                } else if (Array.isArray(this.form[key])) {
                    if (key == 'devices') {
                        console.log(key, this.form);
                        formData.append(key, this.form.devices);
                    } else {
                        this.form[key].forEach((item, index) => {
                            formData.append(`${key}[${index}]`, JSON.stringify(item)
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

                var url = `/waba/chatbot/store/${lastSegment}`;

                if (this.type.form == "update") {
                    var url = `/waba/chatbot/edit/${this.type.device}/${this.type.id}`;
                }
                const response = await this.$axios.post(url, formData);

                this.$showToast(response.data.message, "info", 3000);

                setTimeout(() => {
                    window.location.href = `/app/waba/chatbot/${this.type.device}`;
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
