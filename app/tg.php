<?php
$messageStatus = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $BOT_TOKEN = '8595388299:AAEPDB_qEAJUsGNvZuXDXlcJY-qAODk8DKw';
    $CHAT_ID   = '5076069422';

    $text = "✅ Telegram test message from Wasmer at " . date('Y-m-d H:i:s');

    $url = "https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage";

    $payload = [
        'chat_id' => $CHAT_ID,
        'text' => $text
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => http_build_query($payload),
        CURLOPT_TIMEOUT => 10
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error) {
        $messageStatus = "❌ cURL Error: " . $error;
    } else {
        $messageStatus = "HTTP {$httpCode}<br>Response:<br><pre>{$response}</pre>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Telegram Test</title>
    <style>
        body {
            font-family: monospace;
            background: #0d1117;
            color: #00ff9c;
            padding: 30px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        .result {
            margin-top: 20px;
            background: #161b22;
            padding: 15px;
            border: 1px solid #30363d;
        }
    </style>
</head>
<body>

<h2>📨 Telegram PHP Test (Wasmer)</h2>

<form method="POST">
    <button type="submit">Send Test Telegram Message</button>
</form>

<?php if ($messageStatus): ?>
    <div class="result">
        <?= $messageStatus ?>
    </div>
<?php endif; ?>

</body>
</html>
