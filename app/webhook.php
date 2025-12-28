<?php
header('Content-Type: application/json');

/* =========================
   CONFIG
========================= */
$VERIFY_TOKEN = 'insta_verify_123';

/* Instagram */
$IG_ID        = '17841462449766356';
$IG_TOKEN     = 'IGAAcijxZBgoZB5BZAFlSVXhUS3Y2c3pudDF0ZAk1lT190ZATMzVDYzQ1ROSk1qVVF0ZA2RpNDhVSXZAqYWdOMnJZAY1JaV0lNcEc3d3JXMDZAxTV9fVkFyYy1uMFJtSjh0OUR2OXAtdnFMV0lpUDVNT3Q3U2pST0NoWE12aUFoQ1ZAxR2JSSQZDZD';


/* Telegram (DEBUG ONLY – rotate later) */
$TG_BOT_TOKEN = '8595388299:AAEPDB_qEAJUsGNvZuXDXlcJY-qAODk8DKw';
$TG_CHAT_ID   = '5076069422';

/* =========================
   TELEGRAM LOGGER
========================= */
function tgLog($title, $data = null)
{
    global $TG_BOT_TOKEN, $TG_CHAT_ID;

    if (is_array($data) || is_object($data)) {
        $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    $text = "🟡 *{$title}*\n";
    if ($data) {
        $text .= "```\n{$data}\n```";
    }

    $url = "https://api.telegram.org/bot{$TG_BOT_TOKEN}/sendMessage";

    $payload = [
        'chat_id' => $TG_CHAT_ID,
        'text' => $text,
        'parse_mode' => 'Markdown'
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $payload
    ]);
    curl_exec($ch);
    curl_close($ch);
}

/* =========================
   WEBHOOK VERIFICATION (GET)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (
        isset($_GET['hub_mode']) &&
        $_GET['hub_mode'] === 'subscribe' &&
        $_GET['hub_verify_token'] === $VERIFY_TOKEN
    ) {
        echo $_GET['hub_challenge'];
        exit;
    }

    http_response_code(403);
    echo json_encode(['error' => 'Verification failed']);
    exit;
}

/* =========================
   WEBHOOK EVENTS (POST)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true);

    tgLog('Incoming Webhook RAW', $payload);

    if (!isset($payload['entry'][0]['messaging'][0])) {
        tgLog('No messaging event found');
        http_response_code(200);
        exit;
    }

    $event = $payload['entry'][0]['messaging'][0];

    $senderId = $event['sender']['id'] ?? null;
    $text     = $event['message']['text'] ?? null;

    tgLog('Parsed Event', [
        'sender_id' => $senderId,
        'text' => $text,
        'event' => $event
    ]);

    if ($senderId && $text) {
        sendInstagramReply($senderId);
    }

    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    exit;
}

/* =========================
   SEND INSTAGRAM DM
========================= */
function sendInstagramReply($recipientId)
{
    global $IG_ID, $IG_TOKEN;

    $url = "https://graph.instagram.com/v24.0/{$IG_ID}/messages";

    $payload = [
        "recipient" => [
            "id" => $recipientId
        ],
        "message" => [
            "text" => "Thanks for messaging us! 👋"
        ]
    ];

    tgLog('Outgoing IG Request', [
        'url' => $url,
        'payload' => $payload
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$IG_TOKEN}",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    tgLog('IG API Response', [
        'http_code' => $httpCode,
        'response' => $response
    ]);
}
