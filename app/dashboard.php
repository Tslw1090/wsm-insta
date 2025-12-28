<!DOCTYPE html>
<html>
<head>
    <title>Instagram Webhook Monitor</title>
    <style>
        body {
            background:#0d1117;
            color:#00ff9c;
            font-family: monospace;
        }
        #log {
            white-space: pre-wrap;
            border:1px solid #333;
            padding:10px;
            height:90vh;
            overflow:auto;
        }
    </style>
</head>
<body>

<h2>📡 Instagram Live Webhook Monitor</h2>
<div id="log">Waiting for events...</div>

<script>
let lastSize = 0;

setInterval(() => {
    fetch('stream.log?_' + Date.now())
        .then(res => res.text())
        .then(text => {
            if (text.length > lastSize) {
                document.getElementById('log').textContent = text;
                lastSize = text.length;
            }
        });
}, 1500);
</script>

</body>
</html>
