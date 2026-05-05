import { rmSync, readdir } from "fs";
import fs from "fs";
import { writeFile } from "fs/promises";
import { join } from "path";
import pino from "pino";
import {
    default as makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    delay,
    downloadMediaMessage,
    Browsers,
    fetchLatestBaileysVersion,
    makeCacheableSignalKeyStore,
} from "@whiskeysockets/baileys";
import { toDataURL } from "qrcode";
import dirname from "./dirname.js";
import response from "./response.js";
import axios from "axios";
import FormData from "form-data";
import { Readable } from "stream";

// Maps for session management
const sessions = new Map();
const retries = new Map();
const sessionsDir = (subDir = "") => {
    return join(dirname, "sessions", subDir ? subDir : "");
};

// Directory for session files
const getSessionDir = (filename = "") => {
    return join(dirname, "sessions", filename ? filename : "");
};

// Check if a session exists
const isSessionExists = (sessionId) => {
    return sessions.has(sessionId);
};

// Determine if reconnection should be attempted
const shouldReconnect = (sessionId) => {
    const retryCount = retries.get(sessionId) ?? 0;
    if (retryCount < 5) {
        retries.set(sessionId, retryCount + 1);
        return true;
    }
    return false;
};

const checkSession = (sessionId, isLegacy) => {
    const sessionFile = getSessionDir(
        sessionId + (isLegacy ? "_legacy.json" : "_md.json")
    );
    if (fs.existsSync(sessionFile)) {
        return require(sessionFile);
    }
    return null;
};

const getBrowserConfig = () => {
    if (process.env.WHATSAPP_BROWSER) {
        if (process.env.WHATSAPP_BROWSER === "MAC") {
            return Browsers.macOS("Desktop");
        } else if (process.env.WHATSAPP_BROWSER === "MOBILE") {
            return Browsers.mobile("Mobile");
        } else {
            return Browsers.ubuntu("Chrome");
        }
    }
    return Browsers.ubuntu("Chrome");
};

const backoffReconnect = (attempts) => {
    const delay = Math.min(1000 * Math.pow(2, attempts), 30000);
    return delay;
};

// Helper function to get file extension from MIME type
function getFileExtension(mimeType) {
    const mimeToExt = {
        "image/jpeg": "jpg",
        "image/jpg": "jpg",
        "image/png": "png",
        "image/gif": "gif",
        "image/webp": "webp",
        "audio/mp3": "mp3",
        "audio/mpeg": "mp3",
        "audio/wav": "wav",
        "audio/ogg": "ogg",
        "audio/m4a": "m4a",
        "audio/webm": "webm",
        "audio/flac": "flac",
        "video/mp4": "mp4",
        "video/avi": "avi",
        "video/mov": "mov",
        "video/webm": "webm",
        "application/pdf": "pdf",
        "application/msword": "doc",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
            "docx",
        "text/plain": "txt",
    };
    return mimeToExt[mimeType] || "bin";
}

