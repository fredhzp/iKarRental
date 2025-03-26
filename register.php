<?php
require_once 'storage.php'; 


$storage = new Storage(new JsonIO('users.json'));

session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }


    $existing_user = $storage->findUserByEmail($email);
    if ($existing_user !== null) {
        $errors[] = "Email is already registered.";
    }


        if (empty($errors)) {
   
        $user_id = $storage->addUser($full_name, $email, $password);
        $_SESSION['user_email'] = $email; 
        $_SESSION['message'] = "Registration successful!, you are logged in";

        header('Location: index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Car Rental</title>
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

        p {
            text-align: center;
        }
    </style>
</head>
<body>


    <div class="content">
        <h2>Register for Car Rental</h2>

        <?php if (!empty($errors)): ?>
            <div class="message">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <input type="text" name="full_name" placeholder="Full Name" value="<?= isset($full_name) ? htmlspecialchars($full_name) : '' ?>" required>
            <input type="email" name="email" placeholder="Email Address" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

</body>
</html>
