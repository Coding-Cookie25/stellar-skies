<?php
include 'db_connect.php';

$sql = "SELECT * FROM flights";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Flight List</h2>";
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr style='background-color: #f2f2f2;'>
            <th>ID</th>
            <th>Airline</th>
            <th>Flight #</th>
            <th>Departure</th>
            <th>Arrival</th>
            <th>Departure Time</th>
            <th>Arrival Time</th>
            <th>Duration</th>
            <th>Price</th>
            <th>Seats</th>
            <th>Status</th>
          </tr>";
    
    while($row = $result->fetch_assoc()) {
        // Format duration from minutes to hours and minutes
        $hours = floor($row["duration"] / 60);
        $minutes = $row["duration"] % 60;
        $duration = sprintf("%dh %02dm", $hours, $minutes);
        
        // Format price with currency symbol
        $price = "$" . number_format($row["price"], 2);
        
        // Format date/time
        $departure_time = date("M j, Y H:i", strtotime($row["departure_time"]));
        $arrival_time = date("M j, Y H:i", strtotime($row["arrival_time"]));
        
        // Add status color
        $status_color = '';
        switch(strtolower($row["status"])) {
            case 'scheduled': $status_color = 'blue'; break;
            case 'delayed': $status_color = 'orange'; break;
            case 'cancelled': $status_color = 'red'; break;
            case 'completed': $status_color = 'green'; break;
            default: $status_color = 'black';
        }
        
        echo "<tr>
                <td>" . $row["id"] . "</td>
                <td>" . $row["airline"] . "</td>
                <td>" . $row["flight_number"] . "</td>
                <td>" . $row["departure_airport"] . "</td>
                <td>" . $row["arrival_airport"] . "</td>
                <td>" . $departure_time . "</td>
                <td>" . $arrival_time . "</td>
                <td>" . $duration . "</td>
                <td style='text-align: right;'>" . $price . "</td>
                <td style='text-align: center;'>" . $row["available_seats"] . "</td>
                <td style='color: " . $status_color . ";'>" . ucfirst($row["status"]) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No flights found in the database.</p>";
}

$conn->close();
?>
