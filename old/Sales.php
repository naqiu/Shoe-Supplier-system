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

// Function to fetch and display sales by agent
function fetchSalesByAgent($conn, $supplierId) {
    $query = "SELECT agent_id, COUNT(id) as total_orders, SUM(sales_amount) as total_sales
              FROM orders
              WHERE agent_id IS NOT NULL AND EXISTS (
                  SELECT 1 FROM products WHERE products.id = orders.product_id AND products.supplier_id = $supplierId
              )
              GROUP BY agent_id";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo '<section>';
        echo '<h2>Sales Performance by Agent:</h2>';
        echo '<ul>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<li>';
            echo 'Agent ID: ' . $row['agent_id'] . '<br>';
            echo 'Total Orders: ' . $row['total_orders'] . '<br>';
            echo 'Total Sales: RM' . $row['total_sales'] . '<br>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</section>';
    } else {
        echo '<p>No sales data available.</p>';
    }
}

// Function to fetch total sales performance
function fetchTotalPerformance($conn, $supplierId) {
    $query = "SELECT COUNT(id) as total_orders, SUM(sales_amount) as total_sales
              FROM orders
              WHERE EXISTS (
                  SELECT 1 FROM products WHERE products.id = orders.product_id AND products.supplier_id = $supplierId
              )";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo '<section>';
        echo '<h2>Supplier Total Sales Performance:</h2>';
        echo '<p>Total Orders: ' . $row['total_orders'] . '</p>';
        echo '<p>Total Sales: RM' . $row['total_sales'] . '</p>';
        echo '</section>';
    } else {
        echo '<p>No sales data available.</p>';
    }
}

// Display sales information by agent
fetchSalesByAgent($conn, $_SESSION['user_id']);

// Display total sales performance
fetchTotalPerformance($conn, $_SESSION['user_id']);

include 'footer.php';
mysqli_close($conn);
?>
