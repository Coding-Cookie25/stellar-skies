<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // or show error message
    exit;
}


require_once 'db_connect.php'; // Your DB connection file

// Check if session has payment data
if (!isset($_SESSION['user_id']) || !isset($_SESSION['total_price'])) {
    echo "Invalid session data.";
    exit;
}

// Get values from session
$user_id = $_SESSION['user_id'];
$booking_id = $_SESSION['booking_id'];
$flight_id = $_SESSION['flight_id'];
$passenger_count = $_SESSION['passenger_count'];
$seat_class = $_SESSION['seat_class'];
$special_requests = $_SESSION['special_requests'];
$total_price = $_SESSION['total_price'];

// Step 1: Update `bookings` table to confirm the payment
$bookingSql = "UPDATE bookings SET status = 'confirmed', payment_status = 'paid' 
               WHERE booking_id = ?";
$stmt = $conn->prepare($bookingSql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();

// Step 2: Insert flight booking details into `flight_bookings` table
$flightSql = "INSERT INTO flight_bookings (booking_id, flight_id, passenger_count, seat_class, special_requests)
              VALUES (?, ?, ?, ?, ?)";
$stmt2 = $conn->prepare($flightSql);
$stmt2->bind_param("iiiss", $booking_id, $flight_id, $passenger_count, $seat_class, $special_requests);
$stmt2->execute();

// Optionally clear session after successful booking
session_unset();
session_destroy();

// Redirect or show confirmation
echo "<script>alert('Booking confirmed!'); window.location.href='confirmation_page.html';</script>";
exit;
?>
