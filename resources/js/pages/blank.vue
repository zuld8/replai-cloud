<template>
    <div
        class="welcome-content d-flex align-items-center justify-content-center"
    >
        <img :src="attribute.image" class="w-25" />
        <div class="welcome-info text-center">
            <div
                class="welcome-box bg-white d-inline-flex align-items-center mt-2"
            >
                <h6>
                    Start New Message Or Select Chat available in history
                </h6>
            </div>
        </div>
        <a
            href="javascript:void(0);"
            class="btn btn-primary mt-2"
            @click="newChat()"
            ><i class="ti ti-location me-2"></i>Start New Message</a
        >
    </div>

    <!-- Chat Group -->
    <div
        class="modal fade"
        id="newChatModal"
        tabindex="-1"
        aria-labelledby="filePreviewNewChatModal"
        aria-hidden="true"
        ref="newChatModal"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Message</h4>
                    <button
                        type="button"
                        class="btn-close"
                        id="closeModalNewChat"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="search-wrap contact-search mb-3">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                v-model="contact.search"
                                placeholder="Search Contacts...."
                            />
                            <a
                                href="javascript:void(0);"
                                class="input-group-text"
                                ><i class="ti ti-search"></i
                            ></a>
                        </div>
                    </div>
                    <h6 class="mb-3 fw-medium fs-16">
                        {{
                            contact.search == "" || contact.search == null
                                ? "Contact List"
                                : "Search Contact : " + contact.search
                        }}
                    </h6>
                    <div class="contact-scroll contact-select mb-3">
                        <div
                            class="contact-user d-flex align-items-center justify-content-between"
                        >
                            <div class="input-group">
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="contact.phone"
                                    placeholder="Enter Whatsapp Number"
                                />
                                <a
                                    @click="newChatPhone()"
                                    href="javascript:void(0);"
                                    class="input-group-text"
                                    ><i class="ti ti-send"></i
                                ></a>
                            </div>
                        </div>
                        <div
                            class="contact-user d-flex align-items-center justify-content-between"
                            v-for="(
                                cp, index
                            ) in $store.getters.get_contacts.filter((item) =>
                                item.name
                                    .toLowerCase()
                                    .includes(contact.search.toLowerCase())
                            )"
                            :key="index"
                        >
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-lg">
                                    <img
                                        :src="cp.photo"
                                        class="rounded-circle"
                                        alt="image"
                                    />
                                </div>
                                <div class="ms-2">
                                    <h6>{{ cp.name }}</h6>
                                    <p>{{ cp.phone }}</p>
                                </div>
                            </div>
                            <button
                                class="btn btn-primary btn-sm"
                                type="button"
                                @click="newChatContact(cp)"
                            >
                                <i class="ti ti-send"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Chat Group -->
</template>
<script>
import imageLocation from "@/assets/icons/new-message.png";
import imageUser from "@/assets/icons/user.png";
export default {
    components: {},
    data() {
        return {
            contact: {
                search: "",
                phone: "",
            },
            attribute: {
                image: imageLocation,
                user: imageUser,
                modal: false,
            },
        };
    },
    computed: {},
    methods: {
        newChat() {
            const modal = new bootstrap.Modal(this.$refs.newChatModal, {
                backdrop: "static",
                keyboard: false,
            });
            modal.show();
        },

        newChatPhone() {
            this.closeModal()
            return this.$router.push({
                name: "chat_room",
                params: {
                    id: this.$route.params.id,
                    chatid: `${this.contact.phone}-s-whatsapp-net`,
                },
                query: {
                    name: this.contact.phone,
                    photo: this.attribute.user,
                },
            });
            
        },

        newChatContact(detail) {
            this.closeModal()
            return this.$router.push({
                name: "chat_room",
                params: {
                    id: this.$route.params.id,
                    chatid: `${detail.phone}-s-whatsapp-net`,
                },
                query: {
                    name: detail.name,
                    photo: detail.photo,
                },
            });
            
        },

        closeModal() {
            var buttonClose = document.getElementById('closeModalNewChat');
            buttonClose.click();
        }
    },
    beforeDestroy() {},
    updated() {},
    mounted() {},

    watch: {},
};
</script>
