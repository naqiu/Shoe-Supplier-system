<!-- Add the following styles at the top of each PHP file (e.g., agent.php, changePassword.php) after the opening PHP tag -->

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f0f0f0;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #333;
        color: white;
        text-align: center;
        padding: 10px;
    }

    section {
        background-color: #fff;
        padding: 20px;
        margin: 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    ul {
        list-style-type: none;
        padding: 0;
    }

    li {
        margin-bottom: 10px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 10px;
    }

    form {
        margin-top: 20px;
    }

    button {
        background-color: #4caf50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    button:hover {
        background-color: #45a049;
    }

    input {
        padding: 8px;
        margin: 5px 0;
        box-sizing: border-box;
    }

    h2 {
        color: #333;
    }

    footer {
        background-color: #333;
        color: white;
        text-align: center;
        padding: 10px;
        position: fixed;
        bottom: 0;
        width: 100%;
    }
</style>

<?php
session_start();

// Check if the user is logged in as a supplier
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'header.php';

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'distribution_system';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the agent ID is provided in the URL
if (isset($_GET['id'])) {
    $agentId = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch agent information based on the provided ID
    $query = "SELECT * FROM users WHERE id = $agentId AND role = 'Agent'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $agent = mysqli_fetch_assoc($result);

        // Handle password change form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);

            // Update the agent's password
            $updateQuery = "UPDATE users SET password = '$newPassword' WHERE id = $agentId";
            $updateResult = mysqli_query($conn, $updateQuery);

            if ($updateResult) {
                echo '<script>alert("Password updated successfully!");</script>';
            } else {
                echo '<script>alert("Error updating password!");</script>';
            }
        }
?>

<section>
    <h2>Change Password for <?php echo $agent['username']; ?></h2>
    <form method="post" action="">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required>

        <button type="submit">Change Password</button>
    </form>
</section>

<?php
    } else {
        echo '<p>Agent not found.</p>';
    }
} else {
    echo '<p>Agent ID not provided.</p>';
}

include 'footer.php';
mysqli_close($conn);
?>