// Create a new session - UPDATED VERSION WITHOUT STORE
const createSession = async (
    sessionId,
    isLegacy = false,
    responseObject = null,
    markOnline = false,
    historyMode = false
) => {
    const existingSession = checkSession(sessionId, isLegacy);
    if (existingSession) {
        return existingSession;
    }

    const sessionFilename =
        (isLegacy ? "legacy_" : "md_") + sessionId + (isLegacy ? ".json" : "");
    const logger = pino({ level: "warn" });

    let state, saveCreds;

    if (!isLegacy) {
        ({ state, saveCreds } = await useMultiFileAuthState(
            getSessionDir(sessionFilename)
        ));
    }

    const { version, isLatest } = await fetchLatestBaileysVersion();

    // INI YANG PENTING: Gunakan makeWASocket langsung, bukan baileys.default()
    const sock = makeWASocket({
        version: version,
        auth: {
            creds: state.creds,
            keys: makeCacheableSignalKeyStore(state.keys, logger),
        },
        printQRInTerminal: false,
        logger: logger,
        generateHighQualityLinkPreview: true,
        markOnlineOnConnect: markOnline,
        syncFullHistory: historyMode,
        connectTimeoutMs: 120000,
        defaultQueryTimeoutMs: 60000,
        browser: getBrowserConfig(),
        getMessage: async (key) => {
            const msg = messageStore.get(key.id); return msg || { conversation: "" };
        },
        patchMessageBeforeSending: (message) => {
            const requiresPatch = !!(
                message.buttonsMessage || message.listMessage
            );
            if (requiresPatch) {
                message = {
                    viewOnceMessage: {
                        message: {
                            messageContextInfo: {
                                deviceListMetadataVersion: 2,
                                deviceListMetadata: {},
                            },
                            ...message,
                        },
                    },
                };
            }

            return message;
        },
    });

    // Store chats and contacts in memory without makeInMemoryStore
    const sessionData = {
        ...sock,
        isLegacy: isLegacy,
        chats: new Map(),
        contacts: {},
    };

    sessions.set(sessionId, sessionData);

    console.log("Starting connection for session:", sessionId);

    sock.ev.on("creds.update", saveCreds);

    // Store chats data
    sock.ev.on("chats.set", ({ chats }) => {
        const session = sessions.get(sessionId);
        if (session) {
            chats.forEach((chat) => {
                session.chats.set(chat.id, chat);
            });
        }
    });

    // Store contacts data
    sock.ev.on("contacts.set", ({ contacts }) => {
        const session = sessions.get(sessionId);
        if (session) {
            contacts.forEach((contact) => {
                session.contacts[contact.id] = contact;
            });
        }
    });

    sock.ev.on("contacts.update", (updates) => {
        const session = sessions.get(sessionId);
        if (session) {
            updates.forEach((update) => {
                if (session.contacts[update.id]) {
                    session.contacts[update.id] = {
                        ...session.contacts[update.id],
                        ...update,
                    };
                } else {
                    session.contacts[update.id] = update;
                }
            });
        }
    });

    sock.ev.on("messages.upsert", async (messageUpdate) => {
        try {
            // Store messages for getMessage callback
            try { const msgs = m.messages || []; msgs.forEach(msg => { if (msg.key && msg.key.id) { addToMessageStore(msg.key.id, msg.message); } }); } catch(e) {}
            const message = messageUpdate.messages[0];

            if (!message) {
                console.log("No message in update");
                return;
            }

            await handleIncomingMessage(sock, sessionId, messageUpdate);
        } catch (err) {
            console.log("Error processing message upsert:", err.message);
        }
    });

    sock.ev.on("connection.update", async (update) => {
        const { connection, lastDisconnect, qr } = update;
        const statusCode = lastDisconnect?.error?.output?.statusCode;

        if (connection === "open") {
            retries.delete(sessionId);
            const phone = sock.user.id.split(":")[0];
            setDeviceStatus(sessionId, 1);
        }

        if (connection === "close") {
            if (
                statusCode === DisconnectReason.loggedOut ||
                !shouldReconnect(sessionId)
            ) {
                if (responseObject && !responseObject.headersSent) {
                    response(
                        responseObject,
                        500,
                        false,
                        "Unable to create session."
                    );
                }
                return deleteSession(sessionId, isLegacy);
            } else {
                const attempts = retries.get(sessionId) || 0;
                retries.set(sessionId, attempts + 1);
                const reconnectDelay = backoffReconnect(attempts);
                setTimeout(
                    () => createSession(sessionId, isLegacy),
                    reconnectDelay
                );
            }
        }

        if (qr) {
            if (responseObject && !responseObject.headersSent) {
                try {
                    const qrCodeDataUrl = await toDataURL(qr);
                    response(
                        responseObject,
                        200,
                        true,
                        "QR code received, please scan the QR code.",
                        { qr: qrCodeDataUrl }
                    );
                    return;
                } catch {
                    response(
                        responseObject,
                        500,
                        false,
                        "Unable to create QR code."
                    );
                }
            }
        }
    });
};

