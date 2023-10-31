const app = require('express');
const WebSocket = require('ws');
const webSocket = new WebSocket();
const fetch = require('node-fetch');
const nodemon = require('nodemon');
const bodyParser = require('body-parser');
const cors = require('cors');

app.use(bodyParser.json());
app.use(cors());

const server = require('http').createServer();

const wss = webSocket.Server({ "server": server });

// WebSocket connection event
wss.on('connection', (ws) => {
  console.log('WebSocket connection established.');

  ws.on('close', () => {
    console.log('WebSocket connection closed.');
  });

  ws.on('message', (message) => {
    const request = JSON.parse(message);
    const accessToken = request.accessToken;

    fetch('https://open.tiktokapis.com/v2/user/info/?fields=follower_count,likes_count', {
      headers: {
        'Authorization': `Bearer ${accessToken}`
      }
    })
      .then(response => response.json())
      .then(data => {
        ws.send(JSON.stringify(data));
      })
      .catch(error => {
        console.error('Error fetching data:', error);
      });
  });
});

server.listen();
