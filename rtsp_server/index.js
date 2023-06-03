const express = require('express');
const app = express();

const { proxy, scriptUrl } = require('rtsp-relay')(app);

const handler = proxy({
    url: `rtsp://tapo123:tapo123@10.10.88.248/stream1`,
    verbose: false,
    additionalFlags: ['-q', '1']
});

const WebSocket = require('ws');

const wss = new WebSocket.Server({ port: 8080 });

wss.on('connection', (ws) => {
  console.log('A client connected.');

  ws.on('message', (message) => {
    console.log('Received message:', message.toString());

    wss.broadcast(message.toString());
    
  });

  wss.broadcast = function broadcast(message) {
 
    wss.clients.forEach(function each(client) {
      
        client.send(message);
     });
  };

  ws.on('close', () => {
    console.log('A client disconnected.');
  });
});


// the endpoint our RTSP uses
app.ws('/api/stream', handler);

// this is an example html page to view the stream
app.get('/', (req, res) =>
res.send(`
    <canvas id='canvas'></canvas>

    <script src='${scriptUrl}'></script>
    <script>
    loadPlayer({
        url: 'ws://' + location.host + '/api/stream',
        canvas: document.getElementById('canvas')
    });
    </script>
    `),
);

app.listen(2000);

process.stdout.write("[INFO] Starting the RTSP Server...\n");
process.stdout.write("[SUCCESS] RTSP Server listening on: ws://localhost:2000/api/stream\n");
