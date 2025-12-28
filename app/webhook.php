<?php
header('Content-Type: application/json');

$VERIFY_TOKEN = 'insta_verify_123';

/* ================================
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

/* ================================
   INSTAGRAM EVENTS (POST)
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    // Push event to dashboard using SSE file
    file_put_contents(
        __DIR__ . '/stream.log',
        json_encode([
            'time' => date('H:i:s'),
            'event' => $data
        ]) . PHP_EOL,
        FILE_APPEND
    );

    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    exit;
}
