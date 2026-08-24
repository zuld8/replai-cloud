<template>
    <div class="blank-page-wrapper">
        <button class="mobile-menu-toggle d-lg-none" @click="$emit('toggle-left-sidebar')">
            <i class='bx bx-menu'></i>
        </button>
        <div class="welcome-content">
            <div class="welcome-container">
                <div class="text-center">
                    <!-- Logo -->
                    <div class="mb-4">
                        <img :src="attribute.icon" :alt="attribute.app_name" class="welcome-logo" />
                    </div>

                    <!-- Welcome Text with i18n -->
                    <h2 class="welcome-title">{{ $t('sidebar.welcome') }} {{ attribute.app_name }}</h2>
                    <p class="welcome-subtitle">{{ $t('sidebar.select_contact') }}</p>

                    <!-- Feature Icons -->
                    <div class="feature-grid">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="ti ti-users"></i>
                            </div>
                            <small class="feature-label">{{ $t('sidebar.manage_chat') }}</small>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="ti ti-message-circle"></i>
                            </div>
                            <small class="feature-label">{{ $t('sidebar.realtime_chat') }}</small>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="bx bx-data"></i>
                            </div>
                            <small class="feature-label">{{ $t('sidebar.manage_contact') }}</small>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="welcome-actions">
                        <a href="javascript:void(0);" @click="newChat" class="btn btn-primary btn-lg">
                            {{ $t('contact.start_chat') }}
                        </a>
                    </div>

                    <!-- Additional Info -->
                    <div class="welcome-info mt-4">
                        <p class="text-muted small">
                            <i class="ti ti-info-circle me-1"></i>
                            {{ $t('sidebar.use_sidebar_info') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal New Chat -->
        <div class="modal fade" id="newChatModal" ref="newChatModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $t('contact.new_chat') }}</h5>
                        <button type="button" class="btn-close" id="closeModalNewChat" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="contact-blank-tab" data-bs-toggle="tab"
                                    data-bs-target="#contact-blank-pane" type="button" role="tab">
                                    {{ $t('contact.from_contacts') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="phone-blank-tab" data-bs-toggle="tab"
                                    data-bs-target="#phone-blank-pane" type="button" role="tab">
                                    {{ $t('contact.add_new_contact') || 'Tambah Kontak Baru' }}
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Contact Tab -->
                            <div class="tab-pane fade show active" id="contact-blank-pane" role="tabpanel" tabindex="0">
                                                                <!-- Device/Channel dropdown -->
                                <div class="mb-2">
                                    <select class="form-select form-select-sm" v-model="selectedModalDevice">
                                        <option value="" disabled>Pilih Device / Channel</option>
                                        <option v-for="device in deviceList" :key="device.id" :value="device.id">
                                            {{ device.phone ? device.phone + ' (' + device.name + ')' : device.name }}
                                        </option>
                                    </select>
                                </div>
<div class="mb-3">
                                    <div class="search-input-group">
                                        <i class='bx bx-search'></i>
                                        <input type="text" class="form-control" v-model="modalContactSearch"
                                            :placeholder="$t('contact.search_contact')" />
                                    </div>
                                </div>

                                <div class="contact-list-modal" @scroll="handleModalContactScroll">
                                    <!-- Loading state -->
                                    <div v-if="modalContactsLoading"
                                        class="d-flex justify-content-center align-items-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>

                                    <!-- Contact list -->
                                    <template v-else>
                                        <div v-for="item in filteredModalContacts" :key="item.id"
                                            class="contact-item-modal" @click="newChatContact(item)"
                                            @mouseover="hoveredContactId = item.id"
                                            @mouseleave="hoveredContactId = null"
                                            :style="hoveredContactId === item.id ? 'background:#f5faff;' : ''">
                                            <img :src="item.photo || item.avatar_url || attribute.user"
                                                class="contact-avatar" alt="Avatar" />
                                            <div class="contact-info-modal">
                                                <div class="contact-name">{{ item.name }}</div>
                                                <div class="contact-phone">{{ item.phone }}</div>
                                            </div>
                                        </div>

                                        <!-- Load more indicator -->
                                        <div v-if="modalContactsLoadingMore"
                                            class="d-flex justify-content-center align-items-center py-2">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Empty State -->
                                    <div v-if="!modalContactsLoading && filteredModalContacts.length === 0"
                                        class="empty-state-modal">
                                        <i class='bx bx-user-x'></i>
                                        <p>{{ $t('contact.contact_not_found') }}</p>
                                    </div>
                                </div>

                            </div>

                            <!-- Phone Tab -->
                            <div class="tab-pane fade" id="phone-blank-pane" role="tabpanel" tabindex="0">
                                <div class="mb-2">
                                    <label class="form-label">{{ $t('contact.name') || 'Nama' }}</label>
                                    <input type="text" class="form-control" v-model="newContact.name"
                                        :placeholder="$t('contact.name_placeholder') || 'Masukkan nama kontak'" />
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">{{ $t('contact.phone_number') || 'Nomor Telepon'
                                        }}</label>
                                    <input type="text" class="form-control" v-model="newContact.phone"
                                        :placeholder="$t('contact.phone_placeholder') || 'Contoh: 628123456789'"
                                        @input="validatePhoneInput" />
                                    <small class="form-text text-muted">
                                        {{ $t('contact.phone_hint') || 'Awalan 0 akan diganti otomatis menjadi 62' }}
                                    </small>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">{{ $t('contact.device') || 'Device' }}</label>
                                    <select class="form-select" v-model="newContact.deviceId">
                                        <option value="" disabled>{{ $t('contact.select_device') || 'Pilih Device' }}
                                        </option>
                                        <option v-for="device in deviceList" :key="device.id" :value="device.id">
                                            {{ device.phone }} ({{ device.name }})
                                        </option>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-secondary" @click="resetAddContact">
                                        {{ $t('common.cancel') || 'Batal' }}
                                    </button>
                                    <button class="btn btn-success" @click="saveContact"
                                        :disabled="!newContact.name || !newContact.phone || !newContact.deviceId">
                                        {{ $t('common.save') || 'Simpan' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import imageLocation from "@/assets/icons/new-message.png";
import imageUser from "@/assets/icons/user.png";

export default {
    name: "BlankPage",

    data() {
        return {
            contact: {
                search: "",
                phone: "",
            },
            attribute: {
                app_name: "",
                name: "",
                icon: "",
                logo: "",
                image: imageLocation,
                user: imageUser,
                modal: false,
            },
            // Modal contacts dengan infinite scroll
            modalContacts: [],
            modalContactsPage: 1,
            modalContactsLoading: false,
            modalContactsLoadingMore: false,
            modalContactsHasMore: true,
            modalContactSearch: "",
            hoveredContactId: null,

            // New contact form
            showAddContact: false,
            newContact: {
                name: "",
                phone: "",
                deviceId: "",
            },
            deviceList: [],
            selectedModalDevice: "",
        };
    },

    computed: {
        filteredModalContacts() {
            if (!this.modalContactSearch) return this.modalContacts;
            const search = this.modalContactSearch.toLowerCase();
            return this.modalContacts.filter(
                (c) =>
                    (c.name && c.name.toLowerCase().includes(search)) ||
                    (c.phone && c.phone.toLowerCase().includes(search))
            );
        },
    },

    methods: {
        /**
         * Get system attributes (logo, app name, etc)
         */
        async getLogo() {
            try {
                const response = await this.$axios.get(`/components/system`);
                this.attribute.app_name = response.data.app_name || response.data.name;
                this.attribute.name = response.data.name;
                this.attribute.icon = response.data.icon;
                this.attribute.logo = response.data.logo;
            } catch (error) {
                console.error(error);
            }
        },

        /**
         * Get contacts list for modal with infinite scroll
         */
        async fetchModalContacts(loadMore = false) {
            if (loadMore) {
                this.modalContactsLoadingMore = true;
            } else {
                this.modalContactsLoading = true;
                this.modalContactsPage = 1;
            }

            try {
                const response = await this.$axios.get(`/crm/stores`, {
                    params: {
                        page: this.modalContactsPage,
                        limit: 10,
                        name: this.modalContactSearch,
                    },
                });

                const data = response.data.contacts || [];

                if (loadMore) {
                    this.modalContacts = [...this.modalContacts, ...data];
                } else {
                    this.modalContacts = data;
                }

                this.modalContactsHasMore = data.length >= 10;
            } catch (error) {
                console.error('Error fetching contacts:', error);
                if (this.$handleErrorResponse) {
                    this.$handleErrorResponse(error);
                }
            } finally {
                this.modalContactsLoading = false;
                this.modalContactsLoadingMore = false;
            }
        },

        /**
         * Handle scroll untuk load more contacts
         */
        handleModalContactScroll(e) {
            const el = e.target;
            if (
                !this.modalContactsLoadingMore &&
                this.modalContactsHasMore &&
                el.scrollTop + el.clientHeight >= el.scrollHeight - 40
            ) {
                this.modalContactsPage++;
                this.fetchModalContacts(true);
            }
        },

        /**
         * Get device list untuk dropdown
         */
        async fetchDeviceList() {
            try {
                const response = await this.$axios.get("/master/components/devices");
                this.deviceList = response.data;
            } catch (error) {
                console.error('Error fetching device list:', error);
                this.deviceList = [];
            }
        },

        /**
         * Open new chat modal
         */
        newChat() {
            this.contact.search = "";
            this.contact.phone = "";
            this.showAddContact = false;
            this.newContact = { name: "", phone: "", deviceId: "" };
            this.modalContacts = [];
            this.modalContactsPage = 1;
            this.modalContactsHasMore = true;
            this.modalContactsLoading = true;
            this.modalContactSearch = "";
            this.selectedModalDevice = "";

            const modal = new bootstrap.Modal(this.$refs.newChatModal, {
                backdrop: false,
                keyboard: false,
            });
            modal.show();

            this.fetchModalContacts();
        },

        /**
         * Start chat by phone number
         */
        async newChatPhone() {
            if (!this.contact.phone) {
                if (this.$showToast) {
                    this.$showToast('Nomor telepon wajib diisi', 'error', 3000);
                } else {
                    alert('Nomor telepon wajib diisi');
                }
                return;
            }

            try {
                this.closeModal();

                return this.$router.push({
                    name: "chat_room",
                    params: {
                        id: this.$route.params.id,
                        chatid: `${this.contact.phone}@s.whatsapp.net`,
                    },
                    query: {
                        name: this.contact.phone,
                        photo: this.attribute.user,
                    },
                });
            } catch (error) {
                console.error('Error starting chat:', error);
                if (this.$handleErrorResponse) {
                    this.$handleErrorResponse(error);
                }
            }
        },

        
        /**
         * Start chat from contact — POST dulu bikin HistoryChat, baru navigate
         */
        async newChatContact(detail) {
            // Kalau dari saveContact (sudah POST), langsung navigate
            if (detail._ready) {
                this.closeModal();
                const isWaba = detail._type === 'waba';
                return this.$router.push({
                    name: "chat_room",
                    params: { id: this.$route.params.id, chatid: detail.id },
                    query: {
                        name: detail.name,
                        photo: detail.photo || this.attribute.user,
                        ...(isWaba ? { tpl: '1' } : {}),
                    },
                });
            }

            // Tentukan device dari dropdown modal
            const dev = this.getSelectedModalDevice();
            if (!dev) {
                const msg = 'Pilih device/channel terlebih dahulu';
                this.$showToast ? this.$showToast(msg, 'error', 3000) : alert(msg);
                return;
            }

            try {
                const type = this.mapDeviceType(dev.from);
                const { data } = await this.$axios.post('/crm/contacts', {
                    name:      detail.name || detail.phone,
                    phone:     detail.phone,
                    type:      type,
                    device_id: dev.id,
                    store_id:  detail.id || null,
                });

                const history = data.contact;
                this.closeModal();

                const isWaba = type === 'waba';
                this.$router.push({
                    name: 'chat_room',
                    params: { id: this.$route.params.id, chatid: history.id },
                    query: {
                        name: history.name || detail.name,
                        photo: detail.photo || this.attribute.user,
                        ...(isWaba ? { tpl: '1' } : {}),
                    },
                });
            } catch (e) {
                const msg = e.response?.data?.message || 'Gagal membuat percakapan';
                this.$showToast ? this.$showToast(msg, 'error', 4000) : alert(msg);
                console.error('newChatContact error', e.response?.data || e);
            }
        },

        /**
         * Save new contact
         */
        async saveContact() {
            if (!this.newContact.name || !this.newContact.phone || !this.newContact.deviceId) {
                if (this.$showToast) {
                    this.$showToast('Semua field wajib diisi', 'error', 3000);
                } else {
                    alert('Semua field wajib diisi');
                }
                return;
            }

            try {
                const deviceIndex = this.deviceList.findIndex(
                    (c) => c.id === this.newContact.deviceId
                );

                if (deviceIndex === -1) {
                    const msg = 'Device tidak valid';
                    this.$showToast ? this.$showToast(msg, 'error', 3000) : alert(msg);
                    return;
                }

                const deviceDetail = this.deviceList[deviceIndex];
                const type = this.mapDeviceType(deviceDetail.from);

                const response = await this.$axios.post("/crm/contacts", {
                    name:      this.newContact.name,
                    phone:     this.newContact.phone,
                    type:      type,
                    device_id: deviceDetail.id,
                });

                if (this.$showToast) {
                    this.$showToast('Kontak berhasil ditambahkan', 'success', 3000);
                }

                this.resetAddContact();
                const contact = response.data.contact;
                this.newChatContact({ ...contact, _ready: true, _type: type });

            } catch (error) {
                console.error('Error saving contact:', error);
                if (this.$handleErrorResponse) {
                    this.$handleErrorResponse(error);
                }
            }
        },

        /**
         * Reset add contact form
         */
        resetAddContact() {
            this.showAddContact = false;
            this.newContact = { name: "", phone: "", deviceId: "" };
        },

        /**
         * Validate phone input for new contact
         */
        validatePhoneInput(event) {
            let value = event.target.value;
            // Remove non-numeric characters
            value = value.replace(/[^0-9]/g, "");
            // Replace leading 0 with 62
            if (value.startsWith("0")) {
                value = "62" + value.slice(1);
            }
            this.newContact.phone = value;
        },

        /**
         * Validate phone input for direct chat
         */
        validatePhoneInputDirect(event) {
            let value = event.target.value;
            // Remove non-numeric characters
            value = value.replace(/[^0-9]/g, "");
            // Replace leading 0 with 62
            if (value.startsWith("0")) {
                value = "62" + value.slice(1);
            }
            this.contact.phone = value;
        },

        /**
         * Mapping device type dari ComponentController ke backend type
         */
        mapDeviceType(from) {
            const map = {
                unofficial: 'whatsapp',
                waba:       'waba',
                telegram:   'telegram',
                instagram:  'instagram',
                messanger:  'messanger',
                livechat:   'livechat',
            };
            return map[from] || 'whatsapp';
        },

        /**
         * Dapatkan device terpilih di modal
         */
        getSelectedModalDevice() {
            if (!this.selectedModalDevice) {
                if (this.deviceList.length === 1) return this.deviceList[0];
                return null;
            }
            return this.deviceList.find(d => d.id === this.selectedModalDevice) || null;
        },


        /**
         * Close modal
         */
        closeModal() {
            const modal = bootstrap.Modal.getInstance(this.$refs.newChatModal);
            if (modal) {
                modal.hide();
            }

            // Reset form
            this.contact.search = "";
            this.contact.phone = "";
            this.showAddContact = false;
            this.newContact = { name: "", phone: "", deviceId: "" };
        }
    },

    mounted() {
        this.getLogo();
        this.fetchDeviceList();
    },
};
</script>

<style scoped>
.blank-page-wrapper {
    flex: 1;
    display: flex;
    min-height: 100%;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.welcome-content {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.welcome-container {
    max-width: 600px;
    width: 100%;
}

.welcome-logo {
    max-width: 120px;
    height: auto;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
}

.welcome-title {
    font-size: 28px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 12px;
}

.welcome-subtitle {
    font-size: 16px;
    color: #6b7280;
    margin-bottom: 40px;
}

.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.feature-item {
    text-align: center;
}

.feature-icon {
    width: 64px;
    height: 64px;
    background-color: #fff;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
}

.feature-icon:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.feature-icon i {
    font-size: 28px;
    color: #005C4B;
}

.feature-label {
    display: block;
    color: #6b7280;
    font-size: 14px;
}

.welcome-actions {
    margin-top: 32px;
}

.welcome-info {
    padding-top: 24px;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

/* Modal Styles */
.search-input-group {
    display: flex;
    align-items: center;
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 6px 10px;
    transition: all 0.2s;
}

.search-input-group:focus-within {
    border-color: #005C4B;
    background-color: #fff;
}

.search-input-group i {
    color: #9ca3af;
    font-size: 18px;
}

.search-input-group input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    padding: 0 8px;
    font-size: 13px;
    color: #374151;
}

.contact-list-modal {
    max-height: 350px;
    overflow-y: auto;
}

.contact-item-modal {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 4px;
}

.contact-item-modal:hover {
    background-color: #f0f9ff;
}

.contact-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.contact-info-modal {
    flex: 1;
    min-width: 0;
}

.contact-name {
    font-weight: 600;
    font-size: 14px;
    color: #1f2937;
    margin-bottom: 2px;
}

.contact-phone {
    font-size: 12px;
    color: #6b7280;
}

.empty-state-modal {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}

.empty-state-modal i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

.empty-state-modal p {
    margin: 0;
    font-size: 14px;
}

/* Spinner styles */
.spinner-border-sm {
    width: 1.5rem;
    height: 1.5rem;
    border-width: 0.2em;
}

/* Responsive */
@media (max-width: 768px) {
    .welcome-title {
        font-size: 24px;
    }

    .welcome-subtitle {
        font-size: 14px;
    }

    .feature-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .welcome-logo {
        max-width: 80px;
    }
}
/* Button Hamburger untuk Mobile - hanya di blank page */
.mobile-menu-toggle {
    position: fixed;
    top: 70px; /* sesuaikan dengan header height */
    left: 15px;
    z-index: 999;
    width: 45px;
    height: 45px;
    border: none;
    border-radius: 50%;
    background: #005C4B;
    color: white;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mobile-menu-toggle:hover {
    background: #004d3e;
    transform: scale(1.05);
}

.mobile-menu-toggle:active {
    transform: scale(0.95);
}
</style>