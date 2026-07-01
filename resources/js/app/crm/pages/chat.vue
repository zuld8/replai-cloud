<!-- new chat vue -->
<template>
    <!-- Wrapper untuk Chat dan Right Sidebar -->
    <div class="chat-wrapper">
        <div class="main-chat">
            <!-- Chat Header -->
            <div class="chat-header-main">
                <!-- ── 2-baris identity: avatar · nama · chip channel · status sesi ── -->
                <button class="ch-back d-lg-none" @click="$emit('toggle-left-sidebar')" aria-label="Kembali">
                    <i class="bx bx-arrow-back"></i>
                </button>
                <div class="ch-avatar">
                    <img v-if="detail.photo" :src="detail.photo" :alt="detail.name" />
                    <span class="ch-chdot" :style="{background: channelMeta.dot}">
                        <i :class="channelMeta.icon"></i>
                    </span>
                </div>
                <div class="ch-identity">
                    <div class="ch-name">
                        {{ detail.name }}
                        <span v-if="!detail.phone && detail.bsuid" class="ch-badge-username">
                            <i class="bx bx-lock-alt"></i> Username
                        </span>
                    </div>
                    <div class="ch-sub">
                        <span class="ch-chip" :style="{background: channelMeta.bg, color: channelMeta.color}">
                            <i :class="channelMeta.icon"></i> {{ channelMeta.label }}
                        </span>
                        <span v-if="detail.wa_username" class="ch-st" style="color:#5B3FB0;">
                            <i class="bx bx-at"></i> {{ detail.wa_username }}
                        </span>
                        <span class="ch-st" :style="{color: sessionInfo.color}">
                            <i :class="sessionInfo.icon"></i> {{ sessionInfo.text }}
                        </span>
                        <span v-if="sessionInfo.template" class="ch-chip ch-tpl" @click="openTemplatePanel">
                            <i class="bx bx-note"></i> Kirim template
                        </span>
                    </div>
                </div>
                <!-- Lead attribution banner -->
                <div v-if="detail.lead_source && detail.lead_source !== 'organic'"
                     class="lead-banner"
                     :class="`lead-banner-${detail.lead_source}`"
                     @click="detail.lead_source_detail?.source_url && window.open(detail.lead_source_detail.source_url,'_blank')">
                    <i :class="leadBannerIcon(detail.lead_source)"></i>
                    <span class="lead-banner-label">Lead {{ leadBannerLabel(detail.lead_source) }}</span>
                    <span v-if="detail.lead_source_detail?.headline" class="lead-banner-headline">
                        · {{ detail.lead_source_detail.headline }}
                    </span>
                    <img v-if="detail.lead_source_detail?.media_url"
                         :src="detail.lead_source_detail.media_url"
                         class="lead-banner-thumb"
                         @error="$event.target.style.display='none'" />
                </div>
                <!-- ── Ad context card — shows when lead_source_detail has source_url or headline ── -->
                <div v-if="detail.lead_source_detail && (detail.lead_source_detail.source_url || detail.lead_source_detail.headline)"
                     class="ad-context-card">
                    <div class="ad-context-head"><i class="bx bxs-megaphone"></i> Datang dari iklan</div>
                    <img v-if="detail.lead_source_detail.media_url"
                         :src="detail.lead_source_detail.media_url"
                         class="ad-context-thumb"
                         @error="$event.target.style.display='none'" />
                    <div class="ad-context-body">
                        <div v-if="detail.lead_source_detail.headline" class="ad-context-title">{{ detail.lead_source_detail.headline }}</div>
                        <div v-if="detail.lead_source_detail.body" class="ad-context-text">{{ detail.lead_source_detail.body }}</div>
                        <a v-if="detail.lead_source_detail.source_url"
                           :href="detail.lead_source_detail.source_url"
                           target="_blank" class="ad-context-link">
                            Lihat iklan <i class="bx bx-link-external"></i>
                        </a>
                    </div>
                </div>
                <!-- session-banner-24 dihapus — status sesi sudah tampil di ch-sub (sessionInfo) -->
                <div class="controls">
                    <!-- ch-back tombol kembali sudah dipindah ke identity block di atas -->
                    <button class="btn-control" @click="changeTakeOver(!detail.takeover)">
                        <i class="bx bx-bot"></i>
                        <span class="d-none d-sm-inline">{{ detail.takeover ? 'Bot Nonaktif' : 'Bot Aktif' }}</span>
                    </button>
                    <select class="status-select" v-model="detail.status" @change="changeStatus">
                        <option value="open">Terbuka</option>
                        <option value="resolved">Selesai</option>
                        <option value="block">Blokir</option>
                    </select>
                    <button class="btn-control d-none d-lg-inline" @click="toggleRightSidebar">
                        <i class="bx bx-menu"></i>
                    </button>
                    <!-- Mobile: tombol Info langsung kelihatan, sekali tap langsung buka panel -->
                    <button class="btn-control mobile-info-btn d-lg-none" @click="toggleRightSidebar()" aria-label="Info kontak">
                        <i class="bx bx-info-circle"></i>
                    </button>
                    <!-- Mobile burger: show controls in ⋮ dropdown -->
                    <div class="mobile-header-burger d-lg-none" style="position:relative">
                        <button class="btn-control mobile-burger-btn" @click="mobileBurgerOpen = !mobileBurgerOpen">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div v-if="mobileBurgerOpen" class="mobile-burger-menu" @click.self="mobileBurgerOpen=false">
                            <div class="burger-item" @click="changeTakeOver(!detail.takeover); mobileBurgerOpen=false">
                                <i class="bx bx-bot"></i>
                                {{ detail.takeover ? 'Bot Nonaktif' : 'Bot Aktif' }}
                            </div>
                            <div class="burger-item">
                                <i class="bx bx-transfer-alt"></i>
                                <select class="burger-status-select" v-model="detail.status" @change="changeStatus; mobileBurgerOpen=false">
                                    <option value="open">Terbuka</option>
                                    <option value="resolved">Selesai</option>
                                    <option value="block">Blokir</option>
                                </select>
                            </div>
                            <!-- Lihat Kontak dihapus dari ⋮ — tombol ℹ️ langsung di header mobile sudah cukup -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="chat-messages" id="chatMessages" @scroll="handleChatScroll">
                <div v-if="message.loader" class="d-flex justify-content-center p-4">
                    <div class="spinner-border text-primary"></div>
                </div>

                <!-- Messages List -->
                <div v-for="(msg, index) in filteredMessages" :key="msg.id" :id="'msg-' + msg.id">

                    <div v-if="shouldShowDateSeparator(index)" class="date-separator">
                        {{ formatDateSeparator(msg.datetime.date_id || msg.datetime.date) }}
                    </div>

                    <div v-if="msg.media_type !== 'reaction' && msg.media_type !== 'revoked'" class="message" :class="msg.sent_by === 'system' ? 'sent' : 'received'">
                        <div class="message-wrapper">


                            <!-- Message Actions -->
                            <div class="message-actions">
                                <div class="action-menu-btn" @click="toggleActionMenu(msg.id)">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </div>
                                <div class="dropdown-menu-custom" :class="{ show: activeDropdownId === msg.id }">
                                    <div class="dropdown-item-custom" @click="replyToMessage(msg)">
                                        <i class="bx bx-reply"></i>
                                        <span>{{ $t('common.reply') }}</span>
                                    </div>
                                    <div class="dropdown-item-custom delete" @click="deleteMessage(msg.id, index)">
                                        <i class="bx bx-trash"></i>
                                        <span>{{ $t('common.delete') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="message" :class="msg.sent_by === 'system' ? 'sent' : 'received'">
                                <!-- Message Bubble -->
                                <div class="message-bubble">
                                    <!-- Origin chip (outbound) / date (inbound) -->
                                    <div class="msg-origin-row" v-if="msg.sent_by === 'system'">
                                        <span class="msg-origin" :class="`origin-${msg.source || 'system'}`">
                                            <i :class="originIcon(msg.source)"></i>
                                            {{ msg.source === 'agent' ? (msg.sent_by_name || 'Agen') : originLabel(msg.source) }}
                                        </span>
                                        <span class="msg-origin-date">· {{ msg.datetime.date_id || msg.datetime.date }}</span>
                                    </div>
                                    <div class="message-sender-info" v-else>
                                        <span class="sender-date">{{ msg.datetime.date }}</span>
                                    </div>

                                    <!-- Reply Quote -->
                                    <div class="reply-quote" v-if="msg.reply_to" @click="scrollToMessage(msg.reply_id)">
                                        <div class="reply-quote-sender">{{ msg.reply_to }}</div>
                                        <div class="reply-quote-text">{{ truncateText(msg.reply_text, 60) }}</div>
                                    </div>

                                    <!-- Image -->
                                    <div v-if="msg.media_type === 'image' && msg.media_url" class="message-media">
                                        <img :src="msg.media_url" alt="image"
                                            @click="openImagePreview(msg.media_url)"
                                            @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='flex'"
                                        />
                                        <div class="media-unavailable" style="display:none;">
                                            <i class="bx bx-image-alt"></i>
                                            <small>Media tidak tersedia</small>
                                        </div>
                                    </div>
                                    <!-- Image tanpa URL (gagal download) -->
                                    <div v-else-if="msg.media_type === 'image' && !msg.media_url" class="media-unavailable-wrapper">
                                        <div class="media-unavailable">
                                            <i class="bx bx-image-alt"></i>
                                            <small>Media tidak tersedia</small>
                                        </div>
                                    </div>

                                    <!-- Video -->
                                    <div v-if="msg.media_type === 'video' && msg.media_url" class="message-media">
                                        <video controls>
                                            <source :src="msg.media_url" type="video/mp4" />
                                        </video>
                                    </div>
                                    <div v-else-if="msg.media_type === 'video' && !msg.media_url" class="media-unavailable-wrapper">
                                        <div class="media-unavailable">
                                            <i class="bx bx-video-off"></i>
                                            <small>Video tidak tersedia</small>
                                        </div>
                                    </div>

                                    <!-- Audio -->
                                    <div v-if="msg.media_type === 'audio' && msg.media_url"
                                        class="message-media audio-media">
                                        <audio controls>
                                            <source :src="msg.media_url" type="audio/mpeg" />
                                        </audio>
                                    </div>

                                    <!-- Sticker Compact -->
                                    <div v-if="msg.media_type === 'sticker' && msg.media_url"
                                        class="sticker-compact-wrapper"
                                        @click="openStickerLightbox(msg.media_url)"
                                        title="Tap untuk lihat stiker">
                                        <img
                                            :src="msg.media_url"
                                            alt="sticker"
                                            class="sticker-compact-img"
                                            @error="onStickerError($event)"
                                        />
                                        <div class="sticker-fallback" style="display:none;">
                                            <span>&#127991;</span>
                                            <small>Sticker</small>
                                        </div>
                                    </div>

                                    <!-- Document -->
                                    <div v-if="isDocumentType(msg.media_type) && msg.media_url"
                                        class="message-document">
                                        <div class="document-icon">
                                            <i :class="getDocumentIcon(msg.media_type)"></i>
                                        </div>
                                        <div class="document-info">
                                            <div class="document-name">{{ getFileName(msg.media_url, msg.original_name) }}</div>
                                            <div class="document-size">{{ formatFileSize(msg.media_size) }}</div>
                                        </div>
                                        <a :href="msg.media_url" target="_blank" class="document-download">
                                            <i class="bx bx-download"></i>
                                        </a>
                                    </div>

                                    <!-- Location Card -->
                                    <div v-if="msg.media_type === 'location' && parseLocation(msg)" class="location-msg">
                                        <div class="loc-card">
                                            <div class="loc-pin"><i class="bx bx-map"></i></div>
                                            <div class="loc-info">
                                                <div class="loc-name">{{ parseLocation(msg).name || 'Lokasi dibagikan' }}</div>
                                                <div class="loc-addr" v-if="parseLocation(msg).address">{{ parseLocation(msg).address }}</div>
                                                <div class="loc-coords" v-if="parseLocation(msg).lat">
                                                    {{ parseLocation(msg).lat }}, {{ parseLocation(msg).long }}
                                                </div>
                                            </div>
                                            <a :href="mapsUrl(parseLocation(msg))" target="_blank" class="loc-btn">
                                                <i class="bx bx-navigation"></i> Buka di Maps
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Contact Card (msg.extra.contacts) -->
                                    <div v-if="msg.media_type === 'contacts' && parseContacts(msg)" class="contact-msg">
                                        <div v-for="(c,ci) in parseContacts(msg)" :key="ci" class="contact-card-item">
                                            <div class="cc-avatar"><i class="bx bx-user"></i></div>
                                            <div class="cc-info">
                                                <div class="cc-name">{{ c.name }}</div>
                                                <div class="cc-phone" v-if="c.phone">{{ c.phone }}</div>
                                            </div>
                                            <div class="cc-actions" v-if="c.phone">
                                                <a :href="'https://wa.me/' + c.phone" target="_blank" class="cc-btn cc-chat">
                                                    <i class="bx bxl-whatsapp"></i> Chat
                                                </a>
                                                <button class="cc-btn cc-save" @click="saveSharedContact(c)">
                                                    <i class="bx bx-user-plus"></i> Simpan
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Message Text (teks fallback kontak, atau pesan biasa) -->
                                    <div class="message-text" v-if="msg.message && msg.media_type !== 'contacts' && msg.media_type !== 'location'" v-html="formattedText(msg.message)">
                                    </div>

                                    <!-- Button Reply (user tapped WA interactive button) -->
                                    <div v-if="msg.media_type === 'button' && !msg.message"
                                         class="msg-type-indicator msg-button-reply">
                                        <i class="bx bx-grid-horizontal"></i>
                                        <span>Membalas dengan tombol</span>
                                    </div>

                                    <!-- Unsupported message type (poll, location, contact card, etc.) -->
                                    <div v-if="msg.media_type === 'unsupported' && !msg.message"
                                         class="msg-type-indicator msg-unsupported">
                                        <i class="bx bx-info-circle"></i>
                                        <span>Pesan tidak didukung</span>
                                    </div>

                                    <!-- General fallback: completely blank message -->
                                    <div v-if="!msg.message && !msg.media_url
                                               && msg.media_type !== 'image' && msg.media_type !== 'video'
                                               && msg.media_type !== 'audio' && msg.media_type !== 'sticker'
                                               && !isDocumentType(msg.media_type)
                                               && msg.media_type !== 'button' && msg.media_type !== 'unsupported'"
                                         class="msg-type-indicator msg-unknown">
                                        <i class="bx bx-question-mark"></i>
                                        <span>{{ msg.media_type || 'Pesan kosong' }}</span>
                                    </div>

                                    <!-- Template Buttons -->
                                    <div v-if="msg.buttons && msg.buttons.length" class="msg-tbtns">
                                        <a v-for="(b, bi) in msg.buttons" :key="bi" class="msg-tbtn"
                                           :href="b.type === 'url' ? b.url : null"
                                           :target="b.type === 'url' ? '_blank' : null"
                                           @click="b.type !== 'url' ? $event.preventDefault() : null">
                                            <i :class="b.type === 'url' ? 'bx bx-link-external' : 'bx bx-reply'"></i>
                                            {{ b.text }}
                                        </a>
                                    </div>

                                    <!-- Message Time -->
                                    <div class="message-time">
                                        {{ msg.datetime.time }}
                                        <i class="bx bx-check-double" v-if="msg.sent_by === 'system'"></i>
                                    </div>

                                    <!-- Reactions -->
                                    <div class="message-reactions" v-if="msg.reactions && msg.reactions.length > 0">
                                        <span class="reaction-bubble" v-for="(reaction, rIdx) in groupReactions(msg.reactions)" :key="rIdx"
                                            :title="reaction.reactors.join(', ')">
                                            <span class="reaction-emoji">{{ reaction.emoji }}</span>
                                            <span class="reaction-count" v-if="reaction.count > 1">{{ reaction.count }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Reply Bar -->
            <div class="reply-bar" :class="{ show: replyingTo }">
                <div style="width: 3px; height: 40px; background-color: #10b981; border-radius: 2px;"></div>
                <div class="reply-bar-content">
                    <div class="reply-bar-label">{{ $t('chat.reply_to') }}</div>
                    <div class="reply-bar-text">{{ replyingTo?.message || '' }}</div>
                </div>
                <div class="reply-bar-close" @click="cancelReply">
                    <i class="bx bx-x"></i>
                </div>
            </div>

            <transition name="fade">
                <div v-if="pasteIndicator.show" class="paste-indicator">
                    {{ pasteIndicator.message }}
                </div>
            </transition>

            <!-- Chat Input Area -->
            <div class="chat-input-area">
                <div class="input-wrapper">
                    <!-- Template Panel -->
                    <div class="template-panel" v-if="templatePanel.show" @click.stop>
                        <!-- Template List View -->
                        <div v-if="!templatePanel.selectedTemplate">
                            <div class="template-panel-header">
                                <span><i class="bx bx-notepad"></i> Templates</span>
                                <button class="template-close" @click="closeTemplatePanel"><i class="bx bx-x"></i></button>
                            </div>
                            <div class="template-search">
                                <input type="text" v-model="templatePanel.search" @input="searchTemplates" placeholder="Search templates..." />
                            </div>
                            <div class="template-list" v-if="!templatePanel.loading">
                                <div class="template-item" v-for="tmpl in templatePanel.list" :key="tmpl.id"
                                    @click="selectTemplate(tmpl)">
                                    <div class="template-item-header">
                                        <span class="template-name">{{ tmpl.name }}</span>
                                        <span class="template-badge" :class="tmpl.category?.toLowerCase()">{{ tmpl.category }}</span>
                                    </div>
                                    <div class="template-item-meta">
                                        <span v-if="tmpl.header_type && tmpl.header_type !== 'text'" class="template-media-tag">
                                            <i :class="tmpl.header_type === 'image' ? 'bx bx-image' : tmpl.header_type === 'video' ? 'bx bx-video' : 'bx bx-file'"></i>
                                            {{ tmpl.header_type }}
                                        </span>
                                        <span v-if="tmpl.body_params > 0" class="template-param-tag">
                                            <i class="bx bx-edit"></i> {{ tmpl.body_params }} params
                                        </span>
                                    </div>
                                    <div class="template-preview-text">{{ truncateText(tmpl.body_text, 80) }}</div>
                                </div>
                                <div v-if="templatePanel.list.length === 0" class="template-empty">
                                    No templates found
                                </div>
                            </div>
                            <div class="template-loading" v-else>
                                <i class="bx bx-loader-alt bx-spin"></i> Loading...
                            </div>
                        </div>

                        <!-- Template Detail / Preview View -->
                        <div v-else>
                            <div class="template-panel-header">
                                <button class="template-back" @click="templatePanel.selectedTemplate = null">
                                    <i class="bx bx-arrow-back"></i>
                                </button>
                                <span>{{ templatePanel.selectedTemplate.name }}</span>
                                <button class="template-close" @click="closeTemplatePanel"><i class="bx bx-x"></i></button>
                            </div>

                            <!-- Media Upload (if template has media header) -->
                            <div class="template-media-upload" v-if="templatePanel.selectedTemplate.header_type && ['image','video','document'].includes(templatePanel.selectedTemplate.header_type)">
                                <label class="template-media-label">
                                    <i :class="templatePanel.selectedTemplate.header_type === 'image' ? 'bx bx-image-add' : templatePanel.selectedTemplate.header_type === 'video' ? 'bx bx-video-plus' : 'bx bx-file'"></i>
                                    <span v-if="!templatePanel.headerMediaName">Upload {{ templatePanel.selectedTemplate.header_type }}</span>
                                    <span v-else>{{ templatePanel.headerMediaName }}</span>
                                    <input type="file" ref="templateMediaInput" @change="handleTemplateMedia" style="display:none"
                                        :accept="templatePanel.selectedTemplate.header_type === 'image' ? 'image/*' : templatePanel.selectedTemplate.header_type === 'video' ? 'video/*' : '*'" />
                                </label>
                            </div>

                            <!-- Parameters -->
                            <div class="template-params" v-if="templatePanel.selectedTemplate.body_params > 0">
                                <div class="template-param-field" v-for="i in templatePanel.selectedTemplate.body_params" :key="i">
                                    <label>Parameter {{ i }}</label>
                                    <input type="text" v-model="templatePanel.params[i-1]"
                                        :placeholder="i === 1 ? detail.name : 'Parameter ' + i" />
                                </div>
                            </div>

                            <!-- Preview -->
                            <div class="template-preview-box">
                                <div class="template-preview-label">Preview</div>
                                <div class="template-preview-card">
                                    <div class="tpc-header" v-if="templatePanel.selectedTemplate.header_type === 'text' && templatePanel.selectedTemplate.header_text">
                                        <strong>{{ templatePanel.selectedTemplate.header_text }}</strong>
                                    </div>
                                    <div class="tpc-header" v-if="templatePanel.selectedTemplate.header_type === 'image' && templatePanel.headerMediaPreview">
                                        <img :src="templatePanel.headerMediaPreview" style="max-width:100%;border-radius:6px" />
                                    </div>
                                    <div class="tpc-body">{{ getTemplatePreview() }}</div>
                                    <div class="tpc-footer" v-if="templatePanel.selectedTemplate.footer_text">
                                        {{ templatePanel.selectedTemplate.footer_text }}
                                    </div>
                                    <div class="tpc-buttons" v-if="templatePanel.selectedTemplate.buttons?.length">
                                        <div class="tpc-btn" v-for="(btn, bi) in templatePanel.selectedTemplate.buttons" :key="bi">
                                            {{ btn.text }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Send Button -->
                            <div class="template-actions">
                                <button class="template-cancel-btn" @click="templatePanel.selectedTemplate = null">Cancel</button>
                                <button class="template-send-btn" @click="sendTemplateMessage" :disabled="templatePanel.sending || !canSendTemplate()">
                                    <i class="bx bx-send" v-if="!templatePanel.sending"></i>
                                    <i class="bx bx-loader-alt bx-spin" v-else></i>
                                    Send Template
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Replies Panel -->
                    <div class="quick-replies-panel" :class="{ show: quickReplyPopup.show }">
                        <div class="quick-replies-header">
                            <span>{{ $t('quick_reply.title') }}</span>
                            <a href="javascript:void(0)" class="manage-quick-reply" @click="manageQuickReply">
                                {{ $t('quick_reply.manage') }}
                            </a>
                        </div>
                        <div class="quick-reply-item" v-for="(item, index) in quick_replies" :key="index"
                            :class="{ selected: quickReplyPopup.selectedIndex === index }"
                            @click="selectQuickReply(item)">
                            <div class="quick-reply-title">
                                <span>{{ item.name }}:</span>
                            </div>
                            <div class="quick-reply-preview">{{ truncateText(item.content, 100) }}</div>
                        </div>
                    </div>

                    <!-- Input Controls (when takeover is active) -->
                    <template v-if="detail.takeover">
                        <div class="plus-menu-container" style="position: relative;">
                            <button class="input-btn" @click="togglePlusMenu">
                                <i class="bx bx-plus"></i>
                            </button>
                            <div class="plus-menu-dropdown" v-if="showPlusMenu">
                                <div class="plus-menu-item" @click="openTemplatePanel" v-if="detail.from === 'waba'">
                                    <i class="bx bx-notepad"></i>
                                    <span>Template</span>
                                </div>
                                <div class="plus-menu-item" @click="openQuickReplyFromMenu">
                                    <i class="bx bx-reply"></i>
                                    <span>Quick Reply</span>
                                </div>
                                <div class="plus-menu-item" @click="triggerFileFromMenu">
                                    <i class="bx bx-paperclip"></i>
                                    <span>Attach File</span>
                                </div>
                            </div>
                        </div>
                        <button class="input-btn" @click="toggleEmoji">
                            <i class="bx bx-smile"></i>
                        </button>
                        <input type="file" ref="fileInput" style="display: none" @change="handleFileChange"
                            accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" />
                        <textarea class="chat-input" ref="chatInput" v-model="send.text" @input="onMessageInput"
                            @keydown="handleKeyDown" @paste="handlePaste"
                            @keydown.arrow-down.prevent="navigateQuickReplies('down')"
                            @keydown.arrow-up.prevent="navigateQuickReplies('up')" @keydown.esc="hideQuickReplies"
                            :placeholder="$t('chat.type_message') + ' ' + $t('chat.quick_reply_hint')"
                            rows="1"></textarea>

                        <button class="input-btn primary" @click="sendMessage" :disabled="send.loader">
                            <i class="bx bx-send"></i>
                        </button>
                        <button class="input-btn success" @click="resolved" v-if="detail.status !== 'resolved'">
                            <i class="bx bx-check"></i>
                        </button>
                    </template>

                    <!-- Takeover Button (when not active) -->
                    <template v-else>
                        <button class="btn btn-info w-100" @click="changeTakeOver(true)">
                            <i class="bx bx-hand-stop me-2"></i>
                            {{ $t('chat.takeover_conversation') }}
                        </button>
                    </template>
                </div>
            </div>

            <!-- Emoji Picker -->
            <div class="emoji-picker-wrapper" v-if="showEmojiPicker">
                <Picker :data="emojiIndex" set="twitter" @select="showEmoji" />
            </div>
        </div>
    </div>

    <!-- Modal File Preview -->
    <!-- Backdrop panel Informasi (mobile only) — tap untuk nutup -->
    <div v-if="showRightSidebar" class="sidebar-right-overlay d-lg-none" @click="toggleRightSidebar()"></div>

    <div class="sidebar-right" v-if="showRightSidebar" id="rightSidebar">
        <div class="sidebar-right-header">
            {{ $t('info.information') }}
            <button class="sidebar-close d-lg-none" @click="toggleRightSidebar()" aria-label="Tutup">
                <i class="bx bx-x"></i>
            </button>
        </div>

        <!-- User Profile -->
        <div class="user-profile">
            <div class="user-avatar-large">
                <img :src="detail.photo" :alt="detail.name" />
                <div class="whatsapp-badge" v-if="detail.from === 'whatsapp' || detail.from === 'waba'">
                    <i class="bx bxl-whatsapp"></i>
                </div>
            </div>
            <div class="user-name">
                {{ detail.name }}
                <span v-if="!detail.phone && detail.bsuid" class="badge-username">
                    <i class="bx bx-lock-alt"></i> Username
                </span>
            </div>
            <!-- Phone: tampil kalau ada; kalau tidak ada tampil "disembunyikan" -->
            <div class="user-phone" v-if="detail.phone">
                <span>{{ detail.phone }}</span>
                <i class="bx bx-copy copy-icon" @click="copyPhone"></i>
            </div>
            <div class="user-phone muted" v-else>
                <i class="bx bx-lock-alt"></i> Nomor disembunyikan
            </div>
            <!-- @username (WA username handle) -->
            <div class="user-username" v-if="detail.wa_username">
                <i class="bx bx-at"></i> {{ detail.wa_username }}
            </div>
            <!-- BSUID copyable (support/debug — hanya kontak BSUID-only) -->
            <div class="user-bsuid" v-if="!detail.phone && detail.bsuid" @click="copyBsuid" title="Salin BSUID">
                BSUID: {{ detail.bsuid && detail.bsuid.length > 16 ? detail.bsuid.slice(0,16) + '…' : detail.bsuid }}
                <i class="bx bx-copy"></i>
            </div>
        </div>

        <!-- 24H Conversation Window (WABA only) -->
        <div class="window-status-card" v-if="detail.from === 'waba'">
            <div class="window-status" :class="isInsideWindow ? 'inside' : 'outside'">
                <i :class="isInsideWindow ? 'bx bx-chat' : 'bx bx-time-five'"></i>
                <span>{{ isInsideWindow ? 'Inside 24H Window' : 'Outside 24H Window' }}</span>
                <span class="window-badge" :class="isInsideWindow ? 'badge-inside' : 'badge-outside'">
                    {{ isInsideWindow ? 'Free Message' : 'Template Only' }}
                </span>
            </div>
            <div class="window-timer" v-if="isInsideWindow && windowTimeLeft">
                <i class="bx bx-timer"></i>
                <small>Expires in {{ windowTimeLeft }}</small>
            </div>
            <div class="window-timer expired" v-if="!isInsideWindow && detail.last_incoming_at">
                <i class="bx bx-info-circle"></i>
                <small>Last reply {{ windowExpiredAgo }}</small>
            </div>
        </div>

        <!-- Info Sections -->
        <div class="info-section">
            <!-- Handle By -->
            <div class="info-item">
                <div class="info-label">{{ $t('info.handle_by') }}</div>
                <select class="info-select" v-model="detail.handled" @change="selectUser">
                    <option v-for="user in users" :key="user.id" :value="user">
                        {{ user.name }}
                    </option>
                </select>
            </div>

            <!-- Collaborator -->
            <div class="info-item">
                <div class="info-header-row">
                    <div class="info-label">{{ $t('info.collaborator') }}</div>
                    <a href="javascript:void(0)" class="btn-add" @click="showAddCollaborator = true">{{
                        $t('info.add_team')
                    }}</a>
                </div>
                <div v-for="collab in detail.collabolators" :key="collab.id" class="collaborator-item mb-2">
                    <span>{{ collab.name }}</span>
                    <i class="bx bx-x remove-btn" @click="removeCollab(collab.id)"></i>
                </div>
                <select v-if="showAddCollaborator" class="info-select mt-2" @change="selectCollabolator"
                    v-model="info_collabolator.form">
                    <option :value="null">{{ $t('info.select_collaborator') }}</option>
                    <option v-for="user in users" :key="user.id" :value="user">
                        {{ user.name }}
                    </option>
                </select>
            </div>

            <!-- Label -->
            <div class="info-item">
                <div class="info-header-row">
                    <div class="info-label">{{ $t('info.label') }}</div>
                    <a href="javascript:void(0)" class="btn-add" @click="showAddLabel = true">{{ $t('info.add_label')
                    }}</a>
                </div>
                <div class="tag-container">
                    <span v-for="label in detail.label" :key="label.id" class="tag-item">
                        {{ label.name }}
                        <i class="bx bx-x remove-tag" @click="removeLabel(label.id)"></i>
                    </span>
                    <span v-if="!detail.label || detail.label.length === 0" class="empty-state-inline">
                        {{ $t('info.no_label') || 'Belum ada label' }}
                    </span>
                </div>
                <select v-if="showAddLabel" class="info-select mt-2" @change="selectLabel" v-model="info_label.form">
                    <option :value="null">{{ $t('info.select_label') }}</option>
                    <option v-for="label in info_label.labels" :key="label.id" :value="label">
                        {{ label.name }}
                    </option>
                </select>
            </div>

            <div class="info-item">
    <div class="info-header-row">
        <div class="info-label">{{ $t('info.pipeline_stage') || 'Pipeline & Stage' }}</div>
        <a href="javascript:void(0)" class="btn-add" @click="showAddPipeline = true">
            {{ $t('info.change_pipeline') || 'Ubah Pipeline' }}
        </a>
    </div>
    
    <!-- Display Current Pipeline & Stage -->
    <div v-if="detail.pipeline && detail.pipeline.id" class="pipeline-display">
        <div class="pipeline-badge">
            <i class="bx bx-git-branch"></i>
            <span>{{ detail.pipeline.name }}</span>
        </div>
        <div v-if="detail.pipeline.stage && detail.pipeline.stage.id" class="stage-badge">
            <i class="bx bx-flag"></i>
            <span>{{ detail.pipeline.stage.name }}</span>
            <i class="bx bx-x remove-tag" @click="removePipelineAndStage"></i>
        </div>
    </div>
    <div v-else class="empty-state-inline">
        {{ $t('info.no_pipeline') || 'Belum ada pipeline' }}
    </div>
    
    <!-- Form untuk Add/Change Pipeline & Stage -->
    <div v-if="showAddPipeline" class="pipeline-form mt-3">
        <!-- Select Pipeline -->
        <select 
            class="info-select mb-2" 
            v-model="info_pipeline.form_pipeline" 
            @change="selectPipeline"
        >
            <option :value="null">{{ $t('info.select_pipeline') || 'Pilih Pipeline' }}</option>
            <option 
                v-for="pipeline in info_pipeline.pipelines" 
                :key="pipeline.id" 
                :value="pipeline"
            >
                {{ pipeline.name }}
            </option>
        </select>
        
        <!-- Select Stage (muncul setelah pilih pipeline) -->
        <select 
            v-if="info_pipeline.form_pipeline" 
            class="info-select" 
            v-model="info_pipeline.form_stage" 
            @change="selectStage"
            :disabled="info_pipeline.loading_stages"
        >
            <option :value="null">
                {{ info_pipeline.loading_stages ? 'Loading...' : ($t('info.select_stage') || 'Pilih Stage') }}
            </option>
            <option 
                v-for="stage in info_pipeline.stages" 
                :key="stage.id" 
                :value="stage"
            >
                {{ stage.name }}
            </option>
        </select>
    </div>
</div>


            <!-- Category -->
            <div class="info-item">
                <div class="info-header-row">
                    <div class="info-label">{{ $t('info.category') || 'Kategori' }}</div>
                    <a href="javascript:void(0)" class="btn-add" @click="showAddCategory = true">
                        {{ $t('info.add_category') || 'Tambah Kategori' }}
                    </a>
                </div>
                <div class="tag-container">
                    <span v-if="detail.category && detail.category.id" class="tag-item category-tag">
                        {{ detail.category.name }}
                        <i class="bx bx-x remove-tag" @click="removeCategory"></i>
                    </span>
                    <span v-else class="empty-state-inline">
                        {{ $t('info.no_category') || 'Belum ada kategori' }}
                    </span>
                </div>
                <select v-if="showAddCategory" class="info-select mt-2" @change="selectCategory"
                    v-model="info_category.form">
                    <option :value="null">{{ $t('info.select_category') || 'Pilih Kategori' }}</option>
                    <option v-for="category in categories" :key="category.id" :value="category">
                        {{ category.name }}
                    </option>
                </select>
            </div>

            <!-- Device Set -->
            <div class="info-item">
                <div class="info-header-row">
                    <div class="info-label">{{ $t('info.device_channel') || 'Device/Channel' }}</div>
                    <a href="javascript:void(0)" class="btn-add" @click="openChangeDeviceModal">
                        {{ $t('info.change_device') || 'Ganti Device' }}
                    </a>
                </div>
                <div class="device-info-container">
                    <div class="device-badge" :class="'badge-' + detail.from">
                        <i :class="getChannelIcon(detail.from)"></i>
                        <span>{{ getChannelName(detail.from) }}</span>
                    </div>
                    <div class="device-name">
                        {{ detail.device || detail.livechat || (detail.from === 'waba' ? 'Nomor WABA aktif' : 'Belum ada device') }}
                    </div>
                </div>
            </div>


            <!-- Additional Data -->
            <div class="info-item">
                <div class="info-header-row">
                    <div class="info-label">{{ $t('info.additional_data') }}</div>
                    <a href="javascript:void(0)" class="btn-add" @click="openAdditionalModal">{{ $t('info.add_data')
                    }}</a>
                </div>
                <div v-if="detail.additional.data.length === 0" class="empty-state">
                    {{ $t('info.no_additional_data') }}
                </div>
                <div v-else>
                    <div v-for="(info, i) in detail.additional.data" :key="i" class="mb-2">
                        <label class="info-label small">{{ info.name }}</label>
                        <input v-if="info.type === 'text'" type="text" class="info-input" v-model="info.value"
                            @change="updateAdditional" />
                        <input v-else-if="info.type === 'number'" type="number" class="info-input" v-model="info.value"
                            @change="updateAdditional" />
                        <input v-else-if="info.type === 'date'" type="date" class="info-input" v-model="info.value"
                            @change="updateAdditional" />
                        <select v-else-if="info.type === 'options'" class="info-select" v-model="info.value"
                            @change="updateAdditional">
                            <option v-for="opt in info.options" :key="opt" :value="opt">{{ opt }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="info-item">
                <div class="info-label">{{ $t('info.notes') }}</div>
                <input type="text" class="info-input" v-model="detail.additional.note" @change="updateAdditional"
                    :placeholder="$t('info.notes')" />
            </div>

            <!-- Detail Data -->
            <div class="info-item">
                <div class="info-label">{{ $t('info.detail_data') }}</div>
                <div class="detail-row">
                    <span class="detail-label">{{ $t('info.assigned_by') }}</span>
                    <span class="detail-value">{{ detail.detail.assignment_by || '-' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ $t('info.handled_by') }}</span>
                    <span class="detail-value">{{ detail.handled?.name || '-' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ $t('info.resolved_by') }}</span>
                    <span class="detail-value">{{ detail.detail.resolved_by || '-' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ $t('info.created_at') }}</span>
                    <span class="detail-value">{{ detail.detail.created_at || '-' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ $t('info.resolved_at') }}</span>
                    <span class="detail-value">{{ detail.detail.resolved_at || '-' }}</span>
                </div>
            </div>

            <!-- Block/Unblock Button -->
            <div class="info-item" v-if="detail.status !== 'resolved'">
                <button v-if="detail.status === 'open'" class="btn btn-danger w-100" @click="blockUnblock">
                    {{ $t('info.block_user') }}
                </button>
                <button v-else-if="detail.status === 'block'" class="btn btn-info w-100" @click="blockUnblock">
                    {{ $t('info.unblock_user') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Modal File Preview -->
    <!-- Sticker Lightbox -->
    <div v-if="stickerLightbox.show"
        class="sticker-lightbox-overlay"
        @click="closeStickerLightbox">
        <div class="sticker-lightbox-box" @click.stop>
            <button class="sticker-lightbox-close" @click="closeStickerLightbox">
                <i class="bx bx-x"></i>
            </button>
            <img :src="stickerLightbox.url" alt="sticker full" class="sticker-lightbox-img" />
        </div>
    </div>

    <div class="modal fade" id="filePreviewModal" ref="filePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $t('file.preview') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div v-if="filePreview.type === 'image'" class="text-center">
                        <img :src="filePreview.url" class="img-fluid" />
                    </div>
                    <div v-else-if="filePreview.type === 'video'" class="text-center">
                        <video controls class="w-100">
                            <source :src="filePreview.url" type="video/mp4" />
                        </video>
                    </div>
                    <div v-else-if="filePreview.type === 'document'" class="text-center py-4">
                        <div class="document-preview-card mx-auto" style="max-width: 360px; background: #f8f9fa; border-radius: 12px; padding: 24px; border: 1px solid #e9ecef;">
                            <i class="bx bxs-file-doc" style="font-size: 48px; color: #4e73df;"></i>
                            <div class="mt-3 fw-semibold" style="word-break: break-all;">{{ filePreview.fileName }}</div>
                            <div class="text-muted small mt-1">{{ filePreview.fileSize }}</div>
                        </div>
                    </div>
                    <textarea class="form-control mt-3" v-model="send.text" rows="3"
                        :placeholder="$t('file.add_caption')"></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" @click="confirmSendFile" :disabled="send.loader">
                        <i class="bx bx-send me-2"></i>
                        {{ send.loader ? $t('chat.sending') : $t('chat.send') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Quick Reply Management -->
    <div class="modal fade" id="quickReplyModal" ref="quickReplyModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $t('quick_reply.manage') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <button class="btn btn-primary" @click="openAddQuickReply">
                            <i class="bx bx-plus me-2"></i>
                            {{ $t('quick_reply.add') }}
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ $t('quick_reply.name') }}</th>
                                    <th>{{ $t('quick_reply.content') }}</th>
                                    <th>{{ $t('quick_reply.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(qr, index) in quick_reply_all" :key="qr.id">
                                    <td>{{ qr.name }}</td>
                                    <td>{{ truncateText(qr.content, 50) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-1" @click="editQuickReply(qr, index)">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" @click="deleteQuickReply(qr.id)">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit Quick Reply -->
    <div class="modal fade" id="addQuickReplyModal" ref="addQuickReplyModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ quickReplyForm.id ? $t('quick_reply.edit') : $t('quick_reply.add') }} {{
                        $t('quick_reply.title') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ $t('quick_reply.name') }}</label>
                        <input type="text" class="form-control" v-model="quickReplyForm.name" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ $t('quick_reply.content') }}</label>
                        <textarea class="form-control" v-model="quickReplyForm.content" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ $t('quick_reply.file_optional') }}</label>
                        <input type="file" class="form-control" @change="handleQuickReplyFile" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" @click="saveQuickReply" :disabled="send.loader">
                        <i class="bx bx-save me-2"></i>
                        {{ send.loader ? $t('quick_reply.saving') : $t('quick_reply.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Additional Data -->
    <div class="modal fade" id="additionalModal" ref="additionalModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $t('additional.add_field') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ $t('additional.field_name') }}</label>
                        <input type="text" class="form-control" v-model="additionalForm.name" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ $t('additional.field_type') }}</label>
                        <select class="form-select" v-model="additionalForm.type">
                            <option value="text">{{ $t('additional.type_text') }}</option>
                            <option value="number">{{ $t('additional.type_number') }}</option>
                            <option value="date">{{ $t('additional.type_date') }}</option>
                            <option value="options">{{ $t('additional.type_options') }}</option>
                        </select>
                    </div>
                    <div v-if="additionalForm.type === 'options'" class="mb-3">
                        <label class="form-label">{{ $t('additional.options_hint') }}</label>
                        <input type="text" class="form-control" v-model="additionalForm.optionsText"
                            :placeholder="$t('additional.options_placeholder')" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" @click="saveAdditionalField">
                        <i class="bx bx-save me-2"></i>
                        {{ $t('common.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Change Device -->
    <div class="modal fade" id="changeDeviceModal" ref="changeDeviceModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $t('info.change_device') || 'Ganti Device/Channel' }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Channel Type Selection -->
                    <div class="mb-3">
                        <label class="form-label">{{ $t('info.channel_type') || 'Tipe Channel' }}</label>
                        <select class="form-select" v-model="deviceForm.type" @change="loadDevices">
                            <option value="whatsapp">WhatsApp </option>
                            <option value="waba">WhatsApp Business API</option>
                            <option value="telegram">Telegram</option>
                            <option value="livechat">LiveChat</option>
                            <option value="instagram">Instagram</option>
                            <option value="messanger">Messanger</option>
                        </select>
                    </div>

                    <!-- Device Selection -->
                    <div class="mb-3">
                        <label class="form-label">{{ $t('info.select_device') || 'Pilih Device' }}</label>
                        <select class="form-select" v-model="deviceForm.device_id" :disabled="deviceForm.loading">
                            <option :value="null">
                                {{ deviceForm.loading ? 'Loading...' : (deviceForm.devices.length === 0 ? 'Tidak ada device' : 'Pilih Device') }}
                            </option>
                            <option v-for="device in deviceForm.devices" :key="device.id" :value="device.id">
                                {{ device.name || device.phone }}
                            </option>
                        </select>
                    </div>

                    <!-- Current Info -->
                    <div class="alert alert-info">
                        <small>
                            <strong>Current:</strong> {{ getChannelName(detail.from) }} - {{ detail.device ||
                            detail.livechat }}
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ $t('common.cancel') || 'Batal' }}
                    </button>
                    <button class="btn btn-primary" @click="saveDeviceChange"
                        :disabled="deviceForm.saving || !deviceForm.device_id">
                        <i class="bx bx-save me-2"></i>
                        {{ deviceForm.saving ? 'Menyimpan...' : ($t('common.save') || 'Simpan') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import socket from "@/socket";
import NProgress from "nprogress";
import 'emoji-mart-vue-fast/css/emoji-mart.css'
import { Picker, EmojiIndex } from "emoji-mart-vue-fast/src";
import data from 'emoji-mart-vue-fast/data/all.json'
let emojiIndex = new EmojiIndex(data);

export default {
    name: "Chat",
    components: {
        Picker,
    },
    data() {
        return {
            currentChatId: null, // Track current chat
            socketInitialized: false,
            emojiIndex: emojiIndex,
            showEmojiPicker: false,
            showPlusMenu: false,
            templatePanel: {
                show: false,
                loading: false,
                list: [],
                search: '',
                selectedTemplate: null,
                params: [],
                headerMediaFile: null,
                headerMediaName: '',
                headerMediaPreview: null,
                sending: false,
            },
            showRightSidebar: false,
                mobileBurgerOpen: false, // reactive flag for mobile ⋮ dropdown
            showAddCollaborator: false,
            showAddCategory: false,
            showAddLabel: false,
            showAddPipeline: false,
            activeDropdownId: null,
            replyingTo: null,
            detail: {
                id: "",
                name: "",
                phone: "",
                photo: "",
                device_id: null,
                status: "open",
                from: "",
                device: "",
                takeover: false,
                handled: null,
                category: null,
                label: [],
                collabolators: [],
                additional: {
                    data: [],
                    note: "",
                },
                pipeline:{
                    id:null,
                    name:"",
                    stage:{
                        id:null,
                        name:""
                    }
                },
                detail: {
                    assignment_by: "",
                    resolved_by: "",
                    created_at: "",
                    resolved_at: "",
                },
            },
            message: {
                loader: false,
                list: [],
                page: 1,
                hasMoreMessages: true,
                search: "",
            },
            send: {
                loader: false,
                type: "text",
                text: "",
                file: null,
            },
            users: [],
            categories: [],
            deviceForm: {
                type: 'whatsapp',
                device_id: null,
                devices: [],
                loading: false,
                saving: false,
            },
            info_label: {
                labels: [],
                form: null,
            },
            info_pipeline: {
            pipelines: [],
            stages: [],
            form_pipeline: null,
            form_stage: null,
            loading_stages: false,
        },
            info_category: {
                form: null,
            },
            info_collabolator: {
                form: null,
            },
            quick_replies: [],
            quick_reply_all: [],
            quickReplyPopup: {
                show: false,
                loading: false,
                selectedIndex: 0,
            },
            quickReplyForm: {
                id: null,
                name: "",
                content: "",
                file: null,
            },
            additionalForm: {
                name: "",
                type: "text",
                optionsText: "",
            },
            stickerLightbox: {
                show: false,
                url: '',
            },
            filePreview: {
                type: "",
                url: "",
            },
            pasteIndicator: {
                show: false,
                message: ''
            },
            devices: [],
            shouldScrollToBottom: true,
            isUserScrolling: false,
        };
    },
    computed: {
        isInsideWindow() {
            if (!this.detail || !this.detail.last_incoming_at) return false;
            const lastMsg = new Date(this.detail.last_incoming_at);
            const now = new Date();
            const diffMs = now - lastMsg;
            return diffMs < 24 * 60 * 60 * 1000;
        },
        windowTimeLeft() {
            if (!this.detail || !this.detail.last_incoming_at) return null;
            const lastMsg = new Date(this.detail.last_incoming_at);
            const expires = new Date(lastMsg.getTime() + 24 * 60 * 60 * 1000);
            const now = new Date();
            const diffMs = expires - now;
            if (diffMs <= 0) return null;
            const hours = Math.floor(diffMs / (60 * 60 * 1000));
            const mins = Math.floor((diffMs % (60 * 60 * 1000)) / (60 * 1000));
            if (hours > 0) return hours + 'h ' + mins + 'm';
            return mins + ' min';
        },
        windowExpiredAgo() {
            if (!this.detail || !this.detail.last_incoming_at) return '';
            const lastMsg = new Date(this.detail.last_incoming_at);
            const now = new Date();
            const diffMs = now - lastMsg;
            const hours = Math.floor(diffMs / (60 * 60 * 1000));
            if (hours < 24) return hours + ' hours ago';
            const days = Math.floor(hours / 24);
            return days + ' day' + (days > 1 ? 's' : '') + ' ago';
        },

        /**
         * Chip channel — ikon, warna, label per channel
         */
        channelMeta() {
            const m = {
                waba:      { label:'WA Business', icon:'bx bxl-whatsapp',   bg:'#E1F5EE', color:'#0F6E56', dot:'#25D366' },
                whatsapp:  { label:'WA Personal', icon:'bx bxl-whatsapp',   bg:'#E1F5EE', color:'#0F6E56', dot:'#25D366' },
                instagram: { label:'Instagram',   icon:'bx bxl-instagram',  bg:'#FBEAF0', color:'#993556', dot:'#D4537E' },
                messanger: { label:'Messenger',   icon:'bx bxl-messenger',  bg:'#E6F1FB', color:'#185FA5', dot:'#0084FF' },
                telegram:  { label:'Telegram',    icon:'bx bxl-telegram',   bg:'#E6F1FB', color:'#185FA5', dot:'#229ED9' },
                livechat:  { label:'Live Chat',   icon:'bx bx-message-dots',bg:'#F1EFE8', color:'#64748B', dot:'#64748B' },
            };
            return m[this.detail?.from] || m.whatsapp;
        },

        /**
         * Status sesi per channel (WABA/IG/Messenger 24h/7d, WA device, dll)
         */
        sessionInfo() {
            const from = this.detail?.from;
            if (from === 'waba' || from === 'messanger' || from === 'instagram') {
                const hours = from === 'instagram' ? 168 : 24;
                const last  = this.detail.last_incoming_at ? new Date(this.detail.last_incoming_at) : null;
                const inside = last ? (Date.now() - last.getTime()) < hours * 3600 * 1000 : false;
                if (inside) {
                    const left = this.windowLeft(last, hours);
                    return {
                        text:     from === 'instagram' ? `Bisa balas · ${left}` : `Sesi aktif · sisa ${left}`,
                        color:    '#0F6E56',
                        icon:     'bx bx-time',
                        template: false
                    };
                }
                return { text: 'Sesi tutup', color: '#993C1D', icon: 'bx bx-time-five', template: true };
            }
            if (from === 'whatsapp')
                return { text: this.detail.device ? `Device: ${this.detail.device}` : 'WhatsApp Personal', color: '#64748B', icon: 'bx bx-mobile',   template: false };
            if (from === 'telegram')
                return { text: 'Tanpa batas waktu', color: '#64748B', icon: 'bx bx-infinite',  template: false };
            if (from === 'livechat')
                return { text: 'Online sekarang',   color: '#0F6E56', icon: 'bxs bxs-circle',  template: false };
            return { text: '', color: '#64748B', icon: '', template: false };
        },

        filteredMessages() {
            if (!this.message.search) return this.message.list;
            return this.message.list.filter(msg =>
                msg.message?.toLowerCase().includes(this.message.search.toLowerCase())
            );
        },
    },
    methods: {

        /**
         * Parse contacts from msg.extra
         */
        parseContacts(msg) {
            try {
                const d = msg.extra || msg.extra_data;
                if (!d) return null;
                const obj = typeof d === 'string' ? JSON.parse(d) : d;
                return obj.contacts && obj.contacts.length ? obj.contacts : null;
            } catch(e) { return null; }
        },

        /**
         * Parse location from msg.extra
         */
        parseLocation(msg) {
            try {
                const d = msg.extra || msg.extra_data;
                if (!d) return null;
                const obj = typeof d === 'string' ? JSON.parse(d) : d;
                return obj.location || null;
            } catch(e) { return null; }
        },

        /**
         * Google Maps URL from location data
         */
        mapsUrl(l) {
            if (l && l.lat && l.long) return 'https://www.google.com/maps?q=' + l.lat + ',' + l.long;
            return 'https://www.google.com/maps';
        },

        /**
         * Save shared contact to CRM
         */
        async saveSharedContact(c) {
            try {
                await this.$axios.post('/crm/contacts/quick-save', { name: c.name, phone: c.phone });
                if (this.$toast?.success) this.$toast.success('Kontak ' + c.name + ' disimpan');
                else alert('Kontak ' + c.name + ' disimpan');
            } catch(e) {
                if (this.$toast?.error) this.$toast.error('Gagal simpan kontak');
                else alert('Gagal simpan kontak: ' + (e?.response?.data?.message || ''));
            }
        },

        /**
         * Generic window time-left formatter (returns "Xj Ym" or "Ym")
         */
        windowLeft(last, hours) {
            const ms = (last.getTime() + hours * 3600 * 1000) - Date.now();
            if (ms <= 0) return '0m';
            const h = Math.floor(ms / 3600000);
            const m = Math.floor((ms % 3600000) / 60000);
            return h > 0 ? `${h}j ${m}m` : `${m}m`;
        },

        leadBannerLabel(s) {
            const m = { ad:'dari Iklan', story:'Balas Story', post:'dari Postingan', link:'via Link' };
            return m[s] || '';
        },
        leadBannerIcon(s) {
            const m = { ad:'bx bxs-megaphone', story:'bx bxl-instagram', post:'bx bx-image', link:'bx bx-link' };
            return m[s] || 'bx bx-tag';
        },
        originLabel(source) {
            const labels = {
                notification: 'Notifikasi',
                broadcast:    'Broadcast',
                bot:          'Bot AI',
                agent:        'Agen',
                system:       'Sistem',
                echo_wa:      'Dibalas di WA (HP)',
                echo_ig:      'Dibalas di Instagram',
                echo_fb:      'Dibalas di Fanpage',
                echo_waba:    'Dibalas di WA Hybrid',
            };
            return labels[source] || 'Sistem';
        },
        originIcon(source) {
            const icons = {
                notification: 'bx bx-bell',
                broadcast:    'bx bxs-megaphone',
                bot:          'bx bx-bot',
                agent:        'bx bx-user',
                system:       'bx bx-cog',
                echo_wa:      'bx bxl-whatsapp',
                echo_ig:      'bx bxl-instagram',
                echo_fb:      'bx bxl-facebook',
                echo_waba:    'bx bxl-whatsapp',
            };
            return icons[source] || 'bx bx-cog';
        },

         prefillMessageFromQuery() {
        const queryText = this.$route.query.text;
        
        if (queryText) {
            try {
                // Decode URL-encoded text
                this.send.text = decodeURIComponent(queryText);
                
                console.log("✅ Message pre-filled:", this.send.text);
                
                // Auto-resize textarea
                this.$nextTick(() => {
                    this.autoResizeTextarea();
                    
                    // Focus textarea
                    if (this.$refs.chatInput) {
                        this.$refs.chatInput.focus();
                    }
                });
                
                // Remove query param dari URL (clean up)
                this.$router.replace({
                    name: this.$route.name,
                    params: this.$route.params,
                    query: {} // Clear query
                });
                
            } catch (error) {
                console.error("❌ Error decoding text:", error);
            }
        }
    },

        
        async getPipelines() {
        try {
            const response = await this.$axios.get(`/kanban/pipelines`);
            this.info_pipeline.pipelines = response.data.pipelines;
        } catch (error) {
            console.error('Error getting pipelines:', error);
        }
    },

    async getStagesByPipeline(pipelineId) {
        if (!pipelineId) {
            this.info_pipeline.stages = [];
            return;
        }
        
        this.info_pipeline.loading_stages = true;
        try {
            const response = await this.$axios.get(`/crm/labels`, {
                params: { 
                    type: 'CRM',
                    pipeline_id: pipelineId  
                }
            });
            this.info_pipeline.stages = response.data.labels;
        } catch (error) {
            console.error('Error getting stages:', error);
            this.info_pipeline.stages = [];
        } finally {
            this.info_pipeline.loading_stages = false;
        }
    },

    async selectPipeline() {
        if (!this.info_pipeline.form_pipeline) {
            this.info_pipeline.stages = [];
            this.info_pipeline.form_stage = null;
            return;
        }
         
        await this.getStagesByPipeline(this.info_pipeline.form_pipeline.id);
         
        this.info_pipeline.form_stage = null;
    },
    
    async selectStage() {
        if (!this.info_pipeline.form_stage || !this.info_pipeline.form_pipeline) {
            return;
        }
        
        // Update detail local
        this.detail.pipeline = {
            id: this.info_pipeline.form_pipeline.id,
            name: this.info_pipeline.form_pipeline.name,
            stage: {
                id: this.info_pipeline.form_stage.id,
                name: this.info_pipeline.form_stage.name
            }
        };
        
        // Reset forms
        this.info_pipeline.form_pipeline = null;
        this.info_pipeline.form_stage = null;
        
        // Post ke backend
        await this.postPipelineAndStage();
        
        // Hide dropdown
        this.showAddPipeline = false;
    },

    async postPipelineAndStage() {
        try {
            const response = await this.$axios.post(
                `/crm/labels/pipeline/${this.$route.params.chatid}`,
                {
                    pipeline_id: this.detail.pipeline?.id || null,
                    label: this.detail.pipeline?.stage?.id || null
                }
            );
            
            console.log('✅ Pipeline & Stage updated:', response.data);
            
            this.$showToast(
                'Pipeline & Stage berhasil diupdate',
                'success',
                3000
            );
        } catch (error) {
            console.error('❌ Error updating pipeline:', error);
            this.$showToast(
                'Gagal update Pipeline & Stage',
                'error',
                3000
            );
        }
    },

     async removePipelineAndStage() {
        if (!confirm('Hapus Pipeline & Stage?')) return;
        
        this.detail.pipeline = {
            id: null,
            name: "",
            stage: {
                id: null,
                name: ""
            }
        };
        
        await this.postPipelineAndStage();
    },

        /**
   * Get channel icon
   */
        getChannelIcon(type) {
            const icons = {
                whatsapp:  'bx bxl-whatsapp',
                waba:      'bx bxl-whatsapp',
                telegram:  'bx bxl-telegram',
                livechat:  'bx bx-message-dots',
                instagram: 'bx bxl-instagram',
                messanger: 'bx bxl-messenger',
            };
            return icons[type] || 'bx bx-message';
        },

        /**
         * Get channel name
         */
        getChannelName(type) {
            const names = {
                whatsapp:  'WhatsApp',
                waba:      'WhatsApp Business API',
                telegram:  'Telegram',
                livechat:  'LiveChat',
                instagram: 'Instagram',
                messanger: 'Messenger',
            };
            return names[type] || type;
        },

        /**
         * Open change device modal
         */
        openChangeDeviceModal() {
            // Set current type and device
            this.deviceForm.type = this.detail.from;
            this.deviceForm.device_id = this.detail.device_id;

            // Load devices for current type
            this.loadDevices();


            // Show modal
            const modal = new bootstrap.Modal(this.$refs.changeDeviceModal, {
                backdrop: false,
                keyboard: false,
            });
            modal.show();
        },

        /**
         * Load devices based on selected type
         */
        async loadDevices() {
            this.deviceForm.loading = true;
            this.deviceForm.devices = [];
            this.deviceForm.device_id = null;

            try {
                const response = await this.$axios.get('/crm/devices', {
                    params: { type: this.deviceForm.type }
                });

                this.deviceForm.devices = response.data.data || [];

                // Auto-select current device if same type
                if (this.deviceForm.type === this.detail.from && this.detail.device_id) {
                    this.deviceForm.device_id = this.detail.device_id;
                }

                console.log('✅ Devices loaded:', this.deviceForm.devices.length);
            } catch (error) {
                console.error('❌ Error loading devices:', error);

                this.$showToast(
                    `Gagal memuat daftar device. Silakan coba lagi.`,
                    "error",
                    3000
                );
            } finally {
                this.deviceForm.loading = false;
            }
        },

        /**
         * Save device change
         */
        async saveDeviceChange() {
            if (!this.deviceForm.device_id) {
                this.$showToast(
                    `Pilih Device terlebih dahulu`,
                    "error",
                    3000
                );
                return;
            }

            // Confirm if changing type
            if (this.deviceForm.type !== this.detail.from) {
                const confirmed = confirm(
                    `Anda akan mengubah channel dari ${this.getChannelName(this.detail.from)} ke ${this.getChannelName(this.deviceForm.type)}. Lanjutkan?`
                );
                if (!confirmed) return;
            }

            this.deviceForm.saving = true;

            try {
                const response = await this.$axios.post(
                    `/crm/devices/change/${this.$route.params.chatid}`,
                    {
                        type: this.deviceForm.type,
                        device: this.deviceForm.device_id,
                        from: this.deviceForm.type, // Backend expects 'from' parameter
                    }
                );

                console.log('✅ Device changed successfully:', response.data);

                // Close modal
                const modal = bootstrap.Modal.getInstance(this.$refs.changeDeviceModal);
                modal?.hide();

                // Reload information to get updated data
                await this.getInformation();

                this.$showToast(
                    `Device berhasil di ubah`,
                    "info",
                    3000
                );
            } catch (error) {
                console.error('❌ Error changing device:', error);
                this.$showToast(
                    `Gagal mengubah device. Silakan coba lagi.`,
                    "error",
                    3000
                );
            } finally {
                this.deviceForm.saving = false;
            }
        },


        showEmoji(emoji) {
            this.send.text = this.send.text + emoji.native;
        },

        async selectCategory() {
            if (!this.info_category.form) return;

            this.detail.category = this.info_category.form;
            this.info_category.form = null;

            await this.postCategory();

            // Hide dropdown
            this.showAddCategory = false;
        },

        async removeCategory() {
            if (!confirm('Hapus kategori ini?')) return;

            this.detail.category = null;
            await this.postCategory();
        },

        async postCategory() {
            try {
                const response = await this.$axios.post(
                    `/crm/categories/change/${this.$route.params.chatid}`,
                    {
                        category: {
                            id: this.detail.category?.id || null
                        }
                    }
                );

                console.log('✅ Category updated:', response.data);

                // Update detail jika backend return data
                if (response.data.category) {
                    this.detail.category = response.data.category;
                }
            } catch (error) {
                console.error('❌ Error updating category:', error);
            }
        },


        handleKeyDown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault(); // Cegah default newline
                this.sendMessage();
            }
        },

        async handlePaste(event) {
            const items = event.clipboardData?.items;
            if (!items) return;

            for (let i = 0; i < items.length; i++) {
                const item = items[i];

                if (item.type.indexOf('image') !== -1) {
                    event.preventDefault();

                    const file = item.getAsFile();
                    if (!file) continue;

                    await this.processPastedImage(file);
                    break;
                }
            }
        },

        async processPastedImage(file) {
            console.log('📋 Pasted image:', file.name, file.type);

            const timestamp = Date.now();
            const extension = file.type.split('/')[1] || 'png';
            const renamedFile = new File(
                [file],
                `pasted-image-${timestamp}.${extension}`,
                { type: file.type }
            );

            this.send.file = renamedFile;
            this.send.type = "media";

            this.filePreview.type = "image";
            this.filePreview.url = URL.createObjectURL(renamedFile);

            this.showFilePreviewModal();
        },

        showPasteIndicator(message) {
            this.pasteIndicator.show = true;
            this.pasteIndicator.message = message;

            setTimeout(() => {
                this.pasteIndicator.show = false;
            }, 2000);
        },

        async processPastedImage(file) {
            console.log('📋 Pasted image:', file.name, file.type);

            // Show indicator
            this.showPasteIndicator('📋 Image terpaste! Siap dikirim...');

            // Rename file dengan timestamp untuk uniqueness
            const timestamp = Date.now();
            const extension = file.type.split('/')[1] || 'png';
            const renamedFile = new File(
                [file],
                `pasted-image-${timestamp}.${extension}`,
                { type: file.type }
            );

            // Set file ke send object
            this.send.file = renamedFile;
            this.send.type = "media";

            // Create preview URL
            this.filePreview.type = "image";
            this.filePreview.url = URL.createObjectURL(renamedFile);

            // Show preview modal
            this.showFilePreviewModal();
        },


        autoResizeTextarea() {
            const textarea = this.$refs.chatInput;
            if (!textarea) return;

            textarea.style.height = 'auto'; // Reset height
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px'; // Max 120px
        },


        // === Message Management ===
        async getInformation() {
            try {
                const response = await this.$axios.get(`/crm/information-detail/${this.$route.params.chatid}`);
                this.detail = response.data;
                this.getMessages();
                this.markToRead();
            } catch (error) {
                console.error(error);
            }
        },

        async getMessages(page = 1, prepend = false) {
            try {
                this.message.loader = true;
                const response = await this.$axios.get(`/crm/messages/${this.$route.params.chatid}`, {
                    params: { page, limit: 20 }
                });

                let newMessages = response.data.messages;

                if (prepend) {
                    this.message.list = [...newMessages, ...this.message.list];
                    this.$nextTick(() => {
                        const chatArea = document.querySelector('.chat-messages');
                        chatArea.scrollTop = 100;
                    });
                } else {
                    this.message.list = newMessages;
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                }

                this.message.page = page;
                this.message.hasMoreMessages = newMessages.length > 0;
                this.message.loader = false;
            } catch (error) {
                console.error(error);
                this.message.loader = false;
            }
        },

        async sendMessage() {
            if (!this.send.text && !this.send.file) return;

            NProgress.start();
            this.send.loader = true;

            try {
                const formData = new FormData();
                formData.append("message", this.send.text);
                formData.append("type", this.send.type);

                if (this.replyingTo) {
                    formData.append("reply_to", this.replyingTo.id);
                    formData.append("reply_text", this.replyingTo.message || "");
                }

                if (this.send.file) {
                    formData.append("file", this.send.file);
                }

                const response = await this.$axios.post(
                    `crm/send-message/${this.$route.params.chatid}`,
                    formData,
                    { headers: { "Content-Type": "multipart/form-data" } }
                );

                socket.emit(`crm-update`, response.data);

                this.send = { loader: false, type: "text", text: "", file: null };
                this.replyingTo = null;
                this.shouldScrollToBottom = true;

                this.$nextTick(() => {
                    if (this.$refs.chatInput) {
                        this.$refs.chatInput.style.height = 'auto';
                    }
                });


                NProgress.done();
            } catch (error) {
                console.error(error);
                this.send.loader = false;
                NProgress.done();
            }
        },

        async deleteMessage(id, index) {
            if (!confirm("Hapus pesan ini?")) return;
            try {
                await this.$axios.delete(`/crm/action/delete/${id}`);
                this.message.list.splice(index, 1);
            } catch (error) {
                console.error(error);
            }
        },

        async markToRead() {
            try {
                await this.$axios.post(`/crm/mark-read/${this.$route.params.chatid}`);
            } catch (error) {
                console.error(error);
            }
        },

        // === Reply Management ===
        replyToMessage(msg) {
            this.replyingTo = msg;
            this.activeDropdownId = null;
            this.$nextTick(() => {
                this.$refs.chatInput?.focus();
            });
        },

        cancelReply() {
            this.replyingTo = null;
        },

        scrollToMessage(msgId) {
            const element = document.getElementById('msg-' + msgId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                element.classList.add('highlight');
                setTimeout(() => element.classList.remove('highlight'), 2000);
            }
        },

        // === Quick Reply ===
        async onMessageInput() {

            this.autoResizeTextarea();

            if (this.send.text.startsWith("/")) {
                const keyword = this.send.text.substring(1);
                this.quickReplyPopup.show = true;
                this.quickReplyPopup.loading = true;
                await this.getQuickReplies(keyword);
            } else {
                this.hideQuickReplies();
            }
        },

        async getQuickReplies(search) {
            try {
                const response = await this.$axios.get(`/crm/quick-replies`, {
                    params: { name: search }
                });
                this.quick_replies = response.data.data;
                this.quickReplyPopup.loading = false;
            } catch (error) {
                this.quick_replies = [];
                this.quickReplyPopup.loading = false;
            }
        },

        selectQuickReply(item) {
            let content = item.content;
            content = content.replace(/{name}/g, this.detail.name);
            content = content.replace(/{phone}/g, this.detail.phone);

            this.send.text = content;
            this.hideQuickReplies();
            this.$refs.chatInput?.focus();
            this.$nextTick(() => {
                this.autoResizeTextarea();
            });
        },

        navigateQuickReplies(direction) {
            if (this.quick_replies.length === 0) return;
            if (direction === "down") {
                this.quickReplyPopup.selectedIndex = (this.quickReplyPopup.selectedIndex + 1) % this.quick_replies.length;
            } else {
                this.quickReplyPopup.selectedIndex = (this.quickReplyPopup.selectedIndex - 1 + this.quick_replies.length) % this.quick_replies.length;
            }
        },

        hideQuickReplies() {
            this.quickReplyPopup.show = false;
        },

        manageQuickReply() {
            this.getQuickRepliesAll();
            const modal = new bootstrap.Modal(this.$refs.quickReplyModal, {
                backdrop: false,
                keyboard: false,
            });
            modal.show();
        },

        async getQuickRepliesAll() {
            try {
                const response = await this.$axios.get(`/crm/quick-replies`, {
                    params: { page: 1, limit: 50 }
                });
                this.quick_reply_all = response.data.data;
            } catch (error) {
                console.error(error);
            }
        },

        openAddQuickReply() {
            this.quickReplyForm = { id: null, name: "", content: "", file: null };
            const modal = new bootstrap.Modal(this.$refs.addQuickReplyModal, {
                backdrop: false,
                keyboard: false,
            });
            modal.show();
        },

        editQuickReply(qr, index) {
            this.quickReplyForm = { ...qr, file: null };
            const modal = new bootstrap.Modal(this.$refs.addQuickReplyModal, {
                backdrop: false,
                keyboard: false,
            });
            modal.show();
        },

        async saveQuickReply() {
            this.send.loader = true;
            try {
                const formData = new FormData();
                formData.append("name", this.quickReplyForm.name);
                formData.append("content", this.quickReplyForm.content);
                if (this.quickReplyForm.file) {
                    formData.append("media", this.quickReplyForm.file);
                }

                const url = this.quickReplyForm.id
                    ? `/crm/quick-replies/update/${this.quickReplyForm.id}`
                    : `/crm/quick-replies/create`;

                await this.$axios.post(url, formData, {
                    headers: { "Content-Type": "multipart/form-data" }
                });

                this.send.loader = false;
                const modal = bootstrap.Modal.getInstance(this.$refs.addQuickReplyModal, {
                    backdrop: false,
                    keyboard: false,
                });
                modal?.hide();
                this.getQuickRepliesAll();
            } catch (error) {
                console.error(error);
                this.send.loader = false;
            }
        },

        async deleteQuickReply(id) {
            if (!confirm("Hapus quick reply ini?")) return;
            try {
                await this.$axios.delete(`/crm/quick-replies/remove/${id}`);
                this.getQuickRepliesAll();
            } catch (error) {
                console.error(error);
            }
        },

        handleQuickReplyFile(event) {
            const file = event.target.files[0];
            if (file) {
                this.quickReplyForm.file = file;
            }
        },

        // === File Management ===
        triggerFileInput() {
            this.$refs.fileInput?.click();
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.send.file = file;

            if (file.type.startsWith("image")) {
                this.send.type = "media";
                this.filePreview.type = "image";
                this.filePreview.url = URL.createObjectURL(file);
                this.showFilePreviewModal();
            } else if (file.type.startsWith("video")) {
                this.send.type = "media";
                this.filePreview.type = "video";
                this.filePreview.url = URL.createObjectURL(file);
                this.showFilePreviewModal();
            } else {
                this.send.type = "document";
                this.filePreview.type = "document";
                this.filePreview.fileName = file.name;
                this.filePreview.fileSize = this.formatFileSize(file.size);
                this.showFilePreviewModal();
            }
        },

        showFilePreviewModal() {
            const modal = new bootstrap.Modal(this.$refs.filePreviewModal, {
                backdrop: false,
                keyboard: false,
            });
            modal.show();
        },

        confirmSendFile() {
            const modal = bootstrap.Modal.getInstance(this.$refs.filePreviewModal, {
                backdrop: false,
                keyboard: false,
            });
            modal?.hide();
            this.sendMessage();
        },

        openImagePreview(url) {
            window.open(url, '_blank');
        },

        // === Contact Info Management ===
        async selectUser() {
            try {
                const response = await this.$axios.post(
                    `/crm/users/user-change/${this.$route.params.chatid}`,
                    { handled: this.detail.handled }
                );
                this.detail.detail.assignment_by = response.data.detail.assignment_by;
            } catch (error) {
                console.error(error);
            }
        },

        async selectLabel() {
            if (!this.info_label.form) return;
            const exists = this.detail.label.some(l => l.id === this.info_label.form.id);
            if (!exists) {
                this.detail.label.push(this.info_label.form);
                this.info_label.form = null;
                await this.postLabel();
            }
            this.showAddLabel = false;
        },

        async removeLabel(id) {
            this.detail.label = this.detail.label.filter(l => l.id !== id);
            await this.postLabel();
        },

        async postLabel() {
            try {
                await this.$axios.post(`/crm/labels/change/${this.$route.params.chatid}`, {
                    labels: this.detail.label
                });
            } catch (error) {
                console.error(error);
            }
        },

        async selectCollabolator() {
            if (!this.info_collabolator.form) return;
            const exists = this.detail.collabolators.some(c => c.id === this.info_collabolator.form.id);
            if (!exists) {
                this.detail.collabolators.push(this.info_collabolator.form);
                this.info_collabolator.form = null;
                await this.postCollab();
            }
            this.showAddCollaborator = false;
        },

        async removeCollab(id) {
            this.detail.collabolators = this.detail.collabolators.filter(c => c.id !== id);
            await this.postCollab();
        },

        async postCollab() {
            try {
                await this.$axios.post(`/crm/users/collabolator/${this.$route.params.chatid}`, {
                    collabolator: this.detail.collabolators
                });
            } catch (error) {
                console.error(error);
            }
        },

        openAdditionalModal() {
            this.additionalForm = { name: "", type: "text", optionsText: "" };
            const modal = new bootstrap.Modal(this.$refs.additionalModal, {
                backdrop: false,
                keyboard: false,
            });
            modal.show();
        },

        saveAdditionalField() {
            const newField = {
                name: this.additionalForm.name,
                type: this.additionalForm.type,
                value: "",
                options: this.additionalForm.type === "options"
                    ? this.additionalForm.optionsText.split(",").map(o => o.trim())
                    : []
            };
            this.detail.additional.data.push(newField);
            this.updateAdditional();
            const modal = bootstrap.Modal.getInstance(this.$refs.additionalModal, {
                backdrop: false,
                keyboard: false,
            });
            modal?.hide();
        },

        async updateAdditional() {
            try {
                await this.$axios.post(`/crm/action/change-additional/${this.$route.params.chatid}`, {
                    note: this.detail.additional.note,
                    additional_data: this.detail.additional.data
                });
            } catch (error) {
                console.error(error);
            }
        },

        async blockUnblock() {
            const newStatus = this.detail.status === "block" ? "open" : "block";
            try {
                await this.$axios.post(`/crm/action/block/${this.$route.params.chatid}`, {
                    status: newStatus
                });
                this.detail.status = newStatus;
            } catch (error) {
                console.error(error);
            }
        },

        async resolved(status) {
            try {
                const response = await this.$axios.post(`/crm/action/resolved/${this.$route.params.chatid}`);
                this.detail.status = status;
                this.detail.detail.resolved_by = response.data.detail.resolved_by;
                this.detail.detail.resolved_at = response.data.detail.resolved_at;
                this.detail.takeover = false;
            } catch (error) {
                console.error(error);
            }
        },

        async changeTakeOver(status) {
            try {
                const response = await this.$axios.post(`/crm/takeover-message/${this.$route.params.chatid}`, {
                    takeover: status
                });
                this.detail.takeover = response.data.status;
                this.detail.handled = response.data.handled;
                this.detail.detail.assignment_by = response.data.detail.assignment_by;
            } catch (error) {
                console.error(error);
            }
        },

        async changeStatus() {
             this.resolved(this.detail.status);
        },

        copyPhone() {
            navigator.clipboard.writeText(this.detail.phone);
            this.$showToast(
                `Nomor Telpon di salin`,
                "info",
                3000
            );
        },

        copyBsuid() {
            navigator.clipboard.writeText(this.detail.bsuid || '');
            this.$showToast(`BSUID disalin`, 'info', 3000);
        },

        // === UI Helpers ===
        toggleActionMenu(msgId) {
            this.activeDropdownId = this.activeDropdownId === msgId ? null : msgId;
        },

        toggleRightSidebar() {
            this.showRightSidebar = !this.showRightSidebar;
        },

        togglePlusMenu() {
            this.showPlusMenu = !this.showPlusMenu;
            if (this.showPlusMenu) {
                this.showEmojiPicker = false;
                this.closeTemplatePanel();
            }
        },

        openQuickReplyFromMenu() {
            this.showPlusMenu = false;
            this.send.text = '/';
            this.$nextTick(() => {
                this.$refs.chatInput?.focus();
                this.onMessageInput();
            });
        },

        triggerFileFromMenu() {
            this.showPlusMenu = false;
            this.triggerFileInput();
        },

        async openTemplatePanel() {
            this.showPlusMenu = false;
            this.showEmojiPicker = false;
            this.templatePanel.show = true;
            this.templatePanel.selectedTemplate = null;
            this.templatePanel.search = '';
            await this.loadTemplates();
        },

        closeTemplatePanel() {
            this.templatePanel.show = false;
            this.templatePanel.selectedTemplate = null;
            this.templatePanel.params = [];
            this.templatePanel.headerMediaFile = null;
            this.templatePanel.headerMediaName = '';
            this.templatePanel.headerMediaPreview = null;
        },

        async loadTemplates() {
            this.templatePanel.loading = true;
            try {
                const response = await this.$axios.get(`/crm/templates/${this.$route.params.chatid}`, {
                    params: { search: this.templatePanel.search }
                });
                this.templatePanel.list = response.data.data || [];
            } catch (e) {
                console.error('Failed to load templates', e);
                this.templatePanel.list = [];
            }
            this.templatePanel.loading = false;
        },

        searchTemplates: _.debounce(function () {
            this.loadTemplates();
        }, 300),

        selectTemplate(tmpl) {
            this.templatePanel.selectedTemplate = tmpl;
            this.templatePanel.params = [];
            this.templatePanel.headerMediaFile = null;
            this.templatePanel.headerMediaName = '';
            this.templatePanel.headerMediaPreview = null;
            // Auto-fill first param with customer name
            if (tmpl.body_params > 0) {
                this.templatePanel.params[0] = this.detail.name || '';
                for (let i = 1; i < tmpl.body_params; i++) {
                    this.templatePanel.params[i] = '';
                }
            }
        },

        handleTemplateMedia(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.templatePanel.headerMediaFile = file;
            this.templatePanel.headerMediaName = file.name;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.templatePanel.headerMediaPreview = ev.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.templatePanel.headerMediaPreview = null;
            }
        },

        getTemplatePreview() {
            if (!this.templatePanel.selectedTemplate) return '';
            let text = this.templatePanel.selectedTemplate.body_text || '';
            this.templatePanel.params.forEach((param, i) => {
                text = text.replace(`{{${i + 1}}}`, param || `[param ${i + 1}]`);
            });
            return text;
        },

        canSendTemplate() {
            const tmpl = this.templatePanel.selectedTemplate;
            if (!tmpl) return false;

            // Check media requirement
            if (tmpl.header_type && ['image', 'video', 'document'].includes(tmpl.header_type)) {
                if (!this.templatePanel.headerMediaFile) return false;
            }

            // Check all params filled
            if (tmpl.body_params > 0) {
                for (let i = 0; i < tmpl.body_params; i++) {
                    if (!this.templatePanel.params[i]?.trim()) return false;
                }
            }

            return true;
        },

        async sendTemplateMessage() {
            if (!this.canSendTemplate()) return;

            this.templatePanel.sending = true;
            try {
                const tmpl = this.templatePanel.selectedTemplate;
                const formData = new FormData();
                formData.append('template_id', tmpl.id);

                // Parameters
                this.templatePanel.params.forEach((param, i) => {
                    formData.append(`parameters[${i}]`, param);
                });

                // Media
                if (this.templatePanel.headerMediaFile) {
                    formData.append('header_media', this.templatePanel.headerMediaFile);
                }

                const response = await this.$axios.post(
                    `/crm/send-template/${this.$route.params.chatid}`,
                    formData,
                    { headers: { 'Content-Type': 'multipart/form-data' } }
                );

                if (response.data.status === 200) {
                    // Add message to chat
                    this.getMessages();
                    this.closeTemplatePanel();
                } else {
                    alert('Failed: ' + (response.data.error || 'Unknown error'));
                }
            } catch (e) {
                console.error('Send template error', e);
                alert('Error: ' + (e.response?.data?.error || e.message));
            }
            this.templatePanel.sending = false;
        },

        toggleEmoji() {
            this.showEmojiPicker = !this.showEmojiPicker;
        },

        insertEmoji(emoji) {
            this.send.text += emoji.native;
            this.showEmojiPicker = false;
        },

        scrollToBottom() {
            if (!this.shouldScrollToBottom) return;
            const messageContent = document.querySelector(".chat-messages");
            if (messageContent) {
                messageContent.scrollTop = messageContent.scrollHeight;
            }
        },

        handleChatScroll(e) {
            const element = e.target;
            this.isUserScrolling = true;

            const isNearBottom = element.scrollHeight - element.scrollTop - element.clientHeight < 100;
            this.shouldScrollToBottom = isNearBottom;

            if (element.scrollTop <= 50 && this.message.hasMoreMessages && !this.message.loader) {
                this.getMessages(this.message.page + 1, true);
            }
        },

        // === Utility Methods ===
        formattedText(text) {
            if (!text) return "";
            // Escape HTML first
            let html = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            // Template label
            html = html.replace(/\[Template: ([^\]]+)\]/g, '<span style="display:inline-block;background:rgba(255,255,255,0.15);border-radius:4px;padding:2px 8px;font-size:11px;margin-bottom:4px;opacity:0.8">📋 Template: $1</span>');
            // WhatsApp formatting
            html = html.replace(/\*([^*\n]+)\*/g, '<strong>$1</strong>');
            html = html.replace(/_([^_\n]+)_/g, '<em>$1</em>');
            html = html.replace(/~([^~\n]+)~/g, '<del>$1</del>');
            html = html.replace(/```([^`]+)```/g, '<code>$1</code>');
            // Button rendering
            html = html.replace(/🔘 (.+)/g, '<div style="display:flex;align-items:center;gap:6px;margin:3px 0;padding:6px 12px;background:rgba(255,255,255,0.12);border-radius:8px;border:1px solid rgba(255,255,255,0.2)"><i class="bx bx-link-external" style="font-size:14px"></i><span>$1</span></div>');
            // URLs
            html = html.replace(/(https?:\/\/[^\s<]+)/g, '<a href="$1" target="_blank" style="color:#0ea5e9;text-decoration:underline">$1</a>');
            // Newlines
            html = html.replace(/\n/g, '<br>');
            return html;
        },

        truncateText(text, length) {
            if (!text) return "";
            return text.length > length ? text.substring(0, length) + "..." : text;
        },

        formatDate() {
            const today = new Date();
            return today.toLocaleDateString("id-ID", {
                day: "numeric",
                month: "short",
                year: "numeric"
            });
        },

        isDocumentType(type) {
            return ['document', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'file'].includes(type);
        },

        getDocumentIcon(type) {
            const icons = {
                document: 'bx bxs-file-doc',
                pdf: 'bx bxs-file-pdf',
                doc: 'bx bxs-file-doc',
                docx: 'bx bxs-file-doc',
                xls: 'bx bxs-file',
                xlsx: 'bx bxs-file',
                ppt: 'bx bxs-file',
                pptx: 'bx bxs-file',
            };
            return icons[type] || 'bx bxs-file';
        },

        getFileName(url, originalName = null) {
            if (originalName) return originalName;
            if (!url) return "File";
            const parts = url.split("/");
            return parts[parts.length - 1] || "File";
        },

        formatFileSize(bytes) {
            if (!bytes) return "";
            const sizes = ["Bytes", "KB", "MB", "GB"];
            const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
        },

        async getUsers() {
            try {
                const response = await this.$axios.get(`/users/components`);
                this.users = response.data.users;
            } catch (error) {
                console.error(error);
            }
        },

        async getLabels() {
            try {
                const response = await this.$axios.get(`/crm/labels`);
                this.info_label.labels = response.data.labels;
            } catch (error) {
                console.error(error);
            }
        },

        async getCategories() {
            try {
                const response = await this.$axios.get(`/crm/categories`);
                this.categories = response.data.categories;
            } catch (error) {
                console.error(error);
            }
        },

        /**
        * Setup socket listeners dengan proper cleanup
        */
        setupSocketListeners(chatId) {
            // Cleanup listener lama dulu
            this.cleanupSocketListeners();

            // Set current chat ID
            this.currentChatId = chatId;

            // Listener 1: Specific untuk chat ini
            socket.on(`update-message-${chatId}`, this.handleNewMessage);

            // Listener 2: Global listener sebagai fallback
            socket.on('update-chat-list', this.handleGlobalUpdate);

            // Listener 3: Error handling
            socket.on('error', this.handleSocketError);

            console.log(`✅ Socket listeners setup for chat: ${chatId}`);
            this.socketInitialized = true;
        },

        /**
        * Cleanup semua socket listeners
        */
        cleanupSocketListeners() {
            if (this.currentChatId) {
                socket.off(`update-message-${this.currentChatId}`, this.handleNewMessage);
                console.log(`🧹 Cleaned up listener for chat: ${this.currentChatId}`);
            }
            socket.off('update-chat-list', this.handleGlobalUpdate);
            socket.off('error', this.handleSocketError);
            this.socketInitialized = false;
        },

        /**
        * Handle pesan baru dari event spesifik
        */
        handleNewMessage(newMessage) {
            const exists = this.message.list.find(m => m.id === newMessage.id);
            if (exists) {
                console.log('⚠️ Message already exists, skipping');
                return;
            }

            this.message.list.push(newMessage);

            this.$nextTick(() => {
                if (this.shouldScrollToBottom) {
                    this.scrollToBottom();
                }
                this.markToRead();
            });
        },

        /**
  * Handle update dari global event (fallback)
  */
        handleGlobalUpdate(data) {
            if (data.conversation_id === this.$route.params.chatid) {
                const newMessage = {
                    id: data.id || Date.now(),
                    message: data.message,
                    sent_by: data.sent_by || 'user',
                    sent_by_name: data.sent_by_name,
                    source: data.source || 'system',
                    buttons: data.buttons || [],
                    media_type: data.media_type,
                    media_url: data.media_url,
                    media_size: data.media_size,
                    datetime: {
                        date: data.datetime.date,
                        time: data.datetime.time,
                    },
                    user: {
                        name: data.user.name,
                    },
                    // ✅ TAMBAHKAN INI - Data reply untuk quote
                    reply_to: data.reply_to || null,
                    reply_id: data.reply_id || null,
                    reply_text: data.reply_text || null,
                    reply_type: data.reply_type || null,
                    reply_media_url: data.reply_media_url || null,
                };

                this.handleNewMessage(newMessage);
            }
        },

        /**
         * Handle socket errors
         */
        handleSocketError(error) {
            console.error('❌ Socket error:', error);
        },

        shouldShowDateSeparator(index) {
            if (index === 0) return true;

            const currentMsg = this.filteredMessages[index];
            const prevMsg = this.filteredMessages[index - 1];

            return currentMsg.datetime.date !== prevMsg.datetime.date;
        },

        groupReactions(reactions) {
                if (!reactions) return [];
                const grouped = {};
                reactions.forEach(r => {
                    if (!grouped[r.emoji]) {
                        grouped[r.emoji] = { emoji: r.emoji, count: 0, reactors: [] };
                    }
                    grouped[r.emoji].count++;
                    if (r.from) grouped[r.emoji].reactors.push(r.from);
                });
                return Object.values(grouped);
            },
        formatDateSeparator(dateStr) {
            if (!dateStr) return '';
            let date;
            const s = String(dateStr).trim();

            if (s.includes('/')) {
                // dd/mm/yyyy (from datetime.date)
                const [d2, m2, y2] = s.split('/');
                date = new Date(parseInt(y2), parseInt(m2) - 1, parseInt(d2));
            } else if (/^\d{4}-/.test(s)) {
                // ISO 8601: 2026-06-16T... or 2026-06-16
                date = new Date(s);
            } else if (/^\d{1,2} /.test(s)) {
                // "16 Jun 2026" — date_id from isoFormat
                date = new Date(s);
            } else {
                date = new Date(s);
            }

            // Guard: don't show "Invalid Date"
            if (!date || isNaN(date.getTime())) return '';

            const today = new Date(); today.setHours(0, 0, 0, 0);
            const yesterday = new Date(today); yesterday.setDate(today.getDate() - 1);
            const cmp = new Date(date); cmp.setHours(0, 0, 0, 0);

            if (cmp.getTime() === today.getTime())     return 'Hari Ini';
            if (cmp.getTime() === yesterday.getTime()) return 'Kemarin';

            return date.toLocaleDateString('id-ID', {
                day: 'numeric', month: 'long', year: 'numeric'
            });
        },
    },

    mounted() {
            // 24H Window timer - refresh every minute
            this._windowTimer = setInterval(() => {
                this.$forceUpdate();
            }, 60000);

        this.getInformation();
        this.getUsers();
        this.getLabels();
        this.getCategories();
        this.getPipelines();
        this.getQuickRepliesAll();

        this.prefillMessageFromQuery();

        if (this.$route.params.chatid) {
            this.setupSocketListeners(this.$route.params.chatid);
        }


        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.message-actions')) {
                this.activeDropdownId = null;
            }
            if (!e.target.closest('.emoji-picker') && !e.target.closest('.input-btn') && !e.target.closest('.plus-menu-container')) {
                this.showPlusMenu = false;
                this.showEmojiPicker = false;
            }
        });
    },

    beforeUnmount() {
            if (this._windowTimer) clearInterval(this._windowTimer);

        // Proper cleanup
        this.cleanupSocketListeners();
    },

    watch: {
        "$route.params.chatid": {
            handler(newChatId, oldChatId) {
                if (this.$route.name === "chat_room" && newChatId) {
                    console.log(`🔄 Chat changed: ${oldChatId} → ${newChatId}`);

                    // Reset message state
                    this.message = {
                        loader: true,
                        list: [],
                        page: 1,
                        hasMoreMessages: true,
                        search: "",
                    };
                    this.shouldScrollToBottom = true;

                    // Setup listener baru dengan chat ID baru
                    this.setupSocketListeners(newChatId);

                    // Load data
                    this.getInformation();

                    this.$nextTick(() => {
                        this.prefillMessageFromQuery();
                    });
                
                }
            },
            immediate: true,
        },
    },
};
</script>

<style scoped>
.chat-wrapper {
    display: flex;
    flex: 1;
    min-height: 0;     /* fix: height:100 invalid → min-height:0 untuk flex */
    overflow: hidden;
}

.main-chat {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    position: relative;
}

.chat-header-main {
    flex-shrink: 0;
    z-index: 10;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    background: #F0F4F8; /* brand light blue-gray, no WA doodle */
}

.reply-bar {
    flex-shrink: 0;
}

.chat-input-area {
    flex-shrink: 0;
    background-color: #fff;
    border-top: 1px solid #e5e7eb;
    z-index: 10;
    /* iOS safe area: prevent input from hiding behind home bar */
    padding-bottom: env(safe-area-inset-bottom, 0px);
}

.chat-input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    font-size: 14px;
    resize: none;
    /* Disable manual resize */
    max-height: 120px;
    /* Max 5-6 lines */
    overflow-y: auto;
    line-height: 1.5;
    font-family: inherit;
    transition: border-color 0.2s;
}

.chat-input:focus {
    outline: none;
    border-color: #3b82f6;
}

.chat-input::placeholder {
    color: #9ca3af;
}

/* Auto-resize textarea */
.chat-input::-webkit-scrollbar {
    width: 4px;
}

.chat-input::-webkit-scrollbar-track {
    background: transparent;
}

.chat-input::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.sidebar-right {
    flex-shrink: 0;
    width: 320px;
    overflow-y: auto;
}

.quick-replies-panel {
    position: absolute;
    bottom: 100%;
    left: 0;
    right: 0;
    z-index: 999;
}

/* Message Styles */
.message-wrapper {
    position: relative;
}

.message-actions {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 100;
}

.message.received .message-actions {
    right: -35px;
}

.message.sent .message-actions {
    left: -35px;
}

.dropdown-menu-custom {
    position: absolute;
    z-index: 9999;
}

.message.received .dropdown-menu-custom {
    right: 0;
    top: 100%;
}

.message.sent .dropdown-menu-custom {
    left: 0;
    top: 100%;
}

/* Highlight effect for scrolled messages */
@keyframes highlight {
    0% {
        background-color: rgba(59, 130, 246, 0.2);
    }

    100% {
        background-color: transparent;
    }
}

.message.highlight {
    animation: highlight 2s ease;
}

/* Media Document Styling */
/* ── Compact Sticker ── */
.sticker-compact-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    cursor: pointer;
    border-radius: 10px;
    background: transparent;
    transition: transform 0.15s ease;
    padding: 0;
    vertical-align: middle;
}
.sticker-compact-wrapper:hover {
    transform: scale(1.1);
}
.sticker-compact-img {
    width: 80px !important;
    height: 80px !important;
    max-width: 80px !important;
    max-height: 80px !important;
    min-width: 80px !important;
    min-height: 80px !important;
    object-fit: contain;
    border-radius: 6px;
    display: block;
}
.sticker-fallback {
    width: 80px;
    height: 80px;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.05);
    border-radius: 8px;
    font-size: 26px;
    color: #888;
}
.sticker-fallback small {
    font-size: 10px;
    color: #aaa;
    margin-top: 2px;
}
/* ── Sticker Lightbox ── */
.sticker-lightbox-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.80);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeInBg 0.18s ease;
}
@keyframes fadeInBg {
    from { opacity: 0; } to { opacity: 1; }
}
.sticker-lightbox-box {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: popInSticker 0.2s ease;
}
@keyframes popInSticker {
    from { transform: scale(0.6); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
.sticker-lightbox-img {
    max-width: 300px;
    max-height: 300px;
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 14px;
}
.sticker-lightbox-close {
    position: absolute;
    top: -14px; right: -14px;
    width: 32px; height: 32px;
    border-radius: 50%;
    background: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    color: #333;
    z-index: 1;
}
.sticker-lightbox-close:hover { background: #f0f0f0; }

.message-media img,
.message-media video {
    max-width: 300px;
    border-radius: 8px;
    cursor: pointer;
}

.message-media audio {
    width: 100%;
    max-width: 300px;
}

.message-document {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background-color: #f3f4f6;
    border-radius: 8px;
    max-width: 300px;
}

.document-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #3b82f6;
    color: white;
    border-radius: 8px;
    font-size: 20px;
}

.document-info {
    flex: 1;
    min-width: 0;
}

.document-name {
    font-weight: 600;
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #1f2937;
}

.document-size {
    font-size: 11px;
    color: #6b7280;
    margin-top: 2px;
}

.document-download {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #fff;
    border-radius: 50%;
    color: #3b82f6;
    transition: all 0.2s;
}

.document-download:hover {
    background-color: #dbeafe;
}

/* Responsive */
@media (max-width: 992px) {
    .chat-wrapper {
        position: relative;
    }

    .sidebar-right {
        position: fixed;
        top: 3.75rem;                        /* di bawah navbar global */
        right: 0;
        bottom: 0;
        height: calc(100dvh - 3.75rem);
        width: min(85vw, 360px);             /* drawer — background nongol di kiri */
        z-index: 1001;                        /* di atas backdrop */
        box-shadow: -4px 0 16px rgba(0,0,0,0.15);
        overflow-y: auto;
    }

    .sidebar-right:not(.hidden) {
        right: 0;
        box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
    }
}

@media (max-width: 576px) {
    .sidebar-right {
        width: 85vw;                         /* tetap drawer di semua mobile */
    }

    .message-media img,
    .message-media video {
        max-width: 100%;
    }
}

.emoji-picker-wrapper {
    position: absolute;
    bottom: 80px;
    /* Fixed dari 6% */
    left: 10px;
    z-index: 1000;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* FIXED: Audio Media Styling with proper padding */
.message-media.audio-media {
    padding: 12px;
    background-color: #f3f4f6;
    border-radius: 8px;
    margin: 4px 0;
}

.message-media audio {
    width: 100%;
    max-width: 300px;
    min-width: 250px;
    /* Biar ga terlalu kecil */
}

/* Sender Info Styling */
.message-sender-info {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
    padding-bottom: 6px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    font-size: 12px;
}

.sender-name {
    font-weight: 600;
    color: #10b981;
}

.sender-separator {
    color: #9ca3af;
    font-weight: 500;
}

.sender-date {
    font-size: 11px;
    opacity: 0.7;
}

/* Untuk pesan received (dari customer) */
.message.received .message-sender-info .sender-name {
    color: #3b82f6;
}

/* Paste Indicator */
.paste-indicator {
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #10b981;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    z-index: 100;
    white-space: nowrap;
    margin-bottom: 8px;
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.category-tag {
    background-color: #dbeafe !important;
    color: #1e40af !important;
}

.category-tag .remove-tag {
    color: #1e40af;
    opacity: 0.8;
}

.category-tag .remove-tag:hover {
    opacity: 1;
}

/* Empty state inline */
.empty-state-inline {
    display: inline-block;
    color: #9ca3af;
    font-size: 13px;
    font-style: italic;
}


.device-info-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 12px;
    background-color: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.device-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    width: fit-content;
}

.device-badge i {
    font-size: 16px;
}

/* Channel badges dengan warna berbeda */
.badge-whatsapp {
    background-color: #25d366;
    color: white;
}

.badge-waba {
    background-color: #128c7e;
    color: white;
}

.badge-telegram {
    background-color: #0088cc;
    color: white;
}

.badge-livechat {
    background-color: #8b5cf6;
    color: white;
}

.device-name {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

/* ── BSUID / Username Identity Styles ─────────────────────── */
.badge-username {
    font-size: 10px;
    background: #F1ECFE;
    color: #5B3FB0;
    padding: 2px 7px;
    border-radius: 10px;
    margin-left: 6px;
    font-weight: 500;
    vertical-align: middle;
}

.user-username {
    font-size: 12px;
    color: #5B3FB0;
    display: flex;
    align-items: center;
    gap: 4px;
    justify-content: center;
    margin-top: 2px;
}

.user-phone.muted {
    color: #94A3B8;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
    justify-content: center;
}

.user-bsuid {
    font-size: 11px;
    color: #94A3B8;
    font-family: monospace;
    background: #F5F8FC;
    padding: 3px 8px;
    border-radius: 6px;
    margin-top: 4px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    user-select: none;
}
.user-bsuid:hover {
    background: #EBF3FD;
    color: #5B3FB0;
}

/* Modal Form Styling */
.form-select:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
}

.alert-info {
    background-color: #dbeafe;
    border-color: #93c5fd;
    color: #1e40af;
    border-radius: 6px;
    padding: 10px;
}

.alert-info small {
    display: block;
    margin: 0;
}

/* Pipeline & Stage Display */
.pipeline-display {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.pipeline-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background-color: #dbeafe;
    color: #1e40af;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    width: fit-content;
}

.pipeline-badge i {
    font-size: 16px;
}

.stage-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background-color: #dcfce7;
    color: #166534;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    width: fit-content;
    margin-left: 16px;
}

.stage-badge i.bx-flag {
    font-size: 14px;
}

.stage-badge .remove-tag {
    margin-left: 4px;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.stage-badge .remove-tag:hover {
    opacity: 1;
}

.pipeline-form {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* ============================
   Chat Input Bar - Beautified
   ============================ */
.chat-input-area {
    padding: 12px 16px;
    /* iOS safe area: prevent input bar from hiding behind home bar / gesture strip */
    padding-bottom: calc(12px + env(safe-area-inset-bottom, 0px));
}

.input-wrapper {
    display: flex;
    align-items: flex-end;
    gap: 8px;
}

.input-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border: none;
    background: #f3f4f6;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #6b7280;
    font-size: 18px;
    flex-shrink: 0;
}

.input-btn:hover {
    background: #e5e7eb;
    color: #374151;
    transform: scale(1.05);
}

.input-btn.primary {
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: white;
    box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
}

.input-btn.primary:hover {
    background: linear-gradient(135deg, #0284c7, #0369a1);
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
    transform: scale(1.08);
}

.input-btn.primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.input-btn.success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.input-btn.success:hover {
    background: linear-gradient(135deg, #059669, #047857);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    transform: scale(1.08);
}

.chat-input {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.chat-input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

/* ============================
   + Menu Dropdown
   ============================ */
.plus-menu-container {
    position: relative;
}

.plus-menu-dropdown {
    position: absolute;
    bottom: 100%;
    left: 0;
    margin-bottom: 8px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    overflow: hidden;
    z-index: 100;
    min-width: 160px;
    animation: slideUp 0.15s ease;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.plus-menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    cursor: pointer;
    transition: background 0.15s;
    font-size: 14px;
    color: #374151;
}

.plus-menu-item:hover {
    background: #f3f4f6;
}

.plus-menu-item i {
    font-size: 18px;
    color: #6b7280;
}

/* ============================
   Template Panel
   ============================ */
.template-panel {
    position: absolute;
    bottom: 100%;
    left: 0;
    right: 0;
    background: white;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    max-height: 420px;
    overflow-y: auto;
    z-index: 999;
    animation: slideUp 0.2s ease;
}

.template-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 600;
    font-size: 14px;
    color: #1f2937;
    position: sticky;
    top: 0;
    background: white;
    z-index: 1;
}

.template-panel-header span {
    display: flex;
    align-items: center;
    gap: 8px;
}

.template-close, .template-back {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 20px;
    color: #6b7280;
    padding: 2px;
    border-radius: 4px;
}

.template-close:hover, .template-back:hover {
    color: #374151;
    background: #f3f4f6;
}

.template-search {
    padding: 8px 16px;
    border-bottom: 1px solid #f3f4f6;
    position: sticky;
    top: 52px;
    background: white;
    z-index: 1;
}

.template-search input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
}

.template-search input:focus {
    border-color: #0ea5e9;
}

.template-list {
    max-height: 280px;
    overflow-y: auto;
}

.template-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background 0.15s;
}

.template-item:hover {
    background: #f9fafb;
}

.template-item-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}

.template-name {
    font-weight: 600;
    font-size: 13px;
    color: #1f2937;
}

.template-badge {
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: 500;
    text-transform: uppercase;
}

.template-badge.marketing {
    background: #fef3c7;
    color: #92400e;
}

.template-badge.utility {
    background: #dbeafe;
    color: #1e40af;
}

.template-badge.authentication {
    background: #ede9fe;
    color: #5b21b6;
}

.template-item-meta {
    display: flex;
    gap: 8px;
    margin-bottom: 4px;
}

.template-media-tag, .template-param-tag {
    font-size: 11px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 3px;
}

.template-preview-text {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.4;
}

.template-empty, .template-loading {
    padding: 24px;
    text-align: center;
    color: #9ca3af;
    font-size: 13px;
}

/* Template Detail */
.template-media-upload {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
}

.template-media-label {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 16px;
    border: 2px dashed #d1d5db;
    border-radius: 10px;
    cursor: pointer;
    justify-content: center;
    color: #6b7280;
    font-size: 13px;
    transition: all 0.2s;
}

.template-media-label:hover {
    border-color: #0ea5e9;
    color: #0ea5e9;
}

.template-media-label i {
    font-size: 22px;
}

.template-params {
    padding: 12px 16px;
}

.template-param-field {
    margin-bottom: 8px;
}

.template-param-field label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 4px;
    text-transform: uppercase;
}

.template-param-field input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
}

.template-param-field input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 2px rgba(14,165,233,0.1);
}

.template-preview-box {
    padding: 12px 16px;
}

.template-preview-label {
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    margin-bottom: 6px;
}

.template-preview-card {
    background: #f0f9f4;
    border-radius: 10px;
    padding: 12px;
    font-size: 13px;
    line-height: 1.5;
    color: #1f2937;
    border-left: 4px solid #25d366;
}

.tpc-header {
    font-weight: 600;
    margin-bottom: 6px;
}

.tpc-body {
    white-space: pre-wrap;
}

.tpc-footer {
    margin-top: 6px;
    font-size: 11px;
    color: #6b7280;
}

.tpc-buttons {
    margin-top: 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.tpc-btn {
    text-align: center;
    padding: 6px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 12px;
    color: #0ea5e9;
}

.template-actions {
    display: flex;
    gap: 8px;
    padding: 12px 16px;
    border-top: 1px solid #e5e7eb;
    position: sticky;
    bottom: 0;
    background: white;
}

.template-cancel-btn {
    flex: 1;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    font-size: 13px;
    color: #374151;
    font-weight: 500;
    transition: all 0.2s;
}

.template-cancel-btn:hover {
    background: #f3f4f6;
}

.template-send-btn {
    flex: 2;
    padding: 10px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: white;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s;
}

.template-send-btn:hover {
    background: linear-gradient(135deg, #0284c7, #0369a1);
    box-shadow: 0 4px 12px rgba(14,165,233,0.3);
}

.template-send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}


/* 24H Conversation Window */
.window-status-card {
    padding: 4px 16px 8px;
}
.window-status {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
}
.window-status.inside {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border: 1px solid #6ee7b7;
}
.window-status.outside {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    border: 1px solid #fca5a5;
}
.window-status i {
    font-size: 18px;
}
.window-badge {
    margin-left: auto;
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.badge-inside {
    background: #065f46;
    color: #d1fae5;
}
.badge-outside {
    background: #991b1b;
    color: #fee2e2;
}
.window-timer {
    display: flex;
    align-items: center;
    gap: 4px;
    justify-content: center;
    color: #6b7280;
    margin-top: 6px;
    font-size: 12px;
}
.window-timer i {
    font-size: 14px;
}
.window-timer.expired {
    color: #9ca3af;
}


/* Message Reactions — WhatsApp-style floating pill */
.message-bubble {
    position: relative;
}
.message-reactions {
    position: absolute;
    bottom: -10px;
    left: 4px;
    display: flex;
    gap: 2px;
    z-index: 2;
}
.reaction-bubble {
    display: inline-flex;
    align-items: center;
    gap: 1px;
    background: #fff;
    border-radius: 20px;
    padding: 2px 5px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    cursor: default;
    transition: transform 0.15s ease;
    font-size: 0;
}
.reaction-bubble:hover {
    transform: scale(1.2);
}
.reaction-emoji {
    font-size: 16px;
    line-height: 1;
}
.reaction-count {
    font-size: 10px;
    color: #666;
    font-weight: 600;
    margin-left: 1px;
}
/* Add bottom margin to bubbles with reactions to avoid overlap */
.message-bubble:has(.message-reactions) {
    margin-bottom: 14px;
}
/* Sent message reactions — float right */
.message-row.sent .message-reactions {
    left: auto;
    right: 4px;
}

/* Media unavailable placeholder */
.media-unavailable-wrapper {
    display: flex;
    align-items: center;
}
.media-unavailable {
    display: flex;
    align-items: center;
    gap: 6px;
    background: rgba(0,0,0,0.1);
    border-radius: 8px;
    padding: 8px 14px;
    color: #888;
    font-size: 0.8rem;
}
.media-unavailable i {
    font-size: 1.2rem;
}


/* ============================================================
   Blank Bubble Fix: Indicators for special message types
   ============================================================ */
.msg-type-indicator {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #aaa;
    font-style: italic;
    padding: 2px 0;
}
.msg-type-indicator i {
    font-size: 14px;
    flex-shrink: 0;
}
.msg-button-reply { color: #25a244; }
.msg-button-reply i { color: #25a244; }
.msg-unsupported { color: #999; }
.msg-unknown { color: #bbb; }


/* === Mobile responsive: main chat area === */
@media (max-width: 992px) {
    .main-chat {
        /* Removed: height: 100dvh — caused input to go off-screen */
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
    }

    .chat-messages {
        flex: 1;
        min-height: 0;  /* Critical: allow shrinking so input stays visible */
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Touch target: toggle + control buttons >= 44px */
    .btn-control {
        min-width: 44px;
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .chat-header-main {
        padding: 8px 12px;
    }

    .status-select {
        font-size: 12px;
        padding: 4px 6px;
    }
}


/* ── Origin chip (asal pesan outbound) ─────────────────── */
.msg-origin-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
    flex-wrap: wrap;
}
.msg-origin {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 500;
    padding: 2px 8px;
    border-radius: 20px;
    white-space: nowrap;
}
.origin-notification { background: #E0F2F1; color: #0F6E56; }
.origin-broadcast    { background: #FEF3C7; color: #B45309; }
.origin-bot          { background: #F1ECFE; color: #5B3FB0; }
.origin-agent        { background: #EAF3FC; color: #1B5FA6; }
.origin-system       { background: #F1F5F9; color: #64748B; }
.msg-origin i        { font-size: 11px; }
.msg-origin-date     { font-size: 10px; color: #94A3B8; }

/* ── Bubble max-width (desktop tidak full-width) ──────── */
.message.sent .message-bubble  { max-width: 440px; }
@media (max-width: 768px) {
    .message.sent .message-bubble { max-width: 85%; }
}

/* ── Template buttons ────────────────────────────────── */
.msg-tbtns {
    border-top: 1px solid rgba(255, 255, 255, .15);
    margin-top: 8px;
}
.msg-tbtn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 9px;
    font-size: 12px;
    font-weight: 500;
    color: #9FE3C8;
    text-decoration: none;
    cursor: pointer;
    transition: opacity .15s;
}
.msg-tbtn:hover { opacity: .8; }
.msg-tbtn + .msg-tbtn { border-top: 1px solid rgba(255, 255, 255, .1); }
.msg-tbtn i { font-size: 14px; }


/* ── Echo chip (dibalas dari luar CRM) ─────────────────── */
.origin-echo_wa,
.origin-echo_ig,
.origin-echo_fb,
.origin-echo_waba {
    background: #EAF3FC;
    color: #1B5FA6;
    font-style: italic;
}


/* ── Lead attribution banner ─────────────────────────── */
/* ─── Ad context card ──────────────────────────────────────────── */
.ad-context-card {
    margin: 8px 16px;
    border: 1px solid #FDE68A;
    border-radius: 10px;
    background: #FFFBEB;
    overflow: hidden;
    font-size: 12px;
}
.ad-context-head {
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 600;
    color: #92400E;
    background: #FEF3C7;
    display: flex;
    align-items: center;
    gap: 5px;
}
.ad-context-thumb {
    width: 100%;
    max-height: 120px;
    object-fit: cover;
    display: block;
}
.ad-context-body { padding: 8px 12px; }
.ad-context-title { font-weight: 600; color: #1a1a1a; margin-bottom: 3px; }
.ad-context-text  { color: #555; margin-bottom: 5px; font-size: 11.5px; line-height: 1.4; }
.ad-context-link  {
    display: inline-flex; align-items: center; gap: 4px;
    color: #D97706; font-size: 11px; font-weight: 600; text-decoration: none;
}
.ad-context-link:hover { text-decoration: underline; }

.lead-banner {
    display:flex; align-items:center; gap:6px; padding:5px 12px;
    font-size:11px; font-weight:600; cursor:pointer;
    border-bottom:1px solid rgba(0,0,0,.06);
    transition:opacity .15s;
}
.lead-banner:hover { opacity:.8; }
.lead-banner-ad    { background:#FFFBEB; color:#92400E; }
.lead-banner-story { background:#FDF2F8; color:#9D174D; }
.lead-banner-post  { background:#EFF6FF; color:#1E40AF; }
.lead-banner-link  { background:#F0FDF4; color:#166534; }
.lead-banner i     { font-size:13px; flex-shrink:0; }
.lead-banner-headline { font-weight:400; opacity:.85; }
.lead-banner-thumb { width:24px; height:24px; border-radius:4px; object-fit:cover;
                     flex-shrink:0; margin-left:auto; }


/* ── WABA 24h session banner ─────────────────────────── */
/* removed: session-banner-24 CSS */
.session-active  { background: #ECFDF5; color: #065F46; }
.session-expired { background: #FFFBEB; color: #92400E; }
.session-banner-24 i { font-size: 14px; }
/* removed: session-banner-24 CSS */
.btn-open-template-banner:hover { background: #155a4a; }


/* ── Mobile (@media ≤ 768px) ─────────────────────────── */
@media (max-width: 768px) {
    .chat-wrapper {
        /* FIXED: jangan set height:100dvh di sini.
         * Parent .chat-container sudah calc(100dvh - 3.75rem) untuk navbar.
         * height:100dvh melebihi parent → overflow:hidden clip input ke bawah layar.
         * Biarkan flex:1 (dari CSS utama) yang mengatur tinggi. */
        flex: 1;
        min-height: 0;
    }
    .chat-input-area {
        padding: 8px;
        /* Pastikan input selalu kelihatan — tidak ter-shrink */
        flex-shrink: 0;
    }
    .chat-input {
        font-size: 16px; /* prevent iOS zoom on focus */
    }
    /* Hide desktop controls on mobile */
    .controls .btn-control:not(.btn-back-mobile):not(.mobile-burger-btn):not(.mobile-info-btn) {
        display: none !important;
    }
    .controls .status-select { display: none !important; }
    /* Session banner compact */
    .session-banner-24 { font-size: 10px; padding: 4px 10px; }
    /* Lead banner compact */
    .lead-banner { font-size: 10px; padding: 4px 10px; }
}

/* ── Mobile burger menu ──────────────────────────────── */
.mobile-burger-menu {
    position: absolute; right: 0; top: 110%; z-index: 999;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 10px; min-width: 180px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    overflow: hidden;
}
.burger-item {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px; font-size: 13px; font-weight: 500;
    color: #374151; cursor: pointer;
    transition: background .15s;
}
.burger-item:hover { background: #f9fafb; }
.burger-item + .burger-item { border-top: 1px solid #f3f4f6; }
.burger-item i { font-size: 16px; color: #6b7280; }
.burger-status-select {
    border: none; outline: none; background: transparent;
    font-size: 13px; font-weight: 500; color: #374151;
    cursor: pointer; flex: 1;
}
.btn-back-mobile { color: #1E6F5C !important; }


/* ═══════════════════════════════════════════════════════════════════════════
   REDESIGN: Panel Informasi CRM — brand palette (#2E8DE1 / #5B3FB0 / #25D366)
   Semua override di sini menang karena berada setelah definisi lama.
   ═════════════════════════════════════════════════════════════════════════ */

/* ── B1. User Profile Header ──────────────────────────────────────────── */
.user-profile {
    padding: 20px 16px 16px;
    text-align: center;
    border-bottom: 1px solid #E4EAF2;
    background: #fff;
}

.user-avatar-large {
    width: 64px !important;
    height: 64px !important;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto 10px;
    position: relative;
    border: 2px solid #E4EAF2;
}

.user-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.whatsapp-badge {
    position: absolute;
    bottom: -1px;
    right: -1px;
    width: 20px;
    height: 20px;
    background: #25D366;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 11px;
    border: 2px solid #fff;
}

.user-name {
    font-size: 15px !important;
    font-weight: 500 !important;
    color: #1e293b !important;
    line-height: 1.4;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-wrap: wrap;
    gap: 5px;
    margin-bottom: 4px;
}

.user-phone {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px;
    font-size: 13px !important;
    color: #64748b !important;
    margin-top: 2px;
}

.user-phone .copy-icon {
    font-size: 14px;
    color: #94A3B8;
    cursor: pointer;
    transition: color 0.15s;
}
.user-phone .copy-icon:hover { color: #2E8DE1; }

/* BSUID / Username styles */
.badge-username {
    font-size: 10px !important;
    background: #F1ECFE !important;
    color: #5B3FB0 !important;
    padding: 2px 7px !important;
    border-radius: 10px !important;
    font-weight: 500 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px;
    vertical-align: middle;
    border: none;
}

.user-username {
    font-size: 12px;
    color: #5B3FB0;
    display: flex;
    align-items: center;
    gap: 4px;
    justify-content: center;
    margin-top: 3px;
}

.user-phone.muted {
    color: #94A3B8 !important;
    font-size: 12px !important;
    display: flex;
    align-items: center;
    gap: 4px;
    justify-content: center;
}

.user-bsuid {
    font-size: 11px !important;
    color: #94A3B8;
    font-family: monospace;
    background: #F5F8FC;
    padding: 3px 8px;
    border-radius: 6px;
    margin-top: 5px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    user-select: none;
    transition: background 0.15s, color 0.15s;
}
.user-bsuid:hover { background: #EBF3FD; color: #5B3FB0; }

/* ── B2. Window 24H Card ──────────────────────────────────────────────── */
.window-status-card {
    margin: 10px 14px 2px;
    border-radius: 8px;
    overflow: hidden;
}

.window-status {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 9px 12px !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    border-radius: 8px;
}

.window-status.inside {
    background: #E1F5EE !important;
    color: #0F6E56 !important;
}

.window-status.outside {
    background: #FAECE7 !important;
    color: #993C1D !important;
}

.window-status i { font-size: 15px; }

.window-badge {
    font-size: 10px !important;
    padding: 2px 7px !important;
    border-radius: 6px !important;
    font-weight: 600 !important;
    margin-left: auto;
}

.badge-inside {
    background: #0F6E56 !important;
    color: #fff !important;
}

.badge-outside {
    background: #993C1D !important;
    color: #fff !important;
}

.window-timer {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    padding: 4px 12px 6px;
    color: #0F6E56;
}

.window-timer.expired { color: #993C1D; }

/* ── B3. Info Section Rows ────────────────────────────────────────────── */
.info-section {
    padding-bottom: 8px;
}

.info-item {
    padding: 11px 16px !important;
    border-top: 0.5px solid #E4EAF2;
}

.info-label {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #94A3B8 !important;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 6px;
}

.info-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}

.btn-add {
    font-size: 12px !important;
    color: #2E8DE1 !important;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.15s;
    cursor: pointer;
}
.btn-add:hover { color: #185FA5 !important; }

.info-select {
    width: 100%;
    font-size: 13px;
    border: 1px solid #E4EAF2;
    border-radius: 6px;
    padding: 6px 10px;
    background: #fff;
    color: #374151;
    outline: none;
    transition: border-color 0.15s;
}
.info-select:focus { border-color: #2E8DE1; box-shadow: none; }

.info-input {
    width: 100%;
    font-size: 13px;
    border: 1px solid #E4EAF2;
    border-radius: 6px;
    padding: 6px 10px;
    background: #fff;
    color: #374151;
    outline: none;
    transition: border-color 0.15s;
}
.info-input:focus { border-color: #2E8DE1; box-shadow: none; }

/* Empty states — italic muted, consistent */
.empty-state-inline {
    font-size: 12px !important;
    color: #94A3B8 !important;
    font-style: italic !important;
    display: inline-block;
}

.empty-state {
    font-size: 12px;
    color: #94A3B8;
    font-style: italic;
}

/* ── B4. Pills — Label / Kategori / Pipeline ──────────────────────────── */
.tag-container {
    display: flex !important;
    flex-wrap: wrap;
    gap: 5px;
    min-height: 22px;
    align-items: center;
}

.tag-item {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 12px !important;
    padding: 3px 9px !important;
    border-radius: 10px !important;
    background: #EAF3FC !important;
    color: #185FA5 !important;
    font-weight: 500;
}

.category-tag {
    background: #EAF3FC !important;
    color: #185FA5 !important;
}

.category-tag .remove-tag { color: #185FA5; }

.remove-tag {
    cursor: pointer;
    font-size: 12px;
    opacity: 0.6;
    transition: opacity 0.15s;
}
.remove-tag:hover { opacity: 1; }

/* Pipeline & Stage pills */
.pipeline-badge {
    background: #F1ECFE;
    color: #5B3FB0;
    border-radius: 10px;
    padding: 3px 9px;
    font-size: 12px;
    display: inline-flex;
    gap: 4px;
    align-items: center;
    font-weight: 500;
}

.stage-badge {
    background: #F5F8FC;
    color: #64748B;
    border-radius: 10px;
    padding: 3px 9px;
    font-size: 12px;
    display: inline-flex;
    gap: 4px;
    align-items: center;
}

/* Collaborator row */
.collaborator-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    padding: 3px 0;
    color: #374151;
}

.remove-btn {
    cursor: pointer;
    color: #94A3B8;
    font-size: 14px;
    transition: color 0.15s;
}
.remove-btn:hover { color: #DC2626; }

/* ── B5. Device/Channel Card ──────────────────────────────────────────── */
.device-info-container {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 10px 12px;
    background: #F9FAFB;
    border-radius: 8px;
    border: 0.5px solid #E4EAF2;
}

.device-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    width: fit-content;
}

/* Override dark badge-waba with brand green */
.badge-waba {
    background: #E1F5EE !important;
    color: #0F6E56 !important;
}

.badge-whatsapp {
    background: #E1F5EE !important;
    color: #0F6E56 !important;
}

.badge-instagram { background: #fce4ec; color: #880e4f; }
.badge-telegram  { background: #e3f2fd; color: #0277bd; }
.badge-livechat  { background: #ede7f6; color: #4527a0; }

.device-name {
    font-size: 12px !important;
    font-weight: 500;
    color: #64748B !important;
}

/* ── B6. Detail Data rows + Block button ──────────────────────────────── */
.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    font-size: 12px;
    gap: 8px;
    padding: 3px 0;
}

.detail-label {
    color: #94A3B8;
    flex-shrink: 0;
    font-size: 12px;
}

.detail-value {
    color: #374151;
    font-weight: 500;
    text-align: right;
    font-size: 12px;
    word-break: break-all;
}

/* Block / Unblock full-width buttons */
.btn.btn-danger.w-100 {
    background: #DC2626 !important;
    border-color: #DC2626 !important;
    border-radius: 8px !important;
    padding: 10px !important;
    font-weight: 500 !important;
    font-size: 13px !important;
    transition: background 0.15s !important;
}
.btn.btn-danger.w-100:hover {
    background: #B91C1C !important;
    border-color: #B91C1C !important;
}
.btn.btn-info.w-100 {
    border-radius: 8px !important;
    padding: 10px !important;
    font-weight: 500 !important;
    font-size: 13px !important;
}

/* Sidebar header */
.sidebar-right-header {
    padding: 14px 16px 10px;
    font-size: 15px;
    font-weight: 600;
    color: #1e293b;
    border-bottom: 1px solid #E4EAF2;
    background: #fff;
}

/* ──────────────────────────────────────────────────────────────────────── */

/* ═══════════════════════════════════════════════════════════════════════
   REDESIGN: Chat Header — 2-baris, chip channel, status sesi per channel
   ═════════════════════════════════════════════════════════════════════ */

.chat-header-main {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 14px;
    border-bottom: 0.5px solid #E4EAF2;
    background: #fff;
    min-height: 56px;
}

/* Back arrow (mobile only, leftmost) */
.ch-back {
    width: 30px;
    height: 30px;
    border: 0.5px solid #cbd5e1;
    border-radius: 8px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #64748B;
    flex-shrink: 0;
    transition: background 0.15s;
}
.ch-back:hover { background: #F1F5F9; }

/* Avatar circle */
.ch-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    position: relative;
    flex: 0 0 auto;
    overflow: visible;
    background: #EAF3FC;
}

.ch-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    display: block;
}

/* Channel dot (bottom-right of avatar) */
.ch-chdot {
    position: absolute;
    bottom: -1px;
    right: -1px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ch-chdot i {
    font-size: 8px;
    color: #fff;
}

/* Identity block — expands to fill space */
.ch-identity {
    min-width: 0;
    flex: 1;
    overflow: hidden;
}

/* Baris 1 — Nama */
.ch-name {
    font-size: 14px !important;
    font-weight: 500 !important;
    color: #1E2A4A !important;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: flex;
    align-items: center;
    gap: 6px;
}

.ch-badge-username {
    font-size: 10px;
    background: #F1ECFE;
    color: #5B3FB0;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: 500;
    flex: 0 0 auto;
    white-space: nowrap;
}

/* Baris 2 — chip + status */
.ch-sub {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 2px;
    flex-wrap: wrap;
    line-height: 1;
}

/* Channel chip (tint lembut, Gaya A) */
.ch-chip {
    font-size: 10px;
    padding: 2px 7px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-weight: 500;
    flex: 0 0 auto;
    white-space: nowrap;
}

/* Template chip (merah) */
.ch-tpl {
    background: #FEECEC !important;
    color: #993C1D !important;
    cursor: pointer;
    transition: opacity 0.15s;
}
.ch-tpl:hover { opacity: 0.8; }

/* Status text kecil */
.ch-st {
    font-size: 11px;
    color: #94A3B8;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    white-space: nowrap;
}
.ch-st i { font-size: 12px; }

/* ── Override: span.name lama (hidden, diganti ch-identity) ── */
.chat-header-main > span.name { display: none !important; }

/* ─────────────────────────────────────────────────────────── */

/* ═══════════════════════════════════════════════════════════════════
   FIX: Mobile — panel Informasi bisa dibuka + UX info button
   ═════════════════════════════════════════════════════════════════ */

/* Mobile Info button — biru brand, langsung visible di header */
.mobile-info-btn {
    color: #2E8DE1 !important;
    background: #EAF3FC !important;
    border: 0.5px solid #BFDBFE !important;
}
.mobile-info-btn:hover {
    background: #DBEAFE !important;
}
.mobile-info-btn i {
    font-size: 18px;
}

/* Mobile ⋮ burger — pastikan selalu muncul di mobile */
.mobile-burger-btn {
    display: flex !important;
}

/* Sidebar-right-header — flex row, teks + tombol tutup */
.sidebar-right-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}

/* Tombol tutup (✕) di panel Informasi mobile */
.sidebar-close {
    border: none;
    background: transparent;
    font-size: 20px;
    color: #64748B;
    cursor: pointer;
    padding: 2px 4px;
    line-height: 1;
    border-radius: 6px;
    transition: background 0.15s, color 0.15s;
}
.sidebar-close:hover {
    background: #F1F5F9;
    color: #1e293b;
}

/* Drawer panel full-screen di mobile */
@media (max-width: 576px) {
    .sidebar-right:not(.hidden) {
        right: 0 !important;
        box-shadow: -4px 0 16px rgba(0,0,0,0.12);
    }
}

/* ─────────────────────────────────────────────────────────── */

/* ═══════════════════════════════════════════════════════════════
   FIX: Panel Informasi mobile — ✕ tidak tertimpa navbar
   ═════════════════════════════════════════════════════════════ */

/* Header panel sticky — tetap kelihatan saat scroll */
@media (max-width: 992px) {
    .sidebar-right-header {
        position: sticky !important;
        top: 0;
        background: #fff !important;
        z-index: 2;
        border-bottom: 0.5px solid #E4EAF2;
        padding: 12px 16px !important;
    }
}

/* ─────────────────────────────────────────────────────────── */

/* ═══════════════════════════════════════════════════════════════
   Panel Informasi mobile — drawer 85vw + backdrop tap-to-close
   ═════════════════════════════════════════════════════════════ */

/* Backdrop gelap — cuma mobile (d-lg-none sudah handle display) */
.sidebar-right-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    z-index: 1000;       /* di bawah panel (z-index:1001) */
    cursor: pointer;
}

/* ─────────────────────────────────────────────────────────── */

/* ═══════════════════════════════════════════════════════════════
   Contact Card — pesan kontak yang dibagi customer
   ═════════════════════════════════════════════════════════════ */

.contact-msg {
    margin-bottom: 4px;
    min-width: 220px;
    max-width: 100%;
}

.contact-card-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    padding: 9px 11px;
    margin-bottom: 5px;
    border: 1px solid rgba(255,255,255,0.2);
}

/* Incoming message — adjust background */
.message-wrapper.user .contact-card-item {
    background: rgba(0,0,0,0.05);
    border-color: rgba(0,0,0,0.08);
}

.cc-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0F6E56;
    font-size: 18px;
    flex: 0 0 auto;
}

.message-wrapper.user .cc-avatar {
    color: #2E8DE1;
}

.cc-info {
    flex: 1;
    min-width: 0;
}

.cc-name {
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cc-phone {
    font-size: 11px;
    opacity: 0.8;
    margin-top: 1px;
}

.cc-actions {
    display: flex;
    gap: 5px;
    flex: 0 0 auto;
}

.cc-btn {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    cursor: pointer;
    text-decoration: none;
    font-weight: 500;
    white-space: nowrap;
    transition: opacity 0.15s;
}
.cc-btn:hover { opacity: 0.8; }

.cc-chat {
    background: #fff;
    color: #0F6E56;
    border: none;
}

.cc-save {
    background: transparent;
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.5);
}

.message-wrapper.user .cc-save {
    color: #64748B;
    border-color: #CBD5E1;
}

/* ─────────────────────────────────────────────────────────── */

/* ═══════════════════════════════════════════════════════════════
   Location Card — pesan lokasi yang dibagi customer
   ═════════════════════════════════════════════════════════════ */

.location-msg {
    margin-bottom: 4px;
    min-width: 220px;
    max-width: 100%;
}

.loc-card {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    padding: 9px 11px;
    border: 1px solid rgba(255,255,255,0.2);
}

.message-wrapper.user .loc-card {
    background: rgba(0,0,0,0.05);
    border-color: rgba(0,0,0,0.08);
}

.loc-pin {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #D85A30;
    font-size: 20px;
    flex: 0 0 auto;
}

.loc-info {
    flex: 1;
    min-width: 0;
}

.loc-name {
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.loc-addr, .loc-coords {
    font-size: 11px;
    opacity: 0.8;
    margin-top: 1px;
}

.loc-btn {
    margin-left: auto;
    font-size: 11px;
    padding: 5px 9px;
    border-radius: 6px;
    background: #fff;
    color: #185FA5;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    font-weight: 500;
    flex: 0 0 auto;
    transition: opacity 0.15s;
}
.loc-btn:hover { opacity: 0.8; }

.message-wrapper.user .loc-btn {
    background: #EAF3FC;
    color: #185FA5;
}

/* ─────────────────────────────────────────────────────────── */
</style>