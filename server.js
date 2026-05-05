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

const host = process.env.SOCKET_HOST || undefined;
const port = parseInt(process.env.SOCKET_PORT ?? 8000);

const hostexpress = process.env.EXPRESS_HOST || undefined;
const portexpress = parseInt(process.env.EXPRESS_PORT ?? 8000);

// Buat HTTP server
const server = http.createServer();

// Buat Socket.IO server dan kaitkan dengan HTTP server
const io = new Server(server, {
    cors: {
        origin: "*", // Izinkan akses dari semua domain
    },
    pingInterval: 5000, // Kirim ping setiap 5 detik
    pingTimeout: 10000, // Timeout jika tidak ada respons setelah 10 detik
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
                            console.log(
                                "Gagal mengirim callback:",
                                error.message
                            );
                        } else {
                            if (resultdata) {
                                socket.emit("agent-stop-typing");
                                socket.emit("receive-message", resultdata);
                                io.emit("update-chat-list", resultdata);
                                io.emit(
                                    `update-message-${resultdata.conversation_id}`,
                                    resultdata
                                );
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
app.use(express.json()); // Parsing JSON pada request body

// Endpoint HTTP untuk trigger event WhatsApp
app.post("/trigger-whatsapp", (req, res) => {
    const data = req.body;

    // Pastikan data memiliki conversation_id atau properti lain yang diperlukan
    if (!data || !data.conversation_id) {
        return res.status(400).json({
            status: "error",
            message: "conversation_id diperlukan",
        });
    }

    // Emit event ke semua client yang tersambung
    io.emit("update-chat-list", data);
    io.emit(`update-message-${data.conversation_id}`, data);

    res.json({
        status: "success",
        message: "Event WhatsApp berhasil dipicu",
    });
});

// Menjalankan Express server pada port 3000
app.listen(portexpress, hostexpress, () => {
    console.log(
        `Socket.IO server running on http://${hostexpress}:${portexpress}`
    );
});
