<?php
session_start();

$users = json_decode(file_get_contents('users.json'), true);
foreach ($users as $user) {
    if ($user['email'] === $_SESSION['user_email']) {
        $_SESSION['admin_status'] = $user['admin_status'];  
        break;
    }
}

if (!$_SESSION['admin_status']) {
    header('Location: profile.php');
    exit();
}

$cars = json_decode(file_get_contents('cars.json'), true);
$bookings = json_decode(file_get_contents('bookings.json'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    $new_car = [
        'id' => uniqid(),
        'brand' => $_POST['brand'],
        'model' => $_POST['model'],
        'year' => $_POST['year'],
        'transmission' => $_POST['transmission'],
        'fuel_type' => $_POST['fuel_type'],
        'passengers' => (int)$_POST['passengers'],
        'daily_price_huf' => (int)$_POST['daily_price_huf'],
        'image' => $_POST['image']
    ];
    $cars[] = $new_car;
    file_put_contents('cars.json', json_encode($cars, JSON_PRETTY_PRINT));
    header('Location: admin_profile.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_car'])) {
    foreach ($cars as &$car) {
        if ($car['id'] === $_POST['car_id']) {
            $car['brand'] = $_POST['brand'];
            $car['model'] = $_POST['model'];
            $car['year'] = $_POST['year'];
            $car['transmission'] = $_POST['transmission'];
            $car['fuel_type'] = $_POST['fuel_type'];
            $car['passengers'] = (int)$_POST['passengers'];
            $car['daily_price_huf'] = (int)$_POST['daily_price_huf'];
            $car['image'] = $_POST['image'];
            break;
        }
    }
    file_put_contents('cars.json', json_encode($cars, JSON_PRETTY_PRINT));
    header('Location: admin_profile.php');
    exit();
}


if (isset($_GET['delete_car_id'])) {

    foreach ($bookings as $key => $booking) {
        if ($booking['car_id'] === $_GET['delete_car_id']) {
            unset($bookings[$key]); 
        }
    }
 
    $cars = array_filter($cars, function ($car) {
        return $car['id'] !== (int)$_GET['delete_car_id'];
    });
    file_put_contents('cars.json', json_encode($cars, JSON_PRETTY_PRINT));
    file_put_contents('bookings.json', json_encode(array_values($bookings), JSON_PRETTY_PRINT)); // Re-index the bookings
    header('Location: admin_profile.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .header {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        .car-list, .booking-list {
            margin: 20px 0;
        }

        .car-card, .booking-card {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            background-color: white;
        }

        .car-card h2, .booking-card h2 {
            font-size: 18px;
        }

        .car-card button, .booking-card button {
            background-color: red;
            color: white;
            padding: 5px;
            border: none;
            cursor: pointer;
        }

        .car-card button:hover, .booking-card button:hover {
            background-color: darkred;
        }

        .form-container {
            margin: 20px 0;
        }

        .form-container input, .form-container select {
            padding: 10px;
            margin: 5px;
            width: 200px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Administrator Profile</h1>
    </div>

    <div class="content">

        <div class="form-container">
            <h2>Add New Car</h2>
            <form method="POST" action="admin_profile.php">
                <input type="text" name="brand" placeholder="Brand" required>
                <input type="text" name="model" placeholder="Model" required>
                <input type="number" name="year" placeholder="Year" required>
                <select name="transmission" required>
                    <option value="Automatic">Automatic</option>
                    <option value="Manual">Manual</option>
                </select>
                <input type="text" name="fuel_type" placeholder="Fuel Type" required>
                <input type="number" name="passengers" placeholder="Number of Passengers" required>
                <input type="number" name="daily_price_huf" placeholder="Daily Price (HUF)" required>
                <input type="text" name="image" placeholder="Car Image URL" required>
                <button type="submit" name="add_car">Add Car</button>
            </form>
        </div>

        <div class="booking-list">
            <h2>All Bookings</h2>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card">
                    <h3>Booking ID: <?= htmlspecialchars($booking['id']) ?></h3>
                    <p><strong>User Email:</strong> <?= htmlspecialchars($booking['user_email']) ?></p>
                    <p><strong>Car ID:</strong> <?= htmlspecialchars($booking['car_id']) ?></p>
                    <p><strong>Start Date:</strong> <?= htmlspecialchars($booking['start_date']) ?></p>
                    <p><strong>End Date:</strong> <?= htmlspecialchars($booking['end_date']) ?></p>
                    <button onclick="window.location.href='admin_profile.php?delete_booking_id=<?= $booking['id'] ?>'">Delete Booking</button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="car-list">
            <h2>All Cars</h2>
            <?php foreach ($cars as $car): ?>
                <div class="car-card">
                    <h2><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h2>
                    <p><strong>Year:</strong> <?= htmlspecialchars($car['year']) ?></p>
                    <p><strong>Transmission:</strong> <?= htmlspecialchars($car['transmission']) ?></p>
                    <p><strong>Fuel Type:</strong> <?= htmlspecialchars($car['fuel_type']) ?></p>
                    <p><strong>Price per Day:</strong> <?= number_format($car['daily_price_huf']) ?> HUF</p>
                    <button onclick="window.location.href='admin_profile.php?edit_car_id=<?= $car['id'] ?>'">Edit Car</button>
                    <button onclick="window.location.href='admin_profile.php?delete_car_id=<?= $car['id'] ?>'">Delete Car</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
