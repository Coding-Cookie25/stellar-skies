<?php
include 'db_connect.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // or show error message
    exit;
}


$first_name = trim($_POST['first_name'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($first_name) || empty($email)) {
    echo json_encode(['error' => 'First name and email are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email format.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO users (first_name, email, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
$stmt->bind_param("ss", $first_name, $email);

if ($stmt->execute()) {
    echo json_encode(['success' => 'User added successfully!']);
} else {
    echo json_encode(['error' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
