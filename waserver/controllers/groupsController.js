"use strict";

import {
    getSession,
    getChatList,
    isExists,
    sendMessage,
    formatGroup,
    getGroupMembers,
    getGroupInfoOnly,
} from "../whatsapp.js";
import response from "../response.js";

const getList = (req, res) => {
    return response(
        res,
        200,
        true,
        "",
        getChatList(res.locals.sessionId, true)
    );
};

const getGroupData = async (req, res) => {
    const session = getSession(res.locals.sessionId);
    const { id } = req.params;
    const { business } = req.query;


    if (!session) {
        return res
            .status(404)
            .json({ success: false, message: "Session not found" });
    }

    console.log("ada session");

    // Kirim response langsung ke user
    res.json({ success: true, message: "Scraping started in background" });

    // Jalankan proses di background
    getGroupMembers(session, id, business); // error-nya ditangani di dalam fungsi itu
};

const getGroups = async (req, res) => {
    const session = getSession(res.locals.sessionId);
    const { id } = req.params;

    if (!session) {
        return res
            .status(404)
            .json({ success: false, message: "Session not found" });
    }

    console.log("ada session");

    // Kirim response langsung ke user
    res.json({ success: true, message: "Scraping started in background" });

    // Jalankan proses di background
    getGroupInfoOnly(session, id,res.locals.sessionId); // error-nya ditangani di dalam fungsi itu
};

const getGroupMetaData = async (req, res) => {
    const session = getSession(res.locals.sessionId);
    const { jid } = req.params;

    try {
        const data = await session.groupMetadata(jid);

        if (!data.id) {
            return response(res, 400, false, "The group is not exists.");
        }

        response(res, 200, true, "", data);
    } catch {
        response(res, 500, false, "Failed to get group metadata.");
    }
};

const send = async (req, res) => {
    const session = getSession(res.locals.sessionId);
    const receiver = formatGroup(req.body.receiver);
    const { message } = req.body;

    try {
        const exists = await isExists(session, receiver, true);

        if (!exists) {
            return response(res, 400, false, "The group is not exists.");
        }

        await sendMessage(session, receiver, message);

        response(res, 200, true, "The message has been successfully sent.");
    } catch {
        response(res, 500, false, "Failed to send the message.");
    }
};

export { getList, getGroupMetaData, send, getGroupData, getGroups };