async function handleIncomingMessage(sock, sessionId, messageUpdate) {
    let quotedMessageContent = null;
    let quotedMessageType = "text";
    let quotedStanzaId = null;
    let quotedParticipant = null;
    try {
        const {
            messages: [message],
        } = messageUpdate;
        const { message: msg, key, pushName } = message;

        if (message.key.remoteJid === "status@broadcast") {
            return;
        }

        if (
            message.messageStubType != undefined ||
            message.pushName == undefined
        ) {
            return;
        }

        var messageContent =
            msg?.conversation ||
            msg?.buttonsResponseMessage?.selectedDisplayText ||
            msg?.listResponseMessage?.title ||
            msg?.extendedTextMessage?.text ||
            msg?.imageMessage?.caption ||
            msg?.videoMessage?.caption ||
            msg?.documentMessage?.caption ||
            msg?.stickerMessage?.fileSha256 ||
            "";

        const isMediaMessage =
            msg?.imageMessage ||
            msg?.videoMessage ||
            msg?.audioMessage ||
            msg?.documentMessage ||
            msg?.documentWithCaptionMessage;

        const messageType = msg?.imageMessage
            ? "image"
            : msg?.videoMessage
            ? "video"
            : msg?.audioMessage
            ? "audio"
            : msg?.documentMessage
            ? "document"
            : msg?.documentWithCaptionMessage
            ? "documentWithCaption"
            : "text";

        let fileName = null;
        let mimeType = null;

        if (messageType == "documentWithCaption") {
            messageContent =
                msg.documentWithCaptionMessage.message.documentMessage.caption;
            mimeType =
                msg.documentWithCaptionMessage.message.documentMessage.mimetype;
        }

        const contactJid = message.key.remoteJid;
        let contactName = pushName || contactJid.split("@")[0];

        // Periksa apakah pesan ini adalah reply ke pesan lain
        if (messageUpdate.type == "notify") {
            let quotedMessage = null;
            const contextInfo =
                msg?.extendedTextMessage?.contextInfo || msg?.contextInfo;
            if (contextInfo && contextInfo?.quotedMessage) {
                const quotedMsg = contextInfo.quotedMessage;
                quotedStanzaId = contextInfo.stanzaId;
                quotedParticipant = contextInfo.participant; // JID pengirim pesan yang di-quote

                if (quotedMsg.conversation) {
                    quotedMessageContent = quotedMsg.conversation;
                    quotedMessageType = "text";
                } else if (quotedMsg.extendedTextMessage) {
                    quotedMessageContent = quotedMsg.extendedTextMessage.text;
                    quotedMessageType = "text";
                } else if (quotedMsg.imageMessage) {
                    quotedMessageContent =
                        quotedMsg.imageMessage.caption || "Image";
                    quotedMessageType = "image";
                } else if (quotedMsg.videoMessage) {
                    quotedMessageContent =
                        quotedMsg.videoMessage.caption || "Video";
                    quotedMessageType = "video";
                } else if (quotedMsg.documentMessage) {
                    quotedMessageContent =
                        quotedMsg.documentMessage.fileName || "Document";
                    quotedMessageType = "document";
                } else if (quotedMsg.audioMessage) {
                    quotedMessageContent = "Audio";
                    quotedMessageType = "audio";
                } else if (quotedMsg.stickerMessage) {
                    quotedMessageContent = "Sticker";
                    quotedMessageType = "sticker";
                } else if (quotedMsg.locationMessage) {
                    quotedMessageContent = "Location";
                    quotedMessageType = "location";
                } else if (quotedMsg.contactMessage) {
                    quotedMessageContent =
                        quotedMsg.contactMessage.displayName || "Contact";
                    quotedMessageType = "contact";
                } else {
                    quotedMessageContent = "[Unsupported Quoted Message]";
                    quotedMessageType = "unknown";
                }
            }
        }

        if (!message.key.fromMe && messageUpdate.type === "notify") {
            let avatarUrl = null;

            try { 
                if (
                    contactJid &&
                    contactJid !== "status@broadcast" &&
                    contactJid.endsWith("@s.whatsapp.net") 
                ) { 
                    avatarUrl = await sock.profilePictureUrl(
                        contactJid,
                        "image"
                    );
                }
            } catch (e) {
                console.log(
                    `Gagal mengambil foto profil untuk ${contactJid}: ${e.message}. Membuat fallback.`
                );
            }

            if (
                !avatarUrl &&
                contactName &&
                contactJid &&
                !contactJid.endsWith("@g.us")
            ) {
                const encodedName = encodeURIComponent(contactName);
                avatarUrl = `https://ui-avatars.com/api/?name=${encodedName}&background=random&size=128`;
            }

            const responseMessage = {};

            let isGroup = message.key.remoteJid.endsWith("@g.us");
            var statusMedia = false;

            if (isMediaMessage) {
                var fileSizeLong = msg[`${messageType}Message`]?.fileLength;
                var fileSize = fileSizeLong ? fileSizeLong.toNumber() : 0;
                statusMedia = true;
                if (
                    fileSize >
                    (parseInt(process.env.MAX_FILE_SIZE_MB) || 2) * 1024 * 1024
                ) {
                    statusMedia = false;
                }
            }

            if (statusMedia) {
                const buffer = await downloadMediaMessage(
                    message,
                    "buffer",
                    {}
                );
                mimeType =
                    messageType != "documentWithCaption"
                        ? msg[`${messageType}Message`]?.mimetype
                        : mimeType;

                if (buffer && mimeType) {
                    const stream = Readable.from(buffer);
                    const extension =
                        mimeType.split("/")[1]?.split(";")[0] || "bin";
                    fileName = `${Date.now()}.${extension}`;
                    const formData = new FormData();

                    formData.append("from", key.remoteJid);
                    formData.append("message_id", key.id);
                    formData.append("message", messageContent);
                    formData.append("from_name", contactName || "");
                    formData.append("file", stream, fileName);
                    formData.append("type", isGroup ? "group" : "single");
                    formData.append(
                        "quoted_message",
                        message ? JSON.stringify(message) : ""
                    );
                    formData.append("stanzaId", quotedStanzaId || "");

                    responseMessage.remote_id = message.key.remoteJid;
                    responseMessage.message_id = message.key.id;
                    responseMessage.jid = message.key.senderLid;
                    sentWebHookMedia(
                        sessionId,
                        formData,
                        responseMessage,
                        avatarUrl
                    );
                }
            } else {
                responseMessage.remote_id = message.key.remoteJid;
                responseMessage.sessionId = sessionId;
                responseMessage.message_id = message.key.id;
                responseMessage.message = messageContent;
                responseMessage.from = key.remoteJid;
                responseMessage.from_name = contactName || "";
                responseMessage.quoted_message = message;
                responseMessage.stanzaId = quotedStanzaId;
                responseMessage.jid = message.key.senderLid;
                sentWebHook(
                    sessionId,
                    responseMessage,
                    isGroup ? "group" : "single",
                    avatarUrl
                );
            }
        }

        if (message.key.fromMe && messageUpdate.type == "notify") {
            const responseMessage = {};

            let isGroup = message.key.remoteJid.endsWith("@g.us");
            var statusMedia = false;

            if (isMediaMessage) {
                var fileSizeLong = msg[`${messageType}Message`]?.fileLength;
                var fileSize = fileSizeLong ? fileSizeLong.toNumber() : 0;
                statusMedia = true;
                if (
                    fileSize >
                    (parseInt(process.env.MAX_FILE_SIZE_MB) || 2) * 1024 * 1024
                ) {
                    statusMedia = false;
                }
            }

            if (statusMedia) {
                const buffer = await downloadMediaMessage(
                    message,
                    "buffer",
                    {}
                );
                mimeType =
                    messageType != "documentWithCaption"
                        ? msg[`${messageType}Message`]?.mimetype
                        : mimeType;

                if (buffer && mimeType) {
                    const stream = Readable.from(buffer);
                    const extension =
                        mimeType.split("/")[1]?.split(";")[0] || "bin";
                    fileName = `${Date.now()}.${extension}`;
                    const formData = new FormData();

                    formData.append("from", message.key.remoteJid);
                    formData.append("message_id", key.id);
                    formData.append("message", messageContent);
                    formData.append("from_name", contactName || "");
                    formData.append("file", stream, fileName);
                    formData.append("type", isGroup ? "group" : "single");
                    formData.append(
                        "quoted_message",
                        message ? JSON.stringify(message) : ""
                    );
                    formData.append("stanzaId", String(quotedStanzaId ?? ""));

                    sendFormeMedia(sessionId, formData);
                }
            } else {
                responseMessage.remote_id = message.key.remoteJid;
                responseMessage.sessionId = sessionId;
                responseMessage.message_id = message.key.id;
                responseMessage.message = messageContent;
                responseMessage.from_name = contactName || "";
                responseMessage.from = message.key.remoteJid;
                responseMessage.quoted_message = message;
                responseMessage.stanzaId = quotedStanzaId;

                sendForme(
                    sessionId,
                    responseMessage,
                    isGroup ? "group" : "single"
                );
            }
        }
    } catch (err) {
        console.log("Ada Error", err);
    }
}

