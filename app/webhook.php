<?php
header('Content-Type: application/json');

$VERIFY_TOKEN = 'insta_verify_123'; // choose any string

/* ----------------------------------
   1️⃣ WEBHOOK VERIFICATION (GET)
-----------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (
        isset($_GET['hub_mode']) &&
        $_GET['hub_mode'] === 'subscribe' &&
        isset($_GET['hub_verify_token']) &&
        $_GET['hub_verify_token'] === $VERIFY_TOKEN
    ) {
        // IMPORTANT: must echo challenge only
        echo $_GET['hub_challenge'];
        exit;
    }

    http_response_code(403);
    echo json_encode(['error' => 'Invalid verify token']);
    exit;
}

/* ----------------------------------
   2️⃣ WEBHOOK EVENTS (POST)
-----------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $payload = file_get_contents('php://input');

    // For now, just acknowledge receipt
    http_response_code(200);
    echo json_encode([
        'status' => 'received',
        'time' => date('H:i:s')
    ]);
    exit;
}
