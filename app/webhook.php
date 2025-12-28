<?php
header('Content-Type: application/json');

$VERIFY_TOKEN = 'insta_verify_123';
$PAGE_TOKEN   = 'IGAAcijxZBgoZB5BZAFlSVXhUS3Y2c3pudDF0ZAk1lT190ZATMzVDYzQ1ROSk1qVVF0ZA2RpNDhVSXZAqYWdOMnJZAY1JaV0lNcEc3d3JXMDZAxTV9fVkFyYy1uMFJtSjh0OUR2OXAtdnFMV0lpUDVNT3Q3U2pST0NoWE12aUFoQ1ZAxR2JSSQZDZD';

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

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['entry'][0]['messaging'][0])) {
        http_response_code(200);
        exit;
    }

    $event = $input['entry'][0]['messaging'][0];

    // Sender IG User ID
    $senderId = $event['sender']['id'] ?? null;

    // Incoming text
    $messageText = $event['message']['text'] ?? '';

    if ($senderId && $messageText) {

        autoReply($senderId, $PAGE_TOKEN);
    }

    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    exit;
}

/* ===============================
   AUTO REPLY FUNCTION
================================ */
function autoReply($recipientId, $token)
{
    $url = 'https://graph.facebook.com/v19.0/me/messages';

    $payload = [
        'recipient' => ['id' => $recipientId],
        'message' => [
            'text' => 'Thanks for messaging us! 👋 We will get back to you shortly.'
        ]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_URL => $url . '?access_token=' . $token
    ]);

    curl_exec($ch);
    curl_close($ch);
}
