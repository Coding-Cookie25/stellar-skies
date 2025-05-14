<?php
include 'db.php'; // use your existing DB connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);

    // Check if user already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "User already exists."]);
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (email, password_hash, first_name, last_name, phone, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $email, $hashed_password, $first_name, $last_name, $phone);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "User registered successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Registration failed."]);
        }
    }
    $check->close();
    $conn->close();
}
?>
