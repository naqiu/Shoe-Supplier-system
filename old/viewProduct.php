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

include 'header.php';

// Fetch products from the database
$query = "SELECT * FROM products WHERE supplier_id = ?";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    $supplierId = $_SESSION['user_id'];
    mysqli_stmt_bind_param($stmt, 'i', $supplierId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo '<section>';
        echo '<h2>Your Products</h2>';
        echo '<table border="1">';
        echo '<tr><th>Product Name</th><th>Description</th><th>Price</th><th>Stock</th><th>Action</th></tr>';
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['product_name'] . '</td>';
            echo '<td>' . $row['product_description'] . '</td>';
            echo '<td>' . $row['product_price'] . '</td>';
            echo '<td>' . $row['stock'] . '</td>';
            echo '<td>';
            echo '<form method="post" action="deleteProduct.php">';
            echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
            echo '<button type="submit">Delete</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '</section>';
    } else {
        echo '<p>No products found.</p>';
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo '<p>Database error. Please try again later.</p>';
}

?>

<form method="post" action="logout.php">
    <button type="submit">Logout</button>
</form>

<?php include 'footer.php'; ?>