// Set device status via API
const setDeviceStatus = (deviceId, status) => {
    const apiUrl = `${process.env.APP_URL}/api-app/whatsapp/set-status/${deviceId}/${status}`;
    try {
        axios
            .post(apiUrl, {})
            .then(function (response) {})
            .catch(function (error) {
                console.log(error, "set status device");
            });
    } catch {}
};

// Send a webhook notification
const sentWebHook = async (
    sessionId,
    messageDetails,
    type,
    avatarUrl = null
) => {
    const webhookUrl = `${process.env.APP_URL}/api-app/whatsapp/callback/${sessionId}`;
    try {
        const response = await axios.post(webhookUrl, {
            from: messageDetails.remote_id,
            message_id: messageDetails.message_id,
            message: messageDetails.message,
            from_name: messageDetails.from_name,
            avatar_url: avatarUrl,
            type: type,
            quoted_message: messageDetails.quoted_message,
            stanzaId: messageDetails.stanzaId,
            jid: messageDetails.jid,
        });

        if (response.status === 200) {
            const session = sessions.get(response.data.session_id) ?? null;

            if (response.data.reply && session) {
                if (response.data.autoread) {
                    await session.readMessages([
                        {
                            remoteJid: messageDetails.remote_id,
                            id: messageDetails.message_id,
                        },
                    ]);
                }

                if (response.data.replydata) {
                    response.data.message.replydata = response.data.replydata;
                }

                const resReply = await sendMessage(
                    session,
                    response.data.receiver,
                    response.data.message,
                    null,
                    response.data.delay == 0 ? 1000 : response.data.delay
                );

                // if (
                //     response.data?.chatid != undefined &&
                //     response.data?.chatid != null
                // ) {
                //     await axios.post(
                //         `${process.env.APP_URL}/api-app/whatsapp/update-detail-chat/${response.data.chatid}`,
                //         resReply
                //     );
                // }
            }
        }
    } catch (error) {
        console.log("error webhook", error);
    }
};

