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

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'distribution_system';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['new_approval_status'])) {
    $orderId = $_POST['id'];
    $newApprovalStatus = $_POST['new_approval_status'];

    // Update the approval status in the database
    $updateQuery = "UPDATE orders SET approval_status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $newApprovalStatus, $orderId);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            // Approval status updated successfully
            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            // Set a session variable to indicate approval
            $_SESSION['order_approved'] = true;

            header('Location: supplier.php');
            exit();
        } else {
            echo '<p>Error updating approval status. Please try again.</p>';
        }
    } else {
        echo '<p>Database error. Please try again later.</p>';
    }
} else {
    // Redirect if accessed without proper POST data
    header('Location: supplier.php');
    exit();
}
?>