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

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $supplierId = $_SESSION['user_id'];

    // Check if the product belongs to the logged-in user before deleting
    $checkQuery = "SELECT * FROM products WHERE id = ? AND supplier_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);

    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, 'ii', $productId, $supplierId);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            // Product belongs to the logged-in user, proceed with deletion
            $deleteQuery = "DELETE FROM products WHERE id = ?";
            $deleteStmt = mysqli_prepare($conn, $deleteQuery);

            if ($deleteStmt) {
                mysqli_stmt_bind_param($deleteStmt, 'i', $productId);
                $deleteResult = mysqli_stmt_execute($deleteStmt);

                if ($deleteResult) {
                    echo '<p>Product deleted successfully!</p>';
                } else {
                    echo '<p>Error deleting product. Please try again.</p>';
                }

                mysqli_stmt_close($deleteStmt);
            } else {
                echo '<p>Database error. Please try again later.</p>';
            }
        } else {
            echo '<p>You do not have permission to delete this product.</p>';
        }

        mysqli_stmt_close($checkStmt);
    } else {
        echo '<p>Database error. Please try again later.</p>';
    }
}

header('Location: viewProduct.php');
exit();
?>