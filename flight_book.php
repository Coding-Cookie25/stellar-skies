<?php
session_start();
include 'db_connect.php'; // Your DB connection file

$booking_id = $_SESSION['booking_id'];
$flight_id = $_POST['flight_id']; // From the form
$passenger_count = $_POST['passenger_count'];
$seat_class = $_POST['seat_class'];
$special_requests = $_POST['special_requests'];

// Insert into flight_bookings
$sql = "INSERT INTO flight_bookings (booking_id, flight_id, passenger_count, seat_class, special_requests)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiss", $booking_id, $flight_id, $passenger_count, $seat_class, $special_requests);

if ($stmt->execute()) {
    echo "Flight booking successful!";
    // You can redirect to a confirmation page
    // header("Location: confirmation.php");
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>
