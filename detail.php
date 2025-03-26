<?php
session_start();
// Include the necessary files for the booking logic and storage management
require_once 'storage.php';

// Load the car details from the JSON file
$storage = new Storage(new JsonIO('bookings.json'));
$data = file_get_contents('cars.json');
$cars = json_decode($data, true);

$booking_data = file_get_contents('bookings.json');
$bookings = json_decode($booking_data, true); 


$car_id = isset($_GET['id']) ? $_GET['id'] : null;

$car = null;
if ($car_id) {
    foreach ($cars as $c) {
        if ($c['id'] == $car_id) {
            $car = $c;
            break;
        }
    }
}

if (!$car) {
    echo "Car not found!";
    exit;
}

$is_logged_in = isset($_SESSION['user_email']) ? true : false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_logged_in) {
    header('Location: login.php?login_prompt=true');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_logged_in) {

    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    $user_email = $_SESSION['user_email']; 

    $booking_id = $storage->addBooking($start_date, $end_date, $user_email, $car_id);


    if ($booking_id) {
        $_SESSION['booking_details'] = [
            'booking_id' => $booking_id,
            'car' => $car,  
            'start_date' => $start_date,
            'end_date' => $end_date
        ];


        header('Location: booking_confirmation.php');
        exit(); 
    } else {

        echo "The car is already booked for the selected dates. Please choose a different date range.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental - <?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        h1 {
            text-align: center;
            padding: 20px;
            background-color: #007BFF;
            color: white;
            margin: 0;
        }

        .user-greeting {
            color: white;
            font-size: 18px;
            font-weight: bold;
            margin-right: 20px;
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

        .content {
            padding: 120px 20px 20px;
        }

        .car-details {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .car-details img {
            width: 400px;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }

        .car-details .details {
            max-width: 600px;
        }

        .car-details .details h2 {
            font-size: 24px;
            color: #333;
        }

        .car-details .details p {
            font-size: 16px;
            color: #666;
            margin: 10px 0;
        }

        .car-details .details button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .car-details .details button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="header">
    <div class="header-title">iKarRental</div>

        <?php if ($is_logged_in): ?>

            <div class="user-greeting">
                Hi, <?= htmlspecialchars($_SESSION['user_email']) ?>
            </div>
            <button onclick="window.location.href='logout.php'">Logout</button>
        <?php else: ?>
            <button onclick="window.location.href='login.php'">Login</button>
            <button onclick="window.location.href='register.php'">Register</button>
        <?php endif; ?>
        </div>
    </div>

    <div class="content">
        <div class="car-details">
            <img src="<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>">
            <div class="details">
                <h2><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?> (<?= $car['year'] ?>)</h2>
                <p><strong>Transmission:</strong> <?= htmlspecialchars($car['transmission']) ?></p>
                <p><strong>Fuel Type:</strong> <?= htmlspecialchars($car['fuel_type']) ?></p>
                <p><strong>Passengers:</strong> <?= htmlspecialchars($car['passengers']) ?></p>
                <p><strong>Daily Price:</strong> <?= number_format($car['daily_price_huf']) ?> HUF</p>

                <form method="POST" action="">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required>

                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required>

                    <button type="submit">Book Now</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
