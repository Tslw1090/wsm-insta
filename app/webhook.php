<?php
header('Content-Type: application/json');

$VERIFY_TOKEN = 'insta_verify_123';
$IG_ID        = '17841462449766356';
$IG_TOKEN     = 'IGAAcijxZBgoZB5BZAFlSVXhUS3Y2c3pudDF0ZAk1lT190ZATMzVDYzQ1ROSk1qVVF0ZA2RpNDhVSXZAqYWdOMnJZAY1JaV0lNcEc3d3JXMDZAxTV9fVkFyYy1uMFJtSjh0OUR2OXAtdnFMV0lpUDVNT3Q3U2pST0NoWE12aUFoQ1ZAxR2JSSQZDZD';

/* ===============================
   WEBHOOK VERIFICATION (GET)
================================ */
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
    exit;
}

/* ===============================
   WEBHOOK EVENTS (POST)
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $payload = json_decode(file_get_contents('php://input'), true);

    if (!isset($payload['entry'][0]['messaging'][0])) {
        http_response_code(200);
        exit;
    }

    $event = $payload['entry'][0]['messaging'][0];

    $senderId = $event['sender']['id'] ?? null;
    $text     = $event['message']['text'] ?? null;

    if ($senderId && $text) {
        sendInstagramReply($senderId, $text);
    }

    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    exit;
}

/* ===============================
   SEND INSTAGRAM DM
================================ */
function sendInstagramReply($recipientId, $incomingText)
{
    global $IG_ID, $IG_TOKEN;

    $url = "https://graph.instagram.com/v24.0/me/messages";

    $payload = [
        "recipient" => [
            "id" => $recipientId
        ],
        "message" => [
            "text" => "Thanks for your message! 👋 We will reply shortly."
        ]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Bearer $IG_TOKEN",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS     => json_encode($payload)
    ]);

    curl_exec($ch);
    curl_close($ch);
}
