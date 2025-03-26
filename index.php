<?php
session_start();

$data = file_get_contents('cars.json');
$cars = json_decode($data, true);

$bookings_data = file_get_contents('bookings.json');
$bookings = json_decode($bookings_data, true);

$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : null;
$transmission = isset($_GET['transmission']) && $_GET['transmission'] !== '' ? $_GET['transmission'] : null;
$seats = isset($_GET['seats']) && $_GET['seats'] !== '' ? (int)$_GET['seats'] : null;
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;


function isCarAvailable($car_id, $start_date, $end_date, $bookings) {
    foreach ($bookings as $booking) {
        if ($booking['car_id'] == $car_id) {
            if (
                ($start_date >= $booking['start_date'] && $start_date <= $booking['end_date']) ||
                ($end_date >= $booking['start_date'] && $end_date <= $booking['end_date']) ||
                ($start_date <= $booking['start_date'] && $end_date >= $booking['end_date'])
            ) {
                return false;  
            }
        }
    }
    return true;  
}


$filtered_cars = array_filter($cars, function ($car) use ($min_price, $max_price, $transmission, $seats) {
    if ($min_price !== null && (int)$car['daily_price_huf'] < $min_price) {
        return false;
    }
    if ($max_price !== null && (int)$car['daily_price_huf'] > $max_price) {
        return false;
    }
    if ($transmission !== null && $car['transmission'] !== $transmission) {
        return false;
    }
    if ($seats !== null && (int)$car['passengers'] < $seats) {
        return false; 
    }
    return true;
});


$is_logged_in = isset($_SESSION['user_email']) ? true : false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental</title>
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


        .content {
            padding: 120px 20px 20px; 
        }

        .filter-form {
            margin-top: 100px; 
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .filter-form label, .filter-form input, .filter-form select {
            margin: 5px;
        }

        .car-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .car-card {
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
            object-fit: cover;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .car-card-content {
            padding: 15px;
            flex: 1;
        }

        .car-card h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .car-card p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }

        .car-card form {
            margin-top: 10px;
        }

        .car-card input[type="date"] {
            width: calc(50% - 5px);
            padding: 5px;
            margin-right: 5px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .car-card button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .car-card button:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>

    <div class="header">
    <div class="header-title">iKarRental</div>

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

    <form class="filter-form" method="GET" action="">
        <label for="min_price">Min Price:</label>
        <input type="number" id="min_price" name="min_price" value="<?= htmlspecialchars($min_price) ?>">

        <label for="max_price">Max Price:</label>
        <input type="number" id="max_price" name="max_price" value="<?= htmlspecialchars($max_price) ?>">

        <label for="transmission">Transmission:</label>
        <select id="transmission" name="transmission">
            <option value="">Any</option>
            <option value="Automatic" <?= $transmission === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
            <option value="Manual" <?= $transmission === 'Manual' ? 'selected' : '' ?>>Manual</option>
        </select>

        <label for="seats">Min Passengers:</label>
        <input type="number" id="seats" name="seats" value="<?= htmlspecialchars($seats) ?>">
        
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">


        <button type="submit">Apply Filters</button>
    </form>


    <div class="content">
        <div class="car-list">
        <?php if (!empty($filtered_cars)): ?>
            <?php foreach ($filtered_cars as $car): ?>
                <div class="car-card">
                    <a href="detail.php?id=<?= htmlspecialchars($car['id']) ?>">
                        <img src="<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>">
                        <div class="car-card-content">
                            <h2>
                                <?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?> (<?= $car['year'] ?>)
                            </h2>
                            <p><strong>Transmission:</strong> <?= htmlspecialchars($car['transmission']) ?></p>
                            <p><strong>Fuel Type:</strong> <?= htmlspecialchars($car['fuel_type']) ?></p>
                            <p><strong>Passengers:</strong> <?= htmlspecialchars($car['passengers']) ?></p>
                            <p><strong>Daily Price:</strong> <?= number_format($car['daily_price_huf']) ?> HUF</p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No cars found matching your filters.</p>
        <?php endif; ?>
</div>

    </div>

</body>
</html>
