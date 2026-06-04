<?php

function geocode($pdo, $address) {

    // 1. CACHE
    $stmt = $pdo->prepare("SELECT * FROM geocache WHERE address = ?");
    $stmt->execute([$address]);
    $cache = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cache) {
        return [$cache['lat'], $cache['lng']];
    }

    // 2. API
    $url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q=" . urlencode($address);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Solideat-BTS-Project)");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: application/json"
    ]);

    $response = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http != 200 || !$response) {
        return null;
    }

    $data = json_decode($response, true);

    // DEBUG OPTION (à activer si besoin)
    // var_dump($data); exit;

    if (isset($data[0]['lat']) && isset($data[0]['lon'])) {

        $lat = $data[0]['lat'];
        $lng = $data[0]['lon'];

        // 3. CACHE INSERT
        $stmt = $pdo->prepare("
            INSERT INTO geocache(address, lat, lng)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE lat=VALUES(lat), lng=VALUES(lng)
        ");

        $stmt->execute([$address, $lat, $lng]);

        return [$lat, $lng];
    }

    return null;
}