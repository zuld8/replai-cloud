<template>
    <!-- Crate Template -->
    <form id="myForm" @submit.prevent="handleSubmit" enctype="multipart/form-data" method="POST"
        class="col-lg-8 col-sm-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" :class="form.type_content == 'description'
                            ? 'active'
                            : ''
                            " @click="form.type_content = 'description'">
                            <i class="tf-icons bx bx-text me-1"></i> {{ $t('text') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" :class="form.type_content == 'list' ? 'active' : ''"
                            @click="form.type_content = 'list'">
                            <i class="tf-icons bx bx-list-ul me-1"></i> {{ $t('list') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" :class="form.type_content == 'button' ? 'active' : ''
                            " @click="form.type_content = 'button'">
                            <i class="tf-icons bx bx-link-external me-1"></i>
                            {{ $t('button') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" :class="form.type_content == 'location' ? 'active' : ''
                            " @click="form.type_content = 'location'">
                            <i class="tf-icons bx bx-map me-1"></i>
                            {{ $t('location') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" :class="form.type_content == 'vote' ? 'active' : ''"
                            @click="form.type_content = 'vote'">
                            <i class="tf-icons bx bx-poll me-1"></i>
                            {{ $t('polling') }}
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div>
                    <div class="tab-pane active show">
                        <div class="row">
                            <div class="col-12 mb-1" v-if="form.type_content == 'button'">
                                <div
                                    style="border: 1px solid #ffc107; padding: 12px; border-radius: 4px; background-color: #fffbe6;">
                                    <p style="margin: 0; font-weight: bold;">{{ $t('warning') }}</p>
                                    <p style="margin: 0;">{{ $t('button_warning_message') }}</p>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">{{ $t('insert_name') }}</label>
                                <input class="form-control" v-model="form.name" type="text" required />
                            </div>

                            <div class="col-12 mb-3 mt-3" v-if="
                                form.type_content != 'location' &&
                                form.type_content != 'vote' &&
                                form.type_content != 'template' &&
                                form.type_content != 'list'
                            ">
                                <label class="form-label">{{ $t('header') }}</label>
                                <small>{{ $t('header_description') }}</small>

                                <nav class="nav nav-tabs nav-justified d-sm-flex d-block">
                                    <a class="nav-link m-2 text-center" :class="form.media_type == 'text'
                                        ? 'active'
                                        : ''
                                        " style="
                                            border: 1px solid #00ceec;
                                            border-radius: 6px;
                                            display: flow;
                                        " @click="form.media_type = 'text'" href="javascript:void(0);"><i
                                            class="ti ti-text-plus fs-16 me-2"></i>{{ $t('text') }}</a>
                                    <a class="nav-link m-2 text-center" :class="form.media_type == 'image'
                                        ? 'active'
                                        : ''
                                        " style="
                                            border: 1px solid #00ceec;
                                            border-radius: 6px;
                                            display: flow;
                                        " @click="form.media_type = 'image'" href="javascript:void(0);"><i
                                            class="ti ti-photo fs-16 me-2"></i>{{ $t('image') }}</a>
                                    <a class="nav-link m-2 text-center" :class="form.media_type == 'video'
                                        ? 'active'
                                        : ''
                                        " style="
                                            border: 1px solid #00ceec;
                                            border-radius: 6px;
                                            display: flow;
                                        " @click="form.media_type = 'video'" href="javascript:void(0);"><i
                                            class="ti ti-video fs-16 me-2"></i>{{ $t('video') }}
                                    </a>
                                    <a class="nav-link m-2 text-center" :class="form.media_type == 'document'
                                        ? 'active'
                                        : ''
                                        " style="
                                            border: 1px solid #00ceec;
                                            border-radius: 6px;
                                            display: flow;
                                        " @click="form.media_type = 'document'" href="javascript:void(0);"><i
                                            class="ti ti-file fs-16 me-2"></i>{{ $t('document') }}
                                    </a>
                                    <a v-if="
                                        form.type_content == 'description'
                                    " class="nav-link m-2 text-center" :class="form.media_type == 'audio'
                                        ? 'active'
                                        : ''
                                        " style="
                                            border: 1px solid #00ceec;
                                            border-radius: 6px;
                                            display: flow;
                                        " @click="form.media_type = 'audio'" href="javascript:void(0);"><i
                                            class="ti ti-music fs-16 me-2"></i>{{ $t('audio') }}</a>
                                </nav>
                            </div>

                            <div class="col-12 mb-4" v-if="form.type_content == 'list'">
                                <label class="form-label">{{ $t('list_title') }}</label>
                                <input class="form-control" v-model="form.list.title"
                                    :placeholder="$t('placeholder_title')" type="text" />
                            </div>

                            <div class="col-12 mb-4" v-if="form.type_content == 'list'">
                                <label class="form-label">{{ $t('button_name') }}</label>
                                <input class="form-control" v-model="form.list.button_name"
                                    :placeholder="$t('placeholder_button_name')" type="text" />
                            </div>

                            <div class="col-12 mb-4" v-if="form.type_content == 'vote'">
                                <label class="form-label">{{ $t('question') }}</label>
                                <input class="form-control" v-model="form.list.title"
                                    :placeholder="$t('placeholder_question')" type="text" />
                            </div>

                            <div class="col-lg-6 col-sm-12 mb-4" v-if="form.type_content == 'location'">
                                <label class="form-label">{{ $t('latitude') }}</label>
                                <input class="form-control" v-model="form.lang"
                                    :placeholder="$t('placeholder_latitude')" type="text" />
                            </div>

                            <div class="col-lg-6 col-sm-12 mb-4" v-if="form.type_content == 'location'">
                                <label class="form-label">{{ $t('longitude') }}</label>
                                <input class="form-control" v-model="form.long"
                                    :placeholder="$t('placeholder_longitude')" type="text" />
                            </div>

                            <div class="col-12 mb-4" v-if="
                                form.media_type != '' &&
                                form.media_type != null &&
                                form.media_type != 'text'
                            ">
                                <label class="form-label headerlabel">{{ $t('upload_media') }}
                                </label>
                                <input class="form-control" type="file" @change="handleFileUpload" id="fileInput" />
                            </div>

                            <div class="col-12 mb-4" v-if="
                                form.type_content != 'location' &&
                                form.type_content != 'vote' &&
                                form.media_type != 'audio'
                            ">
                                <label class="form-label">{{ $t('insert_body_message') }}</label>
                                <textarea class="form-control" style="height: 200px" v-model="form.body_message"
                                    required></textarea>
                            </div>

                            <div class="col-12 mb-4" v-if="
                                form.type_content == 'list' ||
                                form.type_content == 'button'
                            ">
                                <label class="form-label">{{ $t('footer_text') }}</label>
                                <input class="form-control" v-model="form.footer_message"
                                    :placeholder="$t('placeholder_footer')" type="text" />
                            </div>

                            <div class="col-12 row">
                                <div class="col-6 mb-2" v-if="form.type_content == 'list'">
                                    <button class="btn btn-primary add-call w-100" type="button" data-type="call"
                                        @click="addButton('list')">
                                        {{ $t('add_list') }}
                                    </button>
                                </div>
                                <div class="col-6 mb-2" v-if="form.type_content == 'button'">
                                    <button class="btn btn-primary add-web w-100" type="button" data-type="web"
                                        @click="addButton('button')">
                                        {{ $t('add_button') }}
                                    </button>
                                </div>
                                <div class="col-6 mb-2" v-if="form.type_content == 'vote'">
                                    <button class="btn btn-primary add-copy w-100" type="button" data-type="copy"
                                        @click="addButton('vote')">
                                        {{ $t('add_option') }}
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 row p-0">
                                <!-- For List Buttons -->
                                <div class="col-12 callbutton mt-2" v-if="form.type_content == 'list'"
                                    v-for="(listItem, li) in form.list.sections" :key="li">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th colspan="2">
                                                    {{ $t('list_section') }}
                                                </th>
                                                <th class="d-flex justify-content-end">
                                                    <button type="button" class="btn btn-sm btn-outline btn-info me-2"
                                                        @click="addList(li)">
                                                        <i class="bx bx-plus-circle fs-16 text-gray"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline btn-secondary"
                                                        @click="
                                                            removeButton(
                                                                li,
                                                                'list'
                                                            )
                                                            ">
                                                        <i class="ti ti-x fs-16 text-gray"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="3">
                                                    <input class="form-control" type="text"
                                                        :placeholder="$t('section_name')" required
                                                        v-model="listItem.title" />
                                                </td>
                                            </tr>
                                            <tr v-for="(segment, s) in listItem.rows" :key="s">
                                                <td>
                                                    <label class="form-label">{{ $t('list_name') }}</label>
                                                    <input class="form-control" type="text" required
                                                        v-model="segment.title" />
                                                </td>
                                                <td>
                                                    <label class="form-label">{{ $t('list_description') }}</label>
                                                    <input class="form-control" type="text" v-model="segment.description
                                                        " />
                                                </td>
                                                <td class="align-items-center" style="
                                                        vertical-align: middle;
                                                    " v-if="s != 0">
                                                    <button type="button" class="btn btn-sm btn-outline btn-danger"
                                                        @click="
                                                            removeList(li, s)
                                                            ">
                                                        <i class="ti ti-x fs-16 text-gray"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- End For List -->

                                <!-- Button -->
                                <div class="col-12 callbutton mt-2" v-if="form.type_content == 'button'"
                                    v-for="(item, index) in form.buttons" :key="index">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th colspan="2">
                                                    {{ $t('custom_button') }}
                                                </th>
                                                <th class="d-flex justify-content-end">
                                                    <button type="button" class="btn btn-sm btn-outline btn-secondary"
                                                        @click="
                                                            removeButton(
                                                                index,
                                                                'button'
                                                            )
                                                            ">
                                                        <i class="ti ti-x fs-16 text-gray"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="3">
                                                    <label class="form-label">{{ $t('button_text') }}</label>
                                                    <input class="form-control" type="text" required v-model="item.button_name
                                                        " />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- End Button -->

                                <!-- Options -->
                                <div class="col-12 callbutton mt-2" v-if="form.type_content == 'vote'"
                                    v-for="(option, o) in form.options" :key="o">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th colspan="2">
                                                    {{ $t('answer_options') }}
                                                </th>
                                                <th class="d-flex justify-content-end">
                                                    <button type="button" class="btn btn-sm btn-outline btn-secondary"
                                                        @click="
                                                            removeButton(
                                                                o,
                                                                'vote'
                                                            )
                                                            ">
                                                        <i class="ti ti-x fs-16 text-gray"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="3">
                                                    <label class="form-label">{{ $t('option') }}</label>
                                                    <input class="form-control" type="text" required
                                                        v-model="option.name" />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- End Options -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" :disabled="loader.submit">
                    <i class="ti ti-device-floppy fs-16 me-1"></i>{{
                        loader.submit
                            ? $t('loading')
                            : type.id == null
                                ? $t('add_template')
                                : $t('save_changes')
                    }}
                </button>
            </div>
        </div>
    </form>
    <!-- End Create -->

    <!-- Preview Template -->
    <div class="col-lg-4 col-sm-12 whatsapp-chat-body"
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
                <div class="bMIBDo">{{ $t('preview_user') }}</div>
                <div class="iSpIQi text-center" v-if="form.media_type !== 'text'"
                    style="padding: 50px; background-color: darkgrey">

                    <!-- Preview Gambar -->
                    <div v-if="form.media_type === 'image'">
                        <img v-if="form.media.url" :src="form.media.url" alt="Image Preview"
                            style="max-width: 100%; max-height: 200px;" />
                        <i v-else class="ti ti-photo-plus text-white icon-image" style="font-size: 50px"></i>
                    </div>

                    <!-- Preview Video -->
                    <div v-else-if="form.media_type === 'video'">
                        <video v-if="form.media.url" controls style="max-width: 100%; max-height: 200px;">
                            <source :src="form.media.url" type="video/mp4">
                            {{ $t('browser_not_support_video') }}
                        </video>
                        <i v-else class="ti ti-video text-white icon-video" style="font-size: 50px"></i>
                    </div>

                    <!-- Ikon Dokumen -->
                    <div v-else-if="form.media_type === 'document'">
                        <i class="ti ti-file-type-pdf text-white icon-document" style="font-size: 50px"></i>
                    </div>

                    <!-- Ikon Audio -->
                    <div v-else-if="form.media_type === 'audio'">
                        <i class="ti ti-music text-white icon-document" style="font-size: 50px"></i>
                    </div>

                </div>

                <div class="iSpIQi text-center" v-if="form.type_content == 'location'"
                    style="padding: 50px; background-color: darkgrey">
                    <i class="ti ti-map-pin text-center text-white icon-image" style="font-size: 50px"></i>
                </div>
                <div class="iSpIQi" v-if="
                    (form.type_content == 'list' &&
                        form.list.title != '' &&
                        form.list.title != null) ||
                    (form.type_content == 'button' &&
                        form.list.title != '' &&
                        form.list.title != null)
                ">
                    {{ form.list.title }}
                </div>
                <div class="iSpIQi" v-if="
                    form.type_content == 'vote' &&
                    form.list.title != '' &&
                    form.list.title != null
                ">
                    {{ form.list.title }}
                </div>
                <div class="iSpIQe" v-if="
                    form.type_content != 'location' &&
                    form.type_content != 'vote' &&
                    form.media_type != 'audio' &&
                    form.body_message != ''
                ">
                    {{ form.body_message }}
                </div>
                <div class="iSpIQe mt-2" v-if="
                    form.type_content == 'vote' && form.options.length > 0
                ">
                    <div>
                        <label class="form-check" v-for="(option, o) in form.options">
                            <input class="form-check-input" type="radio" name="radios" />
                            <span class="form-check-label">{{
                                option.name
                                }}</span>
                        </label>
                    </div>
                </div>
                <div class="iSpIQd" v-if="
                    (form.type_content == 'list' &&
                        form.footer_message != '') ||
                    (form.type_content == 'button' &&
                        form.footer_message != '')
                ">
                    {{ form.footer_message }}
                </div>
                <div class="cqCDVm">{{ $t('preview_time') }}</div>
            </div>
        </div>
        <div id="listButton">
            <div class="whatsapp-chat-bubble phone-call" v-if="form.type_content == 'list'">
                <div class="whatsapp-chat-message-loader" style="opacity: 0">
                    <div style="position: relative; display: flex">
                        <div class="ixsrax"></div>
                        <div class="dRvxoz"></div>
                        <div class="kXBtNt"></div>
                    </div>
                </div>
                <div class="whatsapp-chat-button text-info" style="opacity: 1">
                    <div class="iSpIQi text-center">
                        <i class="ti ti-list fs-16 me-2"></i>
                        <span>{{ form.list.button_name }}</span>
                    </div>
                </div>
            </div>
            <div class="whatsapp-chat-bubble phone-call" v-if="form.type_content == 'button'"
                v-for="(item, index) in form.buttons">
                <div class="whatsapp-chat-message-loader" style="opacity: 0">
                    <div style="position: relative; display: flex">
                        <div class="ixsrax"></div>
                        <div class="dRvxoz"></div>
                        <div class="kXBtNt"></div>
                    </div>
                </div>
                <div class="whatsapp-chat-button text-info" style="opacity: 1">
                    <div class="iSpIQi text-center">
                        <span>{{ item.button_name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Preview -->

    <!-- Modal For Information -->
    <div class="modal fade" id="modalInformation" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modalInformationLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="modalInformationLabel">
                        {{ $t('applicable_parameters') }}
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row p-2">
                    <div class="col-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2" class="text-center">
                                        {{ $t('parameter_list_title') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <b>{whatsapp_name}</b>
                                    </td>
                                    <td>
                                        {{ $t('whatsapp_name') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>{store}</b>
                                    </td>
                                    <td>
                                        {{ $t('store_name') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>{phone}</b>
                                    </td>
                                    <td>{{ $t('phone') }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>{email}</b>
                                    </td>
                                    <td>{{ $t('email') }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>{address}</b>
                                    </td>
                                    <td>{{ $t('address') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Information -->
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
                type_content: "description",
                media_type: "text",
                image: null,
                media: {
                    type: "",
                    url: null
                },
                header_text: "",
                body_message: "",
                footer_message: "",
                image: "",
                lang: "",
                long: "",
                buttons: [],
                options: [],
                list: {
                    title: "",
                    button_name: "",
                    sections: [],
                },
            },
            file: null,
            maxform: {
                call: 1,
                web: 2,
                copy: 1,
                custom: 6,
            },
            loader: {
                submit: false,
            },
        };
    },
    computed: {},
    methods: {

        $t(key) {
            if (window.i18n && window.i18n.translations && window.i18n.translations[key]) {
                return window.i18n.translations[key];
            }
            return key;
        },
        
        addButton(type) {
            var max = this.maxform;

            if (type == "list") {
                this.form.list.sections.push({
                    title: "",
                    rows: [
                        {
                            title: "",
                            description: "",
                        },
                    ],
                });
            }

            if (type == "button") {
                this.form.buttons.push({
                    button_name: "",
                });
            }

            if (type == "vote") {
                this.form.options.push({
                    name: "",
                });
            }
        },

        addList(index) {
            this.form.list.sections[index].rows.push({
                title: "",
                description: "",
            });
        },

        removeList(index, item) {
            this.form.list.sections[index].rows.splice(item, 1);
        },

        removeButton(index, type) {
            if (type == "list") {
                this.form.list.sections.splice(index, 1);
            }

            if (type == "button") {
                this.form.buttons.splice(index, 1);
            }

            if (type == "vote") {
                this.form.options.splice(index, 1);
            }
        },

        async handleSubmit() {
            const formData = new FormData();

            for (const key in this.form) {
                if (key === "image" && this.form[key]) {
                    formData.append(
                        key,
                        this.form.image == null || this.form.image == ""
                            ? null
                            : this.form[key]
                    );
                } else if (Array.isArray(this.form[key])) {
                    this.form[key].forEach((item, index) => {
                        if (key === "buttons") {
                            formData.append(
                                `${key}[${index}][button_name]`,
                                item.button_name
                            );
                        } else if (key === "options") {
                            formData.append(
                                `${key}[${index}][name]`,
                                item.name
                            );
                        } else {
                            formData.append(
                                `${key}[${index}]`,
                                JSON.stringify(item)
                            );
                        }
                    });
                } else if (
                    key === "list" &&
                    typeof this.form[key] === "object"
                ) {
                    for (const subKey in this.form[key]) {
                        if (
                            subKey === "sections" &&
                            Array.isArray(this.form[key][subKey])
                        ) {
                            this.form[key][subKey].forEach(
                                (section, sectionIndex) => {
                                    formData.append(
                                        `${key}[${subKey}][${sectionIndex}][title]`,
                                        section.title
                                    );

                                    if (Array.isArray(section.rows)) {
                                        section.rows.forEach(
                                            (row, rowIndex) => {
                                                for (const rowKey in row) {
                                                    formData.append(
                                                        `${key}[${subKey}][${sectionIndex}][rows][${rowIndex}][${rowKey}]`,
                                                        row[rowKey]
                                                    );
                                                }
                                            }
                                        );
                                    }
                                }
                            );
                        } else {
                            formData.append(
                                `${key}[${subKey}]`,
                                this.form[key][subKey]
                            );
                        }
                    }
                } else {
                    formData.append(key, this.form[key]);
                }
            }

            try {
                this.loader.submit = true;

                var url = `/master/templates/store`;

                if (this.type.form == "update") {
                    var url = `/master/templates/edit/${this.type.id}`;
                }
                const response = await this.$axios.post(url, formData);

                this.$showToast(response.data.message, "info", 3000);

                setTimeout(() => {
                    window.location.href = `/app/master/templates`;
                }, 3000);
            } catch (error) {
                this.loader.submit = false;
                this.$showToast(error.response.data.message, "error", 3000);
            }
        },

        handleFileUpload(event) {
            this.form.image = event.target.files[0];
        },

        async getDetails() {
            try {
                const response = await this.$axios.get(
                    `/master/templates/details/${this.type.id}`
                );

                var data = response.data;
                this.form = data.details;
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

        if (segments.length == 5) {
            this.type = {
                form: "create",
                device: "",
                id: null,
            };
        } else {
            this.type = {
                form: "update",
                device: "",
                id: lastSegment,
            };

            this.getDetails();
        }
    },
    watch: {
        "form.type_content"(newValue) {
            if (newValue != "description" && this.form.media_type == "audio") {
                this.form.media_type = "text";
            }

            if (
                newValue == "location" ||
                newValue == "vote" ||
                newValue == "list"
            ) {
                this.form.media_type = "text";
            }
        },
    },
};
</script>
