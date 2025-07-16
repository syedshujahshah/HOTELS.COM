<?php
require_once 'db.php';
$db = new Database();

$bookingRef = $_GET['ref'] ?? '';
$booking = $db->getBookingByReference($bookingRef);

if (!$booking) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Hotels.com Clone</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .confirmation-container {
            background: white;
            max-width: 600px;
            margin: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .success-header {
            background: linear-gradient(135deg, #00b894 0%, #00a085 100%);
            color: white;
            text-align: center;
            padding: 3rem 2rem;
        }

        .success-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .success-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .success-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .booking-details {
            padding: 2rem;
        }

        .booking-ref {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 2rem;
            border: 2px dashed #667eea;
        }

        .ref-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .ref-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 2px;
        }

        .detail-section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            padding: 0.5rem 0;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            font-weight: 600;
            color: #333;
        }

        .hotel-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .hotel-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .hotel-address {
            color: #666;
        }

        .total-amount {
            background: #667eea;
            color: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 2rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #e1e5e9;
        }

        .important-info {
            background: #fff3cd;
            color: #856404;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 2rem;
            border-left: 4px solid #ffc107;
        }

        .info-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .confirmation-container {
                margin: 1rem;
            }

            .success-header {
                padding: 2rem 1rem;
            }

            .success-title {
                font-size: 1.5rem;
            }

            .booking-details {
                padding: 1.5rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="success-header">
            <div class="success-icon">✅</div>
            <h1 class="success-title">Booking Confirmed!</h1>
            <p class="success-subtitle">Your reservation has been successfully processed</p>
        </div>

        <div class="booking-details">
            <div class="booking-ref">
                <div class="ref-label">Booking Reference Number</div>
                <div class="ref-number"><?php echo htmlspecialchars($booking['booking_reference']); ?></div>
            </div>

            <div class="hotel-info">
                <div class="hotel-name"><?php echo htmlspecialchars($booking['hotel_name']); ?></div>
                <div class="hotel-address"><?php echo htmlspecialchars($booking['address'] . ', ' . $booking['city'] . ', ' . $booking['country']); ?></div>
            </div>

            <div class="detail-section">
                <h3 class="section-title">Guest Information</h3>
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['guest_name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['guest_email']); ?></span>
                </div>
                <?php if ($booking['guest_phone']): ?>
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['guest_phone']); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="detail-section">
                <h3 class="section-title">Stay Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Check-in:</span>
                    <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Check-out:</span>
                    <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Guests:</span>
                    <span class="detail-value"><?php echo $booking['number_of_guests']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Rooms:</span>
                    <span class="detail-value"><?php echo $booking['number_of_rooms']; ?></span>
                </div>
            </div>

            <div class="total-amount">
                Total Amount: $<?php echo number_format($booking['total_amount'], 2); ?>
            </div>

            <?php if ($booking['special_requests']): ?>
            <div class="detail-section">
                <h3 class="section-title">Special Requests</h3>
                <p><?php echo htmlspecialchars($booking['special_requests']); ?></p>
            </div>
            <?php endif; ?>

            <div class="action-buttons">
                <a href="index.php" class="btn btn-primary">Book Another Hotel</a>
                <button onclick="window.print()" class="btn btn-secondary">Print Confirmation</button>
            </div>

            <div class="important-info">
                <div class="info-title">Important Information:</div>
                <ul style="margin-left: 1rem; margin-top: 0.5rem;">
                    <li>Please save your booking reference number for future correspondence</li>
                    <li>A confirmation email has been sent to your registered email address</li>
                    <li>Check-in time is typically 3:00 PM and check-out is 11:00 AM</li>
                    <li>Please bring a valid ID and credit card for check-in</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to top on page load
        window.addEventListener('load', function() {
            window.scrollTo(0, 0);
        });

        // Add some celebration animation
        document.addEventListener('DOMContentLoaded', function() {
            const successIcon = document.querySelector('.success-icon');
            successIcon.style.animation = 'bounce 1s ease-in-out';
        });

        // Add CSS animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes bounce {
                0%, 20%, 60%, 100% {
                    transform: translateY(0);
                }
                40% {
                    transform: translateY(-20px);
                }
                80% {
                    transform: translateY(-10px);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
