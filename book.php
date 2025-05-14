<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // or show error message
    exit;
}

include 'db_connect.php'; // Your DB connection file

// Assuming form data is coming via POST
$flight_id = $_POST['flight_id']; // Flight selection from the form
$passenger_count = $_POST['passenger_count'];
$seat_class = $_POST['seat_class'];
$special_requests = $_POST['special_requests'];
$total_price = $_POST['total_price']; // Calculate or get this from the form

// Assuming user is logged in, getting user data
$user_id = $_SESSION['user_id']; 

// Store necessary data in session for later use in payment_success.php
$_SESSION['flight_id'] = $flight_id;
$_SESSION['passenger_count'] = $passenger_count;
$_SESSION['seat_class'] = $seat_class;
$_SESSION['special_requests'] = $special_requests;
$_SESSION['total_price'] = $total_price;

// Insert into bookings table
$bookingSql = "INSERT INTO bookings (user_id, booking_type, status, total_price, created_at, payment_status)
               VALUES (?, 'flight', 'pending', ?, NOW(), 'unpaid')";
$stmt = $conn->prepare($bookingSql);
$stmt->bind_param("id", $user_id, $total_price);
$stmt->execute();

$booking_id = $conn->insert_id; // Get the new booking ID

// Store booking_id in session to use in payment_success.php
$_SESSION['booking_id'] = $booking_id;

// Redirect to payment success page (payment confirmation page)
header("Location: payment_success.php");
exit();
?>
