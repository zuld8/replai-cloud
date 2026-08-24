<template>
    <div class="sidebar-left" id="leftSidebar">
        <button class="sidebar-close-btn d-lg-none" @click="$emit('close-sidebar')">
            <i class='bx bx-x'></i>
        </button>
        <!-- Sidebar Header dengan Logo dan Nama -->
        <div class="sidebar-header-top">
            <router-link :to="{ name: 'blank_chat' }" class="d-flex align-items-center gap-2">
                <img :src="attribute.icon" :alt="attribute.name" class="logo-crm" />
                <span class="app-name">{{ attribute.name }}</span>
            </router-link>
        </div>

        <!-- Filter Channel -->
        <div class="channel-filter-section">
            <div class="select-icon">
                <i class="bx bx-globe" style="color:#2E8DE1"></i>
                <select class="channel-select" v-model="filter.from" @change="applyFilter">
                    <option value="">{{ $t('sidebar.all_channel') }}</option>
                    <option value="whatsapp">{{ $t('channel.whatsapp') }}</option>
                    <option value="waba">{{ $t('channel.waba') }}</option>
                    <option value="livechat">{{ $t('channel.livechat') }}</option>
                    <option value="telegram">{{ $t('channel.telegram') }}</option>
                    <option value="instagram">{{ $t('channel.instagram') }}</option>
                    <option value="messanger">{{ $t('channel.messenger') }}</option>
                </select>
            </div>
        </div>

        <!-- Search -->
        <div class="chat-search">
            <div class="search-wrapper">
                <div class="search-input-group">
                    <i class='bx bx-search'></i>
                    <input type="text" v-model="filter.name" @input="searchData()"
                        :placeholder="$t('sidebar.search_message')" />
                </div>
                <button class="filter-btn" @click="openFilter()" :title="$t('filter.filter_data')">
                    <i class='bx bx-filter'></i>
                </button>
            </div>

            <!-- Button Mulai Percakapan -->
            <div class="new-chat-section">
                <button class="btn-new-chat" @click="openContactModal">
                    <i class='bx bx-message-add'></i>
                    <span>{{ $t('sidebar.new_conversation') }}</span>
                </button>
            </div>

            <!-- Filter Device -->
            <div class="device-filter-section">
                <div class="select-icon">
                    <i class="bx bx-mobile-alt" style="color:#5B3FB0"></i>
                    <select class="device-select" v-model="platform_devices" @change="onDeviceChange">
                        <option :value="null">{{ $t('sidebar.all_device') }}</option>
                        <option v-for="device in deviceList" :key="device.id" :value="device">
                            [{{ device.type }}] {{ device.name }}{{ device.phone ? ' · ' + device.phone : '' }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="assignment-tabs">
                <button class="tab-assignment" :class="{ active: activeAssignmentTab === '' }"
                    @click="switchAssignmentTab('')" title="Semua percakapan">
                    <span>Semua</span>
                </button>
                <button class="tab-assignment" :class="{ active: activeAssignmentTab === 'unread' }"
                    @click="switchAssignmentTab('unread')" title="Belum di baca">
                    <span>Belum dibaca</span>
                </button>
                <button class="tab-assignment" :class="{ active: activeAssignmentTab === 'assignment' }"
                    @click="switchAssignmentTab('assignment')" title="Percakapan yang diambil alih Agent">
                    <span>Ditugaskan</span>
                </button>
                <button class="tab-assignment" :class="{ active: activeAssignmentTab === 'unassignment' }"
                    @click="switchAssignmentTab('unassignment')" title="Percakapan yang belum di-assign">
                    <span>Belum ditugaskan</span>
                </button>
                <button class="tab-assignment" :class="{ active: activeAssignmentTab === 'block' }"
                    @click="switchAssignmentTab('block')" title="Percakapan yang diblokir">
                    <span>Diblokir</span>
                </button>
            </div>

        </div>

        <!-- Chat List Content -->
        <div class="chat-list" @scroll="handleChatScroll">


            <!-- Chat Items -->
            <div class="chat-items-container">
                <div class="chat-item" v-for="list in chats.list" :key="list.id"
                     :class="{ active: $route.params.chatid === list.id, unread: list.not_read > 0 }"
                     @click="selectChat(list)">

                    <!-- AVATAR: foto orang / defaultUserIcon; logo channel di badge kecil saja -->
                    <div class="ci-avatar">
                        <img :src="(list.photo && !list.photo.includes('default') && !list.photo.includes('user.png'))
                                    ? list.photo : defaultUserIcon"
                             class="ci-img"
                             loading="lazy"
                             @error="$event.target.src=defaultUserIcon"/>
                        <span class="ci-badge" :class="`ci-badge--${list.from}`">
                            <i :class="getChannelIcon(list.from)"></i>
                        </span>
                    </div>

                    <!-- BODY -->
                    <div class="ci-body">
                        <div class="ci-main">
                            <!-- Tengah: nama + preview -->
                            <div class="ci-center">
                                <div class="ci-name-row">
                                    <span class="ci-name">{{ list.name }}</span>
                                    <!-- Chip sesi 24 jam WABA -->
                                    <template v-if="list.from === 'waba' && list.last_inbound_at">
                                        <span v-if="getWabaSessionStatus(list.last_inbound_at)"
                                              class="ci-24h"
                                              :title="getWabaSessionStatus(list.last_inbound_at).status === 'expired' ? 'Sesi WhatsApp 24 jam habis — kirim template untuk memulai' : 'Sesi WhatsApp aktif — sisa waktu untuk kirim pesan biasa'"
                                              :class="{
                                                  'active': getWabaSessionStatus(list.last_inbound_at).status === 'active',
                                                  'soon':   getWabaSessionStatus(list.last_inbound_at).status === 'warning',
                                                  'closed': getWabaSessionStatus(list.last_inbound_at).status === 'expired'
                                              }">
                                            <i :class="getWabaSessionStatus(list.last_inbound_at).status === 'expired' ? 'bx bx-lock-alt' : 'bx bx-time'"></i>
                                            {{ getWabaSessionStatus(list.last_inbound_at).label }}
                                        </span>
                                    </template>
                                    <!-- Lead chip — sebelah jam aktif (Bug2 fix) -->
                                    <span v-if="list.lead_source && list.lead_source !== 'organic'"
                                          class="ci-chip lead-chip ci-lead-inline"
                                          :class="`lead-source-${list.lead_source}`"
                                          :title="leadChipLabel(list.lead_source)">
                                        <i :class="leadChipIcon(list.lead_source)"></i>
                                        {{ leadChipLabel(list.lead_source) }}
                                    </span>
                                </div>
                                <div class="ci-preview">
                                    <i v-if="list.media_type==='image'" class="bx bx-image-alt"></i>
                                    <i v-else-if="list.media_type==='video'" class="bx bx-video"></i>
                                    <i v-else-if="list.media_type==='audio'" class="bx bx-microphone"></i>
                                    <i v-else-if="isDocumentType(list.media_type)" class="bx bx-file"></i>
                                    {{ truncateText(list.last_message.message, 32) }}
                                </div>
                            </div>
                            <!-- Kanan: notif+waktu / status saja -->
                            <div class="ci-right">
                                <div class="ci-top">
                                    <span class="ci-unread" v-if="list.not_read > 0">{{ list.not_read }}</span>
                                    <span class="ci-time">{{ list.last_message.time || list.last_message.date }}</span>
                                </div>
                                <div class="ci-status-row">
                                    <i v-if="list.takeover === 'no' || list.takeover === false"
                                       class="ci-bot bx bx-bot"
                                       title="Bot aktif — balasan otomatis sedang jalan"></i>
                                    <span class="ci-status" :class="`st-${list.status}`">{{ getStatusText(list.status) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- BARIS AKUN PENUH: nama akun kanan + ⋯ selalu tampil -->

                        <div class="ci-acctrow" v-if="list.device || list.telegram || list.livechat || list.instagram">
                            <span class="ci-dev" :title="list.device||list.telegram||list.livechat||list.instagram">
                                <i :class="getChannelIcon(list.from)"></i>{{ list.device||list.telegram||list.livechat||list.instagram }}
                            </span>
                            <div class="chat-dropdown" @click.stop>
                                <button class="ci-dots"
                                        :class="{active: activeDropdown === list.id}"
                                        @click="toggleDropdown(list.id)">
                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                </button>
                                <div class="dropdown-menu-chat" :class="{show: activeDropdown === list.id}">
                                    <div class="dropdown-item-chat" @click="togglePin(list)">
                                        <i :class="list.is_pinned ? 'bx bxs-pin pin-active' : 'bx bx-pin'"></i>
                                        <span>{{ list.is_pinned ? 'Lepas Sematan' : 'Sematkan' }}</span>
                                    </div>
                                    <div class="dropdown-item-chat" @click="resolveChat(list)">
                                        <i :class="list.status === 'resolved' ? 'bx bx-refresh c-resolve' : 'bx bx-check-circle c-resolve'"></i>
                                        <span>{{ list.status === 'resolved' ? 'Buka Kembali' : 'Selesaikan' }}</span>
                                    </div>
                                    <div class="dropdown-item-chat" @click="openAssignModal(list)">
                                        <i class="bx bx-user-plus c-assign"></i>
                                        <span>Tugaskan Agen</span>
                                    </div>
                                    <div class="dropdown-item-chat" @click="openLabelModal(list)">
                                        <i class="bx bx-label c-label"></i>
                                        <span>Label</span>
                                    </div>
                                    <div class="dropdown-item-chat" @click="list.not_read > 0 ? markRead(list) : markUnread(list)">
                                        <i :class="list.not_read > 0 ? 'bx bx-envelope-open' : 'bx bx-envelope'"></i>
                                        <span>{{ list.not_read > 0 ? 'Tandai Dibaca' : 'Tandai Belum Dibaca' }}</span>
                                    </div>
                                    <div class="dropdown-divider-line"></div>
                                    <div class="dropdown-item-chat has-submenu"
                                         @mouseenter="showSubMenu = list.id"
                                         @mouseleave="showSubMenu = null">
                                        <i class="bx bx-dots-horizontal-rounded"></i>
                                        <span>Lainnya</span>
                                        <i class="bx bx-chevron-right submenu-arrow"></i>
                                        <div class="dropdown-submenu" v-show="showSubMenu === list.id">
                                            <div class="dropdown-item-chat" @click.stop="toggleArchive(list)">
                                                <i :class="list.is_archived ? 'bx bx-archive-out c-archive' : 'bx bx-archive-in c-archive'"></i>
                                                <span>{{ list.is_archived ? 'Keluarkan Arsip' : 'Arsip' }}</span>
                                            </div>
                                            <div class="dropdown-item-chat" @click.stop="blockChat(list)">
                                                <i class="bx bx-block c-block"></i>
                                                <span>{{ list.status === 'block' ? 'Buka Blokir' : 'Blokir' }}</span>
                                            </div>
                                            <div class="dropdown-item-chat" @click.stop="confirmAction('clear', list.id)">
                                                <i class="bx bx-eraser c-clear"></i>
                                                <span>Hapus Riwayat Chat</span>
                                            </div>
                                            <div class="dropdown-item-chat c-danger" @click.stop="confirmAction('delete', list.id)">
                                                <i class="bx bx-trash"></i>
                                                <span>Hapus Percakapan</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BARIS LABEL (kiri) -->
                        <div class="ci-labels" v-if="list.labels && list.labels.length">
                            <span v-for="lbl in list.labels.slice(0,2)" :key="lbl.id"
                                  class="ci-chip"
                                  :style="{background:(lbl.color||'#888')+'1f', color:lbl.color||'#475569'}">
                                <i class="bx bx-purchase-tag"></i>{{ lbl.name }}
                            </span>
                            <span v-if="list.labels.length > 2"
                                  class="ci-more"
                                  @click.stop="openLabelModal(list)">+{{ list.labels.length - 2 }}</span>
                        </div>
                    </div>

                </div>
                <!-- Empty State -->
                <div v-if="!chats.loader && chats.list.length === 0" class="empty-state">
                    <i class='bx bx-message-square-detail'></i>
                    <p>{{ $t('sidebar.no_conversation') }}</p>
                </div>

                <!-- Loader -->
                <div v-if="chats.loader" class="loader-container">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ $t('common.loading') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Filter -->
        <div class="modal fade" id="filterModal" ref="filterModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $t('filter.filter_data') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ $t('filter.start_date') }}</label>
                                <input type="date" class="form-control" v-model="filter.start_date" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $t('filter.end_date') }}</label>
                                <input type="date" class="form-control" v-model="filter.end_date" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $t('filter.chat_status') }}</label>
                                <select class="form-select" v-model="filter.status">
                                    <option value="">{{ $t('filter.all') }}</option>
                                    <option value="open">{{ $t('status.open') }}</option>
                                    <option value="resolved">{{ $t('status.resolved') }}</option>
                                    <option value="block">{{ $t('status.block') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $t('filter.platform') }}</label>
                                <select class="form-select" v-model="filter.from">
                                    <option value="">{{ $t('filter.all') }}</option>
                                    <option value="whatsapp">{{ $t('channel.whatsapp') }}</option>
                                    <option value="waba">{{ $t('channel.waba') }}</option>
                                    <option value="livechat">{{ $t('channel.livechat') }}</option>
                                    <option value="telegram">{{ $t('channel.telegram') }}</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $t('filter.handled_by') || 'Handled By' }}</label>
                                <select class="form-select" v-model="filter.handled">
                                    <option value="">{{ $t('filter.all') || 'Semua Agent' }}</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ 'Label' }}</label>
                                <div v-for="(label, index) in labelList" :key="index" class="form-check">
                                    <input class="form-check-input" type="checkbox" :value="label.id"
                                        v-model="filter.label" :id="'label_' + index" />
                                    <label class="form-check-label" :for="'label_' + index">
                                        {{ label.name }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="resetFilter">
                            <i class='bx bx-reset me-1'></i>
                            {{ $t('filter.reset_filter') }}
                        </button>
                        <button type="button" class="btn btn-primary" @click="applyFilter">
                            <i class='bx bx-check me-1'></i>
                            {{ $t('filter.apply_filter') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Contact -->
        <div class="modal fade" id="contactModal" ref="contactModal" tabindex="-1">

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
                                <button class="nav-link active" id="contact-tab" data-bs-toggle="tab"
                                    data-bs-target="#contact-pane" type="button" role="tab">
                                    {{ $t('contact.from_contacts') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="phone-tab" data-bs-toggle="tab"
                                    data-bs-target="#phone-pane" type="button" role="tab">
                                    {{ $t('contact.add_new_contact') || 'Tambah Kontak Baru' }}
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Contact Tab -->
                            <div class="tab-pane fade show active" id="contact-pane" role="tabpanel" tabindex="0">
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
                                            class="contact-item-modal" @click="selectExistingContact(item)"
                                            @mouseover="hoveredContactId = item.id"
                                            @mouseleave="hoveredContactId = null"
                                            :style="hoveredContactId === item.id ? 'background:#f5faff;' : ''">
                                            <img :src="item.photo || item.avatar_url || attribute.user"
                                                class="contact-avatar" alt="Avatar" loading="lazy" />
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
                            <div class="tab-pane fade" id="phone-pane" role="tabpanel" tabindex="0">
                                <div class="mb-2">
                                    <label class="form-label">{{ $t('contact.name') || 'Nama' }}</label>
                                    <input type="text" class="form-control" v-model="newContact.name"
                                        :placeholder="$t('contact.name_placeholder') || 'Masukkan nama kontak'" />
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">{{ $t('contact.phone_number') || 'Nomor Telepon'
                                    }}</label>
                                    <input type="text" class="form-control" v-model="newContact.phone"
                                        :placeholder="$t('contact.phone_placeholder') || 'Contoh: 628123456789'" />
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

        <div class="modal fade" id="deviceSelectorModal" ref="deviceSelectorModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Device untuk Chat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <p class="text-muted mb-3">
                                Pilih device untuk memulai percakapan dengan <strong>{{ selectedContact.name }}</strong>
                            </p>
                            <label class="form-label">Device / Channel</label>
                            <select class="form-select" v-model="selectedDeviceId">
                                <option value="" disabled>Pilih Device</option>
                                <option v-for="device in deviceList" :key="device.id" :value="device.id">
                                    {{ device.phone }} ({{ device.name }})
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="button" class="btn btn-primary" @click="createChatSession"
                            :disabled="!selectedDeviceId || creatingChat">
                            <span v-if="creatingChat">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Memproses...
                            </span>
                            <span v-else>
                                Mulai Chat
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Dialog -->
    <transition name="crm-fade">
        <div v-if="pendingConfirm" class="crm-modal-overlay" @click.self="pendingConfirm = null">
            <div class="crm-confirm-dialog">
                <div class="crm-confirm-icon" :class="pendingConfirm.type === 'delete' ? 'c-icon-danger' : 'c-icon-warn'">
                    <i :class="pendingConfirm.type === 'delete' ? 'bx bx-trash' : 'bx bx-eraser'"></i>
                </div>
                <h4 class="crm-confirm-title">
                    {{ pendingConfirm.type === 'delete' ? 'Hapus Percakapan?' : 'Hapus Riwayat Chat?' }}
                </h4>
                <p class="crm-confirm-desc">
                    <template v-if="pendingConfirm.type === 'delete'">
                        Percakapan ini akan dihapus. Tindakan ini <strong>tidak dapat dibatalkan</strong>.
                    </template>
                    <template v-else>
                        Semua pesan akan dihapus secara permanen, namun percakapan tetap ada.
                    </template>
                </p>
                <div class="crm-confirm-actions">
                    <button class="btn-crm-cancel" @click="pendingConfirm = null">Batal</button>
                    <button class="btn-crm-confirm" :class="pendingConfirm.type === 'delete' ? 'btn-crm-danger' : 'btn-crm-warn'"
                            @click="executeConfirmedAction">
                        {{ pendingConfirm.type === 'delete' ? 'Hapus' : 'Hapus Riwayat' }}
                    </button>
                </div>
            </div>
        </div>
    </transition>
    <!-- Assign Agent Modal -->
    <transition name="crm-fade">
        <div v-if="assignModal" class="crm-modal-overlay" @click.self="assignModal = null">
            <div class="crm-confirm-dialog crm-assign-dialog">
                <h4 class="crm-confirm-title">Assign Agent</h4>
                <div class="crm-agent-list">
                    <div v-for="agent in agentsList" :key="agent.id"
                         class="crm-agent-item" @click="assignAgent(assignModal, agent.id)">
                        <div class="crm-agent-avatar">{{ agent.name ? agent.name.charAt(0).toUpperCase() : '?' }}</div>
                        <div class="crm-agent-info">
                            <span class="crm-agent-name">{{ agent.name }}</span>
                            <span class="crm-agent-email">{{ agent.email }}</span>
                        </div>
                    </div>
                    <p v-if="agentsList.length === 0" class="crm-confirm-desc" style="margin:12px 0">Memuat...</p>
                </div>
                <button class="btn-crm-cancel" @click="assignModal = null" style="width:100%;margin-top:12px">Batal</button>
            </div>
        </div>
    </transition>
    <!-- Label Picker Modal -->
    <transition name="crm-fade">
        <div v-if="labelModal" class="crm-modal-overlay" @click.self="labelModal = null">
            <div class="crm-confirm-dialog crm-assign-dialog">
                <h4 class="crm-confirm-title">Pilih Label</h4>
                <div class="crm-agent-list">
                    <div v-for="lbl in labelsList" :key="lbl.id"
                         class="crm-agent-item" @click="changeLabel(labelModal, lbl)">
                        <i class="bx bx-label" style="color:#f59e0b;font-size:18px"></i>
                        <span class="crm-agent-name">{{ lbl.name }}</span>
                    </div>
                    <p v-if="labelsList.length === 0" class="crm-confirm-desc" style="margin:12px 0">Tidak ada label.</p>
                </div>
                <button class="btn-crm-cancel" @click="labelModal = null" style="width:100%;margin-top:12px">Batal</button>
            </div>
        </div>
    </transition>
</template>

<script>
import { mapActions } from "vuex";
import socket from "@/socket";
import defaultUserIcon from "@/assets/icons/user.png";
import NProgress from "nprogress";
import rintone from "@/assets/ringtone.mp3";
var _ = require("lodash");

export default {
    name: "GroupComponent",
    data() {
        return {
            merchantId: null,
            music: rintone,
            platform_devices: null, // null = "Semua Perangkat" (default)
            attribute: {
                name: "",
                icon: "",
                logo: "",
            },
            selectedContact: {
                id: null,
                name: '',
                phone: '',
                store_id: null
            },
            selectedDeviceId: '',
            creatingChat: false,
            activeAssignmentTab: "",
            unassignedCount: 0,
            assignedCount: 0,
            filter: {
                name: "",
                start_date: "",
                end_date: "",
                status: "",
                agent: "",
                resolvedby: "",
                label: [],
                from: "",
                tab: "",
                handled: "",
                assignment_status: "",
                device_id: null,       // WhatsApp Unofficial
                waba_id: null,         // WhatsApp Business (WABA)
                telegram_id: null,     // Telegram
                instagram_id: null,    // Instagram
                messenger_id: null,    // Messenger
                livechat_id: null,     // Live Chat
            },
            labelList: [],
            chats: {
                loader: false,
                list: [],
                totalchats: 0,
                cursor: null,    // keyset cursor (ISO8601) — null = load pertama
                cursorId: null,  // id item terakhir (tie-breaker)
                limit: 25,
                hasMoreChats: true,
            },
            modalContactsPage: 1,
            modalContactsLoading: false,
            modalContactsLoadingMore: false,
            modalContactsHasMore: true,
            newContact: {
                name: "",
                phone: "",
                deviceId: "",
            },
            deviceList: [],
            modalContacts: [],
            modalContactSearch: "",
            hoveredContactId: null,
            activeDropdown: null,
            showSubMenu:    null,
            pendingConfirm: null,
            assignModal:    null,
            labelModal:     null,
            agentsList:     [],
            labelsList:     [],
            defaultUserIcon,
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
        ...mapActions(["saving_contacts"]),

        // ── Lead attribution helpers ──────────────────────────────────
        leadChipLabel(s) {
            const map = { ad: 'Iklan', story: 'Story', post: 'Post', link: 'Link' };
            return map[s] || s;
        },
        leadChipIcon(s) {
            const map = {
                ad:    'fas fa-bullhorn',
                story: 'fas fa-film',
                post:  'fas fa-thumbs-up',
                link:  'fas fa-link',
            };
            return (map[s] || 'fas fa-tag') + ' lead-chip-icon';
        },

        /**
         * Hitung status sesi 24 jam WABA berdasarkan last_message_at.
         * Returns: { status: 'active'|'warning'|'expired', label: '14j'|'1j 20m'|'Sesi habis', hours: number }
         */
        getWabaSessionStatus(lastMessageAt) {
            if (!lastMessageAt) return null;
            const last = new Date(lastMessageAt);
            const now  = new Date();
            const expiry = new Date(last.getTime() + 24 * 60 * 60 * 1000);
            const diffMs = expiry - now;
            if (diffMs <= 0) return { status: 'expired', label: 'Sesi habis', hours: 0 };
            const diffH = Math.floor(diffMs / 3600000);
            const diffM = Math.floor((diffMs % 3600000) / 60000);
            const label = diffH > 0 ? `${diffH}j${diffM > 0 ? ' ' + diffM + 'm' : ''}` : `${diffM}m`;
            if (diffMs <= 2 * 3600000) return { status: 'warning', label, hours: diffH };
            return { status: 'active', label, hours: diffH };
        },

        onDeviceChange() {
            // Reset SEMUA device filter dulu
            this.filter.device_id    = null; // WA Unofficial
            this.filter.waba_id      = null; // WA Business
            this.filter.telegram_id  = null;
            this.filter.instagram_id = null;
            this.filter.messenger_id = null;
            this.filter.livechat_id  = null;

            // Set filter sesuai channel device yang dipilih
            const d = this.platform_devices;
            if (d && d.id) {
                if      (d.from === 'unofficial') this.filter.device_id    = d.id;
                else if (d.from === 'waba')       this.filter.waba_id      = d.id;
                else if (d.from === 'telegram')   this.filter.telegram_id  = d.id;
                else if (d.from === 'instagram')  this.filter.instagram_id = d.id;
                else if (d.from === 'messanger')  this.filter.messenger_id = d.id;
                else if (d.from === 'livechat')   this.filter.livechat_id  = d.id;
            }

            this.searchData();
        },

        selectExistingContact(contact) {
            // Close contact modal
            const contactModal = bootstrap.Modal.getInstance(this.$refs.contactModal);
            if (contactModal) contactModal.hide();

            this.selectedContact = {
                id: contact.id,
                name: contact.name,
                phone: contact.phone,
                store_id: contact.id, // Store ID
                avatar_url: contact.photo || contact.avatar_url
            };

            this.selectedDeviceId = '';

            setTimeout(() => {
                const deviceModal = new bootstrap.Modal(this.$refs.deviceSelectorModal, {
                    backdrop: false,
                    keyboard: false,
                });
                deviceModal.show();
            }, 300);
        },

        selectExistingContact(contact) {
            // Close contact modal
            const contactModal = bootstrap.Modal.getInstance(this.$refs.contactModal);
            if (contactModal) contactModal.hide();

            // Set selected contact
            this.selectedContact = {
                id: contact.id,
                name: contact.name,
                phone: contact.phone,
                store_id: contact.id, // Store ID
                avatar_url: contact.photo || contact.avatar_url
            };

            // Reset device selection
            this.selectedDeviceId = '';

            // Open device selector modal
            setTimeout(() => {
                const deviceModal = new bootstrap.Modal(this.$refs.deviceSelectorModal, {
                    backdrop: false,
                    keyboard: false,
                });
                deviceModal.show();
            }, 300);
        },

        async createChatSession() {
            if (!this.selectedDeviceId || !this.selectedContact.store_id) {
                if (this.$showToast) {
                    this.$showToast('Pilih device terlebih dahulu', 'error', 3000);
                }
                return;
            }

            this.creatingChat = true;

            try {
                const device = this.deviceList.find(d => d.id === this.selectedDeviceId);
                const type = device?.from === 'unofficial' ? 'whatsapp' : 'waba';

                const response = await this.$axios.post('/crm/contacts', {
                    name: this.selectedContact.name,
                    phone: this.selectedContact.phone,
                    device_id: this.selectedDeviceId,
                    type: type,
                    store_id: this.selectedContact.store_id
                });

                const deviceModal = bootstrap.Modal.getInstance(this.$refs.deviceSelectorModal);
                if (deviceModal) deviceModal.hide();

                if (this.$showToast) {
                    this.$showToast('Sesi chat berhasil dibuat', 'success', 2000);
                }

                setTimeout(() => {
                    this.$router.push({
                        name: "chat_room",
                        params: {
                            chatid: response.data.contact.id // HistoryChat ID
                        }
                    });
                }, 300);

                this.getChatList(null, false);

            } catch (error) {
                console.error('Error creating chat session:', error);
                if (this.$handleErrorResponse) {
                    this.$handleErrorResponse(error);
                } else {
                    if (this.$showToast) {
                        this.$showToast(error.response?.data?.message || 'Gagal membuat sesi chat', 'error', 3000);
                    }
                }
            } finally {
                this.creatingChat = false;
            }
        },


        async saveNewContact() {
            if (!this.newContact.name || !this.newContact.phone || !this.newContact.deviceId) {
                if (this.$showToast) {
                    this.$showToast('Semua field wajib diisi', 'error', 3000);
                }
                return;
            }

            try {
                // Determine device type
                const device = this.deviceList.find(d => d.id === this.newContact.deviceId);
                const type = device?.from === 'unofficial' ? 'whatsapp' : 'waba';

                // Hit endpoint yang sama dengan createChatSession
                const response = await this.$axios.post("/crm/contacts", {
                    name: this.newContact.name,
                    phone: this.newContact.phone,
                    device_id: this.newContact.deviceId,
                    type: type
                    // Tidak ada store_id karena kontak baru
                });

                if (this.$showToast) {
                    this.$showToast('Kontak berhasil ditambahkan', 'success', 3000);
                }

                // Close modal
                const contactModal = bootstrap.Modal.getInstance(this.$refs.contactModal);
                if (contactModal) contactModal.hide();

                // Add to modal contacts list (untuk next time)
                if (response.data.store) {
                    this.modalContacts.unshift(response.data.store);
                }

                // Reset form
                this.resetAddContact();

                // Redirect to chat room
                setTimeout(() => {
                    this.$router.push({
                        name: "chat_room",
                        params: {
                            chatid: response.data.contact.id // HistoryChat ID
                        }
                    });
                }, 300);

                // Refresh chat list
                this.getChatList(null, false);

            } catch (error) {
                console.error('Error saving contact:', error);
                if (this.$handleErrorResponse) {
                    this.$handleErrorResponse(error);
                } else {
                    if (this.$showToast) {
                        this.$showToast(error.response?.data?.message || 'Gagal menyimpan kontak', 'error', 3000);
                    }
                }
            }
        },


        async getUsers() {
            try {
                const response = await this.$axios.get(`/users/components`);
                this.users = response.data.users;
            } catch (error) {
                console.error('Error loading users:', error);
                this.users = [];
            }
        },

        toggleLabelFilter(labelId) {
            const index = this.filter.label.indexOf(labelId);
            if (index > -1) {
                this.filter.label.splice(index, 1);
            } else {
                this.filter.label.push(labelId);
            }
        },

        isLabelSelected(labelId) {
            return this.filter.label.includes(labelId);
        },

        getChannelColor(from) {
            const colors = {
                whatsapp: "#25d366",
                waba: "#25d366",
                livechat: "#1877f2",
                telegram: "#0088cc",
                messanger: "#0084ff",
                instagram: "#e1306c",
            };
            return colors[from] || "#667eea";
        },

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
        * Reset add contact form
        */
        resetAddContact() {
            this.newContact = { name: "", phone: "", deviceId: "" };
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
                let from = 'whatsapp';
                let deviceId = null;
                let wabaId = null;

                const deviceIndex = this.deviceList.findIndex(
                    (c) => c.id === this.newContact.deviceId
                );

                if (deviceIndex !== -1) {
                    const deviceDetail = this.deviceList[deviceIndex];
                    from = deviceDetail.from === 'unofficial' ? 'whatsapp' : 'waba';
                    deviceId = deviceDetail.from === 'unofficial' ? this.newContact.deviceId : null;
                    wabaId = deviceDetail.from === 'unofficial' ? null : this.newContact.deviceId;
                }

                if (deviceId === null && wabaId === null) {
                    if (this.$showToast) {
                        this.$showToast('Device tidak valid', 'error', 3000);
                    } else {
                        alert('Device tidak valid');
                    }
                    return;
                }

                const response = await this.$axios.post("/crm/contacts", {
                    name: this.newContact.name,
                    phone: this.newContact.phone,
                    from: from,
                    waba_id: wabaId,
                    device_id: deviceId
                });

                if (this.$showToast) {
                    this.$showToast('Kontak berhasil ditambahkan', 'success', 3000);
                }

                // Add to modal contacts list
                this.modalContacts.unshift(response.data.contact);
                this.resetAddContact();
                this.startChat(response.data.contact);

                // Optional: langsung buka chat dengan kontak baru
                // this.newChatContact(response.data.contact);
            } catch (error) {
                console.error('Error saving contact:', error);
                if (this.$handleErrorResponse) {
                    this.$handleErrorResponse(error);
                }
            }
        },

        getChannelIcon(from) {
            const icons = {
                whatsapp: "bx bxl-whatsapp",
                waba: "bx bxl-whatsapp",
                livechat: "bx bx-message-square-dots",
                telegram: "bx bxl-telegram",
                messanger: "bx bxl-messenger",
                instagram: "bx bxl-instagram",
            };
            return icons[from] || "bx bx-message";
        },

        selectChat(chat) {
            this.$router.push({
                name: "chat_room",
                params: { chatid: chat.id },
            });
            this.$emit("close-sidebar");
        },

        searchData() {
            this.doSearch(this);
        },

        doSearch: _.debounce((rootInstance) => {
            rootInstance.getChatList();
        }, 300),

        async getChatList(cursor = null, append = false) {
            NProgress.start();
            this.chats.loader = true;

            try {
                // Keyset cursor params — gantikan page-based OFFSET
                const cursorParams = {};
                if (cursor) { cursorParams.cursor = cursor; }
                if (this.chats.cursorId) { cursorParams.cursor_id = this.chats.cursorId; }

                // Buang key null/undefined/empty sebelum dikirim ke backend
                const cleanFilter = Object.fromEntries(
                    Object.entries({ ...cursorParams, ...this.filter })
                          .filter(([, v]) => v !== null && v !== undefined && v !== '')
                );
                const response = await this.$axios.get(`/crm/contacts`, {
                    params: cleanFilter,
                });

                let data = response.data;
                let newContacts = data.contacts;
                this.merchantId = data.merchant_id;

                if (append) {
                    this.chats.list = [...this.chats.list, ...newContacts];
                } else {
                    // Load pertama: pinned di atas, lalu contacts biasa
                    const pinned = data.pinned || [];
                    this.chats.list = [...pinned, ...newContacts];
                }

                this.chats.totalchats = data.total;
                this.chats.cursor   = data.next_cursor   || null;
                this.chats.cursorId = data.next_cursor_id || null;
                this.chats.hasMoreChats = data.has_more === true;
                this.chats.loader = false;
                NProgress.done();
            } catch (error) {
                console.error(error);
                this.chats.loader = false;
                NProgress.done();
            }
        },

        newMessageNotification(listData) {
            if (listData.sent_by !== "system") {
                // Sound notification
                const audio = new Audio(this.music);
                audio.play().catch(error => console.error("Audio error:", error));

                const senderName = listData.sent_by_name || 'User';
                const msgText = listData.message || 'Pesan baru';

                // Toast jika tab aktif
                if (document.visibilityState === "visible" && this.$showToast) {
                    this.$showToast(
                        `Pesan baru dari ${senderName}`,
                        "info",
                        3000
                    );
                }

                // Browser Notification jika tab tidak aktif
                if (document.visibilityState !== "visible") {
                    if (Notification.permission === "granted") {
                        const notif = new Notification(`💬 ${senderName}`, {
                            body: msgText.length > 80 ? msgText.substring(0, 80) + '...' : msgText,
                            icon: '/favicon.ico',
                            tag: listData.conversation_id || 'new-message',
                        });
                        notif.onclick = () => {
                            window.focus();
                            if (listData.conversation_id) {
                                this.$router.push({ name: 'chat_room', params: { chatid: listData.conversation_id } });
                            }
                        };
                    } else if (Notification.permission === "default") {
                        Notification.requestPermission();
                    }
                }
            }
        },

        switchAssignmentTab(tabName) {
            this.activeAssignmentTab = tabName;
            if (tabName === "resolved") {
                // Filter khusus untuk tab resolved
                this.filter.assignment_status = "resolved";
                this.filter.status = "resolved";
                this.filter.takeover = "no";
            } else {
                this.filter.assignment_status = tabName;
                this.filter.status = "";
                this.filter.takeover = "";
            }
            this.getChatList(null, false);

            if (tabName === "unassignment") {
            } else if (tabName === "assignment") {
            }
        },

        openFilter() {
            const modal = new bootstrap.Modal(this.$refs.filterModal, {
                backdrop: false,
                keyboard: false,
            });
            modal.show();
        },

        applyFilter() {
            const modal = bootstrap.Modal.getInstance(this.$refs.filterModal, {
                backdrop: false,
                keyboard: false,
            });
            if (modal) modal.hide();
            this.searchData();
        },

        resetFilter() {
            this.filter = {
                name: "",
                start_date: "",
                end_date: "",
                status: "",
                agent: "",
                resolvedby: "",
                label: [],
                from: "",
                tab: this.$store.getters.get_active_tab,
                assignment_status: "unassignment",
                handled: "",
                device_id: null,
                waba_id: null,
            };

            this.platform_devices = null;
            this.switchAssignmentTab("unassignment");
            this.applyFilter();
        },


        openContactModal() {
            this.fetchModalContacts();
            const modal = new bootstrap.Modal(this.$refs.contactModal, {
                backdrop: false,
                keyboard: false,
            });
            modal.show();
        },

        async fetchModalContacts() {
            try {
                const response = await this.$axios.get(`/crm/stores`, {
                    params: { page: 1, limit: 50 },
                });
                this.modalContacts = response.data.contacts || [];
            } catch (error) {
                console.error(error);
            }
        },

        startChat(detail) {

            const modal = bootstrap.Modal.getInstance(this.$refs.contactModal);
            if (modal) modal.hide();

            return this.$router.push({
                name: "chat_room",
                params: {
                    id: this.$route.params.id,
                    chatid: detail.id || `${detail.phone}@s.whatsapp.net`,
                },
                query: {
                    name: detail.name,
                    photo: detail.photo || this.attribute.user,
                },
            });
        },

        handleChatScroll(e) {
            // FIX: threshold 150px supaya tidak trigger terlalu sensitif
            const bottom = e.target.scrollHeight - e.target.scrollTop <= e.target.clientHeight + 150;
            if (bottom && !this.chats.loader && this.chats.hasMoreChats) {
                this.getChatList(this.chats.cursor, true);
            }
        },

        async getLogo() {
            try {
                const response = await this.$axios.get(`/components/system`);
                this.attribute = response.data;
            } catch (error) {
                console.error(error);
            }
        },

        async fetchDeviceList() {
            try {
                const response = await this.$axios.get("/master/components/devices");
                this.deviceList = response.data;
            } catch (error) {
                this.deviceList = [];
            }
        },

        // Utility methods
        truncateName(name, length = 20) {
            if (!name) return "";
            return name.length > length ? name.substring(0, length) + "..." : name;
        },

        truncateText(text, length = 30) {
            if (!text) return "";
            return text.length > length ? text.substring(0, length) + "..." : text;
        },

        getStatusText(status) {
            const statusMap = {
                open:     "Aktif",
                resolved: "Selesai",
                block:    "Diblokir"
            };
            return statusMap[status] || status;
        },

        isDocumentType(type) {
            return ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'file', 'document'].includes(type);
        },

        // ─── Context menu actions ─────────────────────────────────────
        async togglePin(list) {
            try {
                const res = await this.$axios.post(`/crm/action/pin/${list.id}`);
                list.is_pinned = res.data.is_pinned;
                const idx = this.chats.list.findIndex(c => c.id === list.id);
                if (idx >= 0) {
                    this.chats.list.splice(idx, 1);
                    list.is_pinned
                        ? this.chats.list.unshift(list)
                        : this.chats.list.push(list);
                }
            } catch (e) {
                console.error('togglePin', e);
                this.$toast?.error('Gagal mengubah pin chat');
            }
            this.activeDropdown = null;
        },
        async resolveChat(list) {
            try { await this.$axios.post(`/crm/action/resolved/${list.id}`); list.status = list.status === 'resolved' ? 'open' : 'resolved'; }
            catch (e) { console.error('resolveChat', e); }
            this.activeDropdown = null;
        },
        openAssignModal(list) { this.assignModal = list; this.activeDropdown = null; if (!this.agentsList.length) this.loadAgents(); },
        async assignAgent(list, agentId) {
            try {
                await this.$axios.post(`/crm/users/user-change/${list.id}`, { user_id: agentId });
                const agent = this.agentsList.find(a => a.id === agentId);
                if (agent) list.user = agent.name;
            } catch (e) {
                console.error('assignAgent', e);
                this.$toast?.error('Gagal assign agent');
            }
            this.assignModal = null;
        },
        openLabelModal(list) { this.labelModal = list; this.activeDropdown = null; if (!this.labelsList.length) this.loadLabels(); },
        async changeLabel(list, label) { // label = full object {id, name, color}
            try {
                await this.$axios.post(`/crm/labels/change/${list.id}`, { labels: [label] });
                list.labels = [label]; // update chip display instantly
                list.label   = label;  // keep legacy field in sync
            } catch (e) {
                console.error('changeLabel', e);
                this.$toast?.error('Gagal mengubah label');
            }
            this.labelModal = null;
        },
        async markRead(list) {
            try { await this.$axios.post(`/crm/mark-read/${list.id}`); list.not_read = 0; }
            catch (e) { console.error('markRead', e); }
            this.activeDropdown = null;
        },
        async markUnread(list) {
            try { await this.$axios.post(`/crm/action/mark-unread/${list.id}`); list.not_read = Math.max(list.not_read || 0, 1); }
            catch (e) { console.error('markUnread', e); }
            this.activeDropdown = null;
        },
        async toggleArchive(list) {
            try {
                const res = await this.$axios.post(`/crm/action/archive/${list.id}`);
                list.is_archived = res.data.is_archived;
                // Remove from current inbox view immediately
                this.chats.list = this.chats.list.filter(c => c.id !== list.id);
            } catch (e) {
                console.error('toggleArchive', e);
                this.$toast?.error('Gagal mengarsipkan chat');
            }
            this.activeDropdown = null;
        },
        async blockChat(list) {
            try { await this.$axios.post(`/crm/action/block/${list.id}`); list.status = list.status === 'block' ? 'open' : 'block'; }
            catch (e) { console.error('blockChat', e); }
            this.activeDropdown = null; this.showSubMenu = null;
        },
        confirmAction(type, chatId) { this.pendingConfirm = { type, chatId }; this.activeDropdown = null; this.showSubMenu = null; },
        async executeConfirmedAction() {
            if (!this.pendingConfirm) return;
            const { type, chatId } = this.pendingConfirm;
            this.pendingConfirm = null;
            try {
                if (type === 'delete') {
                    await this.$axios.delete(`/crm/remove/${chatId}`);
                    this.chats.list = this.chats.list.filter(c => c.id !== chatId);
                } else if (type === 'clear') {
                    await this.$axios.post(`/crm/action/clear-history/${chatId}`);
                    const chat = this.chats.list.find(c => c.id === chatId);
                    if (chat) chat.last_message = { message: 'Riwayat chat dihapus', time: '', date: '' };
                }
            } catch (e) { console.error('executeConfirmedAction', e); }
        },
        async loadAgents() { try { const res = await this.$axios.get('/crm/users'); this.agentsList = res.data.agents || []; } catch (e) { console.error(e); } },
        async loadLabels() { try { const res = await this.$axios.get('/crm/labels/?type=CRM'); this.labelsList = res.data.labels || []; } catch (e) { console.error(e); } },
        // ─────────────────────────────────────────────────────────────────

        toggleDropdown(chatId) {
            this.activeDropdown = this.activeDropdown === chatId ? null : chatId;
        },

        closeDropdownOutside(event) {
            if (event && event.target && event.target.closest('.dropdown-menu-chat, .dropdown-submenu, .dropdown-toggle-btn')) {
                return;
            }
            this.activeDropdown = null;
            this.showSubMenu    = null;
        },

        async deleteChat(id) {
            this.confirmAction('delete', id);
        },

        getLabelName(labelJson) {
            try {
                if (!labelJson) return null;

                let labels;
                if (typeof labelJson === 'string') {
                    labels = JSON.parse(labelJson);
                } else if (Array.isArray(labelJson)) {
                    labels = labelJson;
                } else {
                    return null;
                }

                const labelNames = labels
                    .filter(label => label && label.name)
                    .map(label => label.name)
                    .filter(name => name.trim() !== '');

                return labelNames.length > 0 ? labelNames.join(', ') : null;
            } catch (error) {
                console.error('Error parsing label JSON:', error);
                return null;
            }
        },

        async getLabels() {
            try {
                const response = await this.$axios.get(`/crm/labels`);
                this.labelList = response.data.labels;
            } catch (error) {
                this.$handleErrorResponse(error);
                console.log(error);
            }
        },



    },

    mounted() {
        // Request browser notification permission
        if ("Notification" in window && Notification.permission === "default") {
            Notification.requestPermission();
        }

        // FIX: Jalankan semua request paralel (bukan sequential)
        // Hemat ~70% waktu load awal - dari ~1000ms → ~200ms
        Promise.all([
            this.getLogo(),
            this.getChatList(),
            this.fetchDeviceList(),
            this.getLabels(),
            this.getUsers(),
        ]).catch(() => {}); // error sudah di-handle di masing-masing fungsi

        // Close dropdown when clicking outside
            document.addEventListener('click', this.closeDropdownOutside);
            this.loadAgents();
            this.loadLabels();

        window.addEventListener("socket-reconnected", () => {
            this.getChatList(null, false);
        });

        socket.on("update-chat-list", (newMessage) => {

            if (this.merchantId && newMessage.merchant_id && newMessage.merchant_id === this.merchantId) {
                let chatIndex = this.chats.list.findIndex(
                    (c) => c.id === newMessage.conversation_id
                );

                if (chatIndex !== -1) {
                    const currentChat = this.chats.list[chatIndex];
                    // FIX: increment not_read hanya jika pesan dari customer (from='user')
                    // dan agent tidak sedang membuka chat tersebut
                    const isCurrentlyViewing = this.$route.params?.chatid === newMessage.conversation_id;
                    const isFromUser = newMessage.from === 'user';
                    const newNotRead = (isFromUser && !isCurrentlyViewing)
                        ? (currentChat.not_read || 0) + 1
                        : currentChat.not_read;

                    const updatedChat = {
                        ...currentChat,
                        not_read: newNotRead,
                        takeover: newMessage.takeover ?? currentChat.takeover,
                        last_message: {
                            message: newMessage.message,
                            date: newMessage.date,
                            time: newMessage.time,
                            datetime: new Date().toISOString(),
                        },
                    };
                    this.chats.list.splice(chatIndex, 1);
                    this.chats.list.unshift(updatedChat);
                    this.newMessageNotification(newMessage);
                } else {
                    // Chat baru - langsung reload list supaya muncul segera
                    clearTimeout(this._reloadTimer);
                    this._reloadTimer = setTimeout(() => {
                        this.getChatList(null, false);
                    }, 500); // Kurangi dari 3000 -> 500ms untuk respons lebih cepat
                    this.newMessageNotification(newMessage);
                }
            } else {
            }
        });
        // Realtime flip icon bot saat agen lain ambil alih (FIX 4b)
        socket.on("takeover-changed", (data) => {
            // Type-safe: id bisa integer dari DB, conversation_id bisa string dari URL
            const isMine = this.merchantId && String(data.merchant_id) === String(this.merchantId);
            if (isMine) {
                const idx = this.chats.list.findIndex(c => String(c.id) === String(data.conversation_id));
                if (idx !== -1) {
                    // splice lebih reliable daripada $set untuk Vue 2 array reactivity
                    const updated = Object.assign({}, this.chats.list[idx], { takeover: data.takeover });
                    this.chats.list.splice(idx, 1, updated);
                }
            }
        });
        // local-takeover: sidebar flip langsung saat agen klik Ambil Alih
        this._localTakeoverHandler = (e) => {
            // Type-safe: params dari URL = string, c.id dari API bisa integer
            const i = this.chats.list.findIndex(c => String(c.id) === String(e.detail.chatid));
            if (i !== -1) {
                const updated = Object.assign({}, this.chats.list[i], { takeover: e.detail.takeover });
                this.chats.list.splice(i, 1, updated);
            }
        };
        window.addEventListener('local-takeover', this._localTakeoverHandler);
    },

    beforeDestroy() {
        document.removeEventListener('click', this.closeDropdownOutside);

        socket.off("update-chat-list");
        socket.off("takeover-changed");
        window.removeEventListener('local-takeover', this._localTakeoverHandler);
        window.removeEventListener("socket-reconnected");
    },
    watch: {
        $route(to, from) {
            // Update unread count when entering chat room
            if (to.name === 'chat_room') {
                const chatIndex = this.chats.list.findIndex(
                    (i) => to.params.chatid === i.id
                );
                if (chatIndex !== -1) {
                    this.chats.list[chatIndex].not_read = 0;
                }
            }
        },
    }
};
</script>

<style scoped>
/* ===== Sidebar Header ===== */
.sidebar-header-top {
    padding: 8px 14px;
    border-bottom: 1px solid #e5e7eb;
    background-color: #fff;
}

.logo-crm {
    max-height: 24px;
    width: auto;
}

.app-name {
    font-size: 13px;
    font-weight: 600;
    color: #1f2937;
    margin-left: 0.1rem !important;
    line-height: 1.3;
}

/* ===== Channel Filter ===== */
.channel-filter-section {
    padding: 6px 14px;
    border-bottom: 1px solid #e5e7eb;
    background-color: #fff;
}

.channel-select {
    width: 100%;
    padding: 6px 10px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 13px;
    background-color: #fff;
    cursor: pointer;
    outline: none;
    transition: all 0.2s;
}

.channel-select:hover {
    border-color: #3b82f6;
}

.channel-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* ===== Search ===== */
.chat-search {
    padding: 8px 14px;
    border-bottom: 1px solid #e5e7eb;
    background-color: #fff;
}

.search-wrapper {
    display: flex;
    gap: 8px;
}

.search-input-group {
    flex: 1;
    display: flex;
    align-items: center;
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 6px 10px;
    transition: all 0.2s;
}

.search-input-group:focus-within {
    border-color: #3b82f6;
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

.search-input-group input::placeholder {
    color: #9ca3af;
}

.filter-btn {
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    color: #6b7280;
    transition: all 0.2s;
}

.filter-btn:hover {
    background-color: #e5e7eb;
    color: #374151;
}

/* ===== Chat List ===== */
.chat-list {
    flex: 1;
    overflow-y: auto;
    background-color: #fff;
}

/* ===== Tabs ===== */
.tabs-wrapper {
    display: flex;
    border-bottom: 1px solid #e5e7eb;
    padding: 0 16px;
    background-color: #fff;
    gap: 4px;
}

.tab-btn {
    flex: 1;
    border: none;
    background: transparent;
    padding: 12px 8px;
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.tab-btn:hover {
    color: #374151;
    background-color: #f9fafb;
}

.tab-btn.active {
    color: #3b82f6;
    border-bottom-color: #3b82f6;
    background-color: #eff6ff;
}

.tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background-color: #3b82f6;
    color: white;
    font-size: 11px;
    font-weight: 600;
    border-radius: 10px;
}

.tab-badge.secondary {
    background-color: #6b7280;
}

/* ===== New Chat Button ===== */
.new-chat-section {
    padding: 6px 2px;
    background-color: #fff;
}

.btn-new-chat {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 8px 12px;
    background-color: #2E8DE1;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-new-chat:hover {
    background-color: #1B6FB8;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(46, 141, 225, 0.3);
}

.btn-new-chat i {
    font-size: 18px;
}

/* ===== Device Filter ===== */
.device-filter-section {
    padding: 4px 2px;
    background-color: #fff;
}

.device-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 13px;
    background-color: #fff;
    cursor: pointer;
    outline: none;
    transition: all 0.2s;
}

.device-select:hover {
    border-color: #3b82f6;
}

.device-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* ===== Chat Items ===== */
.chat-items-container{
    background-color: #fff;
overflow:visible;}

/* Avatar dengan Photo (untuk WhatsApp) */
.instagram-photo-direct {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    display: block;
}

.ig-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: linear-gradient(45deg, #f09433, #dc2743, #bc1888);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid white;
}

.ig-badge i {
    font-size: 9px;
    color: white;
    line-height: 1;
}
.platform-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
}

.platform-badge i {
    font-size: 10px;
    color: white;
}

/* Avatar dengan Icon (untuk platform lain) */
.chat-name {
    font-weight: 600;
    font-size: 14px;
    color: #1f2937;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Chat Badges Row */
.chat-badges {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
    flex-wrap: wrap;
}

.badge-platform {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 4px;
    font-size: 11px;
    color: white;
}

.badge-device {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 500;
    background-color: #e5e7eb;
    color: #374151;
    max-width: 100px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.chat-preview i {
    color: #9ca3af;
    flex-shrink: 0;
}

.badge-status.badge-status.badge-status/* Chat Label */
.chat-label {
    font-size: 11px;
    color: #d97706;
    font-style: italic;
    font-weight: 600;
    margin-top: 2px;
}

/* Chat Dropdown Menu */
.chat-dropdown {
    position: relative;      /* inline dalam .ci-acctrow, bukan ngambang */
    display: inline-flex;
    align-items: center;
    opacity: 1;
    visibility: visible;
    z-index: 900;
}


.dropdown-menu-chat {
    position: absolute;
    top: calc(100% + 4px);
    right: 0;
    background: #fff;
    background-color: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    min-width: 190px;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transform: translateY(-6px);
    transition: opacity 0.15s ease, transform 0.15s ease, visibility 0.15s;
    z-index: 9999;
    overflow: visible;
    padding: 4px 0;
}

.dropdown-menu-chat.show {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: all !important;
    transform: translateY(0) !important;
}

.dropdown-item-chat {
    padding: 9px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: background-color 0.15s;
    font-size: 13px;
    color: #1f2937 !important;
    background-color: #ffffff !important;
}

.dropdown-item-chat:hover {
    background-color: #f0f4f8 !important;
}

.dropdown-item-chat span {
    color: #1f2937 !important;
    font-weight: 400;
}

.dropdown-item-chat i {
    font-size: 15px;
    color: #6b7280 !important;
    flex-shrink: 0;
}

/* ===== Empty State ===== */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
}

/* ===== Loader ===== */
.loader-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

/* ===== Modal Contact ===== */
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

.contact-item-modal:hover,
.contact-item-modal.hovered {
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

/* ===== Scrollbar ===== */
.chat-list::-webkit-scrollbar,
.contact-list-modal::-webkit-scrollbar {
    width: 6px;
}

.chat-list::-webkit-scrollbar-track,
.contact-list-modal::-webkit-scrollbar-track {
    background: transparent;
}

.chat-list::-webkit-scrollbar-thumb,
.contact-list-modal::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.chat-list::-webkit-scrollbar-thumb:hover,
.contact-list-modal::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* ===== Responsive ===== */
@media (max-width: 992px) {
    .tab-btn {
        font-size: 12px;
        padding: 10px 6px;
    }

    /* Touch target: hamburger/control buttons >= 44px */
    .tab-btn span:last-child {
        display: none;
    }

    .tab-badge {
        position: absolute;
        top: 4px;
        right: 4px;
    }


}

@media (max-width: 576px) {
    .sidebar-header-top {
        padding: 12px 15px;
    }

    .app-name {
        font-size: 14px;
    }

    .btn-new-chat {
        font-size: 13px;
        padding: 8px 12px;
    }

    .btn-new-chat span {
        display: none;
    }

    .platform-badge {
        width: 16px;
        height: 16px;
    }

    .platform-badge i {
        font-size: 9px;
    }

    .badge-device {
        font-size: 9px;
        padding: 2px 4px;
    }
}

/* ===== Assignment Tabs ===== */
.assignment-tabs {
    display: flex;
    gap: 3px;
    padding: 6px 2px;
    background: white;
    border-bottom: 2px solid #e5e7eb;
    overflow-x: auto;
    overflow-y: hidden;
}

.assignment-tabs::-webkit-scrollbar {
    height: 0;
}

.tab-assignment {
    flex: 1;
    min-width: fit-content;
    padding: 4px 8px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    white-space: nowrap;
    position: relative;
}

.tab-assignment:hover {
    background: #f9fafb;
    border-color: #0ea5e9;
    color: #0ea5e9;
}

.tab-assignment.active {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-color: #0ea5e9;
    color: #0284c7;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(14, 165, 233, 0.15);
}

.tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: #0ea5e9;
    color: white;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
}

.tab-badge.secondary {
    background: #6b7280;
}

.tab-assignment.active .tab-badge {
    background: #0284c7;
}

.tab-assignment.active .tab-badge.secondary {
    background: #4b5563;
}

.sidebar-close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 10;
    width: 35px;
    height: 35px;
    border: none;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.1);
    color: #374151;
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.sidebar-close-btn:hover {
    background: rgba(0, 0, 0, 0.2);
}

.handled-filter-section {
    padding: 12px 2px;
    background-color: #fff;
    border-bottom: 1px solid #e5e7eb;
}

.handled-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 13px;
    background-color: #fff;
    cursor: pointer;
    outline: none;
    transition: all 0.2s;
}