// Send webhook with media
const sentWebHookMedia = async (
    sessionId,
    data,
    messageDetails,
    avatarUrl = null
) => {
    const webhookUrl = `${process.env.APP_URL}/api-app/whatsapp/callback/${sessionId}`;
    try {
        if (avatarUrl) {
            data.append("avatar_url", avatarUrl);
        }

        const response = await axios.post(webhookUrl, data, {
            headers: data.getHeaders(),
        });

        if (response.status === 200) {
            const session = sessions.get(response.data.session_id) ?? null;

            if (response.data.reply && session) {
                if (response.data.autoread) {
                    await session.readMessages([
                        {
                            remoteJid: messageDetails.remote_id,
                            id: messageDetails.message_id,
                        },
                    ]);
                }

                await sendMessage(
                    session,
                    response.data.receiver,
                    response.data.message,
                    null,
                    response.data.delay == 0 ? 1000 : response.data.delay
                );
            }
        }
    } catch (error) {
        console.log("error webhook media", error);
    }
};

// Send webhook for messages from me
const sendForme = async (sessionId, messageDetails, type) => {
    const webhookUrl = `${process.env.APP_URL}/api-app/whatsapp/to-me/${sessionId}`;
    try {
        await axios.post(webhookUrl, {
            from: messageDetails.remote_id,
            message_id: messageDetails.message_id,
            message: messageDetails.message,
            from_name: messageDetails.from_name,
            type: type,
        });
    } catch (error) {
        console.log("error webhook to-me", error);
    }
};

