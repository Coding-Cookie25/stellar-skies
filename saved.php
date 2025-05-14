<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // or show error message
    exit;
}
header('Content-Type: application/json');
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = intval($_SESSION['user_id']);

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['flight_id']) || !isset($input['offer_details'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$flight_id = $conn->real_escape_string($input['flight_id']);
$offer_details = $conn->real_escape_string(json_encode($input['offer_details']));

$sql = "INSERT INTO saved_flights (user_id, flight_id, offer_details, saved_time) VALUES ($user_id, '$flight_id', '$offer_details', NOW())";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Flight saved successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save flight']);
}

$conn->close();
?>
