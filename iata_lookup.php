<?php
include 'flights.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // or show error message
    exit;
}


// ✅ Trim the keyword to avoid issues with trailing spaces
$keyword = trim($_GET['keyword'] ?? '');
if (!$keyword) {
    echo json_encode([]);
    exit;
}

$token = getAccessToken();
if (!$token) {
    echo json_encode(['error' => 'Token error']);
    exit;
}

// ✅ Correct URL — no 'max' param!
$url = "https://test.api.amadeus.com/v1/reference-data/locations?subType=CITY,AIRPORT&keyword=" . urlencode($keyword);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token"
    ]
]);

$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);

// ✅ Only send the relevant data back to frontend
echo json_encode($data['data'] ?? []);