const sendFormeMedia = async (sessionId, data) => {
    const webhookUrl = `${process.env.APP_URL}/api-app/whatsapp/to-me/${sessionId}`;
    try {
        await axios.post(webhookUrl, data, {
            headers: data.getHeaders(),
        });
    } catch (error) {
        console.log("error webhook to-me media", error);
    }
};

// Delete a session
const deleteSession = (sessionId, isLegacy = false) => {
    const sessionFile =
        (isLegacy ? "legacy_" : "md_") + sessionId + (isLegacy ? ".json" : "");
    const options = { force: true, recursive: true };
    rmSync(getSessionDir(sessionFile), options);
    sessions.delete(sessionId);
    retries.delete(sessionId);
    setDeviceStatus(sessionId, 0);
};

// Get session by ID
const getSession = (sessionId) => {
    return sessions.get(sessionId) ?? null;
};

// Get chat list for a session
const getChatList = (sessionId, isGroup = false, lastChat = null) => {
    const session = sessions.get(sessionId);
    if (!session || !session.chats) return [];

    const chatSuffix = isGroup ? "@g.us" : "@s.whatsapp.net";
    const chatsArray = Array.from(session.chats.values());

    return chatsArray.filter((chat) => {
        const timestamp =
            typeof chat.conversationTimestamp === "number"
                ? chat.conversationTimestamp
                : chat.conversationTimestamp?.low
                ? chat.conversationTimestamp.low
                : chat.conversationTimestamp?.low?.low
                ? chat.conversationTimestamp.low.low
                : null;

        return (
            chat.id.endsWith(chatSuffix) &&
            timestamp &&
            (lastChat != null ? timestamp > lastChat : true)
        );
    });
};

// Get Contact List Data
const getContactList = (sessionId) => {
    const session = sessions.get(sessionId);
    return session?.contacts ?? {};
};

// Check if a contact or group exists
const isExists = async (session, id, isGroup = false) => {
    try {
        let metadata;
        if (isGroup) {
            metadata = await session.groupMetadata(id);
            return Boolean(metadata.id);
        }
        if (session.isLegacy) {
            metadata = await session.onWhatsApp(id);
        } else {
            [metadata] = await session.onWhatsApp(id);
        }
        return metadata.exists;
    } catch {
        return false;
    }
};

// Send a message
const sendMessage = async (
    session,
    receiverId,
    messageContent,
    autoReadMessage = null,
    delayMs = 1000
) => {
    try {
        await session.sendPresenceUpdate("composing", receiverId);
        await delay(parseInt(delayMs));

        if (autoReadMessage != null) {
            await session.readMessages([autoReadMessage]);
        }

        let quotedOptions = {};
        if (
            messageContent.replydata &&
            messageContent.replydata.quotedMessage
        ) {
            try {
                const quotedMsgObj =
                    typeof messageContent.replydata.quotedMessage === "string"
                        ? JSON.parse(messageContent.replydata.quotedMessage)
                        : messageContent.replydata.quotedMessage;
                quotedOptions = { quoted: quotedMsgObj };
            } catch (e) {
                console.error("Failed to parse quotedMessage:", e);
            }
        }

        return await session.sendMessage(
            receiverId,
            messageContent,
            quotedOptions
        );
    } catch (error) {
        console.error("Error sending message:", error);
        return Promise.reject(error);
    }
};

// Read Message
const changeStatusMessage = async (session, messages, jid) => {
    try {
        await session.readMessages(messages);
        await modifyChat(session, jid, true);
    } catch (err) {
        return Promise.reject(err);
    }
};

const getLastChat = async (session, jid) => {
    try {
        let messages;
        if (session.isLegacy) {
            messages = await session.fetchMessagesFromWA(jid, 1, null);
        } else {
            // Fetch messages directly without store
            messages = await session.fetchMessageHistory(1, jid);
        }
        return messages;
    } catch (err) {
        console.log(err, "last chat error");
        return [];
    }
};

