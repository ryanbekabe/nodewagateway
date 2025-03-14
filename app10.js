const express = require("express");
const { body, validationResult } = require("express-validator");
const { default: makeWASocket, useMultiFileAuthState } = require("@whiskeysockets/baileys");
const axios = require("axios");
const moment = require("moment");
const pino = require("pino");
const qrcode = require("qrcode-terminal");
const http = require("http");
const crypto = require("crypto");

const app = express();
const server = http.createServer(app);

app.use(express.json());

let users = {}; // Menyimpan data pengguna berdasarkan nomor mereka
let tokens = {}; // Menyimpan token dan nomor pemiliknya

function generateToken(number) {
    return crypto.createHash("sha256").update(number).digest("hex");
}

async function startWhatsApp(userId) {
    const { state, saveCreds } = await useMultiFileAuthState(`./auth_info_${userId}`);
    const sock = makeWASocket({
        auth: state,
        logger: pino({ level: "silent" }),
        printQRInTerminal: false
    });

    sock.ev.on("creds.update", saveCreds);
    sock.ev.on("connection.update", async ({ qr, connection, lastDisconnect }) => {
        if (qr) {
            console.log(`Scan QR Code untuk user ${userId}:`);
            qrcode.generate(qr, { small: true });
        }
        if (connection === "open") {
            if (!users[userId].token) {
                const token = generateToken(userId);
                users[userId].token = token;
                tokens[token] = userId;
                console.log(`User ${userId} berhasil login. Token: ${token}`);
            }
            users[userId].isConnected = true;
            console.log(`WhatsApp Connected for ${userId}`);
        }
        if (connection === "close") {
            users[userId].isConnected = false;
            console.log(`WhatsApp Disconnected for ${userId}, Reconnecting...`);
            startWhatsApp(userId);
        }
    });
    users[userId].sock = sock;
}

app.post("/register", [
    body("number").notEmpty().withMessage("Number is required")
], async (req, res) => {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
    }
    
    const { number } = req.body;
    if (users[number]) {
        return res.status(400).json({ status: false, message: "User already registered" });
    }
    
    users[number] = { isConnected: false };
    startWhatsApp(number);
    res.json({ status: true, message: "User registered successfully. Scan the QR code to login." });
});

app.post("/send-message", [
    body("token").notEmpty().withMessage("Token is required"),
    body("number").notEmpty().withMessage("Target number is required"),
    body("message").notEmpty().withMessage("Message is required")
], async (req, res) => {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
    }
    
    const { token, number, message } = req.body;
    const userNumber = tokens[token];
    if (!userNumber || !users[userNumber] || users[userNumber].token !== token) {
        return res.status(403).json({ status: false, message: "Invalid token or user not registered" });
    }
    
    if (!users[userNumber].isConnected) {
        return res.status(500).json({ status: false, message: "WhatsApp is not connected for this user" });
    }
    
    try {
        await users[userNumber].sock.sendMessage(number + "@s.whatsapp.net", { text: message });
        res.json({ status: true, message: "Message sent successfully" });
    } catch (error) {
        res.status(500).json({ status: false, message: "Failed to send message", error });
    }
});

server.listen(3000, () => {
    console.log("Server is running on port 3000");
});
