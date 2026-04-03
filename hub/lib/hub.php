<?php
declare(strict_types=1);

require_once __DIR__ . '/utils.php';

define('UPDATES_FILE', DATA_DIR . '/updates.json');
define('PUBLISHERS_FILE', DATA_DIR . '/publishers.json');

function process_ping(array $payload): array
{
    $required = ['pageUrl', 'questionName'];
    foreach ($required as $field) {
        if (empty($payload[$field])) {
            send_error(400, "Missing required field: $field");
        }
    }

    if (!validate_url($payload['pageUrl'])) {
        send_error(400, 'Invalid pageUrl: must be a valid URL');
    }

    $update = [
        'id' => bin2hex(random_bytes(8)),
        'pageUrl' => filter_var($payload['pageUrl'], FILTER_SANITIZE_URL),
        'questionName' => sanitize_string($payload['questionName'], 300),
        'previousVersion' => isset($payload['previousVersion']) ? sanitize_string($payload['previousVersion'], 20) : null,
        'newVersion' => isset($payload['newVersion']) ? sanitize_string($payload['newVersion'], 20) : null,
        'updateDate' => isset($payload['updateDate']) && validate_iso_date($payload['updateDate'])
            ? $payload['updateDate']
            : gmdate('Y-m-d\TH:i:s\Z'),
        'changeDescription' => isset($payload['changeDescription']) ? sanitize_string($payload['changeDescription'], 500) : null,
        'isNewQuestion' => !empty($payload['isNewQuestion']),
        'publisher' => isset($payload['publisher']) ? sanitize_string($payload['publisher'], 200) : null,
        'publisherUrl' => isset($payload['publisherUrl']) && validate_url($payload['publisherUrl'])
            ? filter_var($payload['publisherUrl'], FILTER_SANITIZE_URL)
            : null,
        'sector' => isset($payload['sector']) ? sanitize_string($payload['sector'], 20) : null,
        'language' => isset($payload['language']) ? sanitize_string($payload['language'], 10) : null,
        'country' => isset($payload['country']) ? strtoupper(sanitize_string($payload['country'], 2)) : null,
        'receivedAt' => gmdate('Y-m-d\TH:i:s\Z'),
    ];

    // Remove null values
    $update = array_filter($update, fn($v) => $v !== null);

    $updates = read_json_file(UPDATES_FILE);

    // Deduplicate
    foreach ($updates as $existing) {
        if (
            ($existing['pageUrl'] ?? '') === $update['pageUrl'] &&
            ($existing['questionName'] ?? '') === $update['questionName'] &&
            ($existing['newVersion'] ?? '') === ($update['newVersion'] ?? '')
        ) {
            return $update; // Already exists, skip
        }
    }

    // Prepend (most recent first)
    array_unshift($updates, $update);

    // Cleanup old entries
    $updates = cleanup_updates($updates);

    write_json_file(UPDATES_FILE, $updates);

    // Update publishers registry
    if (!empty($update['publisher'])) {
        update_publisher($update);
    }

    return $update;
}

function cleanup_updates(array $updates): array
{
    $cutoff = gmdate('Y-m-d\TH:i:s\Z', time() - (MAX_UPDATE_AGE_DAYS * 86400));

    $updates = array_filter($updates, function ($u) use ($cutoff) {
        $date = $u['receivedAt'] ?? $u['updateDate'] ?? '';
        return $date >= $cutoff;
    });

    $updates = array_values($updates);

    // Size check: if too large, trim oldest
    $json = json_encode($updates);
    while (strlen($json) > MAX_UPDATES_FILE_SIZE && count($updates) > 0) {
        array_pop($updates);
        $json = json_encode($updates);
    }

    return $updates;
}

