<?php
session_start();
header('Content-Type: application/json'); // Set the content type to JSON
include 'db_connect.php'; // Include the database connection file

if (!isset($_SESSION['user_id'])) {
    // No user logged in
    echo json_encode([]);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    // Handle query error
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed.']);
    exit;
}

$users = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = [
            'id' => htmlspecialchars($row["id"]),
            'full_name' => htmlspecialchars($row["first_name"] . ' ' . $row["last_name"]),
            'email' => htmlspecialchars($row["email"]),
            'phone' => htmlspecialchars($row["phone"])  // Make sure to include the phone!
        ];
    }
    echo json_encode($users);
} else {
    echo json_encode([]);
}

$conn->close();
?>