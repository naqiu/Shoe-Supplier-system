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

// Function to fetch products below restock threshold


// Function to fetch products below restock threshold
function fetchLowStockProducts($conn, $supplierId) {
    $query = "SELECT * FROM products WHERE supplier_id = $supplierId AND stock < restock_threshold";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo '<section>';
        echo '<h2>Products needing restocking:</h2>';
        echo '<ul>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<li>';
            echo 'Product Name: ' . $row['product_name'] . '<br>';
            echo 'Current Stock: ' . $row['stock'] . '<br>';
            echo 'Restock Threshold: ' . $row['restock_threshold'] . '<br>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</section>';

        // Notify the supplier about low stock
        echo '<script>alert("Some products need restocking!");</script>';
    } else {
        echo '<p>No products need restocking at the moment.</p>';
    }
}

// Function to fetch orders with 'Pending' approval status
function fetchPendingOrders($conn, $supplierId) {
    $query = "SELECT orders.*, products.product_name, users.username AS agent_username
              FROM orders
              INNER JOIN products ON orders.product_id = products.id
              INNER JOIN users ON orders.agent_id = users.id
              WHERE products.supplier_id = $supplierId AND orders.approval_status = 'Pending'
              ORDER BY orders.order_date DESC";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo '<section>';
        echo '<h2>Orders Pending Approval:</h2>';
        echo '<ul>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<li>';
            echo 'Product: ' . $row['product_name'] . '<br>';
            echo 'Customer Name: ' . $row['customer_name'] . '<br>';
            echo 'Address: ' . $row['customer_address'] . '<br>';
            echo 'Contact Number: ' . $row['customer_contact'] . '<br>';
            echo 'Quantity: ' . $row['quantity'] . '<br>';
            echo 'Order Date: ' . $row['order_date'] . '<br>';
            echo 'Agent: ' . $row['agent_username'] . '<br>';
            echo 'Approval Status: ' . $row['approval_status'] . '<br>';

            echo '<form method="post" action="updateApprovalStatus.php">';
            echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
            echo '<select name="new_approval_status">';
            echo '<option value="Approved">Approved</option>';
            echo '</select>';
            echo '<button type="submit">Update Approval Status</button>';
            echo '</form>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</section>';
    } else {
        echo '<p>No orders pending approval.</p>';
    }
}



?>

<section>
    <h2>Welcome, Supplier!</h2>
    <p>You can create a new agent profile:</p>
    <a href="createAgent.php">Create Agent Profile</a>

    <p>You can manage your products:</p>
    <ul>
        <li><a href="createProduct.php">Create Product</a></li>
        <li><a href="updateProduct.php">Update Product Stocks</a></li>
        <li><a href="viewProduct.php">View Products</a></li>
        <li><a href="viewAgents.php">Profile Update</a></li>
        <li><a href="Sales.php">Sales</a></li>
        <li><a href="topSellingProduct.php">Top Selling Product</a></li>
       
    </ul>
</section>

<?php
// Display low stock products
fetchLowStockProducts($conn, $_SESSION['user_id']);

// Display orders with 'Pending' approval status
fetchPendingOrders($conn, $_SESSION['user_id']);


include 'footer.php';
mysqli_close($conn);
if (isset($_SESSION['order_approved'])) {
    echo '<script>alert("Order has been updated!");</script>';
    // Unset the session variable to avoid displaying the alert multiple times
    unset($_SESSION['order_approved']);
}
?>

<form method="post" action="logout.php">
    <button type="submit">Logout</button>
</form>