function get_updates(array $params): array
{
    if (empty($params['since']) || !validate_iso_date($params['since'])) {
        send_error(400, 'Missing or invalid required parameter: since (ISO 8601 datetime)');
    }

    $since = $params['since'];
    $country = $params['country'] ?? null;
    $sector = $params['sector'] ?? null;
    $language = $params['language'] ?? null;
    $limit = min(max((int)($params['limit'] ?? DEFAULT_LIMIT), 1), MAX_LIMIT);

    $updates = read_json_file(UPDATES_FILE);

    // Cleanup on read
    $updates = cleanup_updates($updates);
    write_json_file(UPDATES_FILE, $updates);

    // Filter
    $filtered = array_filter($updates, function ($u) use ($since, $country, $sector, $language) {
        $date = $u['updateDate'] ?? $u['receivedAt'] ?? '';
        if ($date <= $since) return false;
        if ($country !== null && ($u['country'] ?? '') !== strtoupper($country)) return false;
        if ($sector !== null && ($u['sector'] ?? '') !== $sector) return false;
        if ($language !== null && ($u['language'] ?? '') !== strtolower($language)) return false;
        return true;
    });

    $filtered = array_values($filtered);
    $total = count($filtered);
    $filtered = array_slice($filtered, 0, $limit);

    return [
        'hub' => 'aqa-spec.org/hub',
        'queryTime' => gmdate('Y-m-d\TH:i:s\Z'),
        'totalResults' => $total,
        'returned' => count($filtered),
        'updates' => $filtered,
    ];
}

function get_stats(): array
{
    $updates = read_json_file(UPDATES_FILE);
    $publishers = read_json_file(PUBLISHERS_FILE);

    $now = time();
    $last24h = 0;
    $countries = [];
    $sectors = [];
    $last_update = null;

    foreach ($updates as $u) {
        $date = $u['receivedAt'] ?? $u['updateDate'] ?? '';
        $ts = strtotime($date);

        if ($ts && ($now - $ts) < 86400) {
            $last24h++;
        }

        if ($last_update === null || $date > $last_update) {
            $last_update = $date;
        }

        if (!empty($u['country'])) {
            $c = $u['country'];
            $countries[$c] = ($countries[$c] ?? 0) + 1;
        }

        if (!empty($u['sector'])) {
            $s = $u['sector'];
            $sectors[$s] = ($sectors[$s] ?? 0) + 1;
        }
    }

    arsort($countries);
    arsort($sectors);

    $top_countries = [];
    foreach (array_slice($countries, 0, 10, true) as $c => $count) {
        $top_countries[] = ['country' => $c, 'count' => $count];
    }

    $top_sectors = [];
    foreach (array_slice($sectors, 0, 10, true) as $s => $count) {
        $top_sectors[] = ['sector' => $s, 'count' => $count];
    }

    return [
        'hub' => 'aqa-spec.org/hub',
        'totalUpdates' => count($updates),
        'totalPublishers' => count($publishers),
        'last24h' => $last24h,
        'lastUpdate' => $last_update,
        'topCountries' => $top_countries,
        'topSectors' => $top_sectors,
    ];
}

function update_publisher(array $update): void
{
    $publishers = read_json_file(PUBLISHERS_FILE);

    $name = $update['publisher'];
    $found = false;

    foreach ($publishers as &$p) {
        if ($p['name'] === $name) {
            $p['lastPing'] = $update['receivedAt'] ?? gmdate('Y-m-d\TH:i:s\Z');
            $p['pingCount'] = ($p['pingCount'] ?? 0) + 1;
            if (!empty($update['publisherUrl'])) {
                $p['url'] = $update['publisherUrl'];
            }
            $found = true;
            break;
        }
    }
    unset($p);

    if (!$found) {
        $publishers[] = [
            'name' => $name,
            'url' => $update['publisherUrl'] ?? null,
            'firstSeen' => gmdate('Y-m-d\TH:i:s\Z'),
            'lastPing' => $update['receivedAt'] ?? gmdate('Y-m-d\TH:i:s\Z'),
            'pingCount' => 1,
        ];
    }

    write_json_file(PUBLISHERS_FILE, $publishers);
}
