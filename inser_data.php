<?php
include 'db_connect.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // or show error message
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    // Validation
    if (!$first_name || !$last_name || !$email || !$password || !$confirm_password) {
        die("Error: All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email format.");
    }

    if ($password !== $confirm_password) {
        die("Error: Passwords do not match.");
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (first_name, last_name, email, password_hash, phone, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $password_hash, $phone);

        if ($stmt->execute()) {
            echo "New user registered successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>
