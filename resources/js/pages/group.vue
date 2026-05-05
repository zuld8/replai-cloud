<template>
    <div class="chat chat-messages show" id="middle">
        <div>
            <div class="chat-header">
                <div class="user-details">
                    <div class="d-xl-none">
                        <a class="text-muted chat-close me-2" href="#">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="avatar avatar-lg online flex-shrink-0">
                        <img
                            :src="$route.query.photo"
                            class="rounded-circle"
                            alt="image"
                        />
                    </div>
                    <div class="ms-2 overflow-hidden">
                        <h6>{{ $route.query.name }}</h6>
                        <span class="last-seen">-</span>
                    </div>
                </div>
                <div class="chat-options">
                    <ul>
                        <li>
                            <a
                                href="javascript:void(0)"
                                class="btn chat-search-btn"
                                data-bs-toggle="tooltip"
                                data-bs-placement="bottom"
                                title="Search"
                            >
                                <i class="ti ti-search"></i>
                            </a>
                        </li>

                        <li>
                            <a
                                class="btn no-bg"
                                href="#"
                                data-bs-toggle="dropdown"
                            >
                                <i class="ti ti-dots-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-3">
                                <li>
                                    <router-link
                                        :to="{
                                            name: 'blank_chat',
                                            params: { id: $route.params.id },
                                        }"
                                        class="dropdown-item"
                                        ><i class="ti ti-x me-2"></i>Close
                                        Chat</router-link
                                    >
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="chat-search search-wrap contact-search">
                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control"
                            v-model="message.search"
                            placeholder="Search Message"
                        />
                        <span class="input-group-text"
                            ><i class="ti ti-search"></i
                        ></span>
                    </div>
                </div>
            </div>
            <div class="chat-body chat-page-group slimscroll">
                <div
                    v-if="message.loader"
                    class="d-flex justify-content-center"
                >
                    <div class="lds-roller">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
                <div
                    class="messages chat-area"
                    style="overflow: scroll; height: 80vh"
                >
                    <!-- List Message -->
                    <div
                        class="chats"
                        v-for="(list, index) in message.list.filter((item) =>
                            item.message.detail.text
                                .toLowerCase()
                                .includes(message.search.toLowerCase())
                        )"
                        :key="index"
                        :class="list.sender ? 'chats-right' : ''"
                    >
                        <div class="chat-avatar">
                            <img
                                :src="getPhotoPict(list.details.participant)"
                                class="rounded-circle"
                                alt="image"
                            />
                        </div>
                        <div class="chat-content">
                            <div
                                class="chat-profile-name d-flex justify-content-end"
                                :class="list.sender ? 'me-4' : 'ms-4'"
                            >
                                <h6>
                                    <span class="chat-time">{{
                                        list.time
                                    }}</span
                                    ><span
                                        class="msg-read success"
                                        v-if="list.status == 'READ'"
                                        ><i class="ti ti-checks"></i
                                    ></span>
                                </h6>
                            </div>
                            <div class="chat-info">
                                <div
                                    class="message-content"
                                    :class="
                                        list.message.type == 'audio' &&
                                        !list.sender
                                            ? 'bg-transparent p-0'
                                            : ''
                                    "
                                >
                                    <!-- Image not ready -->
                                    <div
                                        class="chat-img"
                                        v-if="list.message.type == 'image'"
                                    >
                                        <div class="img-wrap">
                                            <img
                                                :src="
                                                    list.message.detail.asset
                                                        ? list.message.detail
                                                              .url
                                                        : attribute.image
                                                "
                                                alt="img"
                                                :style="
                                                    !list.message.detail.asset
                                                        ? 'height: 100%'
                                                        : ''
                                                "
                                            />
                                            <div class="img-overlay">
                                                <a
                                                    class="ti ti-download fs-30"
                                                    href="javascript:void(0);"
                                                    v-if="
                                                        !list.message.detail
                                                            .asset
                                                    "
                                                    @click="
                                                        downloadMedia(
                                                            list.details,
                                                            list.id,
                                                            list.message.mime
                                                        )
                                                    "
                                                    ><i></i
                                                ></a>
                                                <a
                                                    v-else
                                                    class="gallery-img"
                                                    data-fancybox="gallery-img"
                                                    :href="
                                                        list.message.detail.url
                                                    "
                                                    :title="
                                                        list.message.detail.text
                                                    "
                                                    ><i class="ti ti-eye"></i
                                                ></a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Image not ready -->

                                    <!-- Video -->
                                    <div
                                        class="chat-img"
                                        v-if="
                                            list.message.type == 'video' &&
                                            !list.message.detail.asset
                                        "
                                    >
                                        <div class="img-wrap">
                                            <img
                                                :src="attribute.video"
                                                alt="img"
                                                :style="
                                                    !list.message.detail.asset
                                                        ? 'height: 100%'
                                                        : ''
                                                "
                                            />
                                            <div class="img-overlay">
                                                <a
                                                    class="ti ti-download fs-30"
                                                    href="javascript:void(0);"
                                                    v-if="
                                                        !list.message.detail
                                                            .asset
                                                    "
                                                    @click="
                                                        downloadMedia(
                                                            list.details,
                                                            list.id,
                                                            list.message.mime
                                                        )
                                                    "
                                                    ><i></i
                                                ></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="message-video"
                                        v-if="
                                            list.message.type == 'video' &&
                                            list.message.detail.asset
                                        "
                                    >
                                        <video controls="">
                                            <source
                                                :src="list.message.detail.url"
                                                type="video/mp4"
                                            />
                                        </video>
                                    </div>
                                    <!-- End Video -->

                                    <!-- Audio -->
                                    <div
                                        class="chat-img"
                                        v-if="
                                            list.message.type == 'audio' &&
                                            !list.message.detail.asset
                                        "
                                    >
                                        <div class="img-wrap">
                                            <img
                                                :src="attribute.audio"
                                                alt="img"
                                                :style="
                                                    !list.message.detail.asset
                                                        ? 'height: 100%'
                                                        : ''
                                                "
                                            />
                                            <div class="img-overlay">
                                                <a
                                                    class="ti ti-download fs-30"
                                                    href="javascript:void(0);"
                                                    v-if="
                                                        !list.message.detail
                                                            .asset
                                                    "
                                                    @click="
                                                        downloadMedia(
                                                            list.details,
                                                            list.id,
                                                            list.message.mime
                                                        )
                                                    "
                                                    ><i></i
                                                ></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="message-audio"
                                        v-if="
                                            list.message.type == 'audio' &&
                                            list.message.detail.asset
                                        "
                                    >
                                        <audio controls="">
                                            <source
                                                :src="list.message.detail.url"
                                                type="audio/mpeg"
                                            />
                                        </audio>
                                    </div>
                                    <!-- End Audio -->

                                    <!-- Document -->
                                    <div
                                        class="chat-img"
                                        v-if="
                                            list.message.type == 'document' &&
                                            !list.message.detail.asset
                                        "
                                    >
                                        <div class="img-wrap">
                                            <img
                                                :src="attribute.document"
                                                alt="img"
                                                :style="
                                                    !list.message.detail.asset
                                                        ? 'height: 100%'
                                                        : ''
                                                "
                                            />
                                            <div class="img-overlay">
                                                <a
                                                    class="ti ti-download fs-30"
                                                    href="javascript:void(0);"
                                                    v-if="
                                                        !list.message.detail
                                                            .asset
                                                    "
                                                    @click="
                                                        downloadMedia(
                                                            list.details,
                                                            list.id,
                                                            list.message.mime
                                                        )
                                                    "
                                                    ><i></i
                                                ></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="file-attach"
                                        v-if="
                                            list.message.type == 'document' &&
                                            list.message.detail.asset
                                        "
                                    >
                                        <span class="file-icon">
                                            <i class="ti ti-files"></i>
                                        </span>
                                        <div class="ms-2 overflow-hidden">
                                            <h6 class="mb-1">
                                                {{ list.message.detail.title }}
                                            </h6>
                                            <p>
                                                File
                                                {{ list.message.mime }}
                                            </p>
                                        </div>
                                        <a
                                            target="_blank"
                                            :href="list.message.detail.url"
                                            class="download-icon"
                                        >
                                            <i class="ti ti-download"></i>
                                        </a>
                                    </div>
                                    <!-- End Document -->

                                    <!-- Location -->
                                    <div
                                        class="chat-img"
                                        v-if="list.message.type == 'location'"
                                    >
                                        <div class="img-wrap">
                                            <img
                                                :src="attribute.location"
                                                alt="img"
                                                style="height: 100%"
                                            />
                                            <div class="img-overlay">
                                                <a
                                                    class="ti ti-url fs-30"
                                                    target="_blank"
                                                    :href="
                                                        list.message.detail.url
                                                    "
                                                    ><i></i
                                                ></a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Location -->

                                    <div
                                        v-html="
                                            formattedText(
                                                list.message.detail.text
                                            )
                                        "
                                    ></div>
                                    <div
                                        class="message-link"
                                        v-if="list.message.url != null"
                                    >
                                        <a
                                            :href="list.message.url"
                                            target="_blank"
                                            class="link-primary mt-2"
                                            >{{ list.message.url }}</a
                                        >
                                    </div>
                                </div>

                                <!-- For Image -->
                                <div class="chat-actions">
                                    <a
                                        class="#"
                                        href="#"
                                        data-bs-toggle="dropdown"
                                    >
                                        <i class="ti ti-dots-vertical"></i>
                                    </a>
                                    <ul
                                        class="dropdown-menu dropdown-menu-end p-3"
                                    >
                                        <!-- <li>
                                            <a
                                                class="dropdown-item reply-btn"
                                                href="#"
                                                ><i
                                                    class="ti ti-corner-up-left me-2"
                                                ></i
                                                >Reply</a
                                            >
                                        </li> -->
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                @click="deleteMessage(list)"
                                                ><i class="ti ti-trash me-2"></i
                                                >Delete Message</a
                                            >
                                        </li>
                                        <li v-if="list.sender">
                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                @click="deleteEveryOne(list)"
                                                ><i class="ti ti-trash me-2"></i
                                                >Pull Message</a
                                            >
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Message -->
                </div>
            </div>
        </div>
        <div class="chat-footer">
            <form class="footer-form">
                <div class="chat-footer-wrap">
                    <div class="form-wrap">
                        <textarea
                            class="form-control"
                            placeholder="Type Your Message"
                            ref="messageInput"
                            rows="1"
                            :disabled="send.loader"
                            @input="autoResize"
                            @keydown.enter="handleEnter"
                            @keydown.shift.enter.prevent="insertLineBreak"
                            v-model="send.text"
                        ></textarea>
                    </div>
                    <div
                        class="form-item position-relative d-flex align-items-center justify-content-center"
                    >
                        <a
                            href="#"
                            @click="
                                setFileType(
                                    '.pdf, .doc, .docx, .xlsx, .zip, .html, .php, .css, .js, .ppt, .txt'
                                )
                            "
                            class="action-circle position-absolute"
                        >
                            <i class="ti ti-file"></i>
                        </a>
                        <input
                            type="file"
                            class="open-file position-relative"
                            name="files"
                            ref="fileInput"
                            id="files"
                            :accept="fileTypes"
                            @change="handleFileChange"
                        />
                    </div>
                    <div class="form-item">
                        <a href="#" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3">
                            <a
                                href="javascript:void(0);"
                                @click="openModalCam"
                                class="dropdown-item"
                                ><i class="ti ti-camera-selfie me-2"></i
                                >Camera</a
                            >
                            <a
                                href="#"
                                class="dropdown-item"
                                @click="setFileType('image/*')"
                            >
                                <i class="ti ti-photo-up me-2"></i> Gallery
                            </a>
                            <a
                                href="#"
                                class="dropdown-item"
                                @click="setFileType('video/*')"
                            >
                                <i class="ti ti-video me-2"></i> Video
                            </a>
                        </div>
                    </div>
                    <div class="form-btn">
                        <button
                            class="btn btn-primary"
                            type="button"
                            :disabled="send.loader"
                            @click="sendMessage"
                        >
                            <i
                                :class="
                                    send.loader ? 'ti ti-circle' : 'ti ti-send'
                                "
                            ></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for update file -->
    <div
        class="modal fade"
        id="previewmodal"
        tabindex="-1"
        aria-labelledby="filePreviewModalLabel"
        aria-hidden="true"
        ref="fileModal"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Send
                        {{
                            file.previewType == "document"
                                ? "Dokument"
                                : "Media"
                        }}
                    </h4>
                    <button
                        type="button"
                        class="btn-close"
                        id="closeModal"
                        data-bs-dismiss="modal"
                    >
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body row">
                    <div
                        v-if="file.previewType === 'image'"
                        class="col-12 d-flex justify-content-center"
                    >
                        <img
                            :src="file.filePreview"
                            alt="Image Preview"
                            class="img-fluid"
                        />
                    </div>
                    <div
                        v-if="file.previewType === 'video'"
                        class="col-12 d-flex justify-content-center"
                    >
                        <video controls="">
                            <source :src="file.filePreview" type="video/mp4" />
                        </video>
                    </div>
                    <div
                        v-if="file.previewType === 'audio'"
                        class="col-12 d-flex justify-content-center"
                    >
                        <audio controls="">
                            <source :src="file.filePreview" type="audio/mpeg" />
                        </audio>
                    </div>
                    <div
                        v-if="file.previewType === 'document'"
                        class="col-12 d-flex justify-content-center"
                    >
                        <embed
                            :src="file.filePreview"
                            type="application/pdf"
                            width="100%"
                            height="400px"
                            v-if="file.isPDF"
                        />
                        <img
                            v-else
                            :src="attribute.document"
                            class="w-50"
                            alt="img"
                        />
                    </div>

                    <div class="col-12 mt-4">
                        <textarea
                            class="form-control"
                            placeholder="Type Your Message"
                            ref="messageInputModal"
                            rows="1"
                            :disabled="send.loader"
                            @input="autoResizeModal"
                            @keydown.shift.enter.prevent="insertLineBreak"
                            v-model="send.text"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        :disabled="send.loader"
                        class="btn btn-primary"
                        @click="confirmFile"
                    >
                        {{ send.loader ? "Loading..." : "Send Message" }}
                        <i class="ti ti-send ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal for update -->

    <!-- Modal For Take Photo -->
    <div
        class="modal fade"
        id="cameraModal"
        tabindex="-1"
        aria-labelledby="filePreviewModalLabel"
        aria-hidden="true"
        ref="cameraModal"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Take Photo</h4>
                    <button
                        type="button"
                        class="btn-close"
                        id="closeModalCamera"
                        data-bs-dismiss="modal"
                    >
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body row">
                    <div
                        class="col-12 d-flex justify-content-center"
                        v-if="camera.picture == null"
                    >
                        <video ref="videoCamera" autoplay></video>
                        <canvas ref="canvas" style="display: none"></canvas>
                    </div>
                    <div
                        class="col-12 d-flex justify-content-center"
                        v-if="camera.picture != null"
                    >
                        <img
                            :src="camera.picture"
                            alt="Image Preview"
                            class="img-fluid"
                        />
                    </div>
                    <div class="col-12 mt-4">
                        <textarea
                            class="form-control"
                            placeholder="Type Your Message"
                            ref="messageInputCamera"
                            rows="1"
                            :disabled="send.loader"
                            @input="autoResizeCamera"
                            @keydown.shift.enter.prevent="insertLineBreak"
                            v-model="send.text"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        v-if="camera.picture == null"
                        type="button"
                        class="btn btn-primary"
                        @click="capturePhoto"
                    >
                        Take Photo
                        <i class="ti ti-camera ms-2"></i>
                    </button>
                    <button
                        v-if="camera.picture != null && !send.loader"
                        type="button"
                        class="btn btn-primary me-2"
                        @click="reTakePhoto"
                    >
                        Re-Take
                        <i class="ti ti-camera ms-2"></i>
                    </button>
                    <button
                        v-if="camera.picture != null"
                        type="button"
                        :disabled="send.loader"
                        @click="sendPhoto"
                        class="btn btn-primary"
                    >
                        {{ send.loader ? "Loading..." : "Send Photo" }}
                        <i class="ti ti-send ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
