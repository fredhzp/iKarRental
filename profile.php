<?php
session_start();

$user_data = file_get_contents('users.json');
$users = json_decode($user_data, true);


$full_name = '';
$user_email = $_SESSION['user_email'];

if (isset($_SESSION['user_email'])) {
    $logged_in_email = $_SESSION['user_email'];

    foreach ($users as $user) {
        if ($user['email'] === $logged_in_email) {
            $full_name = $user['full_name'];
            break;
        }
    }
}

$bookings_data = file_get_contents('bookings.json');
$bookings = json_decode($bookings_data, true);


$user_bookings = array_filter($bookings, function ($booking) use ($user_email) {
    return $booking['user_email'] === $user_email;
});

$cars_data = file_get_contents('cars.json');
$cars = json_decode($cars_data, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

         .header {
            position: fixed;
            top: 0;
            right: 0;
            padding: 10px 20px;
            background-color: #007BFF;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            z-index: 10;
        }

        .header .header-title {
            flex-grow: 1;
            text-align: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .header button {
            padding: 10px;
            margin: 0 5px;
            background-color: white;
            color: #007BFF;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .header button:hover {
            background-color: #0056b3;
            color: white;
        }

        .user-greeting {
            color: white;
            font-size: 18px;
            font-weight: bold;
            margin-right: 20px;
        }

        .container {
            padding: 60px;
        }

        h1 {
            margin-top: 0;
        }

        .booking-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 300px;
            margin: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .car-card img {
            width: 100%;
            height: 150px;
            padding: 15px;
            object-fit: cover;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .booking-card h2 {
            margin: 0;
            padding: 15px;
            font-size: 18px;
            color: #333;
        }

        .booking-card p {
            margin: 5px 0;
            padding-left: 15px;
            padding-right: 15px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
    <div class="header-title">iKarRental</div>

            <?php if (isset($_SESSION['user_email'])):?>
            <div class="user-greeting">
                Hi, <a href="profile.php" style="color: white; text-decoration: underline;"><?= htmlspecialchars($_SESSION['user_email']) ?></a>
            </div>
            <button onclick="window.location.href='logout.php'">Logout</button>
        <?php else: ?>
            <button onclick="window.location.href='login.php'">Login</button>
            <button onclick="window.location.href='register.php'">Register</button>
        <?php endif; ?>
        </div>
        </div>    
    </div>


    <div class="container">
    <h1>You are logged in as <?=htmlspecialchars($full_name)?></h1>
        <h2>Your Bookings</h2>

        <?php if (!empty($user_bookings)): ?>
            <?php foreach ($user_bookings as $booking): ?>
                <?php 

                    
                    $car = array_filter($cars, function ($c) use ($booking) {
                        return $c['id'] === (int)$booking['car_id'];
                    });
                    $car = reset($car); 
                ?>
                <?php if ($car): ?>
            <div class="booking-card">
            <img src="<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>">
                <h2>
                    <?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?> 
                    (<?= htmlspecialchars($car['year']) ?>)
                </h2>
                <p><strong>Booking ID:</strong> <?= htmlspecialchars($booking['id']) ?></p>
                <p><strong>Start Date:</strong> <?= htmlspecialchars($booking['start_date']) ?></p>
                <p><strong>End Date:</strong> <?= htmlspecialchars($booking['end_date']) ?></p>
                <p><strong>Price per Day:</strong> <?= number_format($car['daily_price_huf']) ?> HUF</p>
            </div>
        <?php else: ?>
            <p>Car details for Booking ID <?= htmlspecialchars($booking['id']) ?> not found.</p>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
    <p>You have no bookings.</p>
<?php endif; ?>
    </div>
</body>
</html>
