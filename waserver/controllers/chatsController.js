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
    getContactList,
} from "../whatsapp.js";
import response from "../response.js";

const getList = (req, res) => {
    var isGroup = req.query.isgroup ?? "";
    var lastChat = req.query.last_chat ?? null;
    lastChat = lastChat != null && lastChat != "" ? parseInt(lastChat) : null;
    isGroup = isGroup == "true" ? true : false;
    return response(
        res,
        200,
        true,
        "",
        getChatList(res.locals.sessionId, isGroup, lastChat)
    );
};

const getContacts = (req, res) => {
    return response(res, 200, true, "", getContactList(res.locals.sessionId));
};

const checkingNumber = (req, res) => {
    return response(res, 200, true, "", checkNumber(req.body.receiver));
};

 const send = async (req, res) => {
    try {
        const session = getSession(req.query.id);
        if (!session) return response(res, 404, false, 'Session not found');

        const type = req.body.isgroup ?? false; 
        const receiver = !type ?  formatPhone(req.body.receiver) : formatGroup(req.body.receiver);
 
        if (!type && !receiver.includes('@lid')) {
            const isRegistered = await isExists(session, receiver, false);
            
            if (!isRegistered) {
                return response(res, 400, false, 'Receiver not exists');
            }
            
            // Ambil JID yang benar jika ada
            if (typeof isRegistered === 'object') {
                if (Array.isArray(isRegistered) && isRegistered.length > 0) {
                    receiver = isRegistered[0].jid || isRegistered[0];
                } else if (isRegistered.jid) {
                    receiver = isRegistered.jid;
                }
            }
        } else if (type) {
            // Validasi untuk group
            const isRegistered = await isExists(session, receiver, true);
            if (!isRegistered) {
                return response(res, 400, false, 'Group not exists');
            }
        }

         

        // Jika ada replydata, tambahkan ke messageContent
        if (req.body.replydata) {
            req.body.message.replydata = req.body.replydata;
        }

        const sentMessage = await sendMessage(
            session, 
            receiver, 
            req.body.message, 
            null, 
            req.body.delay || 1000
        ); 

        return response(res, 200, true, 'Message sent successfully', sentMessage);
    } catch (error) {
        console.log('gagal mengirim pesan', {
              error: error.message,
            stack: error.stack,
            sessionId: req.query.id
        })
        
        
        return response(res, 500, false, 'Failed to send message', error);
    }
}

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
    getContacts,
};
