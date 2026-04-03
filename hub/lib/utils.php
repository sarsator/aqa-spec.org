<?php
declare(strict_types=1);

define('DATA_DIR', __DIR__ . '/../data');
define('RATE_LIMIT_DIR', DATA_DIR . '/rate_limits');
define('MAX_PAYLOAD_SIZE', 10240); // 10KB
define('MAX_PINGS_PER_HOUR', 100);
define('MAX_UPDATE_AGE_DAYS', 90);
define('MAX_UPDATES_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('DEFAULT_LIMIT', 100);
define('MAX_LIMIT', 1000);

function send_json(int $status, array $data): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

function send_error(int $status, string $message): void
{
    send_json($status, ['error' => $message]);
}

function handle_cors(): void
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

function get_client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($parts[0]);
    }
    return $ip;
}

function check_rate_limit(): void
{
    if (!is_dir(RATE_LIMIT_DIR)) {
        mkdir(RATE_LIMIT_DIR, 0755, true);
    }

    cleanup_rate_limits();

    $ip_hash = md5(get_client_ip());
    $file = RATE_LIMIT_DIR . '/' . $ip_hash . '.json';
    $now = time();
    $window = 3600;

    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($data && ($now - $data['window_start']) < $window) {
            if ($data['count'] >= MAX_PINGS_PER_HOUR) {
                $retry = $data['window_start'] + $window - $now;
                header('Retry-After: ' . $retry);
                send_error(429, 'Rate limit exceeded. Max ' . MAX_PINGS_PER_HOUR . ' pings per hour.');
            }
            $data['count']++;
        } else {
            $data = ['count' => 1, 'window_start' => $now];
        }
    } else {
        $data = ['count' => 1, 'window_start' => $now];
    }

    file_put_contents($file, json_encode($data), LOCK_EX);
}

function cleanup_rate_limits(): void
{
    static $cleaned = false;
    if ($cleaned) return;
    $cleaned = true;

    $cutoff = time() - 7200; // 2 hours
    $files = glob(RATE_LIMIT_DIR . '/*.json');
    if (!$files) return;

    foreach ($files as $f) {
        if (filemtime($f) < $cutoff) {
            @unlink($f);
        }
    }
}

function read_json_file(string $path): array
{
    if (!file_exists($path)) {
        return [];
    }
    $content = file_get_contents($path);
    if ($content === false || $content === '') {
        return [];
    }
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function write_json_file(string $path, array $data): void
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), LOCK_EX);
}

function validate_iso_date(string $date): bool
{
    return (bool) preg_match('/^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2}(:\d{2})?(Z|[+-]\d{2}:?\d{2})?)?$/', $date);
}

function validate_url(string $url): bool
{
    return (bool) filter_var($url, FILTER_VALIDATE_URL);
}

function sanitize_string(string $str, int $max_length = 500): string
{
    $str = strip_tags($str);
    $str = trim($str);
    if (function_exists('mb_strlen')) {
        if (mb_strlen($str) > $max_length) {
            $str = mb_substr($str, 0, $max_length);
        }
    } else {
        if (strlen($str) > $max_length) {
            $str = substr($str, 0, $max_length);
        }
    }
    return $str;
}
