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
                    <div class="avatar avatar-lg online flex-shrink-0" data-bs-toggle="offcanvas"
                        data-bs-target="#contact-profile" id="toClickDetail">
                        <img :src="detail.photo" class="rounded-circle" alt="image" />
                    </div>
                    <div class="ms-2 overflow-hidden" data-bs-toggle="offcanvas" data-bs-target="#contact-profile"
                        id="toClickDetail">
                        <h6>{{ detail.name }}</h6>
                        <span class="last-seen text-muted">
                            {{ detail.phone }}</span>
                    </div>
                </div>
                <div class="chat-options">
                    <ul>
                        <li class="d-none d-md-block">
                            <button @click="closeChat()" type="button" class="btn btn-danger me-2">
                                <i class="ti ti-x"></i> Tutup Chat
                            </button>
                        </li>
                        <li v-if="detail.takeover" class="d-none d-md-block">
                            <button @click="resolved()" type="button" class="btn btn-info me-2">
                                <i class="ti ti-check-circle"></i> Resolved Chat
                            </button>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="btn chat-search-btn" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="Search">
                                <i class="ti ti-search"></i>
                            </a>
                        </li>
                        <li title="Detail Information" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <a href="javascript:void(0)" class="btn" data-bs-toggle="offcanvas"
                                data-bs-target="#contact-profile" id="toClickDetail">
                                <i class="ti ti-info-circle"></i>
                            </a>
                        </li>

                        <li>
                            <a class="btn no-bg" href="#" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-3">
                                <li>
                                    <router-link :to="{
                                        name: 'blank_chat',
                                    }" class="dropdown-item"><i class="ti ti-x me-2"></i>Tutup
                                        Chat</router-link>
                                </li>
                                <li v-if="detail.takeover">
                                    <a href="javascript:void(0);" class="dropdown-item" @click="resolved()"><i
                                            class="ti ti-check me-2"></i>Resolved Chat</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="chat-search search-wrap contact-search">
                    <div class="input-group">
                        <input type="text" class="form-control" v-model="message.search" placeholder="Search Message" />
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="chat-body chat-page-group slimscroll">
                <div v-if="message.loader" class="d-flex justify-content-center">
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
                <div class="messages chat-area" @scroll="handleChatScroll($event)">
                    <!-- List Message -->
                    <div class="chats" v-for="(list, index) in message.list.filter((item) =>
                        (item.message?.toLowerCase() || '').includes(
                            message.search.toLowerCase()
                        )
                    )" :id="list.id" :key="index" :class="list.sent_by == 'system' ? 'chats-right' : ''"
                        @mouseover="hoveredMessageId = list.id" @mouseleave="hoveredMessageId = null">
                        <div class="chat-content">
                            <div class="chat-profile-name d-flex justify-content-end" :class="list.sent_by == 'system' ? 'me-4' : 'ms-4'
                                ">
                                <h6>
                                    <span class="chat-time">{{ list.datetime.date }} {{ list.datetime.time
                                        }}</span><span class="msg-read success"><i class="ti ti-checks"></i></span>
                                </h6>
                            </div>
                            <div class="chat-info position-relative">
                                <div class="message-content" :class="list.media_type == 'audio' &&
                                    !list.sender
                                    ? 'bg-transparent p-0'
                                    : ''
                                    ">
                                    <!-- Reply message preview -->
                                    <div class="message-quoted-reply" v-if="list.reply_to" @click="
                                        scrollToOriginalMessage(
                                            list.reply_id
                                        )
                                        ">
                                        <div class="reply-attribution">
                                            <strong>{{ list.reply_to }}</strong>
                                        </div>
                                        <div class="reply-quoted-text">
                                            <img v-if="
                                                list.reply_type === 'image'
                                            " :src="list.reply_media_url" style="max-height: 100px" />
                                            <p v-if="list.reply_text">
                                                {{
                                                    list.reply_text.length > 60
                                                        ? list.reply_text.substring(
                                                            0,
                                                            60
                                                        ) + "..."
                                                        : list.reply_text
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Image not ready -->
                                    <div class="chat-img" v-if="list.media_type == 'image'">
                                        <div class="img-wrap">
                                            <img :src="list.media_url" alt="img" />
                                            <div class="img-overlay">
                                                <a class="gallery-img" data-fancybox="gallery-img"
                                                    :href="list.media_url" :title="list.message"><i
                                                        class="ti ti-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Image not ready -->

                                    <!-- Video -->

                                    <div class="message-video" v-if="
                                        list.media_type == 'video' &&
                                        list.media_url
                                    ">
                                        <video controls="">
                                            <source :src="list.media_url" type="video/mp4" />
                                        </video>
                                    </div>
                                    <!-- End Video -->

                                    <!-- Document and File Attachments -->
                                    <div class="file-attachment-preview" v-if="
                                        [
                                            'pdf',
                                            'docx',
                                            'doc',
                                            'pptx',
                                            'ppt',
                                            'xlsx',
                                            'xls',
                                            'txt',
                                            'json',
                                            'html',
                                            'css',
                                            'js',
                                            'php',
                                            'file',
                                        ].includes(list.media_type) &&
                                        list.media_url
                                    ">
                                        <div class="file-attachment-card">
                                            <div class="file-icon" :class="getFileIconClass(
                                                list.media_type
                                            )
                                                ">
                                                <i :class="getFileIcon(
                                                    list.media_type
                                                )
                                                    "></i>
                                            </div>
                                            <div class="file-info">
                                                <div class="file-name">
                                                    {{
                                                        getFileName(
                                                            list.media_url,
                                                            list.media_type
                                                        )
                                                    }}
                                                </div>
                                                <div class="file-meta">
                                                    <div class="file-type">
                                                        {{
                                                            getFileTypeLabel(
                                                                list.media_type
                                                            )
                                                        }}
                                                    </div>
                                                    <div class="file-size" v-if="list.media_size">
                                                        {{
                                                            formatFileSize(
                                                                list.media_size
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                            <a target="_blank" :href="list.media_url" class="file-download-btn" :title="'Unduh ' +
                                                getFileTypeLabel(
                                                    list.media_type
                                                )
                                                ">
                                                <i class="ti ti-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- End Document and File Attachments -->

                                    <!-- Audio -->
                                    <div class="chat-img" v-if="
                                        list.media_type == 'audio' &&
                                        !list.media_url
                                    ">
                                        <div class="img-wrap">
                                            <img :src="attribute.audio" alt="img" :style="!list.media_url
                                                ? 'height: 100%'
                                                : ''
                                                " />
                                            <div class="img-overlay">
                                                <a class="ti ti-download fs-30" href="javascript:void(0);"
                                                    v-if="!list.media_url" @click="
                                                        downloadMedia(
                                                            list.details,
                                                            list.id,
                                                            list.message.mime
                                                        )
                                                        "><i></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="message-audio" v-if="
                                        list.media_type == 'audio' &&
                                        list.media_url
                                    ">
                                        <audio controls="">
                                            <source :src="list.media_url" type="audio/mpeg" />
                                        </audio>
                                    </div>
                                    <!-- End Audio -->

                                    <div v-html="formattedText(list.message)"></div>
                                </div>
                                <div class="chat-actions" v-if="hoveredMessageId === list.id">
                                    <a class="#" href="#" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end p-3">
                                        <li v-if="detail.from == 'whatsapp'">
                                            <a class="dropdown-item" href="javascript:void(0);"
                                                @click="replyToMessage(list)"><i
                                                    class="ti ti-arrow-back-up me-2"></i>Balas Pesan</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);"
                                                @click="deleteMessage(list.id, index)"><i
                                                    class="ti ti-trash me-2"></i>Hapus Pesan</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Message -->
                </div>
            </div>
            <div class="chat-footer">
                <form class="footer-form">

                    <div class="reply-message-preview" v-if="replyingTo">
                        <div class="reply-message-content">
                            <div class="reply-message-header">
                                <strong>Membalas
                                    {{
                                        replyingTo.sent_by === "system"
                                            ? "Anda"
                                            : replyingTo.sent_by_name
                                    }}</strong>
                                <button type="button" class="reply-message-close" @click="cancelReply">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                            <div class="reply-message-text">
                                <span v-if="replyingTo.media_type == 'image'">
                                    <img :src="replyingTo.media_url" alt="img" style="max-height: 150px" />
                                </span>
                                <span v-if="replyingTo.media_type == 'video'">
                                    <video controls="" style="max-height: 150px">
                                        <source :src="replyingTo.media_url" type="video/mp4" />
                                    </video>
                                </span>
                                <span v-if="replyingTo.media_type == 'audio'">
                                    <audio controls="">
                                        <source :src="replyingTo.media_url" type="audio/mpeg" />
                                    </audio>
                                </span>
                                <!-- Document and File Attachments -->
                                <div class="file-attachment-preview" v-if="
                                    [
                                        'pdf',
                                        'docx',
                                        'doc',
                                        'pptx',
                                        'ppt',
                                        'xlsx',
                                        'xls',
                                        'txt',
                                        'json',
                                        'html',
                                        'css',
                                        'js',
                                        'php',
                                        'file',
                                    ].includes(replyingTo.media_type) &&
                                    replyingTo.media_url
                                ">
                                    <div class="file-attachment-card">
                                        <div class="file-icon" :class="getFileIconClass(
                                            replyingTo.media_type
                                        )
                                            ">
                                            <i :class="getFileIcon(
                                                replyingTo.media_type
                                            )
                                                "></i>
                                        </div>
                                        <div class="file-info">
                                            <div class="file-name">
                                                {{
                                                    getFileName(
                                                        replyingTo.media_url,
                                                        replyingTo.media_type
                                                    )
                                                }}
                                            </div>
                                            <div class="file-meta">
                                                <div class="file-type">
                                                    {{
                                                        getFileTypeLabel(
                                                            replyingTo.media_type
                                                        )
                                                    }}
                                                </div>
                                                <div class="file-size" v-if="replyingTo.media_size">
                                                    {{
                                                        formatFileSize(
                                                            replyingTo.media_size
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Document and File Attachments -->
                                <span v-if="replyingTo.media_type == 'text'">
                                    {{
                                        replyingTo.message &&
                                            replyingTo.message.length > 40
                                            ? replyingTo.message.substring(
                                                0,
                                                40
                                            ) + "..."
                                            : replyingTo.message
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="quick-reply-preview-media" v-if="
                        lastQuickReplyDetail &&
                        ['pdf', 'doc', 'xls', 'ppt', 'image'].includes(
                            lastQuickReplyDetail.type
                        )
                    ">
                        <div class="quick-reply-content">
                            <div class="quick-reply-text">
                                <span v-if="lastQuickReplyDetail.type == 'image'">
                                    <img :src="lastQuickReplyDetail.media_url
                                        " alt="img" style="max-height: 150px" />
                                </span>
                                <div v-if="
                                    ['pdf', 'doc', 'xls', 'ppt'].includes(
                                        lastQuickReplyDetail.type
                                    )
                                " :class="[
                                    'd-inline-flex',
                                    'align-items-center',
                                    'rounded',
                                    'px-2',
                                    'py-1',
                                    {
                                        'bg-danger text-white':
                                            lastQuickReplyDetail.type ===
                                            'pdf',
                                        'bg-primary text-white':
                                            lastQuickReplyDetail.type ===
                                            'doc',
                                        'bg-success text-white':
                                            lastQuickReplyDetail.type ===
                                            'xls',
                                        'bg-warning text-dark':
                                            lastQuickReplyDetail.type ===
                                            'ppt',
                                    },
                                ]" style="margin-top: 5px">
                                    <div v-if="
                                        lastQuickReplyDetail.type === 'pdf'
                                    ">
                                        <i class="bx bxs-file-pdf me-1"></i>
                                        {{ lastQuickReplyDetail.file_name }}
                                    </div>
                                    <!-- tambahkan untuk doc, xls, ppt -->
                                    <div v-if="
                                        lastQuickReplyDetail.type === 'doc'
                                    ">
                                        <i class="bx bxs-file-doc me-1"></i>
                                        {{ lastQuickReplyDetail.file_name }}
                                    </div>
                                    <div v-if="
                                        lastQuickReplyDetail.type === 'xls'
                                    ">
                                        <i class="bx bxs-file-xls me-1"></i>
                                        {{ lastQuickReplyDetail.file_name }}
                                    </div>
                                    <div v-if="
                                        lastQuickReplyDetail.type === 'ppt'
                                    ">
                                        <i class="bx bxs-file-ppt me-1"></i>
                                        {{ lastQuickReplyDetail.file_name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chat-footer-wrap" v-if="detail.takeover">
                        <div class="form-wrap">
                            <textarea class="form-control" placeholder="Kirim pesan. Tekan ''/'' untuk balas cepat"
                                ref="messageInput" rows="1" :disabled="send.loader" @input="onMessageInput"
                                @keydown.enter="handleEnter" @keydown.shift.enter.prevent="insertLineBreak"
                                @keydown.arrow-down.prevent="
                                    navigateQuickReplies('down')
                                    " @keydown.arrow-up.prevent="
                                        navigateQuickReplies('up')
                                        " @keydown.esc.prevent="hideQuickReplies()" @paste="handlePaste"
                                v-model="send.text"></textarea>

                            <!-- Popup Quick Replies -->
                            <div class="quick-replies-popup" v-if="quickReplyPopup.show">
                                <div class="quick-replies-header d-flex justify-content-between align-items-center">
                                    <strong class="text-dark">Quick Replies</strong>
                                    <button class="btn btn-sm btn-outline-primary" type="button"
                                        @click="manageQuickReply">
                                        Kelola Quick Reply
                                    </button>
                                </div>
                                <div class="quick-replies-list">
                                    <div v-if="quickReplyPopup.loading" class="quick-replies-loading">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Memuat quick replies...</span>
                                    </div>
                                    <div v-else-if="quick_replies.length === 0" class="quick-replies-empty">
                                        <i class="ti ti-clipboard-text me-2"></i>
                                        Tidak ada quick replies
                                    </div>
                                    <div v-for="(item, index) in quick_replies" :key="index" class="quick-reply-item"
                                        :class="{
                                            selected:
                                                quickReplyPopup.selectedIndex ===
                                                index,
                                        }" @click="selectQuickReply(item)" @mouseover="
                                            quickReplyPopup.selectedIndex =
                                            index
                                            ">
                                        <div class="quick-reply-item-content">
                                            <div class="quick-reply-title">
                                                {{ item.name }}:
                                            </div>
                                            <div class="quick-reply-preview">
                                                {{
                                                    item.content
                                                        ? item.content.substring(
                                                            0,
                                                            100
                                                        ) +
                                                        (item.content.length >
                                                            100
                                                            ? "..."
                                                            : "")
                                                        : ""
                                                }}
                                            </div>
                                        </div>
                                        <div class="quick-reply-media" v-if="
                                            item.type === 'image' &&
                                            item.media_url
                                        ">
                                            <img :src="item.media_url" alt="img" style="
                                                    max-height: 50px;
                                                    border: 1px solid #ccc;
                                                    border-radius: 5px;
                                                    margin-top: 5px;
                                                " />
                                        </div>
                                        <!-- Tampilkan ikon dan nama file dengan background sesuai tipe file, rounded, dan padding -->
                                        <div v-if="
                                            [
                                                'pdf',
                                                'doc',
                                                'xls',
                                                'ppt',
                                            ].includes(item.type)
                                        " :class="[
                                            'd-inline-flex',
                                            'align-items-center',
                                            'rounded',
                                            'px-2',
                                            'py-1',
                                            {
                                                'bg-danger text-white':
                                                    item.type === 'pdf',
                                                'bg-primary text-white':
                                                    item.type === 'doc',
                                                'bg-success text-white':
                                                    item.type === 'xls',
                                                'bg-warning text-dark':
                                                    item.type === 'ppt',
                                            },
                                        ]" style="margin-top: 5px">
                                            <div v-if="item.type === 'pdf'">
                                                <i class="bx bxs-file-pdf me-1"></i>
                                                {{ item.file_name }}
                                            </div>
                                            <!-- tambahkan untuk doc, xls, ppt -->
                                            <div v-if="item.type === 'doc'">
                                                <i class="bx bxs-file-doc me-1"></i>
                                                {{ item.file_name }}
                                            </div>
                                            <div v-if="item.type === 'xls'">
                                                <i class="bx bxs-file-xls me-1"></i>
                                                {{ item.file_name }}
                                            </div>
                                            <div v-if="item.type === 'ppt'">
                                                <i class="bx bxs-file-ppt me-1"></i>
                                                {{ item.file_name }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="emoji-picker position-absolute" v-if="emojiShow" style="bottom: 100%; right: 16px;">
                            <Picker :data="emojiIndex" set="twitter" @select="showEmoji" />
                        </div>

                        <div class="form-item position-relative d-flex align-items-center justify-content-center me-2">
                            <a href="javascript:void(0)" @click="showEmojiData" class="action-circle position-absolute">
                                <i class="ti ti-mood-smile"></i>
                            </a>
                        </div>
                        <div class="form-item position-relative d-flex align-items-center justify-content-center">
                            <a href="#" @click="
                                setFileType(
                                    '.pdf, .doc, .docx, .xlsx, .zip, .html, .php, .css, .js, .ppt, .txt'
                                )
                                " class="action-circle position-absolute">
                                <i class="ti ti-file"></i>
                            </a>
                            <input type="file" class="open-file position-relative" name="files" ref="fileInput"
                                id="files" :accept="fileTypes" @change="handleFileChange" />
                        </div>
                        <div class="form-item">
                            <a href="#" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-3">
                                <a href="#" class="dropdown-item" @click="setFileType('image/*')">
                                    <i class="ti ti-photo-up me-2"></i> Gallery
                                </a>
                                <a href="#" class="dropdown-item" @click="setFileType('video/*')">
                                    <i class="ti ti-video me-2"></i> Video
                                </a>
                            </div>
                        </div>
                        <div class="form-btn d-flex justify-content-center">
                            <button class="btn btn-info me-2" type="button" :disabled="send.loader"
                                @click="changeTakeOver(false)">
                                <i class="ti ti-checkbox"></i>
                            </button>
                            <button class="btn btn-primary" type="button" :disabled="send.loader" @click="sendMessage">
                                <i :class="send.loader ? 'ti ti-circle' : 'ti ti-send'
                                    "></i>
                            </button>
                        </div>
                    </div>
                    <div class="chat-footer-wrap" v-else>
                        <button class="btn btn-info w-100" type="button" :disabled="send.loader"
                            @click="changeTakeOver(true)">
                            <i class="ti ti-hand-stop me-2"></i> Ambil Alih
                            Percakapan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="chat-offcanvas offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1"
        id="contact-profile" aria-labelledby="chatUserMoreLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="chatUserMoreLabel">Informasi Kontak</h4>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i
                    class="ti ti-x"></i></button>
        </div>
        <div class="offcanvas-body">
            <div class="chat-contact-info">
                <div class="profile-content">
                    <div class="profile-content">
                        <div class="contact-profile-info">
                            <div class="avatar avatar-xxl online mb-2">
                                <img :src="detail.photo" class="rounded-circle" :alt="detail.name" />
                            </div>
                            <h6>{{ detail.name }}</h6>
                            <p class="mb-0 text-dark">
                                {{ detail.phone }}
                                <i @click="copyToClipboard(detail.phone)" class="ti ti-clipboard"></i>
                            </p>
                            <p class="text-dark">
                                Sumber : {{ detail.device }}
                            </p>
                        </div>

                        <div class="content-wrapper">
                            <h5 class="sub-title">Labels </h5>
                            <div class="card">
                                <div class="card-body">
                                    <ul class="list-group profile-item">
                                        <li class="list-group-item">
                                            <Multiselect v-model="info_label.form" :options="info_label.labels"
                                                @select="selectLabel()" :multiple="false" :close-on-select="true"
                                                :clear-on-select="true" :preserve-search="true" :searchable="true"
                                                :internal-search="true" :options-limit="50" :placeholder="'Pilih Label'"
                                                open-direction="bottom" label="name" id="id" track-by="name">
                                            </Multiselect>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-start flex-wrap">
                                            <span class="badge badge-center bg-info d-flex align-items-center me-1 mt-1"
                                                v-for="(label, l) in detail.label" :key="label.id">
                                                {{ label.name }}
                                                <i class="ti ti-x ms-2 cursor-pointer"
                                                    @click="removeLabel(label.id)"></i>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="content-wrapper">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="list-group profile-item">
                                        <li class="list-group-item">
                                            <div class="info">
                                                <h6>Handled By</h6>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <Multiselect v-model="detail.handled" :options="users"
                                                @select="selectUser()" :multiple="false" :close-on-select="true"
                                                :clear-on-select="true" :preserve-search="true" :searchable="true"
                                                :internal-search="true" :options-limit="50"
                                                :placeholder="'Pilih Pengguna'" open-direction="bottom" label="name"
                                                id="id" track-by="name"></Multiselect>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="content-wrapper">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="list-group profile-item">
                                        <li class="list-group-item">
                                            <div class="info">
                                                <h6>Kategori</h6>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <Multiselect v-model="detail.category" :options="categories"
                                                @select="selectCategory()" :multiple="false" :close-on-select="true"
                                                :clear-on-select="true" :preserve-search="true" :searchable="true"
                                                :internal-search="true" :options-limit="50"
                                                :placeholder="'Pilih Kategori'" open-direction="bottom" label="name"
                                                id="id" track-by="name"></Multiselect>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="content-wrapper other-info">
                            <h5 class="sub-title">Collaboralor</h5>
                            <div class="card">
                                <div class="card-body list-group profile-item">
                                    <div class="list-group-item">
                                        <Multiselect v-model="info_collabolator.form" :options="users"
                                            @select="selectCollabolator()" :multiple="false" :close-on-select="true"
                                            :clear-on-select="true" :preserve-search="true" :searchable="true"
                                            :internal-search="true" :options-limit="50" :placeholder="'Pilih Pengguna'"
                                            open-direction="bottom" label="name" id="id" track-by="name"></Multiselect>
                                    </div>

                                    <div v-for="(collab, c) in detail.collabolators" :key="collab.id"
                                        class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-lg bg-skyblue rounded-circle me-2">
                                                <img :src="collab.photo" />
                                            </div>
                                            <div class="chat-user-info">
                                                <h6>{{ collab.name }}</h6>
                                            </div>
                                        </div>
                                        <span class="link-icon" @click="removeCollab(collab.id)"><i
                                                class="ti ti-x"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="content-wrapper">
                            <h5 class="sub-title">Additional Data</h5>
                            <div class="card">
                                <div class="card-body">
                                    <ul class="list-group profile-item">
                                        <li class="list-group-item" style="display: block" v-for="(info, i) in detail.additional
                                            .data" :key="i">
                                            <h6>{{ info.name }}</h6>

                                            <!-- Textarea untuk tipe text -->
                                            <textarea v-if="info.type === 'text'" class="form-control"
                                                v-model="info.value" rows="3" placeholder="Masukkan teks"
                                                @change="addAdditional"></textarea>

                                            <!-- Input number untuk tipe number -->
                                            <input v-else-if="info.type === 'number'" type="number" class="form-control"
                                                v-model="info.value" placeholder="Masukkan angka"
                                                @change="addAdditional" />

                                            <!-- Select untuk tipe options -->
                                            <select v-else-if="info.type === 'options'" class="form-select"
                                                v-model="info.value" @change="addAdditional">
                                                <option v-for="(option, index
                                                ) in info.options" :key="index" :value="option">
                                                    {{ option }}
                                                </option>
                                            </select>

                                            <!-- Input date untuk tipe date -->
                                            <input v-else-if="info.type === 'date'" type="date" class="form-control"
                                                v-model="info.value" @change="addAdditional" />

                                            <!-- Select untuk tipe true/false -->
                                            <select v-else-if="
                                                info.type === 'true_false'
                                            " class="form-select" v-model="info.value" @change="addAdditional">
                                                <option :value="true">True</option>
                                                <option :value="false">
                                                    False
                                                </option>
                                            </select>
                                        </li>

                                        <li class="list-group-item">
                                            <textarea class="form-control" v-model="detail.additional.note" rows="3"
                                                placeholder="Catatan" @change="addAdditional"></textarea>
                                        </li>
                                    </ul>
                                    <div class="text-center mt-3">
                                        <a href="javascript:void(0);" @click="openInformation"
                                            class="view-all link-primary d-inline-flex align-items-center justify-content-center">
                                            Tambah Informasi<i class="ti ti-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="content-wrapper other-info mb-0">
                            <div class="card mb-0">
                                <div class="card-body list-group profile-item">
                                    <div class="list-group-item">
                                        <div class="profile-info">
                                            <h6>
                                                <i class="ti ti-user-circle me-2 text-default"></i>Assigned By
                                            </h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            {{ detail.detail.assignment_by }}
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="profile-info">
                                            <h6>
                                                <i class="ti ti-user-circle me-2 text-default"></i>Handled By
                                            </h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            {{ detail.handled?.name }}
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="profile-info">
                                            <h6>
                                                <i class="ti ti-user-circle me-2 text-default"></i>Kategori
                                            </h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            {{ detail.category?.name }}
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="profile-info">
                                            <h6>
                                                <i class="ti ti-user-circle me-2 text-default"></i>Resolved By
                                            </h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            {{ detail.detail.resolved_by }}
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="profile-info">
                                            <h6>
                                                <i class="ti ti-message me-2 text-default"></i>Ai Agent
                                            </h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            {{ detail.detail.ai }}
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="profile-info">
                                            <h6>
                                                <i class="ti ti-calendar me-2 text-default"></i>Assigned At
                                            </h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            {{ detail.detail.assignment_at }}
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="profile-info">
                                            <h6>
                                                <i class="ti ti-calendar me-2 text-default"></i>Created At
                                            </h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            {{ detail.detail.created_at }}
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="profile-info">
                                            <h6>
                                                <i class="ti ti-user-circle me-2 text-default"></i>Resolved At
                                            </h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            {{ detail.detail.resolved_at }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="content-wrapper other-info mb-0" v-if="detail.status != 'resolved'">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <button v-if="detail.status == 'open'" type="button" class="btn btn-danger w-100"
                                        @click="blockUnblock">
                                        Blokir Pengguna ini
                                    </button>
                                    <button v-if="detail.status == 'block'" type="button" class="btn btn-info w-100"
                                        @click="blockUnblock">
                                        Buka Blokiran
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for update file -->
    <div class="modal fade" id="previewmodal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true"
        ref="fileModal">
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
                    <button type="button" class="btn-close" id="closeModal" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body row">
                    <div v-if="file.previewType === 'image'" class="col-12 d-flex justify-content-center">
                        <img :src="file.filePreview" alt="Image Preview" class="img-fluid" />
                    </div>
                    <div v-if="file.previewType === 'video'" class="col-12 d-flex justify-content-center">
                        <video controls="">
                            <source :src="file.filePreview" type="video/mp4" />
                        </video>
                    </div>
                    <div v-if="file.previewType === 'audio'" class="col-12 d-flex justify-content-center">
                        <audio controls="">
                            <source :src="file.filePreview" type="audio/mpeg" />
                        </audio>
                    </div>
                    <div v-if="file.previewType === 'document'" class="col-12 d-flex justify-content-center">
                        <embed :src="file.filePreview" type="application/pdf" width="100%" height="400px"
                            v-if="file.isPDF" />
                        <img v-else :src="attribute.document" class="w-50" alt="img" />
                    </div>

                    <div class="col-12 mt-4">
                        <textarea class="form-control" placeholder="Tuliskan pesan Anda" ref="messageInputModal"
                            rows="1" :disabled="send.loader" @input="autoResizeModal"
                            @keydown.shift.enter.prevent="insertLineBreak" v-model="send.text"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" :disabled="send.loader" class="btn btn-primary" @click="confirmFile">
                        {{ send.loader ? "Loading..." : "Kirim Pesan" }}
                        <i class="ti ti-send ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal for update -->

    <!-- Modal For Take Photo -->
    <div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true"
        ref="cameraModal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Take Photo</h4>
                    <button type="button" class="btn-close" id="closeModalCamera" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 d-flex justify-content-center" v-if="camera.picture == null">
                        <video ref="videoCamera" autoplay></video>
                        <canvas ref="canvas" style="display: none"></canvas>
                    </div>
                    <div class="col-12 d-flex justify-content-center" v-if="camera.picture != null">
                        <img :src="camera.picture" alt="Image Preview" class="img-fluid" />
                    </div>
                    <div class="col-12 mt-4">
                        <textarea class="form-control" placeholder="Type Your Message" ref="messageInputCamera" rows="1"
                            :disabled="send.loader" @input="autoResizeCamera"
                            @keydown.shift.enter.prevent="insertLineBreak" v-model="send.text"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button v-if="camera.picture == null" type="button" class="btn btn-primary" @click="capturePhoto">
                        Take Foto
                        <i class="ti ti-camera ms-2"></i>
                    </button>
                    <button v-if="camera.picture != null && !send.loader" type="button" class="btn btn-primary me-2"
                        @click="reTakePhoto">
                        Re-Take
                        <i class="ti ti-camera ms-2"></i>
                    </button>
                    <button v-if="camera.picture != null" type="button" :disabled="send.loader" @click="sendPhoto"
                        class="btn btn-primary">
                        {{ send.loader ? "Loading..." : "Send Photo" }}
                        <i class="ti ti-send ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Additional Modal -->
    <div class="modal fade" id="additionalModal" tabindex="-1" aria-labelledby="filePreviewModalLabel"
        aria-hidden="true" ref="additionalModal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Data</h4>
                    <button type="button" class="btn-close" id="closeModaladditional" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body row">
                    <!-- Input Nama -->
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" v-model="additional.data.name"
                            placeholder="Masukkan Nama" />
                    </div>

                    <!-- Select Tipe -->
                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <select class="form-select" v-model="additional.data.type">
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="options">Options</option>
                            <option value="date">Date</option>
                            <option value="true_false">True / False</option>
                        </select>
                    </div>

                    <!-- Input Options (Muncul hanya jika type = options) -->
                    <div v-if="additional.data.type === 'options'" class="mb-3">
                        <label class="form-label">Options</label>
                        <div v-for="(option, index) in additional.data.options" :key="index" class="d-flex mb-2">
                            <input type="text" class="form-control me-2" v-model="additional.data.options[index]"
                                placeholder="Masukkan opsi" />
                            <button class="btn btn-danger" @click="removeOption(index)">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        <br />
                        <button class="btn btn-success" @click="addOption">
                            Tambah Option
                            <i class="ti ti-plus ms-2"></i>
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" @click="addAdditionalAndClose" class="btn btn-primary">
                        Tambahkan
                        <i class="ti ti-send ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Reply -->
    <div class="modal fade" id="quickReplyModal" tabindex="-1" aria-labelledby="filePreviewModalLabel"
        aria-hidden="true" ref="quickReplyRef">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Kelola Quick Reply</h4>
                    <button type="button" class="btn-close" id="closeModalQuickReply" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i>
                    </button>
                </div>

                <div class="modal-body row">

                    <div class="col-12" style="max-height: 75vh;overflow-y: scroll;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Konten</th>
                                    <th>File</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(quick, q) in quick_reply_all" :key="q">
                                    <td>
                                        {{ quick.name }}
                                    </td>
                                    <td>
                                        {{ quick.content ? quick.content.substring(0, 100) + (quick.content.length > 100
                                            ?
                                            "..." : "") : "" }}
                                    </td>
                                    <td>
                                        <img v-if="quick.type === 'image' && quick.media_url" :src="quick.media_url"
                                            alt="img" style="
                                                    max-height: 50px;
                                                    border: 1px solid #ccc;
                                                    border-radius: 5px;
                                                    margin-top: 5px;
                                                " />
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm text-dark"
                                            @click="editQuickReply(quick, q)">
                                            <i class="ti ti-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            @click="removeQuickReply(quick.id)">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="addQuickReply">
                        Tambah Quick Reply
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Quick Reply -->

    <!-- Add Quick Reply -->
    <div class="modal fade" id="addQuickReply" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true"
        ref="addQuickReplyRef">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        {{ quick_reply.editmode ? 'Edit Quick Reply' : 'Tambah Quick Reply' }}
                    </h4>
                    <button type="button" class="btn-close" id="closeModalAddQuickReply" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body row">

                    <!-- Tanggal Awal -->
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" v-model="quick_reply.name" />
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">File ( Optional ) </label>
                        <input type="file" class="form-control" ref="quickReplyFile" id="quickReplyFile"
                            @change="handleFileChangeQuickReply" />
                    </div>

                    <div class="col-12 mt-4">
                        <textarea class="form-control" placeholder="Masukkan Pesan Text" ref="messageInputQuickReply"
                            rows="1" :disabled="send.loader" @input="autoResizeQuickReply"
                            @keydown.shift.enter.prevent="insertLineBreakQuickReply"
                            v-model="quick_reply.content"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" :disabled="send.loader" class="btn btn-primary" @click="submitQuickReply">
                        {{ send.loader ? "Loading..." : "Simpan Quick Reply" }}
                        <i class="ti ti-plus ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Quick Reply -->
</template>

<script>
import imageIcon from "@/assets/icons/image.png";
import imageAudio from "@/assets/icons/audio.png";
import imageVideo from "@/assets/icons/video.png";
import imageDocument from "@/assets/icons/documents.png";
import imageLocation from "@/assets/icons/map.png";
import NProgress from "nprogress";
import { Clipboard } from "lucide-vue-next"; // Pastikan Lucide sudah diinstal 
import socket from "@/socket";
import 'emoji-mart-vue-fast/css/emoji-mart.css'
import { Picker, EmojiIndex } from "emoji-mart-vue-fast/src";
import data from 'emoji-mart-vue-fast/data/all.json'
let emojiIndex = new EmojiIndex(data);
export default {
    components: {
        Picker,
        ClipboardIcon: Clipboard,
    },
    data() {
        return {
            emojiIndex: emojiIndex,
            emojisOutput: "",
            hoveredMessageId: null,
            activeDropdownId: null,
            replyingTo: null,
            intervalId: null,
            chatid: null,
            emojiShow: false,
            autoread: false,
            isUserScrolling: false,
            shouldScrollToBottom: true,
            quick_replies: [],
            quick_reply_all: [],
            lastQuickReplyDetail: null,
            quickReplyPopup: {
                show: false,
                loading: false,
                selectedIndex: 0,
            },
            quick_reply: {
                editmode: false,
                name: "",
                content: "",
                type: "text",
                file: null,
            },
            info_label: {
                labels: [],
                form: null,
            },
            info_collabolator: {
                form: null,
            },
            additional: {
                modal: false,
                data: {
                    name: "",
                    type: "",
                    value: "",
                    options: [],
                },
            },
            users: [],
            categories: [],
            detail: {
                id: "",
                name: "",
                phone: "",
                status: "",
                from: "",
                device: "",
                livechat: "",
                label: [],
                handled: {
                    id: "",
                    name: "",
                },
                category: {
                    id: "",
                    name: "",
                },
                additional: {
                    data: [],
                    note: "",
                },
                takeover: false,
                photo: "",
                detail: {
                    assignment_by: "",
                    resolved_by: "",
                    ai: "",
                    assignment_at: "",
                    resolved_at: "",
                    created_at: "",
                },
            },
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
                modal: false,
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

        showEmojiData() {
            this.emojiShow = !this.emojiShow
        },


        showEmoji(emoji) {
            this.send.text = this.send.text + emoji.native;
        },

        cancelReply() {
            this.replyingTo = null;
        },

        toggleReplyMenu(messageId, event) {
            // Hentikan event agar tidak memicu clickOutside
            if (event) {
                event.stopPropagation();
            }

            if (this.activeDropdownId === messageId) {
                this.activeDropdownId = null;
            } else {
                this.activeDropdownId = messageId;
            }
        },

        replyToMessage(message) {
            this.replyingTo = message;
            this.activeDropdownId = null;
            this.$nextTick(() => {
                if (this.$refs.messageInput) {
                    this.$refs.messageInput.focus();
                }
            });
        },


        manageQuickReply() {
            const modal = new bootstrap.Modal(this.$refs.quickReplyRef, {
                backdrop: "static",
                keyboard: false,
            });
            modal.show();

            this.hideQuickReplies();
        },

        openAddQuickReply() {
            const modal = new bootstrap.Modal(this.$refs.addQuickReplyRef, {
                backdrop: "static",
                keyboard: false,
            });
            modal.show();
        },

        addQuickReply() {
            this.quick_reply = {
                editmode: false,
                name: "",
                content: "",
                type: "text",
                file: null,
            }

            var modal = document.getElementById("closeModalQuickReply");
            modal.click();

            this.openAddQuickReply();
        },

        editQuickReply(data, index) {
            this.quick_reply = {
                editmode: true,
                index: index,
                id: data.id,
                name: data.name,
                content: data.content,
                type: data.type,
                file: null,
            }

            var modal = document.getElementById("closeModalQuickReply");
            modal.click();

            this.openAddQuickReply();
        },

        async submitQuickReply() {
            NProgress.start();
            NProgress.set(0.1);
            this.send.loader = true;
            try {
                const formData = new FormData();
                formData.append("name", this.quick_reply.name);
                formData.append("content", this.quick_reply.content);

                if (this.quick_reply.file) {
                    formData.append("media", this.quick_reply.file);
                }

                var url = `crm/quick-replies/create`;

                if (this.quick_reply.editmode) {
                    url = `crm/quick-replies/update/${this.quick_reply.id}`;
                }

                const response = await this.$axios.post(url,
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                document.getElementById("quickReplyFile").value = "";
                this.send.loader = false;
                NProgress.done();

                var modal = document.getElementById("closeModalAddQuickReply");
                modal.click();


                this.getQuickRepliesAll();

                this.$showToast(response.data.message, "info", 3000);
            } catch (error) {
                console.log(error);
                NProgress.done();
                this.send.loader = false;
                this.$handleErrorResponse(error);
            }
        },

        async removeQuickReply(id) {
            try {

                const response = await this.$axios.delete(
                    `/crm/quick-replies/remove/${id}`);

                this.getQuickRepliesAll();
                this.$showToast(response.data.message, "info", 3000);

            } catch (error) {
                this.$handleErrorResponse(error);
                console.log(error);
                this.message.loader = false;
            }
        },

        handleFileChangeQuickReply(event) {
            const file = event.target.files[0];
            if (file) {
                this.quick_reply.file = file;

                if (file.type.startsWith("image")) {
                    this.quick_reply.type = "image";
                } else if (file.type.startsWith("video")) {
                    this.quick_reply.type = "video";
                } else if (file.type.startsWith("audio")) {
                    this.quick_reply.type = "audio";
                } else if (file.type === "application/pdf") {
                    this.quick_reply.type = "document";
                } else {
                    this.quick_reply.type = "document";
                }
            }
        },

        addOption() {
            this.additional.data.options.push("");
        },

        closeChat() {
            this.$router.push({ name: 'blank_chat' });
        },

        removeOption(index) {
            this.additional.data.options.splice(index, 1);
        },

        addAdditionalAndClose() {
            this.addAdditional("add");

            var modal = document.getElementById("closeModaladditional");
            modal.click();
        },

        async addAdditional(type = "update") {
            if (type == "add") {
                this.detail.additional.data.push(this.additional.data);
            }

            try {
                await this.$axios.post(
                    `/crm/action/change-additional/${this.$route.params.chatid}`,
                    {
                        note: this.detail.additional.note,
                        additional_data: this.detail.additional.data,
                    }
                );
            } catch (error) {
                console.error("Gagal mengirim data ke API", error);
            }
        },

        async blockUnblock() {
            try {
                await this.$axios.post(
                    `/crm/action/block/${this.$route.params.chatid}`,
                    {
                        status:
                            this.detail.status != "block" ? "block" : "open",
                    }
                );

                this.detail.status =
                    this.detail.status != "block" ? "block" : "open";
            } catch (error) {
                console.error("Gagal mengirim data ke API", error);
            }
        },

        async resolved() {
            try {
                const response = await this.$axios.post(
                    `/crm/action/resolved/${this.$route.params.chatid}`
                );

                this.detail.status = "resolved";
                this.detail.detail.resolved_by = response.data.detail.resolved_by;
                this.detail.detail.resolved_at = response.data.detail.resolved_at;
                this.detail.takeover = false;
            } catch (error) {
                console.error("Gagal mengirim data ke API", error);
            }
        },

        async selectUser() {
            try {
                const response = await this.$axios.post(
                    `/crm/users/user-change/${this.$route.params.chatid}`,
                    {
                        handled: this.detail.handled,
                    }
                );

                this.detail.detail.assignment_by = response.data.detail.assignment_by;
                this.detail.detail.assignment_at = response.data.detail.assignment_at;
            } catch (error) {
                console.error("Gagal mengirim data ke API", error);
            }
        },

        async selectCategory() {
            try {
                const response = await this.$axios.post(
                    `/crm/categories/change/${this.$route.params.chatid}`,
                    {
                        category: this.detail.category,
                    }
                );

            } catch (error) {
                console.error("Gagal mengirim data ke API", error);
            }
        },

        selectCollabolator() {
            if (!this.info_collabolator.form) return;

            const exists = this.detail.collabolators.some(
                (collabolator) =>
                    collabolator.id === this.info_collabolator.form.id
            );

            if (!exists) {
                this.detail.collabolators.push(this.info_collabolator.form);
                this.info_collabolator.form = null;
                this.postCollab();
            }
        },

        removeCollab(id) {
            this.detail.collabolators = this.detail.collabolators.filter(
                (collabolator) => collabolator.id !== id
            );

            this.postCollab();
        },

        async postCollab() {
            try {
                await this.$axios.post(
                    `/crm/users/collabolator/${this.$route.params.chatid}`,
                    {
                        collabolator: this.detail.collabolators,
                    }
                );
            } catch (error) {
                console.error("Gagal mengirim data ke API", error);
            }
        },

        selectLabel() {
            if (!this.info_label.form) return;

            const exists = this.detail.label.some(
                (label) => label.id === this.info_label.form.id
            );

            if (!exists) {
                this.detail.label.push(this.info_label.form);
                this.info_label.form = null;
                this.postLabel();
            }
        },

        removeLabel(id) {
            this.detail.label = this.detail.label.filter(
                (label) => label.id !== id
            );

            this.postLabel();
        },

        async postLabel() {
            try {
                await this.$axios.post(
                    `/crm/labels/change/${this.$route.params.chatid}`,
                    {
                        labels: this.detail.label,
                    }
                );
            } catch (error) {
                console.error("Gagal mengirim data ke API", error);
            }
        },

        async copyToClipboard(phone) {
            try {
                await navigator.clipboard.writeText(phone);
                this.$showToast(
                    `Berhasil menyalin Nomor Whatsapp`,
                    "info",
                    3000
                );
            } catch (err) {
                console.error("Gagal menyalin", err);
            }
        },

        async changeTakeOver(status) {
            this.detail.takeover = status;

            try {
                const response = await this.$axios.post(
                    `/crm/takeover-message/${this.$route.params.chatid}`,
                    {
                        takeover: status,
                    }
                );

                this.detail.takeover = response.data.status;
                this.detail.handled = response.data.handled
                this.detail.detail.assignment_by = response.data.detail.assignment_by;
                this.detail.detail.assignment_at = response.data.detail.assignment_at;
            } catch (error) {
                this.$handleErrorResponse(error);
                console.log(error);
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

        async openInformation() {
            this.additional.data = {
                name: "",
                type: "",
                value: "",
                options: [],
            };
            const modal = new bootstrap.Modal(this.$refs.additionalModal, {
                backdrop: "static",
                keyboard: false,
            });
            modal.show();
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

        closeModal() { },

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

        async getInformation() {
            try {
                const response = await this.$axios.get(
                    `/crm/information-detail/${this.$route.params.chatid}`
                );
                this.detail = response.data;

                this.getMessages();
                this.markToRead();
            } catch (error) {
                this.$handleErrorResponse(error);
                console.log(error);
            }
        },

        async markToRead() {
            try {
                await this.$axios.post(
                    `/crm/mark-read/${this.$route.params.chatid}`
                );
            } catch (error) {
                this.$handleErrorResponse(error);
                console.log(error);
            }
        },

        async getLabels() {
            try {
                const response = await this.$axios.get(`/crm/labels`);
                this.info_label.labels = response.data.labels;
            } catch (error) {
                this.$handleErrorResponse(error);
                console.log(error);
            }
        },

        async getCategories() {
            try {
                const response = await this.$axios.get(`/crm/categories`);
                this.categories = response.data.categories;
            } catch (error) {
                this.$handleErrorResponse(error);
                console.log(error);
            }
        },

        async getUsers() {
            try {
                const response = await this.$axios.get(`/users/components`);
                this.users = response.data.users;
            } catch (error) {
                this.$handleErrorResponse(error);
                console.log(error);
            }
        },

        async getMessages(page = 1, prepend = false) {
            try {
                this.message.loader = true;

                const response = await this.$axios.get(
                    `/crm/messages/${this.$route.params.chatid}`,
                    {
                        params: {
                            page: page,
                            limit: 20, // Adjust the limit as needed
                        }
                    }
                );

                var data = response.data;
                var listData = data.messages;

                if (prepend) {
                    listData = listData.filter(
                        (newItem) =>
                            !this.message.list.some(
                                (existingItem) => existingItem.id === newItem.id
                            )
                    );

                    // Prepend old messages to the top
                    this.message.list = [...listData, ...this.message.list];

                    // Save scroll position
                    const chatArea = document.querySelector('.messages.chat-area');
                    const oldHeight = chatArea.scrollHeight;

                    this.$nextTick(() => {
                        const newHeight = chatArea.scrollHeight;
                        chatArea.scrollTop = newHeight - oldHeight;

                        if (page > 1) {
                            this.shouldScrollToBottom = false;
                        }
                    });
                } else {
                    // Initial load or refresh
                    this.shouldScrollToBottom = true;
                    this.message.list = listData;

                    this.$nextTick(() => {
                        this.forceScrollToBottom();

                        // Double-check scroll setelah beberapa waktu untuk memastikan
                        setTimeout(() => {
                            this.forceScrollToBottom();
                        }, 300);
                    });
                }

                // Track pagination
                this.message.page = page;
                this.message.hasMoreMessages = listData.length > 0;
                this.message.loader = false;
                this.autoread = data.autoread;

            } catch (error) {
                this.$handleErrorResponse(error);
                console.log(error);
                this.message.loader = false;
            }
        },

        async deleteMessage(id, index) {
            try {
                await this.$axios.delete(`/crm/action/delete/${id}`);
                this.message.list.splice(index, 1);
            } catch (error) {
                console.log(error);
            }
        },


        getFileIcon(mediaType) {
            const iconMap = {
                pdf: "ti ti-file-type-pdf",
                docx: "ti ti-file-type-doc",
                doc: "ti ti-file-type-doc",
                pptx: "ti ti-file-type-ppt",
                ppt: "ti ti-file-type-ppt",
                xlsx: "ti ti-file-type-xls",
                xls: "ti ti-file-type-xls",
                txt: "ti ti-file-text",
                json: "ti ti-code",
                html: "ti ti-code-dots",
                css: "ti ti-code-css",
                js: "ti ti-brand-javascript",
                php: "ti ti-brand-php",
                file: "ti ti-file",
            };

            return iconMap[mediaType] || "ti ti-file";
        },

        getFileIconClass(mediaType) {
            const classMap = {
                pdf: "file-icon-pdf",
                docx: "file-icon-doc",
                doc: "file-icon-doc",
                pptx: "file-icon-ppt",
                ppt: "file-icon-ppt",
                xlsx: "file-icon-xls",
                xls: "file-icon-xls",
                txt: "file-icon-txt",
                json: "file-icon-code",
                html: "file-icon-code",
                css: "file-icon-code",
                js: "file-icon-code",
                php: "file-icon-code",
                file: "file-icon-generic",
            };

            return classMap[mediaType] || "file-icon-generic";
        },

        getFileTypeLabel(mediaType) {
            const labelMap = {
                pdf: "PDF Document",
                docx: "Word Document",
                doc: "Word Document",
                pptx: "PowerPoint",
                ppt: "PowerPoint",
                xlsx: "Excel Spreadsheet",
                xls: "Excel Spreadsheet",
                txt: "Text File",
                json: "JSON File",
                html: "HTML File",
                css: "CSS File",
                js: "JavaScript",
                php: "PHP File",
                file: "File",
            };

            return labelMap[mediaType] || "File";
        },

        getFileName(url, mediaType) {
            if (!url) return `File.${mediaType}`;

            // Extract filename from URL
            const parts = url.split("/");
            let fileName = parts[parts.length - 1];

            // Remove query parameters if any
            if (fileName.includes("?")) {
                fileName = fileName.split("?")[0];
            }

            // Decode URL encoded characters
            try {
                fileName = decodeURIComponent(fileName);
            } catch (e) {
                console.error("Error decoding filename:", e);
            }

            return fileName || `File.${mediaType}`;
        },

        formatFileSize(sizeInBytes) {
            if (!sizeInBytes) return "";

            const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
            if (sizeInBytes === 0) return "0 Byte";

            const i = parseInt(
                Math.floor(Math.log(sizeInBytes) / Math.log(1024))
            );
            return (
                Math.round(sizeInBytes / Math.pow(1024, i), 2) + " " + sizes[i]
            );
        },


        async deleteEveryOne(detail) {
            try {
                await this.$axios.post(
                    `/device/misc/delete-everyone/${this.$route.params.id}`,
                    {
                        chatid: this.chatid,
                        message: {
                            remoteJid: this.chatid,
                            fromMe: detail.sender,
                            id: detail.id,
                        },
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
            if (message != "" && message != null) {
                return message.replace(/\n/g, "<br>");
            } else {
                return "";
            }
        },

        scrollToBottom() {
            if (!this.shouldScrollToBottom) return;

            const messageContent = document.querySelector(".chat-area");
            if (messageContent) {
                messageContent.scrollTop = messageContent.scrollHeight;
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
                        this.message.list[findMessage].media_url =
                            data.data.downloadPath;
                        this.message.list[findMessage].media_url = true;
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

        autoResizeQuickReply() {
            const textarea = this.$refs.messageInputQuickReply;
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

        insertLineBreakQuickReply() {
            this.send.text += "\n";
            this.$nextTick(() => {
                this.autoResizeQuickReply();
            });
        },

        async sendMessage() {
            NProgress.start();
            NProgress.set(0.1);
            this.send.loader = true;
            try {
                const formData = new FormData();
                // Hapus karakter '/' di awal pesan jika ada
                let messageText = this.send.text;
                if (messageText.startsWith("/")) {
                    messageText = messageText.substring(1);
                }
                formData.append("message", messageText);

                // Tambahkan ID pesan yang dibalas jika ada
                if (this.replyingTo) {
                    formData.append("reply_to", this.replyingTo.id);
                    // Hilangkan karakter '/' di awal pesan jika ada
                    let replyText = this.replyingTo.message || "";
                    if (replyText.startsWith("/")) {
                        replyText = replyText.substring(1);
                    }
                    formData.append("reply_text", replyText);
                }

                if (this.send.file) {
                    if (this.send.type == "photo") {
                        formData.append("file", this.send.file);
                    } else {
                        formData.append("file", this.send.file);
                    }
                }

                if (
                    this.lastQuickReplyDetail &&
                    this.lastQuickReplyDetail.media_url
                ) {
                    // Ketika mode quick reply aktif
                    formData.append("quick_reply", "active");
                    formData.append(
                        "file_path",
                        this.lastQuickReplyDetail.media_url
                    );
                    formData.append(
                        "file_name",
                        this.lastQuickReplyDetail.file_name
                    );
                    formData.append(
                        "file_type",
                        this.lastQuickReplyDetail.file_type
                    );
                    formData.append("type", "document");
                    formData.append(
                        "type_file",
                        this.lastQuickReplyDetail.type
                    );
                    formData.append(
                        "file_size",
                        this.lastQuickReplyDetail.file_size
                    );
                } else {
                    formData.append("type", this.send.type);
                }

                const response = await this.$axios.post(
                    `crm/send-message/${this.$route.params.chatid}`,
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                this.lastQuickReplyDetail = null;
                NProgress.done();
                socket.emit(`crm-update`, response.data);

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
                this.replyingTo = null;

                // Reset textarea height setelah kirim pesan
                this.$nextTick(() => {
                    if (this.$refs.messageInput) {
                        this.$refs.messageInput.style.height = "auto";
                    }
                    if (this.$refs.messageInputModal) {
                        this.$refs.messageInputModal.style.height = "auto";
                    }
                    if (this.$refs.messageInputCamera) {
                        this.$refs.messageInputCamera.style.height = "auto";
                    }
                });

                // this.$showToast(`Berhasil mengirim pesan`, "info", 3000);
                this.shouldScrollToBottom = true;
            } catch (error) {
                NProgress.done();
                this.send.loader = false;
                console.log(error);
                this.$handleErrorResponse(error);
            }
        },

        initializeSocketEvents() {
            if (this.$route.params.chatid) {
                this.chatid = this.$route.params.chatid;
                this.setupMessageListener(this.chatid);
            }
        },

        setupMessageListener(conversationId) {
            if (this.chatid && this.currentConversationId !== conversationId) {
                socket.off(`update-message-${this.chatid}`);
            }

            // Setup new listener
            socket.on(
                `update-message-${conversationId}`,
                this.handleNewMessage
            );
        },

        handleNewMessage(newMessage) {
            // Check if message already exists
            let chatIndex = this.message.list.findIndex(
                (c) => c.id === newMessage.id
            );

            if (chatIndex === -1) {
                if (this.detail.status == 'resolved') {
                    this.detail.status = 'open';
                }

                this.message.list.push(newMessage);
                this.$nextTick(() => {
                    this.scrollToBottom();
                    this.markToRead();
                });
            }
        },

        forceScrollToBottom() {
            this.shouldScrollToBottom = true;
            this.isUserScrolling = false;

            // Gunakan setTimeout untuk memastikan DOM sepenuhnya dirender
            setTimeout(() => {
                const messageContent = document.querySelector(".chat-area");
                if (messageContent) {
                    messageContent.scrollTop = messageContent.scrollHeight;
                }
            }, 100);
        },

        handleChatScroll(event) {
            const element = event.target;

            // Deteksi jika user sedang scroll manual
            this.isUserScrolling = true;

            // Jika user scroll ke dekat bawah, aktifkan auto-scroll
            const isNearBottom =
                element.scrollHeight -
                element.scrollTop -
                element.clientHeight <
                100;
            this.shouldScrollToBottom = isNearBottom;

            if (element.scrollTop <= 50) {
                if (this.message.hasMoreMessages && !this.message.loader) {
                    this.loadOlderMessages();
                }
            }

            // Reset user scrolling flag setelah beberapa waktu
            clearTimeout(this.scrollTimeout);
            this.scrollTimeout = setTimeout(() => {
                this.isUserScrolling = false;
            }, 1000);
        },

        loadOlderMessages() {
            if (this.message.loader || !this.message.hasMoreMessages) return;
            this.getMessages(this.message.page + 1, true);
        },

        async onMessageInput() {
            this.autoResize();

            if (this.send.text.startsWith("/")) {
                const keyword = this.send.text.substring(1);
                this.quickReplyPopup.show = true;
                this.quickReplyPopup.loading = true;
                await this.getQuickReplies(keyword);
            } else {
                this.lastQuickReplyDetail = null;
                this.hideQuickReplies();
            }
        },

        navigateQuickReplies(direction) {
            if (this.quick_replies.length === 0) return;

            if (direction === "down") {
                this.quickReplyPopup.selectedIndex =
                    (this.quickReplyPopup.selectedIndex + 1) %
                    this.quick_replies.length;
            } else if (direction === "up") {
                this.quickReplyPopup.selectedIndex =
                    (this.quickReplyPopup.selectedIndex -
                        1 +
                        this.quick_replies.length) %
                    this.quick_replies.length;
            }
        },

        hideQuickReplies() {
            this.quickReplyPopup.show = false;
        },

        selectQuickReply(item) {
            this.send.text = item.content + "\n";
            this.lastQuickReplyDetail = item;
            this.hideQuickReplies();
            this.insertLineBreak();
        },

        handlePaste(ev) {

            if (ev.clipboardData && ev.clipboardData.items) {
                const items = ev.clipboardData.items;

                for (let i = 0; i < items.length; i++) {
                    const item = items[i];
                    if (
                        item.kind === "file" &&
                        item.type.startsWith("image/")
                    ) {
                        const file = item.getAsFile();

                        if (file) {
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            this.$refs.fileInput.files = dataTransfer.files;

                            // Teruskan ke handler file change
                            this.handleFileChange({
                                target: { files: dataTransfer.files },
                            });

                            ev.preventDefault();
                            break;
                        }
                    }
                }
            }
        },

        async getQuickReplies(search) {
            return new Promise(async (resolve) => {
                try {

                    let url = `crm/quick-replies?`;

                    if (search && search.trim() !== "") {
                        url += `name=${encodeURIComponent(search)}`;
                    }

                    const response = await this.$axios.get(url);
                    this.quick_replies = response.data.data;
                    this.quickReplyPopup.loading = false;
                    resolve(this.quick_replies);
                } catch (error) {
                    this.quick_replies.length = 0;
                    resolve([]);
                }
            });
        },

        async getQuickRepliesAll() {
            try {

                const response = await this.$axios.get(
                    `/crm/quick-replies`,
                    {
                        params: {
                            page: 1,
                            limit: 50,
                        }
                    }
                );

                var data = response.data;
                var listData = data.data;
                this.quick_reply_all = listData;

            } catch (error) {
                this.$handleErrorResponse(error);
                console.log(error);
                this.message.loader = false;
            }
        },


    },
    updated() {
        // Hanya scroll ke bawah dalam kondisi tertentu
        if (this.shouldScrollToBottom && !this.isUserScrolling) {
            this.scrollToBottom();
        }
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

        this.getLabels();
        this.getUsers();
        this.getCategories();
        this.getQuickRepliesAll();
        // this.initializeSocketEvents();

        window.addEventListener("socket-reconnected", () => {
            this.getMessages(1, false);
        });
    },
    beforeRouteLeave(to, from, next) {
        if (this.chatid) {
            socket.off(`update-message-${this.chatid}`);
        }
        next();
    },
    watch: {
        "$route.params.chatid": {
            handler() {
                if (this.$route.name == "chat_room") {
                    this.message = {
                        list: [],
                        loader: true,
                        last_chat: "",
                        search: "",
                    };

                    this.shouldScrollToBottom = true;
                    this.isUserScrolling = false;

                    if (this.chatid && this.currentConversationId !== this.$route.params.chatid) {
                        socket.off(`update-message-${this.chatid}`);
                    }

                    // socket.removeAllListeners();
                    this.initializeSocketEvents();
                    this.getInformation();
                }
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

.messages {
    overflow: scroll;
    height: 84vh;
    padding-bottom: 120px;
    /* Tambahkan padding bottom untuk footer */
}

.chat .chat-footer {
    /* position: fixed; */
    width: 100%;
    bottom: 0;
}

.chats-right .message-content {
    padding-left: 20px;
}

.message-content {
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
    max-width: 100%;
}

/* Styling untuk reply feature */
.chat-info {
    position: relative;
}

/* Chevron untuk pesan lawan chat - di kanan */
.message-reply-button {
    position: absolute;
    top: 5px;
    right: -23px;
    /* Posisi paling kanan */
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.9);
    cursor: pointer;
    z-index: 5;
    color: #555;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Chevron untuk pesan saya - di kiri */
.chats-right .message-reply-button {
    right: auto;
    left: -23px;
    /* Posisi paling kiri */
}

.message-quoted-reply {
    padding: 10px;
    border-radius: 8px;
    background-color: #686a69;
    margin-bottom: 5px;
    cursor: pointer;
}

.chats-right .message-quoted-reply {
    background-color: #02614f;
}

/* Dropdown menu untuk lawan chat - di kanan */
.message-reply-dropdown {
    position: absolute;
    top: 30px;
    right: -173px;
    /* Posisi di kanan */
    min-width: 150px;
    background-color: #fff;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    z-index: 10;
    padding: 8px 0;
}

/* Dropdown menu untuk pesan saya - di kiri */
.chats-right .message-reply-dropdown {
    right: auto;
    left: -173px;
    /* Posisi di kiri */
}

.message-reply-dropdown .dropdown-item {
    padding: 8px 16px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.message-reply-dropdown .dropdown-item:hover {
    background-color: #f8f9fa;
}

/* Modifikasi untuk memastikan reply message preview full width */
.reply-message-preview,
.quick-reply-preview-media {
    width: 100%;
    background-color: #f5f5f5;
    border-left: 4px solid #0d6efd;
    margin-bottom: 8px;
    padding: 12px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.reply-message-content,
.quick-reply-content {
    width: 100%;
}

.reply-message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
    color: #0d6efd;
    width: 100%;
}

.reply-message-close {
    background: none;
    border: none;
    color: #777;
    cursor: pointer;
    padding: 0;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.reply-message-text,
.quick-reply-text {
    color: #555;
    font-size: 0.9em;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
}

.reply-quoted-text {
    color: #e5e5e5;
    font-size: 0.8em;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
}

.chats-right .reply-quoted-text {
    color: #b2b2b2;
}

/* Memastikan footer form memiliki padding untuk semua komponen di dalamnya */
.chat-footer .footer-form {
    display: flex;
    flex-direction: column;
    width: 100%;
}

/* Modifikasi chat-footer-wrap agar menjamin full width */
.chat-footer-wrap {
    width: 100%;
}

/* File Attachment Styling - Modern Design */
.file-attachment-preview {
    position: relative;
    margin: 8px 0;
    max-width: 100%;
    width: 100%;
    display: flex;
    justify-content: flex-start;
}

.chats-right .file-attachment-preview {
    justify-content: flex-end;
}

.file-attachment-card {
    display: flex;
    align-items: center;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    max-width: 280px;
    width: 100%;
    box-sizing: border-box;
}

.file-attachment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.file-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-right: 12px;
    color: white;
}

.file-icon-pdf {
    background-color: #f40f02;
}

.file-icon-doc {
    background-color: #295396;
}

.file-icon-ppt {
    background-color: #d24726;
}

.file-icon-xls {
    background-color: #1d6f42;
}

.file-icon-txt {
    background-color: #5b5b5b;
}

.file-icon-code {
    background-color: #333333;
}

.file-icon-generic {
    background-color: #607d8b;
}

.file-info {
    flex: 1;
    overflow: hidden;
    margin-right: 8px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.file-name {
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #333;
}

.file-meta {
    font-size: 12px;
    color: #757575;
    margin-top: 2px;
}

.file-type {
    margin-right: 8px;
}

.file-download-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f5f5f5;
    color: #555;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.file-download-btn:hover {
    background-color: #e0e0e0;
    color: #333;
}

.file-download-btn::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    transform: scale(0);
    transition: transform 0.3s;
}

.file-download-btn:hover::after {
    transform: scale(1);
}

.chats-right .file-attachment-card {
    background-color: rgba(240, 248, 255, 0.95);
    margin-left: auto;
}

/* Media queries for smaller screens */
@media (max-width: 576px) {
    .file-attachment-card {
        max-width: 220px;
        padding: 8px;
    }

    .file-icon {
        width: 32px;
        height: 32px;
        font-size: 16px;
        margin-right: 8px;
    }

    .file-download-btn {
        width: 30px;
        height: 30px;
    }
}

/* Untuk layar kecil */
@media (max-width: 768px) {
    .message-reply-button {
        width: 20px;
        height: 20px;
        font-size: 12px;
    }

    .reply-message-preview,
    .quick-reply-preview-media {
        padding: 8px;
        margin-bottom: 5px;
    }
}

/* Penyesuaian untuk chat content */
.message-content .file-attachment-preview {
    display: flex;
    justify-content: flex-start;
}

.chats-right .message-content .file-attachment-preview {
    justify-content: flex-end;
}

/* Meningkatkan tampilan file attachment dengan highlight saat dipilih */
.file-attachment-card.highlighted {
    background-color: rgba(255, 255, 0, 0.1);
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.25);
    animation: pulse-highlight 1s;
}

@keyframes pulse-highlight {
    0% {
        box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
    }

    70% {
        box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
    }

    100% {
        box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
    }
}

/* Memperbaiki tampilan vertikal pada file yang lebih kecil */
.file-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* Memastikan tombol download memiliki hover effect yang menarik */
.file-download-btn {
    position: relative;
    overflow: hidden;
}

.file-download-btn::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    transform: scale(0);
    transition: transform 0.3s;
}

.file-download-btn:hover::after {
    transform: scale(1);
}

/* Quick Replies Popup */
.quick-replies-popup {
    position: absolute;
    bottom: 90px;
    /* Sesuaikan dengan tinggi footer */
    margin: 0 10px;
    left: 0;
    right: 0;
    max-height: 300px;
    overflow-y: auto;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1050;
}

.quick-replies-header {
    padding: 10px 15px;
    border-bottom: 1px solid #ddd;
    background-color: #f7f7f7;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

.quick-replies-list {
    padding: 0px;
}

.quick-reply-item {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    cursor: pointer;
    transition: background-color 0.2s;
}

.quick-reply-item:hover {
    background-color: #f1f1f1;
}

.quick-reply-item-content {
    display: flex;
    flex-direction: row;
}

.quick-reply-title {
    font-weight: 600;
    color: rgb(30, 64, 175);
    margin-right: 10px;
}

.quick-reply-preview {
    font-size: 0.9em;
    color: #666;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.quick-replies-loading {
    display: flex;
    align-items: center;
    color: #666;
    padding: 10px;
}

.quick-replies-empty {
    text-align: center;
    color: #999;
    padding: 10px;
}
</style>