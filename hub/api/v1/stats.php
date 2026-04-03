<?php
declare(strict_types=1);

require_once __DIR__ . '/../../lib/hub.php';

handle_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_error(405, 'Method not allowed. Use GET.');
}

header('Cache-Control: public, max-age=60');

$stats = get_stats();

send_json(200, $stats);
