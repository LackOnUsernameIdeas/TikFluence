const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const cors = require('cors');
const fetch = require('node-fetch');

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
  cors: {
    origin: "https://fluence.noit.eu",
    methods: ["GET", "POST"]
  }
});

io.of("/realTimeStatisticData").on('connection', (socket) => {
    console.log('Client connected');
    socket.emit('message', 'Успешна websocket връзка!');

    // Функция за взимане на данни от TikTok API.
    const fetchDataFromTikTokAPI = async (accessToken) => {
        try {
            const response = await fetch('https://open.tiktokapis.com/v2/user/info/?fields=follower_count,likes_count', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${accessToken}`
                }
            });

            if (response.ok) {
                const data = await response.json();
                socket.emit('realTimeData', data); // Send data to the client
            } else {
                socket.emit('message', 'Error fetching data from TikTok API');
            }
        } catch (error) {
            socket.emit('message', 'Error fetching data: ' + error.message);
        }
    }

    //Слушаме за 'sendAccessToken' event от браузъра
    socket.on('sendAccessToken', (accessToken) => {
        let requestCount = 0;
 
        const intervalFunction = setInterval(() => {
            requestCount++;
            if(requestCount >= 10) {
                clearInterval(intervalFunction);
                return;
            }
            fetchDataFromTikTokAPI(accessToken);
        }, 60000);
    });    

    socket.on('disconnect', () => {
        console.log('Client disconnected');
    });
});

server.listen();