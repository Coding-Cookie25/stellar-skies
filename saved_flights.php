
<?php
session_start();
header('Content-Type: application/json');
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = intval($_SESSION['user_id']);

$sql = "SELECT id, flight_id, offer_details, saved_time FROM saved_flights WHERE user_id = $user_id ORDER BY saved_time DESC";
$result = $conn->query($sql);

if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed']);
    exit;
}

$saved_flights = [];

while ($row = $result->fetch_assoc()) {
    $row['offer_details'] = json_decode($row['offer_details'], true);
    $saved_flights[] = $row;
}

echo json_encode($saved_flights);

$conn->close();
?>