// Read or Unread Chat
const modifyChat = async (session, jid, status = false) => {
    try {
        const lastMessages = await getLastChat(session, jid);
        if (lastMessages.length > 0) {
            return await session.chatModify(
                { markRead: status, lastMessages: [lastMessages[0]] },
                jid
            );
        }
    } catch (err) {
        console.log(err, "modify error");
        return Promise.reject(err);
    }
};

// Download media or document
const downloadMediaorDoc = async (
    session,
    messageContent,
    mimes,
    medianame
) => {
    try {
        const buffer = await downloadMediaMessage(
            messageContent,
            "buffer",
            {},
            {
                reuploadRequest: session.updateMediaMessage,
            }
        );
        await writeFile(
            `./public/uploads/wamedia/${medianame}.${mimes}`,
            buffer
        );
        return {
            path: `${process.env.APP_URL}/uploads/wamedia/${medianame}.${mimes}`,
        };
    } catch (error) {
        return Promise.reject(null);
    }
};

// Get profile picture URL
const getPhotoProfileUrl = async (session, phone) => {
    try {
        const ppUrl = await session.profilePictureUrl(phone, "image");
        return {
            url: ppUrl,
            id: phone,
        };
    } catch (error) {
        return Promise.reject(null);
    }
};

// Format phone number for WhatsApp
const formatPhone = (phone) => {
    if (phone.includes("@")) {
        return phone;
    }

    let cleanedPhone = phone.replace(/\D/g, "");

    return cleanedPhone + "@s.whatsapp.net";
};

// Format Group for WhatsApp
const formatGroup = (group) => {
    if (group.endsWith("@g.us")) {
        return group;
    }
    let cleanedGroup = group.replace(/[^\d-]/g, "");
    return cleanedGroup + "@g.us";
};

// Delete message
const deleteMessage = async (session, jid, message) => {
    try {
        await session.chatModify({ deleteForMe: message }, jid);
    } catch (err) {
        console.log(err, "delete for me");
        return Promise.reject(err);
    }
};

const deleteEveryOne = async (session, jid, message) => {
    try {
        await session.sendMessage(jid, { delete: message });
    } catch (err) {
        console.log(err, "delete everyone");
        return Promise.reject(err);
    }
};

// Delete chat
const deleteChat = async (session, jid) => {
    try {
        const lastMessage = await getLastChat(session, jid);
        if (lastMessage.length > 0) {
            const lastMsgInChat = lastMessage[0];
            await session.chatModify(
                {
                    delete: true,
                    lastMessages: [
                        {
                            key: lastMsgInChat.key,
                            messageTimestamp: lastMsgInChat.messageTimestamp,
                        },
                    ],
                },
                jid
            );
        }
    } catch (err) {
        console.log(err, "delete chat");
        return Promise.reject(err);
    }
};

// Get group members
async function getGroupMembers(session, dataid, business) {
    try {
        const groups = await session.groupFetchAllParticipating();
        const group = groups[dataid];
        const metadata = await session.groupMetadata(dataid);

        const membersRaw = await Promise.all(
            metadata.participants.map(async (p) => ({
                wa_id: p.id.split("@")[0],
                name: await getName(session, p.id),
            }))
        );

        const chunkSize = 50;
        for (let i = 0; i < membersRaw.length; i += chunkSize) {
            const chunk = membersRaw.slice(i, i + chunkSize);
            await axios.post(
                `${process.env.APP_URL}/api-app/scrapping/callback/${dataid}/${business}`,
                {
                    group_id: group.id,
                    group_name: group.subject,
                    members: chunk,
                }
            );
            await new Promise((r) => setTimeout(r, 300));
        }
    } catch (err) {
        console.error("Error scraping group members:", err.message);
    }
}

