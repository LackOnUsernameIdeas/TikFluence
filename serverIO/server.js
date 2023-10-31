const express = require('express');
const app = express();
const http = require('http');
const server = http.createServer(app);
const nodemon = require('nodemon');
const bodyParser = require('body-parser');
const fetch = require('node-fetch');
const cors = require('cors');
const { Server } = require("socket.io");
const io = new Server(server);

app.use(bodyParser.json())

app.use(
    cors({
        origin: "https://fluence.noit.eu"
    })
);

io.on('connection', (socket) => {
    console.log('A user connected');

    // Handle the real-time statistic request from the client
    socket.on('sendAccessToken', (accessToken) => {
        fetch('https://open.tiktokapis.com/v2/user/info/?fields=follower_count,likes_count', {
            headers: {
                'Authorization': `Bearer ${accessToken}`
            }
        })
        .then(response => response.json())
        .then(data => {
            // Emit the data back to the client
            socket.emit('realTimeStatisticData', data);
        })
        .catch(error => {
            // Emit the error back to the client
            socket.emit('realTimeStatisticError', error);
        });
    });

    // You can add more socket event handlers here

    // Example: Send a message to the connected client
    socket.emit('message', 'Welcome to the real-time statistics server');

    // Example: Listen for a client message and broadcast it to all clients
    socket.on('chatMessage', (message) => {
        io.emit('chatMessage', message);
    });

    // Handle disconnect
    socket.on('disconnect', () => {
        console.log('A user disconnected');
    });
});

app.listen()