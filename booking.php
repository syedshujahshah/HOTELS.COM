<?php
require_once 'db.php';
$db = new Database();

$hotelId = $_GET['hotel_id'] ?? 0;
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$guests = $_GET['guests'] ?? 1;
$rooms = $_GET['rooms'] ?? 1;

$hotel = $db->getHotelById($hotelId);

if (!$hotel) {
    header('Location: index.php');
    exit;
}

// Calculate total amount
$checkinDate = new DateTime($checkin);
$checkoutDate = new DateTime($checkout);
$nights = $checkinDate->diff($checkoutDate)->days;
$totalAmount = $hotel['price_per_night'] * $nights * $rooms;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingData = [
        'hotel_id' => $hotelId,
        'guest_name' => $_POST['guest_name'],
        'guest_email' => $_POST['guest_email'],
        'guest_phone' => $_POST['guest_phone'],
        'check_in_date' => $checkin,
        'check_out_date' => $checkout,
        'number_of_guests' => $guests,
        'number_of_rooms' => $rooms,
        'total_amount' => $totalAmount,
        'special_requests' => $_POST['special_requests']
    ];
    
    $bookingReference = $db->createBooking($bookingData);
    
    if ($bookingReference) {
        header('Location: confirmation.php?ref=' . $bookingReference);
        exit;
    } else {
        $error = "Booking failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo htmlspecialchars($hotel['name']); ?> - Hotels.com Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }

        .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 1rem;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .booking-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
            margin: 2rem 0 4rem 0;
        }

        .booking-form-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .booking-summary {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .hotel-summary {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #eee;
        }

        .hotel-image-small {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .hotel-info-small {
            flex: 1;
        }

        .hotel-name-small {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .hotel-location-small {
            color: #666;
            font-size: 0.9rem;
        }

        .booking-details {
            margin-bottom: 2rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
        }

        .detail-label {
            color: #666;
        }

        .detail-value {
            font-weight: 600;
            color: #333;
        }

        .price-breakdown {
            border-top: 1px solid #eee;
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
            border-top: 2px solid #667eea;
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1.2rem 2rem;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            width: 100%;
            margin-top: 2rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
        }

        .error-message {
            background: #ffe6e6;
            color: #d63031;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #fab1a0;
        }

        .security-info {
            background: #e8f5e8;
            color: #2d5a2d;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .booking-container {
                grid-template-columns: 1fr;
            }

            .booking-summary {
                position: static;
                order: -1;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">Hotels.com</a>
            </nav>
            <button class="back-btn" onclick="goBack()">← Back to Hotel</button>
        </div>
    </header>

    <div class="container">
        <div class="booking-container">
            <div class="booking-form-section">
                <h1 class="section-title">Complete Your Booking</h1>
                
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" id="bookingForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="guest_name">Full Name *</label>
                            <input type="text" id="guest_name" name="guest_name" required>
                        </div>
                        <div class="form-group">
                            <label for="guest_email">Email Address *</label>
                            <input type="email" id="guest_email" name="guest_email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="guest_phone">Phone Number</label>
                        <input type="tel" id="guest_phone" name="guest_phone" placeholder="+1 (555) 123-4567">
                    </div>

                    <div class="form-group">
                        <label for="special_requests">Special Requests</label>
                        <textarea id="special_requests" name="special_requests" placeholder="Any special requests or preferences..."></textarea>
                    </div>

                    <div class="security-info">
                        🔒 Your information is secure and encrypted. We never share your personal details with third parties.
                    </div>

                    <button type="submit" class="submit-btn">Complete Booking</button>
                </form>
            </div>

            <div class="booking-summary">
                <h2 class="section-title">Booking Summary</h2>
                
                <div class="hotel-summary">
                    <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="hotel-image-small">
                    <div class="hotel-info-small">
                        <div class="hotel-name-small"><?php echo htmlspecialchars($hotel['name']); ?></div>
                        <div class="hotel-location-small"><?php echo htmlspecialchars($hotel['city'] . ', ' . $hotel['country']); ?></div>
                    </div>
                </div>

                <div class="booking-details">
                    <div class="detail-row">
                        <span class="detail-label">Check-in:</span>
                        <span class="detail-value"><?php echo date('M d, Y', strtotime($checkin)); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Check-out:</span>
                        <span class="detail-value"><?php echo date('M d, Y', strtotime($checkout)); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Nights:</span>
                        <span class="detail-value"><?php echo $nights; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Guests:</span>
                        <span class="detail-value"><?php echo $guests; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Rooms:</span>
                        <span class="detail-value"><?php echo $rooms; ?></span>
                    </div>
                </div>

                <div class="price-breakdown">
                    <div class="detail-row">
                        <span class="detail-label">Rate per night:</span>
                        <span class="detail-value">$<?php echo number_format($hotel['price_per_night'], 2); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><?php echo $nights; ?> nights × <?php echo $rooms; ?> room(s):</span>
                        <span class="detail-value">$<?php echo number_format($totalAmount, 2); ?></span>
                    </div>
                    <div class="detail-row total-amount">
                        <span>Total Amount:</span>
                        <span>$<?php echo number_format($totalAmount, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function goBack() {
            const params = new URLSearchParams({
                hotel_id: <?php echo $hotelId; ?>,
                checkin: '<?php echo $checkin; ?>',
                checkout: '<?php echo $checkout; ?>',
                guests: <?php echo $guests; ?>
            });
            window.location.href = 'hotel-details.php?' + params.toString();
        }

        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const name = document.getElementById('guest_name').value.trim();
            const email = document.getElementById('guest_email').value.trim();
            
            if (!name || !email) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }
        });
    </script>
</body>
</html>
