import {
    getSession,
    getChatList,
    isExists,
    sendMessage,
    formatPhone,
    checkNumber,
    downloadMediaorDoc,
    getPhotoProfileUrl,
    changeStatusMessage,
    modifyChat,
    deleteMessage,
    deleteChat,
    deleteEveryOne,
    formatGroup,
    getContactList
} from "../whatsapp.js";
import response from "../response.js";

const getList = (req, res) => {
    var isGroup  = req.query.isgroup ?? '';
    var lastChat = req.query.last_chat ?? null;
    lastChat = lastChat != null && lastChat != '' ? parseInt(lastChat) : null;
    isGroup = isGroup == 'true' ? true : false; 
    return response(res, 200, true, "", getChatList(res.locals.sessionId,isGroup,lastChat));
};

const getContacts = (req, res) => {  
    return response(res, 200, true, "", getContactList(res.locals.sessionId));
};

const checkingNumber = (req, res) => {
    return response(res, 200, true, "", checkNumber(req.body.receiver));
};

const send = async (req, res) => {
    const type = req.body.isgroup ?? false;
    const session = getSession(res.locals.sessionId);
    const receiver = !type ?  formatPhone(req.body.receiver) : formatGroup(req.body.receiver);
    const delay = req.body.delay;
    const { message } = req.body;

    try {
        const exists = await isExists(session, receiver,type);

        if (!exists) {
            return response(res, 400, false, "Nomor yang dituju salah.");
        }

        await sendMessage(session, receiver, message, delay);

        response(res, 200, true, "Berhasil mengirim pesan WhatsApp.");
    } catch (error) { 
        response(
            res,
            500,
            false,
            `Pengiriman pesan WhatsApp gagal: ${error.message}`
        );
    }
};

const downloadMedia = async (req, res) => {
    const session = getSession(res.locals.sessionId);
    const mimes = req.body.type;
    const medianame = req.body.medianame;
    const { message } = req.body;

    try {
        const result = await downloadMediaorDoc(
            session,
            message,
            mimes,
            medianame
        );
        if (result && result.path) {
            response(res, 200, true, "Berhasil mengunduh media", {
                downloadPath: result.path,
            });
        }
    } catch (error) {
        response(res, 500, false, `Terjasi kesalahan : `);
    }
};

const sendBulk = async (req, res) => {
    const session = getSession(res.locals.sessionId);
    const errors = [];

    for (const [key, data] of req.body.entries()) {
        let { receiver, message, delay } = data;

        if (!receiver || !message) {
            errors.push(key);

            continue;
        }

        if (!delay || isNaN(delay)) {
            delay = 1000;
        }

        receiver = formatPhone(receiver);

        try {
            const exists = await isExists(session, receiver);

            if (!exists) {
                errors.push(key);

                continue;
            }

            await sendMessage(session, receiver, message, delay);
        } catch {
            errors.push(key);
        }
    }

    if (errors.length === 0) {
        return response(
            res,
            200,
            true,
            "All messages has been successfully sent."
        );
    }

    const isAllFailed = errors.length === req.body.length;

    response(
        res,
        isAllFailed ? 500 : 200,
        !isAllFailed,
        isAllFailed
            ? "Failed to send all messages."
            : "Some messages has been successfully sent.",
        { errors }
    );
};

const getPhotoProfile = async (req, res) => {
    const session = getSession(res.locals.sessionId);
    const phone = req.body.phone;
    try {
        const result = await getPhotoProfileUrl(session, phone);
        response(res, 200, true, "Berhasil mengambil photo profile", {
            url: result.url,
            phone: result.id,
        });
    } catch (error) {
        response(res, 200, false, `There is an error :`);
    }
};

const readBulkMessage = async (req, res) => {
    const session = getSession(res.locals.sessionId);
    try { 
        await changeStatusMessage(session, req.body.messages, req.body.chatid);
        response(res, 200, true, "Pesan berhasil di baca");
    } catch (error) {
        response(res, 200, false, `There is an error :`, {
            error: error,
        });
    }
}; 

const markToChat = async (req, res) => {
    const session = getSession(res.locals.sessionId);
    try { 
        await modifyChat(session, req.body.chatid, req.body.status);
        response(res, 200, true, "Pesan berhasil di baca");
    } catch (error) {
        response(res, 200, false, `There is an error :`, {
            error: error,
        });
    }
}; 

const chatDelete = async (req, res) => {
    const session = getSession(res.locals.sessionId);
    try { 
        await deleteChat(session, req.body.chatid);
        response(res, 200, true, "Chat berhasil di hapus");
    } catch (error) {
        response(res, 200, false, `There is an error :`, {
            error: error,
        });
    }
}; 

const messageDelete = async (req, res) => {
    const session = getSession(res.locals.sessionId);
    try { 
        await deleteMessage(session, req.body.chatid, req.body.message);
        response(res, 200, true, "Pesan berhasil di hapus");
    } catch (error) {
        response(res, 200, false, `There is an error :`, {
            error: error,
        });
    }
}; 

const deleteMessageEveryone = async (req, res) => {
    const session = getSession(res.locals.sessionId);
    try { 
        await deleteEveryOne(session, req.body.chatid, req.body.message);
        response(res, 200, true, "Pesan berhasil di hapus");
    } catch (error) {
        response(res, 200, false, `There is an error :`, {
            error: error,
        });
    }
}; 

export {
    getList,
    send,
    sendBulk,
    checkingNumber,
    downloadMedia,
    getPhotoProfile,
    readBulkMessage,
    markToChat,
    chatDelete,
    messageDelete,
    deleteMessageEveryone,
    getContacts
};
