import dirname from "./dirname.js";
import response from "./response.js";
import axios from "axios";

const saveOrUpdateInformation = (name, phone, id, callback) => {
    const url = `${process.env.APP_URL}/api-app/chats/save-or-update/${id}`;
    try {
        axios
            .post(url, { name: name, phone: phone })
            .then(function (res) {
                if (res.status === 200) {
                    callback(null, res.data);
                } else {
                    callback(new Error(`Unexpected status: ${res.status}`));
                }
            })
            .catch(function (error) {
                console.error("[backend] saveOrUpdateInformation error:", error.message || error);
                callback(error);
            });
    } catch (e) {
        console.error("[backend] saveOrUpdateInformation unexpected:", e.message || e);
        callback(e);
    }
};

const getHistories = (id, callback) => {
    const url = `${process.env.APP_URL}/api-app/chats/histories/${id}`;
    try {
        axios
            .get(url)
            .then(function (res) {
                if (res.status === 200) {
                    callback(null, res.data.data);
                } else {
                    callback(new Error(`Unexpected status: ${res.status}`));
                }
            })
            .catch(function (error) {
                console.error("[backend] getHistories error:", error.message || error);
                callback(error);
            });
    } catch (e) {
        console.error("[backend] getHistories unexpected:", e.message || e);
        callback(e);
    }
};

const sendMessage = (message, id, file, callback) => {
    const url = `${process.env.APP_URL}/api-app/chats/message/received/${id}`;
    try {
        const formData = new FormData();
        formData.append("message", message);

        if (file) {
            const fileBlob = new Blob([file.chunk], { type: file.mimetype });
            formData.append("file", fileBlob, file.fileName);
        }

        axios
            .post(url, formData)
            .then(function (res) {
                if (res.status === 200) {
                    callback(null, res.data);
                } else {
                    callback(new Error(`Unexpected status: ${res.status}`));
                }
            })
            .catch(function (error) {
                console.error("[backend] sendMessage error:", error.message || error);
                callback(error);
            });
    } catch (e) {
        console.error("[backend] sendMessage unexpected:", e.message || e);
        callback(e);
    }
};

const sendCallback = (message, id, callback) => {
    const url = `${process.env.APP_URL}/api-app/chats/message/callback/${id}`;
    try {
        axios
            .post(url, { message: message })
            .then(function (res) {
                if (res.status === 200) {
                    callback(null, res.data);
                } else {
                    callback(new Error(`Unexpected status: ${res.status}`));
                }
            })
            .catch(function (error) {
                console.error("[backend] sendCallback error:", error.message || error);
                callback(error);
            });
    } catch (e) {
        console.error("[backend] sendCallback unexpected:", e.message || e);
        callback(e);
    }
};

export {
    saveOrUpdateInformation,
    getHistories,
    sendMessage,
    sendCallback
};
