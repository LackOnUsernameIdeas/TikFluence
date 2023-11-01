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
    socket.emit('message', 'Hello from the server!');

    // Function to fetch data from TikTok API using HTTP requests
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
          socket.emit('message', data); // Send data to the client
        } else {
          socket.emit('message', 'Error fetching data from TikTok API');
        }
      } catch (error) {
          socket.emit('message', 'Error fetching data: ' + error.message);
      }
    }

    // Listen for the 'sendAccessToken' event from the client
    socket.on('sendAccessToken', (accessToken) => {
      setInterval(() => {
        fetchDataFromTikTokAPI(accessToken);
      }, 6000);
    });    

    socket.on('disconnect', () => {
      console.log('Client disconnected');
    });
});

server.listen();