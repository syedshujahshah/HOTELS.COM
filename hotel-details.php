<?php
require_once 'db.php';
$db = new Database();

$hotelId = $_GET['hotel_id'] ?? $_GET['id'] ?? 0;
$hotel = $db->getHotelById($hotelId);
$reviews = $db->getHotelReviews($hotelId);

if (!$hotel) {
    header('Location: index.php');
    exit;
}

// Get search parameters for booking
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$guests = $_GET['guests'] ?? 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($hotel['name']); ?> - Hotels.com Clone</title>
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
            max-width: 1200px;
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

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .nav-links a:hover {
            opacity: 0.8;
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

        .hotel-header {
            background: white;
            margin: 2rem 0;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .hotel-image-main {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .hotel-info-header {
            padding: 2rem;
        }

        .hotel-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #333;
        }

        .hotel-location {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .hotel-rating-large {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .stars-large {
            color: #ffd700;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .rating-text-large {
            font-size: 1.2rem;
            color: #666;
        }

        .main-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .hotel-details {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }

        .description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
            margin-bottom: 2rem;
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .amenity-icon {
            color: #667eea;
            margin-right: 0.5rem;
        }

        .reviews-section {
            margin-top: 3rem;
        }

        .review-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .reviewer-name {
            font-weight: 600;
            color: #333;
        }

        .review-rating {
            color: #ffd700;
        }

        .review-text {
            color: #555;
            line-height: 1.6;
        }

        .booking-sidebar {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .price-display {
            text-align: center;
            margin-bottom: 2rem;
        }

        .price-amount {
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
        }

        .price-unit {
            font-size: 1rem;
            color: #666;
        }

        .booking-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }

        .form-group input,
        .form-group select {
            padding: 0.8rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .book-now-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1.2rem 2rem;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            margin-top: 1rem;
        }

        .book-now-btn:hover {
            transform: translateY(-2px);
        }

        .availability-info {
            background: #e8f5e8;
            color: #2d5a2d;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .booking-sidebar {
                position: static;
            }

            .hotel-title {
                font-size: 2rem;
            }

            .price-amount {
                font-size: 2rem;
            }

            .amenities-grid {
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
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="hotels.php">Hotels</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
            <button class="back-btn" onclick="goBack()">← Back to Results</button>
        </div>
    </header>

    <div class="container">
        <div class="hotel-header">
            <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="hotel-image-main">
            <div class="hotel-info-header">
                <h1 class="hotel-title"><?php echo htmlspecialchars($hotel['name']); ?></h1>
                <p class="hotel-location"><?php echo htmlspecialchars($hotel['address'] . ', ' . $hotel['city'] . ', ' . $hotel['country']); ?></p>
                <div class="hotel-rating-large">
                    <span class="stars-large">
                        <?php
                        $rating = floor($hotel['rating']);
                        for ($i = 0; $i < $rating; $i++) {
                            echo '★';
                        }
                        for ($i = $rating; $i < 5; $i++) {
                            echo '☆';
                        }
                        ?>
                    </span>
                    <span class="rating-text-large"><?php echo $hotel['rating']; ?>/5 (<?php echo count($reviews); ?> reviews)</span>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="hotel-details">
                <h2 class="section-title">About This Hotel</h2>
                <p class="description"><?php echo htmlspecialchars($hotel['description']); ?></p>

                <h3 class="section-title">Amenities</h3>
                <div class="amenities-grid">
                    <?php
                    $amenities = explode(', ', $hotel['amenities']);
                    foreach ($amenities as $amenity):
                    ?>
                    <div class="amenity-item">
                        <span class="amenity-icon">✓</span>
                        <span><?php echo htmlspecialchars($amenity); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="reviews-section">
                    <h3 class="section-title">Guest Reviews</h3>
                    <?php if (empty($reviews)): ?>
                        <p>No reviews yet. Be the first to review this hotel!</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <span class="reviewer-name"><?php echo htmlspecialchars($review['guest_name']); ?></span>
                                <span class="review-rating">
                                    <?php
                                    for ($i = 0; $i < $review['rating']; $i++) {
                                        echo '★';
                                    }
                                    for ($i = $review['rating']; $i < 5; $i++) {
                                        echo '☆';
                                    }
                                    ?>
                                </span>
                            </div>
                            <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="booking-sidebar">
                <div class="price-display">
                    <div class="price-amount">$<?php echo number_format($hotel['price_per_night'], 2); ?></div>
                    <div class="price-unit">per night</div>
                </div>

                <div class="availability-info">
                    <?php echo $hotel['available_rooms']; ?> rooms available
                </div>

                <form class="booking-form" id="bookingForm">
                    <div class="form-group">
                        <label for="checkin">Check-in Date</label>
                        <input type="date" id="checkin" name="checkin" value="<?php echo htmlspecialchars($checkin); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="checkout">Check-out Date</label>
                        <input type="date" id="checkout" name="checkout" value="<?php echo htmlspecialchars($checkout); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="guests">Number of Guests</label>
                        <select id="guests" name="guests" required>
                            <option value="1" <?php echo $guests == 1 ? 'selected' : ''; ?>>1 Guest</option>
                            <option value="2" <?php echo $guests == 2 ? 'selected' : ''; ?>>2 Guests</option>
                            <option value="3" <?php echo $guests == 3 ? 'selected' : ''; ?>>3 Guests</option>
                            <option value="4" <?php echo $guests == 4 ? 'selected' : ''; ?>>4 Guests</option>
                            <option value="5" <?php echo $guests >= 5 ? 'selected' : ''; ?>>5+ Guests</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="rooms">Number of Rooms</label>
                        <select id="rooms" name="rooms" required>
                            <option value="1">1 Room</option>
                            <option value="2">2 Rooms</option>
                            <option value="3">3 Rooms</option>
                            <option value="4">4+ Rooms</option>
                        </select>
                    </div>

                    <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                    <button type="submit" class="book-now-btn">Book Now</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Set minimum date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('checkin').min = today;
            document.getElementById('checkout').min = today;
            
            // Update checkout min date when checkin changes
            document.getElementById('checkin').addEventListener('change', function() {
                const checkinDate = new Date(this.value);
                checkinDate.setDate(checkinDate.getDate() + 1);
                document.getElementById('checkout').min = checkinDate.toISOString().split('T')[0];
            });
        });

        function goBack() {
            if (document.referrer && document.referrer.includes('hotels.php')) {
                window.history.back();
            } else {
                window.location.href = 'hotels.php';
            }
        }

        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                params.append(key, value);
            }
            
            window.location.href = 'booking.php?' + params.toString();
        });
    </script>
</body>
</html>
