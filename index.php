<?php
// Load .env jika menggunakan GitHub
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        putenv(trim($line));
    }
}

// Ambil konfigurasi dari environment variables
$client_id = getenv('INSTAGRAM_CLIENT_ID');
$client_secret = getenv('INSTAGRAM_CLIENT_SECRET');
$redirect_uri = getenv('INSTAGRAM_REDIRECT_URI');

// Tangkap authorization code dari Instagram
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Kirim permintaan ke Instagram untuk access token
    $url = "https://api.instagram.com/oauth/access_token";
    $data = [
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'authorization_code',
        'redirect_uri' => $redirect_uri,
        'code' => $code
    ];

    // Kirim request ke Instagram API
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    // Periksa apakah access_token diterima
    if (isset($result['access_token'])) {
        echo json_encode([
            "success" => true,
            "access_token" => $result['access_token'],
            "user_id" => $result['user_id']
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => $result
        ]);
    }
} else {
    echo json_encode(["error" => "No authorization code received"]);
}
?>
