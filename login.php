<?php
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

$debug = false; // Set to false in production

$response = ['status' => 'error', 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        throw new Exception('Email and password are required');
    }

    // Rest of your existing code...
    
} catch (Exception $e) {
    $response['message'] = $debug ? $e->getMessage() : 'Login failed';
    echo json_encode($response);
    exit;
}
?>