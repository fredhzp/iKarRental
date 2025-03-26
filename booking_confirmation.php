<?php
session_start();

$is_logged_in = isset($_SESSION['user_email']) ? true : false;

if (isset($_SESSION['booking_details'])) {
    $booking_details = $_SESSION['booking_details'];
} 
else {
    $_SESSION['error_message'] = "Booking not found or booking failed.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <<style>
           body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #007BFF;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }

        .content {
            padding: 120px 20px 20px; 
        }

        h1 {
            font-size: 32px;
            color: #333;
            text-align: center;
            margin-top: 20px;
        }

        p {
            font-size: 18px;
            color: #666;
            margin-bottom: 15px;
        }

        h2 {
            font-size: 24px;
            color: #333;
            margin-top: 20px;
        }

        .booking-details {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        .booking-details strong {
            font-weight: bold;
        }

    </style>
</head>
<body>

<?php if (isset($_SESSION['error_message'])): ?>
        <script type="text/javascript">

            alert("<?= htmlspecialchars($_SESSION['error_message']); ?>");

            window.location.href = "index.php";
        </script>
        <?php unset($_SESSION['error_message']);  ?>
    <?php endif; ?>

    <div class="header">
    <div class="header-title"><a href="index.php">iKarRental</a></div>

            <?php if ($is_logged_in): ?>
            <div class="user-greeting">
                Hi, <a href="admin_profile.php" style="color: white; text-decoration: underline;"><?= htmlspecialchars($_SESSION['user_email']) ?></a>
            </div>
            <button onclick="window.location.href='logout.php'">Logout</button>
        <?php else: ?>
            <button onclick="window.location.href='login.php'">Login</button>
            <button onclick="window.location.href='register.php'">Register</button>
        <?php endif; ?>
        </div>
    </div>

    <div class="content">
        <div class="booking-details">
    <h1>Booking Confirmation<br>
    Booking ID: <?= htmlspecialchars($booking_details['booking_id']) ?></h1>
    <h2>Car Details:</h2>
    <p><strong>Car:</strong> <?= htmlspecialchars($booking_details['car']['brand'] . ' ' . $booking_details['car']['model']) ?> (<?= $booking_details['car']['year'] ?>)</p>
    <p><strong>Start Date:</strong> <?= htmlspecialchars($booking_details['start_date']) ?></p>
    <p><strong>End Date:</strong> <?= htmlspecialchars($booking_details['end_date']) ?></p>
</body>
</html>
