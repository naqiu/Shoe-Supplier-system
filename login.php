<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle login form submission
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate users credentials (you may need to enhance this part)
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $users = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $users['id'];
        $_SESSION['username'] = $username;

        if ($users['role'] == 'supplier') {
            header('Location: supplier.php');
            exit();
        } elseif ($users['role'] == 'agent') {
            header('Location: agent.php');
            exit();
        }
    } else {
        $error_message = "Invalid credentials. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="css/site.css" media="screen">
    <title>VANtastic</title>
    <style>
        label {
            min-width: 80px;
            display: inline-block;
        }

        .center {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .input {
            width: 200px;
        }

        .card {
            padding: 40px;
            width: 300px;
            border: 2px solid #ccc;
            border-radius: 2px;
        }
        footer {
            padding-left: 40px;
        }
    </style>
</head>

<body>
    <div class="center">
        <div class="container card">
            <form method="post" action="login.php">

                <h2 class="mt-1">Login</h2>

                <?php if (isset($error_message)): ?>
                    <p style="color: red;"><?php echo $error_message; ?></p>
                <?php endif; ?>

                <label for="username">Username:</label>
                <input class="input mb-2" type="text" id="username" name="username" required><br>

                <label for="password">Password:</label>
                <input class="input mb-2" type="password" id="password" name="password" required><br>

                <button class="btn" type="submit">Login</button>
            </form>
        </div>
    </div>
    <?php
include 'footer.php';
?>