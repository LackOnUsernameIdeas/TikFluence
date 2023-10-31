const express = require('express');
const nodemon = require('nodemon');
const bodyParser = require('body-parser');
const fetch = require('node-fetch');
const cors = require('cors');
const app = express();

app.use(bodyParser.json())

app.use(
    cors({
        origin: "https://fluence.noit.eu"
    })
);

app.post('/realTimeStatisticData', (req, res) => {
    fetch('https://open.tiktokapis.com/v2/user/info/?fields=follower_count,likes_count', {
        headers: {
            'Authorization': `Bearer ${req.body.accessToken}`
        }
    })
    .then(response => response.json())
    .then(data => {
        res.status(200).json(data)
    })
    .catch(error => {
        res.status(500).send(error);
    });
})

app.listen()