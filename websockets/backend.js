import dirname from "./dirname.js";
import response from "./response.js";
import axios from "axios";

const saveOrUpdateInformation = (name, phone, id, callback) => { 
    const url = `${process.env.APP_URL}/api-app/chats/save-or-update/${id}`;
    try {
        axios
            .post(url, {
                name: name,
                phone: phone
            })
            .then(function (response) {
                if (response.status === 200) {
                    callback(null, response.data); 
                }
            })
            .catch(function (error) {
                console.log("error webhook", error);
            });
    } catch {}
};

const getHistories = (id, callback) => {
    const url = `${process.env.APP_URL}/api-app/chats/histories/${id}`;
    try {
        axios
            .get(url)
            .then(function (response) {
                if (response.status === 200) {
                    callback(null, response.data.data); 
                }
            })
            .catch(function (error) {
                console.log("error webhook", error);
            });
    } catch {}
};

const sendMessage = (message, id,file, callback) => {
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
            .then(function (response) {
                if (response.status === 200) {
                    callback(null, response.data); 
                }
            })
            .catch(function (error) {
                console.log("error webhook", error);
            });
    } catch {}
};

const sendCallback = (message, id, callback) => {
    const url = `${process.env.APP_URL}/api-app/chats/message/callback/${id}`;
    try {
        axios
            .post(url, {
                message: message
            })
            .then(function (response) {
                if (response.status === 200) {
                    callback(null, response.data); 
                }
            })
            .catch(function (error) {
                console.log("error webhook", error);
            });
    } catch {}
};


export {
    saveOrUpdateInformation,
    getHistories,
    sendMessage,
    sendCallback
};