</template>

<script>
import imageIcon from "@/assets/icons/image.png";
import imageAudio from "@/assets/icons/audio.png";
import imageVideo from "@/assets/icons/video.png";
import imageDocument from "@/assets/icons/documents.png";
import imageLocation from "@/assets/icons/map.png";
import imageUser from "@/assets/icons/user.png";
import NProgress from "nprogress";
export default {
    components: {},
    data() {
        return {
            intervalId: null,
            chatId: null,
            camera: {
                modal: false,
                picture: null,
                stream: null,
            },
            fileTypes:
                ".pdf, .doc, .docx, .xlsx, .zip, .html, .php, .css, .js, .ppt, .txt",
            attribute: {
                image: imageIcon,
                audio: imageAudio,
                video: imageVideo,
                document: imageDocument,
                location: imageLocation,
                user: imageUser,
                modal: false,
            },
            message: {
                list: [],
                loader: true,
                last_chat: "",
                search: "",
            },
            send: {
                loader: false,
                type: "text",
                text: "",
                file: null,
                location: {
                    long: "",
                    lang: "",
                },
            },
            file: {
                file: null,
                filePreview: null,
                previewType: "",
                fileName: "",
                isPDF: false,
            },
        };
    },
    computed: {},
    methods: {
        getPhotoPict(phone) {
            var userIndex = this.$store.getters.get_contacts.findIndex(
                (i) => phone == i.phone
            );

            if (userIndex !== -1) {
                return this.$store.getters.get_contacts[userIndex].url;
            } else {
                return this.attribute.user;
            }
        },

        async openModalCam() {
            this.camera.modal = true;
            const modal = new bootstrap.Modal(this.$refs.cameraModal, {
                backdrop: "static",
                keyboard: false,
            });
            modal.show();
            await this.openCamera();
        },

        async openCamera() {
            try {
                this.camera.stream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                });
                this.$refs.videoCamera.srcObject = this.camera.stream;
            } catch (error) {
                console.error("Kamera tidak dapat diakses", error);
            }
        },

        capturePhoto() {
            const canvas = this.$refs.canvas;
            const video = this.$refs.videoCamera;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const context = canvas.getContext("2d");
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            this.camera.picture = canvas.toDataURL("image/jpeg");

            if (this.camera.stream) {
                const tracks = this.camera.stream.getTracks();
                tracks.forEach((track) => track.stop());
            }
        },

        async reTakePhoto() {
            this.camera = {
                modal: false,
                picture: null,
                stream: null,
            };

            this.openCamera();
        },

        async sendPhoto() {
            this.send.type = "photo";
            this.send.file = this.camera.picture;

            if (this.camera.stream) {
                const tracks = this.camera.stream.getTracks();
                tracks.forEach((track) => track.stop());
            }

            await this.sendMessage();

            this.camera = {
                modal: false,
                picture: null,
                stream: null,
            };
            var modal = document.getElementById("closeModalCamera");
            modal.click();
        },

        closeModal() {},

        async confirmFile() {
            await this.sendMessage();
            this.closeModal();
        },

        closeCamera() {
            if (this.camera.stream) {
                const tracks = this.camera.stream.getTracks();
                tracks.forEach((track) => track.stop());
            }

            this.camera = {
                modal: false,
                picture: null,
                stream: null,
            };
        },

        triggerFileInput() {
            this.$refs.fileInput.click();
        },
        setFileType(types) {
            this.fileTypes = types;

            setTimeout(() => {
                this.triggerFileInput();
            }, 1000);
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.file.fileName = file.name;
                this.send.file = file;

                if (file.type.startsWith("image")) {
                    this.file.previewType = "image";
                    this.file.filePreview = URL.createObjectURL(file);
                    this.send.type = "media";
                } else if (file.type.startsWith("video")) {
                    this.file.previewType = "video";
                    this.file.filePreview = URL.createObjectURL(file);
                    this.send.type = "media";
                } else if (file.type.startsWith("audio")) {
                    this.file.previewType = "audio";
                    this.file.filePreview = URL.createObjectURL(file);
                    this.send.type = "media";
                } else if (file.type === "application/pdf") {
                    this.file.previewType = "document";
                    this.send.type = "document";
                    this.file.filePreview = URL.createObjectURL(file);
                    this.file.isPDF = true;
                } else {
                    this.send.type = "document";
                    this.file.previewType = "document";
                    this.file.filePreview = null;
                    this.file.isPDF = false;
                }

                // Show the modal
                const modal = new bootstrap.Modal(this.$refs.fileModal, {
                    backdrop: "static", // mencegah modal tertutup dengan klik di luar
                    keyboard: false, // mencegah modal tertutup dengan tombol Esc
                });
                modal.show();
            }
        },

        closeModal() {
            var modal = document.getElementById("closeModal");
            modal.click();
        },

        async confirmFile() {
            await this.sendMessage();
            this.closeModal();
        },

        resetFile() {
            document.getElementById("files").value = "";
            this.send.type = "text";
            this.send.file = null;
            this.file.filePreview = null;
            this.file.fileName = "";
            this.file.previewType = "";
            this.file.isPDF = false;
        },

        async getMessages(lastChat = "") {
            try {
                const response = await this.$axios.get(
                    `/device/chats/detail/${this.$route.params.id}/${this.$route.params.chatid}?last_id=${lastChat}&is_group=true`
                );
                var data = response.data;
                var listData = Array.isArray(data.list)
                    ? data.list
                    : Object.values(data.list);

                listData = listData.filter(
                    (newItem) =>
                        !this.message.list.some(
                            (existingItem) => existingItem.id === newItem.id
                        )
                );

                this.chatId = data.chatid;
                this.message.loader = false;
                this.message.last_chat = data.last_chat;
                this.message.list.push(...listData);

                // function for mark to read message
                if (listData.length > 0) {
                    this.scrollToBottom();
                } 
 
            } catch (error) {
                this.$handleErrorResponse(error);
                console.log(error);
            }
        }, 

        async deleteMessage(detail) {
            try {
                await this.$axios.post(
                    `/device/misc/delete-message/${this.$route.params.id}`,
                    {
                        chatid: this.chatId,
                        message: {
                            key: {
                                remoteJid: this.chatId,
                                fromMe: detail.sender,
                                id: detail.id,
                            },
                            deleteMedia:
                                detail.type == "text" &&
                                detail.type == "location"
                                    ? false
                                    : true,
                            timestamp: detail.timestamp,
                        },
                    },
                    {
                        withCredentials: false,
                    }
                );

                var messageIndex = this.message.list.findIndex(
                    (i) => detail.id == i.id
                );
                if (messageIndex !== -1) {
                    this.message.list.splice(messageIndex, 1);
                }
            } catch (error) {
                console.log(error);
            }
        },

        async deleteEveryOne(detail) {
            try {
                await this.$axios.post(
                    `/device/misc/delete-everyone/${this.$route.params.id}`,
                    {
                        chatid: this.chatId,
                        message: {
                            remoteJid: this.chatId,
                            fromMe: detail.sender,
                            id: detail.id,
                        },
                    },
                    {
                        withCredentials: false,
                    }
                );

                var messageIndex = this.message.list.findIndex(
                    (i) => detail.id == i.id
                );
                if (messageIndex !== -1) {
                    this.message.list.splice(messageIndex, 1);
                }
            } catch (error) {
                console.log(error);
            }
        },

        formattedText(message) {
            return message.replace(/\n/g, "<br>");
        },

        scrollToBottom() {
            const messageContent = document.querySelector(".chat-area");
            if (messageContent) {
                messageContent.scrollTop = messageContent.scrollHeight;
            }
        },

        startPolling() {
            if (this.intervalId) clearInterval(this.intervalId);

            this.intervalId = setInterval(() => {
                if (this.$route.name == "group_room") {
                    if (!this.message.loader) {
                        this.getMessages(this.message.last_chat);
                    }
                } else {
                    this.stopPolling();
                }
            }, 5000);
        },

        stopPolling() {
            if (this.intervalId) {
                clearInterval(this.intervalId);
                this.intervalId = null;
            }
        },

        async downloadMedia(message, name, mime) {
            NProgress.start();
            NProgress.set(0.1);
            try {
                const response = await this.$axios.post(
                    `/device/misc/download-media/${this.$route.params.id}`,
                    {
                        type: mime,
                        medianame: name,
                        message: message,
                    },
                    {
                        withCredentials: false,
                    }
                );

                NProgress.done();

                var data = response.data;
                this.$showToast(`Berhasil mengunduh media`, "info", 3000);
                if (response.status == 200) {
                    var findMessage = this.message.list.findIndex(
                        (i) => name == i.id
                    );

                    if (findMessage !== -1) {
                        this.message.list[findMessage].message.detail.url =
                            data.data.downloadPath;
                        this.message.list[
                            findMessage
                        ].message.detail.asset = true;
                    }
                }
            } catch (error) {
                NProgress.done();
                this.$showToast(`Media gagal di unduh`, "info", 3000);
            }
        },

        autoResize() {
            const textarea = this.$refs.messageInput;
            textarea.style.height = "auto";
            textarea.style.height = textarea.scrollHeight + "px";
        },

        autoResizeModal() {
            const textarea = this.$refs.messageInputModal;
            textarea.style.height = "auto";
            textarea.style.height = textarea.scrollHeight + "px";
        },

        handleEnter(event) {
            if (!event.shiftKey) {
                event.preventDefault();
                this.sendMessage();
            }
        },

        autoResizeCamera() {
            const textarea = this.$refs.messageInputCamera;
            textarea.style.height = "auto";
            textarea.style.height = textarea.scrollHeight + "px";
        },

        insertLineBreak() {
            this.send.text += "\n";
            this.$nextTick(() => {
                this.autoResize();
            });
        },

        async sendMessage() {
            NProgress.start();
            NProgress.set(0.1);
            this.send.loader = true;
            try {
                const formData = new FormData();
                formData.append("type", this.send.type);
                formData.append("isgroup", true);
                formData.append("phone", this.$route.params.chatid);
                formData.append("text", this.send.text);

                if (this.send.file) {
                    if (this.send.type == "photo") {
                        formData.append("photo", this.send.file);
                    } else {
                        formData.append("file", this.send.file);
                    }
                }

                const response = await this.$axios.post(
                    `device/chats/send/${this.$route.params.id}`,
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                NProgress.done();

                this.send = {
                    loader: false,
                    type: "text",
                    text: "",
                    file: null,
                    location: {
                        long: "",
                        lang: "",
                    },
                };

                this.resetFile();
                this.$showToast(`Berhasil mengirim pesan`, "info", 3000);
            } catch (error) {
                NProgress.done();
                this.send.loader = false;
                this.$handleErrorResponse(error);
            }
        },
    },
    beforeDestroy() {
        this.stopPolling();
    },
    updated() {
        this.scrollToBottom();
    },
    mounted() {
        $(".chat-search-btn").on("click", function () {
            $(".chat-search").toggleClass("visible-chat");
        });
        $(".close-btn-chat").on("click", function () {
            $(".chat-search").removeClass("visible-chat");
        });

        $("#closeModal").on("click", () => {
            this.resetFile();
        });

        $("#closeModalCamera").on("click", () => {
            this.closeCamera();
        });
    },

    watch: {
        "$route.params.chatid": {
            handler() {
                this.stopPolling();

                if (this.$route.name == "group_room") {
                    this.message = {
                        list: [],
                        loader: true,
                        last_chat: "",
                        search: "",
                    };
                    this.getMessages("").then(() => {
                        this.startPolling();
                    });
                }

                this.message = {
                    list: [],
                    loader: true,
                    last_chat: "",
                    search: "",
                };
                this.getMessages("").then(() => {
                    this.startPolling();
                });
            },
            immediate: true,
        },
    },
};
</script>
<style>
.lds-roller,
.lds-roller div,
.lds-roller div:after {
    box-sizing: border-box;
}
.lds-roller {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
}
.lds-roller div {
    animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    transform-origin: 40px 40px;
}
.lds-roller div:after {
    content: " ";
    display: block;
    position: absolute;
    width: 7.2px;
    height: 7.2px;
    border-radius: 50%;
    background: currentColor;
    color: #005d4c;
    margin: -3.6px 0 0 -3.6px;
}
.lds-roller div:nth-child(1) {
    animation-delay: -0.036s;
}
.lds-roller div:nth-child(1):after {
    top: 62.62742px;
    left: 62.62742px;
}
.lds-roller div:nth-child(2) {
    animation-delay: -0.072s;
}
.lds-roller div:nth-child(2):after {
    top: 67.71281px;
    left: 56px;
}
.lds-roller div:nth-child(3) {
    animation-delay: -0.108s;
}
.lds-roller div:nth-child(3):after {
    top: 70.90963px;
    left: 48.28221px;
}
.lds-roller div:nth-child(4) {
    animation-delay: -0.144s;
}
.lds-roller div:nth-child(4):after {
    top: 72px;
    left: 40px;
}
.lds-roller div:nth-child(5) {
    animation-delay: -0.18s;
}
.lds-roller div:nth-child(5):after {
    top: 70.90963px;
    left: 31.71779px;
}
.lds-roller div:nth-child(6) {
    animation-delay: -0.216s;
}
.lds-roller div:nth-child(6):after {
    top: 67.71281px;
    left: 24px;
}
.lds-roller div:nth-child(7) {
    animation-delay: -0.252s;
}
.lds-roller div:nth-child(7):after {
    top: 62.62742px;
    left: 17.37258px;
}
.lds-roller div:nth-child(8) {
    animation-delay: -0.288s;
}
.lds-roller div:nth-child(8):after {
    top: 56px;
    left: 12.28719px;
}
@keyframes lds-roller {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