// Get group info only
async function getGroupInfoOnly(session, dataid, sessionId) {
    try {
        const groups = await session.groupFetchAllParticipating();
        const groupJids = Object.keys(groups);

        for (let groupJid of groupJids) {
            const group = groups[groupJid];
            const payload = {
                group_id: group.id,
                group_name: group.subject,
                group_description: group.desc,
                session: sessionId,
            };

            await axios.post(
                `${process.env.APP_URL}/api-app/scrapping/groups/${dataid}`,
                payload
            );
            await new Promise((resolve) => setTimeout(resolve, 300));
        }
    } catch (err) {
        console.error("Error getting group info:", err.message);
    }
}

// Get contacts from device
async function getContactByDevice(session, business) {
    try {
        const contacts = session.contacts || {};
        const filteredContacts = Object.values(contacts).filter(
            (c) => !c.id.endsWith("@g.us") && c.id.endsWith("@s.whatsapp.net")
        );

        console.log(filteredContacts.length, "contacts found");
        const contactsData = await Promise.all(
            filteredContacts.map(async (c) => ({
                wa_id: c.id.split("@")[0],
                name: c.notify ?? "-",
            }))
        );

        const chunkSize = 50;
        for (let i = 0; i < contactsData.length; i += chunkSize) {
            const chunk = contactsData.slice(i, i + chunkSize);
            await axios.post(
                `${process.env.APP_URL}/api-app/scrapping/contacts/${business}`,
                { members: chunk }
            );
            await new Promise((r) => setTimeout(r, 300));
        }
    } catch (err) {
        console.error("Error getting contact list:", err.message);
    }
}

// Get contact name
async function getName(session, jid) {
    // Try to get from session contacts cache
    let name = session.contacts?.[jid]?.name || session.contacts?.[jid]?.notify;
    if (name) return name;

    // Try to fetch directly
    try {
        const contact = await session.fetchContact(jid);
        name = contact?.name || contact?.notify || contact?.pushName;

        // Cache the contact
        if (!session.contacts) {
            session.contacts = {};
        }
        if (name && contact) {
            session.contacts[jid] = contact;
        }
    } catch (e) {
        // Failed to fetch contact
    }
    return name || "";
}

// Initialize session
const initSession = async (req, res) => {
    const { sessionId, isLegacy } = req.body;
    if (!sessionId) {
        return response(res, 400, false, "Session ID is required.");
    }
    if (isSessionExists(sessionId)) {
        return response(res, 400, false, "Session already exists.");
    }
    await createSession(sessionId, isLegacy, res);
    response(res, 200, true, "Session created successfully.");
};

// Cleanup function
const cleanup = () => {
    console.log("Cleaning up sessions...");
    // No need to write store files anymore since we don't use makeInMemoryStore
};

// Initialize all sessions on startup
const init = () => {
    readdir(sessionsDir(), (error, files) => {
        if (error) {
            throw error;
        }
        for (const file of files) {
            if (
                (!file.startsWith("md_") && !file.startsWith("legacy_")) ||
                file.endsWith("_store")
            ) {
                continue;
            }
            const sessionId = file.replace(".json", "");
            const isLegacy = sessionId.split("_")[0] !== "md";
            const cleanedSessionId = sessionId.substring(isLegacy ? 7 : 3);
            createSession(cleanedSessionId, isLegacy);
        }
    });
};

const checkNumber = async (receiver) => {
    // const { token: token, number: number } = requestData.body
    // if (token && number) {
    //   const isExistResult = await wa.isExist(token, number)
    //   return (
    //     console.log(isExistResult),
    //     responseHandler.send({
    //       status: true,
    //       active: isExistResult,
    //     })
    //   )
    // }
    // responseHandler.send({
    //   status: false,
    //   message: 'Check your parameter',
    // })
};

export {
    isSessionExists,
    createSession,
    getSession,
    deleteSession,
    getChatList,
    isExists,
    sendMessage,
    formatPhone,
    formatGroup,
    cleanup,
    init,
    downloadMediaorDoc,
    getPhotoProfileUrl,
    changeStatusMessage,
    modifyChat,
    deleteMessage,
    deleteChat,
    deleteEveryOne,
    getContactList,
    getGroupMembers,
    getGroupInfoOnly,
    getContactByDevice,
    checkNumber,
};
