import express from "express";
import { Server } from "socket.io";
import http from "http";
import "dotenv/config";
import {
    saveOrUpdateInformation,
    getHistories,
    sendMessage,
    sendCallback,
} from "./websockets/backend.js";
import response from "./websockets/response.js";

// Bind to localhost by default — never expose socket/express to internet directly
const host        = process.env.SOCKET_HOST   || "127.0.0.1";
const port        = parseInt(process.env.SOCKET_PORT   ?? 8000);
const hostexpress = process.env.EXPRESS_HOST  || "127.0.0.1";
const portexpress = parseInt(process.env.EXPRESS_PORT  ?? 8001);

// ── API Key auth middleware for Express endpoints ──
const triggerAuth = (req, res, next) => {
    const expectedKey = process.env.TRIGGER_API_KEY;
    if (!expectedKey) {
        console.error("[triggerAuth] TRIGGER_API_KEY not set — refusing request (fail-closed)");
        return res.status(503).json({ status: "error", message: "Service unavailable: auth not configured" });
    }
    const provided = req.headers["x-api-key"];
    if (!provided || provided !== expectedKey) {
        return res.status(401).json({ status: "error", message: "Unauthorized" });
    }
    next();
};

// Buat HTTP server
const server = http.createServer();

// Buat Socket.IO server dan kaitkan dengan HTTP server
const io = new Server(server, {
    cors: {
        origin: process.env.SOCKET_ALLOWED_ORIGINS
            ? process.env.SOCKET_ALLOWED_ORIGINS.split(",").map(o => o.trim())
            : ["https://chat.replai.id"],
    },
    pingInterval: 5000,
    pingTimeout: 10000,
});

io.on("connection", (socket) => {
    socket.on("storeClientInfo", (data, token) => {
        saveOrUpdateInformation(
            data.name,
            data.phone_number,
            token,
            (err, result) => {
                if (err) {
                    console.log("Gagal menyimpan data:", err.message);
                } else {
                    socket.emit("set-conversation", result.id, result.id);
                }
            }
        );
    });

    socket.on("request-history", (convId) => {
        getHistories(convId, (err, result) => {
            if (err) {
                console.log("Gagal mengambil history:", err.message);
            } else {
                socket.emit("history", { data: result });
            }
        });
    });

    socket.on("send-message", (message, data, file, inbox) => {
        sendMessage(message, data.conversationId, file, (err, result) => {
            if (err) {
                console.log("Gagal mengirim pesan:", err.message);
            } else {
                socket.emit("receive-message", result);
                io.emit("update-chat-list", result);
                io.emit(`update-message-${result.conversation_id}`, result);
                socket.emit("agent-typing");
                sendCallback(
                    message,
                    data.conversationId,
                    (error, resultdata) => {
                        if (error) {
                            socket.emit("agent-stop-typing");
                            console.log("Gagal mengirim callback:", error.message);
                        } else {
                            if (resultdata) {
                                socket.emit("agent-stop-typing");
                                socket.emit("receive-message", resultdata);
                                io.emit("update-chat-list", resultdata);
                                io.emit(`update-message-${resultdata.conversation_id}`, resultdata);
                            } else {
                                socket.emit("agent-stop-typing");
                            }
                        }
                    }
                );
            }
        });
    });

    socket.on("crm-update", (message) => {
        io.emit("update-chat-list", message);
        io.emit(`update-message-${message.conversation_id}`, message);
        io.emit("agent-typing");
        io.emit("receive-message", message);
        io.emit("agent-stop-typing");
    });

    socket.on("disconnect", () => {
        console.log("User disconnected:", socket.id);
    });
});

server.listen(port, host, () => {
    console.log(`Socket.IO server running on http://${host}:${port}`);
});

// Membuat HTTP endpoint menggunakan Express
const app = express();
app.use(express.json());

// Endpoint HTTP untuk trigger event WhatsApp — protected by API key
app.post("/trigger-whatsapp", triggerAuth, (req, res) => {
    const data = req.body;

    if (!data || !data.conversation_id) {
        return res.status(400).json({
            status: "error",
            message: "conversation_id diperlukan",
        });
    }

    io.emit("update-chat-list", data);
    io.emit(`update-message-${data.conversation_id}`, data);

    res.json({
        status: "success",
        message: "Event WhatsApp berhasil dipicu",
    });
});

app.post('/trigger-takeover', authenticate, (req, res) => {
    const data = req.body;
    // Emit ke semua agen — frontend filter by merchant_id
    io.emit('takeover-changed', data);
    res.json({ status: 'ok' });
});



app.listen(portexpress, hostexpress, () => {
    console.log(`Express server running on http://${hostexpress}:${portexpress}`);
});

