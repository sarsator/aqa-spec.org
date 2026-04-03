<?php
declare(strict_types=1);

require_once __DIR__ . '/../../lib/hub.php';

handle_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_error(405, 'Method not allowed. Use POST.');
}

// Check payload size
$raw = file_get_contents('php://input');
if (strlen($raw) > MAX_PAYLOAD_SIZE) {
    send_error(413, 'Payload too large. Maximum size: ' . MAX_PAYLOAD_SIZE . ' bytes.');
}

// Rate limit
check_rate_limit();

// Parse JSON
$payload = json_decode($raw, true);
if (!is_array($payload)) {
    send_error(400, 'Invalid JSON payload.');
}

$update = process_ping($payload);

send_json(202, [
    'status' => 'accepted',
    'id' => $update['id'] ?? null,
    'message' => 'Update notification received.',
]);
