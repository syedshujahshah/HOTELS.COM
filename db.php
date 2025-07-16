<?php
// Database connection file for Hotels.com Clone
// Professional database connection with error handling

class Database {
    private $host = 'localhost';
    private $dbname = 'dbegf82emnvgvz';
    private $username = 'ulnrcogla9a1t';
    private $password = 'yolpwow1mwr2';
    private $pdo;
    
    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    // Get all hotels with optional filters
    public function getHotels($city = '', $minPrice = 0, $maxPrice = 1000, $rating = 0, $hotelType = '') {
        $sql = "SELECT * FROM hotels WHERE price_per_night BETWEEN :minPrice AND :maxPrice";
        $params = [':minPrice' => $minPrice, ':maxPrice' => $maxPrice];
        
        if (!empty($city)) {
            $sql .= " AND city LIKE :city";
            $params[':city'] = "%{$city}%";
        }
        
        if ($rating > 0) {
            $sql .= " AND rating >= :rating";
            $params[':rating'] = $rating;
        }
        
        if (!empty($hotelType)) {
            $sql .= " AND hotel_type = :hotelType";
            $params[':hotelType'] = $hotelType;
        }
        
        $sql .= " ORDER BY rating DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Get hotel by ID
    public function getHotelById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM hotels WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    // Get reviews for a hotel
    public function getHotelReviews($hotelId) {
        $stmt = $this->pdo->prepare("SELECT * FROM reviews WHERE hotel_id = :hotelId ORDER BY created_at DESC");
        $stmt->execute([':hotelId' => $hotelId]);
        return $stmt->fetchAll();
    }
    
    // Create a booking
    public function createBooking($data) {
        $bookingRef = 'BK' . strtoupper(uniqid());
        
        $sql = "INSERT INTO bookings (hotel_id, guest_name, guest_email, guest_phone, check_in_date, check_out_date, number_of_guests, number_of_rooms, total_amount, special_requests, booking_reference) 
                VALUES (:hotel_id, :guest_name, :guest_email, :guest_phone, :check_in_date, :check_out_date, :number_of_guests, :number_of_rooms, :total_amount, :special_requests, :booking_reference)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':hotel_id' => $data['hotel_id'],
            ':guest_name' => $data['guest_name'],
            ':guest_email' => $data['guest_email'],
            ':guest_phone' => $data['guest_phone'],
            ':check_in_date' => $data['check_in_date'],
            ':check_out_date' => $data['check_out_date'],
            ':number_of_guests' => $data['number_of_guests'],
            ':number_of_rooms' => $data['number_of_rooms'],
            ':total_amount' => $data['total_amount'],
            ':special_requests' => $data['special_requests'],
            ':booking_reference' => $bookingRef
        ]);
        
        return $result ? $bookingRef : false;
    }
    
    // Get booking by reference
    public function getBookingByReference($reference) {
        $sql = "SELECT b.*, h.name as hotel_name, h.address, h.city, h.country 
                FROM bookings b 
                JOIN hotels h ON b.hotel_id = h.id 
                WHERE b.booking_reference = :reference";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':reference' => $reference]);
        return $stmt->fetch();
    }
    
    // Get featured hotels (top rated)
    public function getFeaturedHotels($limit = 6) {
        $stmt = $this->pdo->prepare("SELECT * FROM hotels ORDER BY rating DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
