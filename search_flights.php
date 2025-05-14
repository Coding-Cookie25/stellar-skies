<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // or show error message
    exit;
}
include 'flights.php';

if (!function_exists('getAccessToken')) {
    die('🚨 getAccessToken() NOT FOUND.');
}

$token = getAccessToken();
if (!$token) {
    die("❌ Failed to get access token.");
}

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$date = $_GET['date'] ?? '';
$adults = $_GET['adults'] ?? '1';
$travelClass = $_GET['travelClass'] ?? 'economy'; // default to economy
$_SESSION['travelClass'] = $travelClass; // store in session
$error = '';

if (!$from || !$to || !$date) {
    $error = '⚠️ Missing required parameters.';
} else {
    $url = "https://test.api.amadeus.com/v2/shopping/flight-offers?originLocationCode=$from&destinationLocationCode=$to&departureDate=$date&adults=$adults";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $token"
        ]
    ]);

    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Flights - Stellar Skies</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet">
    <!-- Add this in <head> -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Rubik', sans-serif;
            background-color: #121212; /* Dark background */
            color: #fff;
            overflow-x: hidden;
        }

        /* Video background */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            filter: brightness(0.3);
        }
    /* Navbar */
    .navbar {
      background: rgba(0, 0, 0, 0.85);
      padding: 1rem 2rem;
    }

    .navbar-brand {
      font-weight: bold;
      color: #fff;
    }
    .nav-link {
      color: #ff4d4d !important;
      margin: 0 10px;
      transition: 0.3s;
    }

    .nav-link:hover {
      color: #fff !important;
    }

    .dropdown-menu-dark {
      background-color: rgba(0, 0, 0, 0.95);
      border: none;
      border-radius: 10px;
    }

    .dropdown-menu-dark .dropdown-item {
      color: #f0f0f0;
      padding: 10px 20px;
      font-size: 1rem;
    }

    .dropdown-menu-dark .dropdown-item:hover {
      background-color: #ff007f;
      color: #fff;
      border-radius: 5px;
    }

    /* Search */
    .search-container {
      display: flex;
      align-items: center;
      background: #2c2c2c;
      padding: 8px 15px;
      border-radius: 50px;
      margin-left: 20px;
    }

    #searchBar {
      background: transparent;
      border: none;
      color: #fff;
      width: 220px;
      padding: 5px 10px;
    }

    #searchBar::placeholder {
      color: #ccc;
    }

    .search-container button {
      background: #817a7d;
      border: none;
      color: white;
      padding: 5px 10px;
      border-radius: 50%;
      cursor: pointer;
      font-size: 16px;
    }
        /* Centering container */
        .main-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            box-shadow: 0px 15px 35px rgba(252, 47, 140, 0.2);
        }

        .booking-form {
            background: rgba(0, 0, 0, 0.8); /* Darker background */
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0px 15px 35px rgba(252, 47, 140, 0.2); /* Neon pink shadow */
            width: flex;
            max-width: 1050px;
            transition: transform 0.3s ease-in-out;
        }

        .booking-form:hover {
            transform: scale(1.05);
        }

        h2 {
            color: #fc2fa3; /* Neon pink */
            font-size: 2.2rem;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .input-group {
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
            color: #bbb; /* Lighter gray */
            font-weight: 500;
            margin-bottom: 8px;
        }

        input[type="date"], input[type="number"], select, input[type="text"] {
            width: 100%;
            padding: 14px;
            border: none;
            background: #333333; /* Dark input fields */
            color: #fff;
            border-radius: 15px;
            font-size: 16px;
            transition: 0.3s ease-in-out;
        }

        input[type="date"]:focus, input[type="number"]:focus, select:focus, input[type="text"]:focus {
            outline: none;
            background: #2a2a2a;
            box-shadow: 0px 0px 8px rgba(252, 47, 140, 0.5); /* Neon pink focus */
        }

        .button {
            background: #fc2fa3; /* Neon pink button */
            color: #fff;
            padding: 16px;
            border-radius: 15px;
            width: 30%;
            font-size: 18px;
            font-weight: 600;
            text-transform: uppercase;
            cursor: pointer;
            border: none;
            transition: background 0.3s, transform 0.3s;
        }

        .button:hover {
            background: #e729a1; /* Slightly darker pink on hover */
            transform: translateY(-5px);
        }

        /* Hover effects for trip options */
        .trip-options {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }

        .trip-options label {
            background: #fc2fa3; /* Neon pink */
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        .trip-options label:hover {
            background: #ff58c3; /* Lighter pink on hover */
        }


        @media (max-width: 600px) {
            .main-container {
                padding: 10px;
            }

            .booking-form {
                padding: 25px;
            }

            .button {
                font-size: 16px;
            }

            .trip-options {
                flex-direction: column;
                align-items: flex-start;
            }

            .trip-options label {
                margin-bottom: 10px;
            }
        }
footer {
  width: 100%;
  background: rgba(0, 0, 0, 0.95);
  padding: 40px 0 60px;
  font-size: 0.95rem;
  color: #bbb;
}

footer .container {
  max-width: 1200px;
  margin: auto;
  text-align: center;
}

footer .links {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 30px;
  margin-bottom: 20px;
}

footer .links a {
  color: #fff;
  text-decoration: none;
}

footer .links a:hover {
  color: #ff4d4d;
}
.autocomplete-container {
      position: relative;
    }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="#">✈ Stellar Skies</a>

    <div class="search-container ml-4">
      <input type="text" id="searchBar" placeholder="Search...">
      <button onclick="performSearch()">🔍</button>
    </div>

    <button class="navbar-toggler text-white" type="button" data-toggle="collapse" data-target="#navbarNav">
      ☰
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="aboutDropdown" data-toggle="dropdown">About Us</a>
          <div class="dropdown-menu dropdown-menu-dark">
            <a class="dropdown-item" href="about.html">Who We Are</a>
            <a class="dropdown-item" href="history.html">History</a>
            <a class="dropdown-item" href="founders.html">Our Founders</a>
            <a class="dropdown-item" href="terms.html">Terms</a>
          </div>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="destDropdown" data-toggle="dropdown">Destinations</a>
          <div class="dropdown-menu dropdown-menu-dark">
            <a class="dropdown-item" href="destinations.html">Explore All</a>
            <a class="dropdown-item" href="popular.html">Popular</a>
            <a class="dropdown-item" href="luxury travel.html">Luxury Trips</a>
            <a class="dropdown-item" href="budget travel.html">Budget Travel</a>
          </div>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="bookDropdown" data-toggle="dropdown">Book Now</a>
          <div class="dropdown-menu dropdown-menu-dark">
            <a class="dropdown-item" href="trip details.html">Trip Details</a>
            <a class="dropdown-item" href="payment page.html">Payment</a>
            <a class="dropdown-item" href="confirmation page.html">Confirmation</a>
          </div>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" data-toggle="dropdown">Account</a>
          <div class="dropdown-menu dropdown-menu-dark">
            <a class="dropdown-item" href="login.html">Login</a>
            <a class="dropdown-item" href="register.html">Sign Up</a>
            <a class="dropdown-item" href="forgot password.html">Forgot Password</a>
          </div>
        </li>
      </ul>
    </div>
  </nav>
<br><br><br><br><br><br>

    <div class="container">
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php elseif (!empty($data['data'])): ?>
            <h2>Available Flights</h2><br><br><br>
            <?php foreach ($data['data'] as $flight): ?>
                <?php
                $segment = $flight['itineraries'][0]['segments'][0];
                $fromCode = $segment['departure']['iataCode'];
                $toCode = $segment['arrival']['iataCode'];
                $departure = $segment['departure']['at'];
                $arrival = $segment['arrival']['at'];
                $airline = $segment['carrierCode'];
                $price = $flight['price']['total'];

                // Adjust price based on travel class
                switch ($travelClass) {
                    case 'business':
                        $price = $price * 2;
                        break;
                    case 'first-class':
                        $price = $price * 3;
                        break;
                    case 'economy':
                    default:
                        // price remains the same
                        break;
                }

                $queryString = http_build_query([
                    'from' => $fromCode,
                    'to' => $toCode,
                    'departure' => $departure,
                    'arrival' => $arrival,
                    'airline' => $airline,
                    'price' => $price
                ]);
                ?>
                <div class="booking-form">
                    <strong>From:</strong> <?= $fromCode ?> → <strong>To:</strong> <?= $toCode ?><br>
                    <strong>Departure:</strong> <?= $departure ?><br>
                    <strong>Arrival:</strong> <?= $arrival ?><br>
                    <strong>Airline:</strong> <?= $airline ?><br>
                    <strong>Price:</strong> ₹<?= $price ?><br>
                    <a href="payment page.html?<?= $queryString ?>">
                      <br>
                        <button class="button">Book Now</button>&nbsp;&nbsp;&nbsp;
                    </a>
                    <button class="button save-flight-btn" data-flight-id="<?= htmlspecialchars($flight['id'] ?? uniqid()) ?>">Save Flight</button>
                </div>
                <br><br>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">No flights found.</p>
        <?php endif; ?>
    </div>
    <footer>
            <div class="container">
              <div class="links">
                <a href="about.html">About</a>
                <a href="destinations.html">Destinations</a>
                <a href="trip details.html">Book</a>
                <a href="contact us.html">Contact</a>
              </div>
              <div class="contact">
                📍 Delhi, India · ✉️ support@stellarskies.com · ☎ +91 9993213543
              </div>
              <div class="copyright">
                &copy; 2025 Stellar Skies Ltd. All rights reserved.
              </div>
            </div>
          </footer>
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.save-flight-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const flightId = this.getAttribute('data-flight-id');
            const flightData = <?= json_encode($flight) ?>;

            fetch('saved.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    flight_id: flightId,
                    offer_details: flightData
                })
            })
            .then(function(response) {
                if (response.status === 401) {
                    alert('Please login first to save flights.');
                    window.location.href = 'login.html';
                    throw new Error('Unauthorized');
                }
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    alert('Flight saved successfully!');
                } else {
                    alert('Failed to save flight: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(function(error) {
                if (error.message !== 'Unauthorized') {
                    alert('Error saving flight: ' + error.message);
                }
            });
        });
    });
});