.handled-select:hover {
    border-color: #3b82f6;
}

.handled-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* 2. Separator between chats */
/* 3. Status badge smaller */
/* Header right — badge + time */
.chat-header-right .chat-header-right /* WABA/Device name row — right-aligned */
/* ================================================================
   Universal Platform Badge System
   Works for BOTH: photo avatars AND colored-circle avatars
   ================================================================ */

/* Outer wrapper - always relative for badge positioning */
/* Also make the existing /* Platform badge - bottom right corner (works for BOTH photo and circle avatars) */
/* Platform badge colors */
/* Platform badge - bottom right corner */
/* Platform badge colors */
.crm-fade-enter-active,.crm-fade-leave-active{transition:opacity .2s}.crm-fade-enter-from,.crm-fade-leave-to{opacity:0}

/* ── Context menu icon colors ── */
.bx.pin-active { color: #f59e0b !important; }
.c-resolve      { color: #22c55e !important; }
.c-assign       { color: #6366f1 !important; }
.c-label        { color: #f59e0b !important; }
.c-archive      { color: #3b82f6 !important; }
.c-block        { color: #f97316 !important; }
.c-clear        { color: #ec4899 !important; }

/* Divider inside dropdown */
.dropdown-divider-line {
    height: 1px;
    background: #e5e7eb;
    margin: 3px 0;
}

/* Submenu trigger item */
.has-submenu {
    position: relative;
    display: flex !important;
    align-items: center !important;
}
.has-submenu > .submenu-arrow {
    margin-left: auto;
    font-size: 14px;
    color: #9ca3af;
}

/* Danger item (delete) */
.c-danger:hover {
    background: rgba(239, 68, 68, 0.08) !important;
}
.c-danger:hover span,
.c-danger:hover i {
    color: #ef4444 !important;
}

/* Submenu panel */
.dropdown-submenu {
    position: absolute;
    left: calc(100% + 4px);
    top: -4px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 4px 0;
    min-width: 190px;
    z-index: 10000;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

/* Confirmation modal overlay */
.crm-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(3px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.crm-confirm-dialog {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 28px;
    width: 340px;
    max-width: 90vw;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    color: #1f2937;
}
.crm-confirm-icon {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 24px;
}
.c-icon-danger { background: rgba(239,68,68,0.1); color: #ef4444; }
.c-icon-warn   { background: rgba(249,115,22,0.1); color: #f97316; }
.crm-confirm-title {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 8px;
    color: #111827;
}
.crm-confirm-desc {
    font-size: 13px;
    color: #6b7280;
    margin: 0 0 20px;
    line-height: 1.5;
}
.crm-confirm-actions { display: flex; gap: 10px; }
.btn-crm-cancel {
    flex: 1;
    padding: 9px 0;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    color: #6b7280;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.2s;
}
.btn-crm-cancel:hover { background: #f3f4f6; color: #374151; }
.btn-crm-confirm {
    flex: 1;
    padding: 9px 0;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s;
    color: #fff;
}
.btn-crm-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
.btn-crm-warn   { background: linear-gradient(135deg, #f97316, #ea580c); }
.btn-crm-confirm:hover { filter: brightness(1.05); transform: translateY(-1px); }

/* Assign agent modal */
.crm-assign-dialog { text-align: left; }
.crm-agent-list { max-height: 280px; overflow-y: auto; }
.crm-agent-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.15s;
}
.crm-agent-item:hover { background: rgba(99,102,241,0.08); }
.crm-agent-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}
.crm-agent-info { flex: 1; min-width: 0; }
.crm-agent-name  { display: block; font-size: 13px; color: #1f2937; font-weight: 500; }
.crm-agent-email { display: block; font-size: 11px; color: #9ca3af; }

/* Transitions */
.crm-fade-enter-active, .crm-fade-leave-active { transition: opacity 0.2s; }
.crm-fade-enter-from, .crm-fade-leave-to { opacity: 0; }


/* Sidebar close button — mobile touch target */
@media (max-width: 992px) {
    .sidebar-close-btn {
        min-width: 44px;
        min-height: 44px;
        width: 44px;
        height: 44px;
        top: 8px;
        right: 8px;
    }
}


/* WABA 24h session chip */
.chat-name-wrap .chat-name { min-width: 0; }



.ci-24h.soon{background:#FEF3C7;color:#B45309;}
.ci-24h.closed{background:#FEECEC;color:#B91C1C;}
/* Preview */
.ci-preview i{font-size:11px;color:#94A3B8;margin-right:2px;}

/* Kolom kanan */
.ci-dev i{font-size:11px;flex-shrink:0;}

/* ⋯ button */
/* Label strip */
.ci-chip i{font-size:10px;flex-shrink:0;}
/* Overflow fix for dropdown */
.chat-items-container{overflow:visible;}
.dropdown-menu-chat{z-index:100;}

/* ══════════════════════════════════════════════════
   CRM Chat Item — ci-* FINAL (nama+akun penuh, ⋯ selalu tampil)
   ══════════════════════════════════════════════════ */

/* Container */
.chat-item{display:flex;gap:10px;padding:10px 11px;border-bottom:1px solid #E4EAF2;cursor:pointer;align-items:flex-start;transition:background .15s;position:relative;}
.chat-item:hover{background:#FAFCFF;}
.chat-item.active{background:#F7FBFF;border-left:2px solid #2E8DE1;padding-left:9px;}

/* Avatar */
.ci-avatar{position:relative;flex-shrink:0;width:38px;height:38px;}
.ci-img{width:38px;height:38px;border-radius:50%;object-fit:cover;}
.ci-fallback{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;}
.ci-badge{position:absolute;bottom:-2px;right:-2px;width:16px;height:16px;border-radius:50%;border:2px solid #fff;display:flex;align-items:center;justify-content:center;color:#fff;font-size:8px;background:#25D366;}
.ci-badge--waba{background:#25D366;}
.ci-badge--telegram{background:#229ED9;}
.ci-badge--instagram{background:#E1306C;}
.ci-badge--messanger{background:#0084FF;}
.ci-badge--livechat{background:#64748B;}

/* Body */
.ci-body{flex:1;min-width:0;}
.ci-main{display:flex;gap:8px;align-items:flex-start;}
.ci-center{flex:1;min-width:0;}

/* Nama user: PENUH, maks 2 baris lalu ellipsis */
.ci-name-row{display:flex;align-items:flex-start;gap:5px;min-width:0;}
.ci-name{font-size:12.5px;font-weight:500;color:#1E2A4A;line-height:1.25;flex:1;min-width:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.chat-item.unread .ci-name{font-weight:600;}

/* WABA 24h chip */
.ci-24h{display:inline-flex;align-items:center;gap:2px;font-size:9px;padding:1px 5px;border-radius:9px;font-weight:600;flex-shrink:0;white-space:nowrap;margin-top:1px;}
.ci-24h.active{background:#DCFCE7;color:#15803D;}
.ci-24h.soon{background:#FEF3C7;color:#B45309;}
.ci-24h.closed{background:#FEECEC;color:#B91C1C;}
.ci-24h i{font-size:9px;}

/* Preview */
.ci-preview{font-size:11px;color:#64748B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;}
.ci-preview i{font-size:11px;color:#94A3B8;margin-right:2px;}

/* Kolom kanan: hanya notif+waktu + status */
.ci-right{display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;min-width:60px;}
.ci-top{display:flex;align-items:center;gap:4px;}
.ci-time{font-size:10px;color:#94A3B8;white-space:nowrap;}
.ci-unread{font-size:9px;background:#16A34A;color:#fff;min-width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center;padding:0 3px;font-weight:600;}
.ci-status{font-size:9.5px;padding:1px 8px;border-radius:10px;font-weight:500;white-space:nowrap;}
.st-open{background:#FEF3C7;color:#B45309;}
.st-resolved{background:#DCFCE7;color:#15803D;}
.st-block{background:#FEECEC;color:#B91C1C;}

/* Baris akun: RATA KANAN, nama akun PENUH, ⋯ selalu tampil */
.ci-acctrow{display:flex;align-items:center;justify-content:flex-end;gap:6px;margin-top:8px;padding-top:7px;border-top:.5px dashed #E8EEF5;}
.ci-dev{font-size:10px;color:#94A3B8;display:inline-flex;align-items:flex-start;gap:3px;line-height:1.3;text-align:right;white-space:normal;word-break:break-word;max-width:calc(100% - 36px);}
.ci-dev i{flex-shrink:0;font-size:11px;margin-top:1px;}

/* ⋯ button: SELALU tampil (bukan hover-only) */
.ci-dots{flex-shrink:0;width:28px;height:20px;border-radius:6px;background:#EEF2F7;border:.5px solid #D7E0EC;color:#475569;display:flex;align-items:center;justify-content:center;padding:0;cursor:pointer;transition:all .15s;}
.ci-dots i{font-size:14px;}
.ci-dots.active,.ci-dots:hover{background:#2E8DE1;border-color:#2E8DE1;color:#fff;}

/* Label strip: kiri, baris sendiri */
.ci-labels{display:flex;align-items:center;gap:5px;margin-top:7px;overflow:hidden;flex-wrap:nowrap;}
.ci-chip{font-size:9.5px;padding:2px 7px;border-radius:5px;display:inline-flex;align-items:center;gap:3px;flex-shrink:0;white-space:nowrap;max-width:110px;overflow:hidden;text-overflow:ellipsis;}
.ci-chip i{font-size:10px;flex-shrink:0;}
.ci-more{font-size:9.5px;padding:2px 7px;border-radius:5px;background:#EEF2F7;color:#475569;font-weight:600;flex-shrink:0;cursor:pointer;}
.ci-more:hover{background:#E2E8F0;}

/* Dropdown visible */
.chat-items-container{overflow:visible;}
.dropdown-menu-chat{z-index:200;}


/* ── Lead attribution chips ──────────────────────────── */
.lead-chip { font-size:9px; padding:2px 6px; border-radius:5px; display:inline-flex;
             align-items:center; gap:3px; flex-shrink:0; white-space:nowrap;
             font-weight:600; margin-bottom:3px; }
.lead-source-ad    { background:#FEF3C7; color:#92400E; border:1px solid #FDE68A; }
.lead-source-story { background:#FCE7F3; color:#9D174D; border:1px solid #FBCFE8; }
.lead-source-post  { background:#EFF6FF; color:#1E40AF; border:1px solid #BFDBFE; }
.lead-source-link  { background:#F0FDF4; color:#166534; border:1px solid #BBF7D0; }
.lead-chip i { font-size:10px; flex-shrink:0; }


/* ── Chat Item Inner Layout (ci-* class fix) ───────────────────────────── */
/* HTML template memakai ci-* bukan chat-*; rules ini override crm.css      */
.ci-center{flex:1;min-width:0;}
.ci-name-row{display:flex;align-items:center;gap:4px;overflow:hidden;}
.ci-name{flex:1;min-width:0;font-size:13px;font-weight:600;color:#1E2A4A;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.ci-preview{font-size:11px;color:#64748B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;}
/* Kolom kanan — cukup lebar untuk unread+time+status */
.ci-right{min-width:82px;flex-shrink:0;display:flex;flex-direction:column;align-items:flex-end;gap:3px;}
.ci-top{display:flex;align-items:center;gap:5px;}
.ci-time{font-size:10px;color:#94A3B8;white-space:nowrap;}
.ci-unread{font-size:9px;background:#16A34A;color:#fff;min-width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center;padding:0 4px;font-weight:600;flex-shrink:0;}
.ci-status{font-size:9.5px;padding:1px 7px;border-radius:10px;font-weight:500;white-space:nowrap;flex-shrink:0;}
/* Bot aktif indicator */
.ci-status-row { display:flex; align-items:center; gap:4px; justify-content:flex-end; }
.ci-bot { font-size:13px; color:#2E8DE1; line-height:1; flex-shrink:0; }
.st-open    {background:#FEF3C7;color:#B45309;}
.st-resolved{background:#DCFCE7;color:#15803D;}
.st-block   {background:#FEECEC;color:#B91C1C;}
/* Chip sesi WABA — ci-24h */
.ci-24h{display:inline-flex;align-items:center;gap:2px;flex-shrink:0;font-size:9px;font-weight:600;padding:1px 5px;border-radius:10px;white-space:nowrap;}
.ci-24h.active{background:#DCFCE7;color:#15803D;}
.ci-24h.soon  {background:#FEF3C7;color:#B45309;}
.ci-24h.closed{background:#FEECEC;color:#B91C1C;}
/* Baris akun (device + ⋯) */
.ci-acctrow{display:flex;align-items:center;gap:6px;margin-top:3px;}
.ci-dev{flex:1;min-width:0;font-size:9.5px;color:#94A3B8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:inline-flex;align-items:center;gap:2px;}
.ci-dev i{font-size:11px;flex-shrink:0;}
/* Lead chip */
.ci-lead-inline{display:inline-flex;align-items:center;gap:2px;flex-shrink:0;font-size:9px;padding:1px 5px;border-radius:8px;white-space:nowrap;background:#EEF2F7;color:#475569;}
/* Tombol ⋯ */
.ci-dots{width:24px;height:18px;border-radius:6px;background:#F8FAFC;border:.5px solid #E4EAF2;color:#94A3B8;display:flex;align-items:center;justify-content:center;padding:0;cursor:pointer;transition:all .15s;flex-shrink:0;}
.ci-dots:hover,.ci-dots.active{background:#2E8DE1;border-color:#2E8DE1;color:#fff;}
</style>

<style>
/*
 * CRM Sidebar Mobile Drawer
 * NON-SCOPED — applies globally to .sidebar-left.
 * Must be non-scoped to override scoped CSS from this component.
 * This is the source of truth; do NOT edit chatui.css.
 */

/* ── Desktop (≥993px): sidebar in-flow flex column ── */
@media (min-width: 993px) {
    .sidebar-left {
        position: relative !important;
        transform: none !important;
        width: 340px;
        min-width: 280px;
        flex-shrink: 0;
        height: auto;
        overflow-y: auto;
        background: #fff;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .sidebar-overlay {
        display: none !important;
    }
}

/* ── Mobile/Tablet (≤992px): off-canvas drawer ── */
@media (max-width: 992px) {
    /* Default: fully off-screen via transform */
    .sidebar-left {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        bottom: 0 !important;
        width: 85vw !important;
        max-width: 360px !important;
        height: 100% !important;
        z-index: 99999 !important;
        transform: translateX(-105%) !important;  /* 105% = fully hidden + buffer */
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        visibility: visible !important;
        opacity: 1 !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        background: #fff !important;
        box-shadow: none !important;
        will-change: transform;
    }

    /* Open: slide in from left */
    .sidebar-left.show {
        transform: translateX(0) !important;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3) !important;
    }

    /* Kill any hover-open behavior on touch devices */
    @media (hover: none) {
        .sidebar-left:not(.show) {
            transform: translateX(-105%) !important;
        }
    }

    /* Overlay: dim background */
    .sidebar-overlay {
        display: block !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: rgba(0, 0, 0, 0.5) !important;
        z-index: 99998 !important;
        opacity: 0 !important;
        visibility: hidden !important;
        transition: opacity 0.3s ease, visibility 0.3s ease !important;
        cursor: pointer;
    }

    .sidebar-overlay.show {
        opacity: 1 !important;
        visibility: visible !important;
    }
}

@media (max-width: 576px) {
    .sidebar-left {
        width: 90vw !important;
        max-width: 340px !important;
    }
}

/* ═══ Chat Item Layout — 2-kolom (chat-body > chat-main > center+right) ═══ */
.chat-item { display:flex; gap:10px; padding:10px 11px; border-bottom:.5px solid #F1F5F9; cursor:pointer; align-items:flex-start; position:relative; }
.chat-item:hover { background:#F7FBFF; }
.chat-item.active { background:#EAF3FC; border-left:3px solid #2E8DE1; padding-left:9px; }
.chat-item.unread { background:#FAFCFF; }

/* chat-body: mengisi sisa setelah avatar */
.chat-body { flex:1; min-width:0; }
.chat-main { display:flex; gap:8px; align-items:flex-start; }

/* Kolom tengah — truncate nama, preview */
.chat-center { flex:1; min-width:0; }
.chat-name-row { display:flex; align-items:center; gap:5px; min-width:0; }
.chat-name { font-size:13px; font-weight:600; color:#1E2A4A; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; flex:1; min-width:0; }
.chat-preview { font-size:11px; color:#64748B; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:3px; }

/* Kolom kanan — lebar tetap, flex column */
.chat-right { display:flex; flex-direction:column; align-items:flex-end; gap:4px; flex-shrink:0; min-width:70px; }
.cr-top { display:flex; align-items:center; gap:5px; }
.chat-time { font-size:10px; color:#94A3B8; white-space:nowrap; }
.cr-bottom { display:flex; align-items:center; gap:4px; }
.device-label { font-size:9.5px; color:#94A3B8; display:inline-flex; align-items:center; gap:2px; max-width:60px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.device-label i { font-size:11px; flex-shrink:0; }

/* Badge status — pill kecil */
.badge-status { font-size:9.5px; padding:1px 8px; border-radius:10px; font-weight:500; white-space:nowrap; }
.status-open   { background:#FEF3C7; color:#B45309; }
.status-solved { background:#DCFCE7; color:#15803D; }
.status-block  { background:#FEECEC; color:#B91C1C; }

/* Badge unread */
.badge-unread { font-size:9px; background:#16A34A; color:#fff; min-width:16px; height:16px; border-radius:50%; display:flex; align-items:center; justify-content:center; padding:0 4px; font-weight:600; }

/* Strip label — full width, 1 baris, maks 2 + "+N" */
.chat-label-row { display:flex; align-items:center; gap:5px; margin-top:6px; overflow:hidden; flex-wrap:nowrap; }
.chat-label-chip { font-size:9.5px; padding:2px 8px; border-radius:5px; display:inline-flex; align-items:center; gap:3px; flex-shrink:0; white-space:nowrap; max-width:110px; overflow:hidden; text-overflow:ellipsis; }
.chat-label-chip i { font-size:10px; flex-shrink:0; }
.chat-label-more { font-size:9.5px; padding:2px 7px; border-radius:5px; background:#EEF2F7; color:#475569; font-weight:600; flex-shrink:0; cursor:pointer; white-space:nowrap; }
.chat-label-more:hover { background:#E2E8F0; }

/* ⋯ Dropdown — mendatar, di cr-bottom */
.dropdown-toggle-btn { width:26px; height:18px; border-radius:6px; background:#F8FAFC; border:.5px solid #E4EAF2; color:#94A3B8; display:flex; align-items:center; justify-content:center; padding:0; cursor:pointer; transition:all .15s; }
.dropdown-toggle-btn:hover, .dropdown-toggle-btn.active { background:#2E8DE1; border-color:#2E8DE1; color:#fff; }

/* Select icon wrapper */
.select-icon { position:relative; }
.select-icon > i { position:absolute; left:10px; top:50%; transform:translateY(-50%); font-size:15px; pointer-events:none; z-index:1; }
.select-icon .channel-select,
.select-icon .device-select { padding-left:32px !important; }

/* Chip sesi 24 jam (WABA) — tetap flex-shrink:0 biar nama yang truncate */
.waba-session-chip { display:inline-flex; align-items:center; gap:2px; flex-shrink:0; font-size:9px; font-weight:600; padding:1px 5px; border-radius:10px; white-space:nowrap; }
.waba-session--active  { background:#DCFCE7; color:#15803D; }
.waba-session--warning { background:#FEF3C7; color:#B45309; }
.waba-session--expired { background:#FEECEC; color:#B91C1C; }
</style>