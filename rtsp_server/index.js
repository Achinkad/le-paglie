const express = require('express');
const app = express();

const { proxy, scriptUrl } = require('rtsp-relay')(app);

const handler = proxy({
    url: `rtsp://tapo123:tapo123@10.10.88.248/stream1`,
    verbose: false,
    additionalFlags: ['-q', '1']
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
