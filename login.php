<?php
session_start(); 

if (isset($_SESSION['user_email'])) {
    header('Location: index.php'); 
}

$is_logged_in = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];


    $data = file_get_contents('users.json');
    $users = json_decode($data, true); 

    foreach ($users as $user) {
        if ($user['email'] === $email && $user['password'] === $password) {
            $_SESSION['user_email'] = $email; 
            $is_logged_in = true;
            

            header('Location: index.php'); 
            exit();
        }
    }


    $login_error = "Invalid email or password.";
}


$login_prompt = isset($_GET['login_prompt']) && $_GET['login_prompt'] == 'true' ? "You need to log in to book a car." : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - iKarRental</title>
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

        .content {
            padding: 50px 20px;
            max-width: 400px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .content h2 {
            font-size: 24px;
            text-align: center;
            color: #333;
        }

        .content input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .content button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .content button:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            color: red;
            margin: 10px 0;
        }
    </style>
</head>
<body>


    <div class="content">
        <h2>Login to iKarRental</h2>

        <?php if ($login_prompt): ?>
            <div class="message"><?= htmlspecialchars($login_prompt) ?></div>
        <?php endif; ?>

        <?php if (isset($login_error)): ?>
            <div class="message"><?= htmlspecialchars($login_error) ?></div>
        <?php endif; ?>


        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <p style="text-align: center;">Don't have an account? <a href="register.php">Register here</a></p>
    </div>

</body>
</html>
