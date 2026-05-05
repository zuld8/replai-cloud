<template>
    <div class="sidebar-group">
        <div class="tab-content">
            <!-- Contact List -->
            <div
                class="tab-pane fade"
                :class="
                    $store.getters.get_active_tab == 'chat' ? 'active show' : ''
                "
                id="chat-menu"
            >
                <div id="chats" class="sidebar-content active slimscroll">
                    <div class="slimscroll">
                        <div class="chat-search-header">
                            <div
                                class="header-title d-flex align-items-center justify-content-between"
                            >
                                <h4 class="mb-3">Whatsapp Messanger</h4>
                            </div>

                            <div class="search-wrap">
                                <div>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="chats.search"
                                            placeholder="Search Contact Name"
                                        />
                                        <span class="input-group-text"
                                            ><i class="ti ti-search"></i
                                        ></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sidebar-body chat-body" id="chatsidebar">
                            <div
                                class="d-flex justify-content-between align-items-center mb-3"
                            >
                                <h5 class="chat-title">
                                    {{
                                        chats.search == "" ||
                                        contact.search == null
                                            ? "All Messages"
                                            : "Search Messages : " + chats.search
                                    }}
                                </h5>
                            </div>

                            <div class="tab-content" id="innerTabContent">
                                <div class="tab-pane fade show active">
                                    <div class="chat-users-wrap">
                                        <div
                                            class="chat-list"
                                            v-for="(
                                                list, index
                                            ) in chats.list.filter((item) =>
                                                item.name
                                                    .toLowerCase()
                                                    .includes(
                                                        chats.search.toLowerCase()
                                                    )
                                            )"
                                            :key="index"
                                        >
                                            <router-link
                                                :to="{
                                                    name: 'chat_room',
                                                    params: {
                                                        id: $route.params.id,
                                                        chatid: list.id,
                                                    },
                                                    query: {
                                                        name: list.name,
                                                        photo: list.photo,
                                                    },
                                                }"
                                                class="chat-user-list"
                                            >
                                                <div
                                                    class="avatar avatar-lg online me-2"
                                                >
                                                    <img
                                                        :src="list.photo"
                                                        class="rounded-circle"
                                                        :alt="list.name"
                                                    />
                                                </div>
                                                <div class="chat-user-info">
                                                    <div class="chat-user-msg">
                                                        <h6>{{ list.name }}</h6>
                                                        <p></p>
                                                        <p>
                                                            <i
                                                                class="ti ti-photo me-2"
                                                                v-if="
                                                                    list.type ==
                                                                    'image'
                                                                "
                                                            ></i>
                                                            <i
                                                                class="ti ti-video me-2"
                                                                v-if="
                                                                    list.type ==
                                                                    'video'
                                                                "
                                                            ></i>
                                                            <i
                                                                class="ti ti-file me-2"
                                                                v-if="
                                                                    list.type ==
                                                                    'document'
                                                                "
                                                            ></i>
                                                            <i
                                                                class="ti ti-music me-2"
                                                                v-if="
                                                                    list.type ==
                                                                    'audio'
                                                                "
                                                            ></i>
                                                            <i
                                                                class="ti ti-map-pin me-2"
                                                                v-if="
                                                                    list.type ==
                                                                    'location'
                                                                "
                                                            ></i>
                                                            {{ list.message }}
                                                        </p>
                                                    </div>
                                                    <div class="chat-user-time">
                                                        <span class="time">{{
                                                            list.time
                                                        }}</span>
                                                        <div class="chat-pin">
                                                            <!-- <i
                                                                class="ti ti-pin me-2"
                                                            ></i> -->
                                                            <span
                                                                v-if="
                                                                    list.unread >
                                                                    0
                                                                "
                                                                class="count-message fs-12 fw-semibold"
                                                                >{{
                                                                    list.unread
                                                                }}</span
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </router-link>
                                            <div class="chat-dropdown">
                                                <a
                                                    class="#"
                                                    href="#"
                                                    data-bs-toggle="dropdown"
                                                >
                                                    <i
                                                        class="ti ti-dots-vertical"
                                                    ></i>
                                                </a>
                                                <ul
                                                    class="dropdown-menu dropdown-menu-end p-3"
                                                >
                                                    <li v-if="list.unread == 0">
                                                        <a
                                                            class="dropdown-item"
                                                            href="javascript:void(0);"
                                                            @click="
                                                                markToUnread(
                                                                    list.uid,
                                                                    false
                                                                )
                                                            "
                                                            ><i
                                                                class="ti ti-check me-2"
                                                            ></i
                                                            >Mark Unread</a
                                                        >
                                                    </li>
                                                    <!-- <li>
                                                        <a
                                                            class="dropdown-item"
                                                            href="#"
                                                            ><i
                                                                class="ti ti-box-align-right me-2"
                                                            ></i
                                                            >Archive Chat</a
                                                        >
                                                    </li> -->
                                                    <!-- <li>
                                                        <a
                                                            class="dropdown-item"
                                                            href="#"
                                                            ><i
                                                                class="ti ti-heart me-2"
                                                            ></i
                                                            >Mark as
                                                            Favourite</a
                                                        >
                                                    </li> -->

                                                    <!-- <li>
                                                        <a
                                                            class="dropdown-item"
                                                            href="#"
                                                            ><i
                                                                class="ti ti-pinned me-2"
                                                            ></i
                                                            >Pin Chats</a
                                                        >
                                                    </li> -->
                                                    <li>
                                                        <a
                                                            class="dropdown-item"
                                                            href="javascript:void(0)"
                                                            @click="
                                                                deleteChat(
                                                                    list.uid
                                                                )
                                                            "
                                                            ><i
                                                                class="ti ti-trash me-2"
                                                            ></i
                                                            >Delete Message </a
                                                        >
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div
                                            v-if="chats.loader"
                                            class="d-flex justify-content-center"
                                        >
                                            <div class="lds-dual-ring"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Contact List -->

            <!-- Group List -->
            <div
                class="tab-pane fade"
                :class="
                    $store.getters.get_active_tab == 'group'
                        ? 'active show'
                        : ''
                "
                id="group-menu"
            >
                <div class="sidebar-content active slimscroll">
                    <div class="slimscroll">
                        <div class="chat-search-header">
                            <div
                                class="header-title d-flex align-items-center justify-content-between"
                            >
                                <h4 class="mb-3">Whatsapp Groups</h4>
                                <div
                                    class="d-flex align-items-center mb-3"
                                ></div>
                            </div>

                            <div class="search-wrap">
                                <form action="#">
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="groups.search"
                                            placeholder="Search Group Name"
                                        />
                                        <span class="input-group-text"
                                            ><i class="ti ti-search"></i
                                        ></span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="sidebar-body chat-body">
                            <div
                                class="d-flex justify-content-between align-items-center mb-3"
                            >
                                <h5>
                                    {{
                                        groups.search == "" ||
                                        groups.search == null
                                            ? "All Groups"
                                            : "Search Groups : " + groups.search
                                    }}
                                </h5>
                            </div>

                            <div class="chat-users-wrap">
                                <div
                                    class="chat-list"
                                    v-for="(list, index) in groups.list.filter(
                                        (item) =>
                                            item.name
                                                .toLowerCase()
                                                .includes(
                                                    groups.search.toLowerCase()
                                                )
                                    )"
                                    :key="index"
                                >
                                    <router-link
                                        :to="{
                                            name: 'group_room',
                                            params: {
                                                id: $route.params.id,
                                                chatid: list.id,
                                            },
                                            query: {
                                                name: list.name,
                                                photo: list.photo,
                                            },
                                        }"
                                        class="chat-user-list"
                                    >
                                        <div
                                            class="avatar avatar-lg online me-2"
                                        >
                                            <img
                                                :src="list.photo"
                                                class="rounded-circle"
                                                :alt="list.name"
                                            />
                                            <!-- <img
                                                v-else
                                                :src="contact.icon"
                                                class="rounded-circle"
                                                :alt="list.name"
                                            /> -->
                                        </div>
                                        <div class="chat-user-info">
                                            <div class="chat-user-msg">
                                                <h6>{{ list.name }}</h6>
                                                <p></p>
                                                <p>
                                                    <i
                                                        class="ti ti-photo me-2"
                                                        v-if="
                                                            list.type == 'image'
                                                        "
                                                    ></i>
                                                    <i
                                                        class="ti ti-video me-2"
                                                        v-if="
                                                            list.type == 'video'
                                                        "
                                                    ></i>
                                                    <i
                                                        class="ti ti-file me-2"
                                                        v-if="
                                                            list.type ==
                                                            'document'
                                                        "
                                                    ></i>
                                                    <i
                                                        class="ti ti-music me-2"
                                                        v-if="
                                                            list.type == 'audio'
                                                        "
                                                    ></i>
                                                    <i
                                                        class="ti ti-map-pin me-2"
                                                        v-if="
                                                            list.type ==
                                                            'location'
                                                        "
                                                    ></i>
                                                    {{ list.message }}
                                                </p>
                                            </div>
                                            <div class="chat-user-time">
                                                <span class="time">{{
                                                    list.time
                                                }}</span>
                                                <div class="chat-pin">
                                                    <!-- <i
                                                                class="ti ti-pin me-2"
                                                            ></i> -->
                                                    <span
                                                        v-if="list.unread > 0"
                                                        class="count-message fs-12 fw-semibold"
                                                        >{{ list.unread }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </router-link>
                                    <div class="chat-dropdown">
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
                                            <li v-if="list.unread == 0">
                                                <a
                                                    class="dropdown-item"
                                                    href="javascript:void(0);"
                                                    @click="
                                                        markToUnread(
                                                            list.uid,
                                                            false
                                                        )
                                                    "
                                                    ><i
                                                        class="ti ti-check me-2"
                                                    ></i
                                                    >Mark Unread</a
                                                >
                                            </li>
                                            <!-- <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    ><i
                                                        class="ti ti-box-align-right me-2"
                                                    ></i
                                                    >Archive Chat</a
                                                >
                                            </li> -->
                                            <!-- <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    ><i
                                                        class="ti ti-heart me-2"
                                                    ></i
                                                    >Mark as Favourite</a
                                                >
                                            </li> -->

                                            <!-- <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    ><i
                                                        class="ti ti-pinned me-2"
                                                    ></i
                                                    >Pin Chats</a
                                                >
                                            </li> -->
                                            <!-- <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="javascript:void(0)"
                                                    @click="
                                                        deleteChat(list.uid)
                                                    "
                                                    ><i
                                                        class="ti ti-trash me-2"
                                                    ></i
                                                    >Hapus Pesan</a
                                                >
                                            </li> -->
                                        </ul>
                                    </div>
                                </div>
                                <div
                                    v-if="groups.loader"
                                    class="d-flex justify-content-center"
                                >
                                    <div class="lds-dual-ring"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Group List -->

            <!-- Contact List -->
            <div
                class="tab-pane fade"
                :class="
                    $store.getters.get_active_tab == 'contact'
                        ? 'active show'
                        : ''
                "
                id="contact-menu"
            >
                <div class="sidebar-content active slimscroll">
                    <div class="slimscroll">
                        <div class="chat-search-header">
                            <div
                                class="header-title d-flex align-items-center justify-content-between"
                            >
                                <h4 class="mb-3">Contact List</h4>
                            </div>

                            <div class="search-wrap">
                                <form action="#">
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="contact.search"
                                            placeholder="Search Contacts...."
                                        />
                                        <span class="input-group-text"
                                            ><i class="ti ti-search"></i
                                        ></span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="sidebar-body chat-body">
                            <div
                                class="d-flex justify-content-between align-items-center mb-3"
                            >
                                <h5>
                                    {{
                                        contact.search == "" ||
                                        contact.search == null
                                            ? "All Contact"
                                            : "Search Contacts : " + contact.search
                                    }}
                                </h5>
                            </div>

                            <div class="chat-users-wrap">
                                <div class="mb-4">
                                    <!-- <h6 class="mb-2">A</h6> -->
                                    <div
                                        class="chat-list"
                                        v-for="(
                                            cp, index
                                        ) in $store.getters.get_contacts.filter(
                                            (item) =>
                                                item.name
                                                    .toLowerCase()
                                                    .includes(
                                                        contact.search.toLowerCase()
                                                    )
                                        )"
                                        :key="index"
                                    >
                                        <router-link
                                            href="javascript:void(0);"
                                            class="chat-user-list"
                                            :to="{
                                                name: 'chat_room',
                                                params: {
                                                    id: $route.params.id,
                                                    chatid: `${cp.phone}-s-whatsapp-net`,
                                                },
                                                query: {
                                                    name: cp.name,
                                                    photo: cp.photo,
                                                },
                                            }"
                                        >
                                            <div
                                                class="avatar avatar-lg online me-2"
                                            >
                                                <img
                                                    :src="cp.photo"
                                                    class="rounded-circle"
                                                    alt="image"
                                                />
                                            </div>
                                            <div class="chat-user-info">
                                                <div class="chat-user-msg">
                                                    <h6>{{ cp.name }}</h6>
                                                    <p>{{ cp.phone }}</p>
                                                </div>
                                            </div>
                                        </router-link>
                                    </div>
                                </div>
                                <!-- <div class="mb-4">
                                    <h6 class="mb-2">C</h6>
                                    <div class="chat-list">
                                        <a
                                            href="javascript:void(0);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#contact-details"
                                            class="chat-user-list"
                                        >
                                            <div
                                                class="avatar avatar-lg offline me-2"
                                            >
                                                <img
                                                    src="profiles/avatar-03"
                                                    class="rounded-circle"
                                                    alt="image"
                                                />
                                            </div>
                                            <div class="chat-user-info">
                                                <div class="chat-user-msg">
                                                    <h6>Clyde Smith</h6>
                                                    <p>is busy now!</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div> 
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Contact List -->
        </div>
    </div>
