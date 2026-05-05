import { io } from "socket.io-client";

const socketUrl = process.env.MIX_SOCKET_URL || '';

console.log('🔌 Initializing socket connection to:', socketUrl);

const socket = io(socketUrl, {
    transports: ["websocket"],
    reconnection: true,
    reconnectionAttempts: Infinity,
    reconnectionDelay: 1000,
    pingInterval: 5000,
    pingTimeout: 10000,
});

// Connection events
socket.on("connect", () => {
    console.log('✅ Socket connected:', socket.id);
});

socket.on("disconnect", (reason) => {
    console.warn('❌ Socket disconnected:', reason);
});

socket.on("connect_error", (error) => {
    console.error('❌ Socket connection error:', error);
});

socket.on("reconnect", (attempt) => {
    console.log(`✅ Socket reconnected after ${attempt} attempts`);
    window.dispatchEvent(new Event("socket-reconnected"));
});

socket.on("reconnect_attempt", (attempt) => {
    console.log(`🔄 Reconnection attempt ${attempt}...`);
});

socket.on("reconnect_error", (error) => {
    console.error('❌ Reconnection error:', error);
});

socket.on("reconnect_failed", () => {
    console.error('❌ Reconnection failed completely');
});

// Visibility change handling
document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "visible") {
        if (!socket.connected) {
            console.log("🔄 Tab visible, reconnecting socket...");
            socket.connect();
        } else {
            console.log("✅ Tab visible, socket already connected");
        }
    }
});

export default socket;