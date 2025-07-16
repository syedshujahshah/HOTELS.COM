<?php
require_once 'db.php';
$db = new Database();

// Get search parameters
$destination = $_GET['destination'] ?? '';
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$guests = $_GET['guests'] ?? 1;
$minPrice = $_GET['min_price'] ?? 0;
$maxPrice = $_GET['max_price'] ?? 1000;
$rating = $_GET['rating'] ?? 0;
$hotelType = $_GET['hotel_type'] ?? '';
$sortBy = $_GET['sort'] ?? 'rating';

// Get hotels based on filters
$hotels = $db->getHotels($destination, $minPrice, $maxPrice, $rating, $hotelType);

// Sort hotels
if ($sortBy === 'price_low') {
    usort($hotels, function($a, $b) { return $a['price_per_night'] <=> $b['price_per_night']; });
} elseif ($sortBy === 'price_high') {
    usort($hotels, function($a, $b) { return $b['price_per_night'] <=> $a['price_per_night']; });
} elseif ($sortBy === 'rating') {
    usort($hotels, function($a, $b) { return $b['rating'] <=> $a['rating']; });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Search Results - Hotels.com Clone</title>
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

        .search-summary {
            background: white;
            padding: 1.5rem;
            margin: 2rem 0;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .search-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .results-count {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        .filters-sort {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-group label {
            font-size: 0.9rem;
            color: #666;
        }

        .filter-group select,
        .filter-group input {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .main-content {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .filters-sidebar {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .filter-section {
            margin-bottom: 2rem;
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }

        .price-range {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .price-input {
            width: 80px;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .filter-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
        }

        .filter-btn:hover {
            background: #5a6fd8;
        }

        .hotels-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .hotel-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .hotel-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .hotel-content {
            display: grid;
            grid-template-columns: 300px 1fr auto;
            gap: 1.5rem;
            padding: 1.5rem;
        }

        .hotel-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .hotel-info {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hotel-name {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .hotel-location {
            color: #666;
            margin-bottom: 1rem;
        }

        .hotel-rating {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stars {
            color: #ffd700;
            margin-right: 0.5rem;
        }

        .rating-text {
            color: #666;
        }

        .hotel-amenities {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .hotel-pricing {
            text-align: right;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
        }

        .hotel-price {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .price-unit {
            font-size: 0.9rem;
            color: #666;
        }

        .book-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            margin-top: 1rem;
        }

        .book-btn:hover {
            transform: translateY(-2px);
        }

        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .no-results h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #666;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .filters-sidebar {
                position: static;
            }

            .hotel-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hotel-pricing {
                text-align: center;
            }

            .search-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .filters-sort {
                width: 100%;
                justify-content: space-between;
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
        </div>
    </header>

    <div class="container">
        <div class="search-summary">
            <div class="search-info">
                <div class="results-count">
                    <?php echo count($hotels); ?> hotels found
                    <?php if ($destination): ?>
                        in <?php echo htmlspecialchars($destination); ?>
                    <?php endif; ?>
                </div>
                <div class="filters-sort">
                    <div class="filter-group">
                        <label>Sort by:</label>
                        <select id="sortSelect" onchange="updateSort()">
                            <option value="rating" <?php echo $sortBy === 'rating' ? 'selected' : ''; ?>>Best Rating</option>
                            <option value="price_low" <?php echo $sortBy === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sortBy === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content">
            <aside class="filters-sidebar">
                <form id="filtersForm">
                    <div class="filter-section">
                        <h3 class="filter-title">Price Range</h3>
                        <div class="price-range">
                            <input type="number" id="minPrice" name="min_price" value="<?php echo $minPrice; ?>" placeholder="Min" class="price-input">
                            <span>-</span>
                            <input type="number" id="maxPrice" name="max_price" value="<?php echo $maxPrice; ?>" placeholder="Max" class="price-input">
                        </div>
                    </div>

                    <div class="filter-section">
                        <h3 class="filter-title">Minimum Rating</h3>
                        <select name="rating" id="ratingFilter">
                            <option value="0" <?php echo $rating == 0 ? 'selected' : ''; ?>>Any Rating</option>
                            <option value="3" <?php echo $rating == 3 ? 'selected' : ''; ?>>3+ Stars</option>
                            <option value="4" <?php echo $rating == 4 ? 'selected' : ''; ?>>4+ Stars</option>
                            <option value="4.5" <?php echo $rating == 4.5 ? 'selected' : ''; ?>>4.5+ Stars</option>
                        </select>
                    </div>

                    <div class="filter-section">
                        <h3 class="filter-title">Hotel Type</h3>
                        <select name="hotel_type" id="hotelTypeFilter">
                            <option value="" <?php echo $hotelType === '' ? 'selected' : ''; ?>>All Types</option>
                            <option value="Hotel" <?php echo $hotelType === 'Hotel' ? 'selected' : ''; ?>>Hotel</option>
                            <option value="Resort" <?php echo $hotelType === 'Resort' ? 'selected' : ''; ?>>Resort</option>
                            <option value="Apartment" <?php echo $hotelType === 'Apartment' ? 'selected' : ''; ?>>Apartment</option>
                            <option value="Villa" <?php echo $hotelType === 'Villa' ? 'selected' : ''; ?>>Villa</option>
                            <option value="Hostel" <?php echo $hotelType === 'Hostel' ? 'selected' : ''; ?>>Hostel</option>
                        </select>
                    </div>

                    <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
                    <input type="hidden" name="checkin" value="<?php echo htmlspecialchars($checkin); ?>">
                    <input type="hidden" name="checkout" value="<?php echo htmlspecialchars($checkout); ?>">
                    <input type="hidden" name="guests" value="<?php echo htmlspecialchars($guests); ?>">

                    <button type="submit" class="filter-btn">Apply Filters</button>
                </form>
            </aside>

            <main class="hotels-list">
                <?php if (empty($hotels)): ?>
                    <div class="no-results">
                        <h3>No hotels found</h3>
                        <p>Try adjusting your search criteria or filters.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($hotels as $hotel): ?>
                    <div class="hotel-card" onclick="viewHotel(<?php echo $hotel['id']; ?>)">
                        <div class="hotel-content">
                            <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="hotel-image">
                            
                            <div class="hotel-info">
                                <div>
                                    <h3 class="hotel-name"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                                    <p class="hotel-location"><?php echo htmlspecialchars($hotel['address'] . ', ' . $hotel['city'] . ', ' . $hotel['country']); ?></p>
                                    <div class="hotel-rating">
                                        <span class="stars">
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
                                        <span class="rating-text"><?php echo $hotel['rating']; ?>/5</span>
                                    </div>
                                </div>
                                <div class="hotel-amenities">
                                    <?php echo htmlspecialchars(substr($hotel['amenities'], 0, 100)) . '...'; ?>
                                </div>
                            </div>
                            
                            <div class="hotel-pricing">
                                <div class="hotel-price">
                                    $<?php echo number_format($hotel['price_per_night'], 2); ?>
                                    <div class="price-unit">per night</div>
                                </div>
                                <button class="book-btn" onclick="event.stopPropagation(); bookHotel(<?php echo $hotel['id']; ?>)">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script>
        function viewHotel(hotelId) {
            const params = new URLSearchParams(window.location.search);
            params.set('hotel_id', hotelId);
            window.location.href = 'hotel-details.php?' + params.toString();
        }

        function bookHotel(hotelId) {
            const params = new URLSearchParams(window.location.search);
            params.set('hotel_id', hotelId);
            window.location.href = 'booking.php?' + params.toString();
        }

        function updateSort() {
            const sortValue = document.getElementById('sortSelect').value;
            const params = new URLSearchParams(window.location.search);
            params.set('sort', sortValue);
            window.location.href = 'hotels.php?' + params.toString();
        }

        document.getElementById('filtersForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const params = new URLSearchParams(window.location.search);
            
            // Update filter parameters
            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.set(key, value);
                } else {
                    params.delete(key);
                }
            }
            
            window.location.href = 'hotels.php?' + params.toString();
        });
    </script>
</body>
</html>
