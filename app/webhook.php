<?php
header('Content-Type: application/json');

$logFile = __DIR__ . '/runtime.log';

// Read input (any random data for now)
$raw = file_get_contents('php://input');

// Incoming log
$time = date('H:i:s');
file_put_contents(
    $logFile,
    "[$time] INCOMING: " . ($raw ?: 'No body') . PHP_EOL,
    FILE_APPEND
);

// Simulate outgoing response
$response = [
    'status' => 'ok',
    'reply' => 'Auto response generated',
    'time' => $time
];

// Outgoing log
file_put_contents(
    $logFile,
    "[$time] OUTGOING: " . json_encode($response) . PHP_EOL,
    FILE_APPEND
);

echo json_encode($response);