</template>

<script>
import { mapActions } from "vuex";
import userImage from "@/assets/icons/user.png";
export default {
    components: {},
    data() {
        return {
            users: [],
            contact: {
                icon: userImage,
                clear: false,
                search: "",
            },
            contactloader: true,
            fetchTimeout: null,
            chats: {
                loader: false,
                list: [],
                totalchats: 0,
                search: "",
                last_chat: "",
            },
            groups: {
                loader: false,
                list: [],
                totalchats: 0,
                search: "",
                last_chat: "",
            },
        };
    },
    computed: {},
    methods: {
        ...mapActions([
            "saving_contacts",
            "saving_contact_list",
            "update_contact_pict",
        ]),

        getPhotoPict(phone, photo, name) {
            var userIndex = this.$store.getters.get_contacts.findIndex(
                (i) => phone == i.id
            );

            if (userIndex !== -1) {
                return this.$store.getters.get_contacts[userIndex].photo;
            } else {
                return photo;
            }
        },

        async getChatList(lastchat = "") {
            try {
                const response = await this.$axios.get(
                    `/device/chats/${this.$route.params.id}?last_chat=${lastchat}`
                );
                var data = response.data;

                var listData = Array.isArray(data.list)
                    ? data.list
                    : Object.values(data.list);

                listData.forEach((newItem) => {
                    const existingIndex = this.chats.list.findIndex(
                        (item) => item.uid === newItem.uid
                    );

                    if (existingIndex !== -1) {
                        newItem.photo = this.chats.list[existingIndex].photo;
                        this.chats.list.splice(existingIndex, 1);
                    }
                });

                this.chats.list = [
                    ...listData,
                    ...this.chats.list.filter(
                        (item) =>
                            !listData.some(
                                (newItem) => newItem.uid === item.uid
                            )
                    ),
                ];

                this.chats.loader = false;

                if (lastchat != "" && listData.length > 0) {
                    this.newMessageNotification(listData);
                }

                this.chats.last_chat = data.last_chat;

                // if (!this.contactloader) {
                //     await this.syncronizePhotoProfile(listData);
                // }
            } catch (error) {
                console.log(error);
                this.$handleErrorResponse(error);
            }
        },

        async getGroupList(lastchat = "") {
            try {
                const response = await this.$axios.get(
                    `/device/chats/${this.$route.params.id}?last_chat=${lastchat}&is_group=true`
                );

                var data = response.data;

                var listData = Array.isArray(data.list)
                    ? data.list
                    : Object.values(data.list);

                listData.forEach((newItem) => {
                    const existingIndex = this.groups.list.findIndex(
                        (item) => item.uid === newItem.uid
                    );
                    if (existingIndex !== -1) {
                        this.groups.list.splice(existingIndex, 1);
                    }
                });

                this.groups.list = [
                    ...listData,
                    ...this.groups.list.filter(
                        (item) =>
                            !listData.some(
                                (newItem) => newItem.uid === item.uid
                            )
                    ),
                ];

                this.groups.loader = false;

                if (lastchat != "" && listData.length > 0) {
                    this.newMessageNotification(listData);
                }

                this.groups.last_chat = data.last_chat;
            } catch (error) {
                this.$handleErrorResponse(error);
            }
        },

        newMessageNotification(listData) {
            var newMessage = listData[0];
            if (!newMessage.sender) {
                this.$showToast(
                    `Pesan baru dari ${newMessage.name}`,
                    "info",
                    3000
                );
            }
        },

        async syncronizePhotoProfile(listData, type = "chat") {
            for (var i in listData) {
                var chatitem = listData[i];
                var userIndex = this.$store.getters.get_contacts.findIndex(
                    (i) => chatitem.id == i.id
                );

                if (userIndex !== -1) {
                    var profile = await this.getPhotoProfile(chatitem.id);
                    if (profile.data.success) {
                        this["update_contact_pict"]({
                            id: profile.data.data.phone,
                            photo: profile.data.data.url,
                        });

                        var userChat = this.chats.list.findIndex(
                            (i) => profile.data.data.phone == i.uid
                        );

                        if (userChat !== -1) {
                            this.chats.list[userChat].photo =
                                profile.data.data.url;
                        }
                    } else {
                        this["update_contact_pict"]({
                            photo: null,
                            id: this.$store.getters.get_contacts[userIndex].id,
                        });
                    }
                }
            }
        },

        async getPhotoProfile(uid) {
            try {
                const response = await this.$axios.post(
                    `/device/misc/get-profile/${this.$route.params.id}`,
                    {
                        phone: uid,
                    }
                );
                var data = response;
                return data;
            } catch (error) {
                return {
                    success: false,
                };
            }
        },

        setAllLoader() {
            this.chats.loader = true;
            this.groups.loader = true;
        },

        async markToUnread(jid, status) {
            try {
                await this.$axios.post(
                    `/device/misc/mark-message/${this.$route.params.id}`,
                    {
                        status: status,
                        chatid: jid,
                    }
                );
                var userIndex = this.chats.list.findIndex((i) => jid == i.uid);
                if (userIndex !== -1) {
                    return (this.chats.list[userIndex].unread = 1);
                }
            } catch (error) {
                console.log(error);
            }
        },

        async deleteChat(jid) {
            try {
                await this.$axios.post(
                    `/device/misc/delete-chat/${this.$route.params.id}`,
                    {
                        chatid: jid,
                    }
                );
                var userIndex = this.chats.list.findIndex((i) => jid == i.uid);
                if (userIndex !== -1) {
                    this.chats.list.splice(userIndex, 1);
                }
            } catch (error) {
                console.log(error);
            }
        },

        startFetchingChats() {
            const fetchChats = () => {
                if (!this.chats.loader) {
                    const isChatTab =
                        this.$store.getters.get_active_tab === "chat";
                    const isGroupTab =
                        this.$store.getters.get_active_tab === "group";

                    if (isChatTab) this.getChatList(this.chats.last_chat);
                    if (isGroupTab) this.getGroupList(this.groups.last_chat);
                }
                // Mulai setTimeout lagi setelah 8 detik
                this.fetchTimeout = setTimeout(fetchChats, 8000);
            };

            fetchChats(); // Memulai fetching pertama kali
        },

        // Fungsi untuk menghentikan interval jika tidak diperlukan
        stopFetchingChats() {
            if (this.fetchTimeout) clearTimeout(this.fetchTimeout);
        },

        startFetchingContacts() {
            const fetchContacts = () => {
                if (!this.contactloader) {
                    this.getContacts();
                }

                this.fetchTimeout = setTimeout(fetchContacts, 60000);
            };

            fetchContacts();
        },

        async getContacts() {
            try {
                const response = await this.$axios.get(
                    `/device/chats/contacts/${this.$route.params.id}`
                );
                var data = response.data;

                var listData = Array.isArray(data.list)
                    ? data.list
                    : Object.values(data.list);

                listData = listData.filter(
                    (newContact) =>
                        !this.$store.getters.get_contacts.some(
                            (existingContact) =>
                                existingContact.id === newContact.id
                        )
                );

                this["saving_contact_list"](listData);
                this.contactloader = false;
                this.getAllPictProfile(listData);
            } catch (error) {
                this.$handleErrorResponse(error);
            }
        },

        async getAllPictProfile(listData) {
            this.syncronizePhotoProfile(listData);
        },

        async firstGetData() {
            if (
                this.$route.params.id &&
                this.$store.getters.get_contacts.length == 0
            ) {
                await this.getContacts("");
            }

            if (
                this.chats.list.length === 0 &&
                this.$route.params.id &&
                this.$store.getters.get_active_tab === "chat"
            ) {
                await this.getChatList("");
            }
            if (
                this.groups.list.length === 0 &&
                this.$route.params.id &&
                this.$store.getters.get_active_tab === "group"
            ) {
                await this.getGroupList("");
            }
        },
    },

    mounted() {
        this.setAllLoader();
    },

    watch: {
        $route(to, from) {
            this.firstGetData();
        },

        "$store.getters.get_active_tab": function (newTab, oldTab) {
            if (this.$route.params.id) {
                this.stopFetchingChats();
                this.startFetchingChats();
            }
        },
    },
};
</script>
<style>
.lds-dual-ring,
.lds-dual-ring:after {
    box-sizing: border-box;
}
.lds-dual-ring {
    display: inline-block;
    width: 80px;
    height: 80px;
    color: #005d4c;
}
.lds-dual-ring:after {
    content: " ";
    display: block;
    width: 64px;
    height: 64px;
    margin: 8px;
    border-radius: 50%;
    border: 6.4px solid currentColor;
    border-color: currentColor transparent currentColor transparent;
    animation: lds-dual-ring 1.2s linear infinite;
}
@keyframes lds-dual-ring {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

/* Selector untuk custom scrollbar */
.chat-users-wrap {
    height: 80vh;
    scrollbar-width: thin; /* Untuk Firefox */
    scrollbar-color: #888 #f1f1f1; /* Untuk Firefox */
}
</style>
