
<style>
    label {
        min-width: 160px;
        display: inline-block;
        vertical-align: top;
        padding-top: 9px;
    }
</style>
<?php
include 'header.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['order_quantity']) && isset($_POST['customer_name']) && isset($_POST['customer_address']) && isset($_POST['customer_contact'])) {
    $productId = $_POST['product_id'];
    $orderQuantity = $_POST['order_quantity'];
    $customerName = $_POST['customer_name'];
    $customerAddress = $_POST['customer_address'];
    $customerContact = $_POST['customer_contact'];

    $query = "SELECT * FROM products WHERE id = $productId";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);

        if ($orderQuantity <= $product['stock']) {
            $approvalStatus = 'Pending';

            $insertQuery = "INSERT INTO orders (product_id, agent_id, customer_name, customer_address, customer_contact, quantity, approval_status, sales_amount)
                            VALUES ($productId, {$_SESSION['user_id']}, '$customerName', '$customerAddress', '$customerContact', $orderQuantity, '$approvalStatus', {$product['product_price']} * $orderQuantity)";
            $insertResult = mysqli_query($conn, $insertQuery);

            if ($insertResult) {
                // Update the stock after the order is placed
                $newStock = $product['stock'] - $orderQuantity;
                $updateQuery = "UPDATE products SET stock = $newStock WHERE id = $productId";
                $updateResult = mysqli_query($conn, $updateQuery);

                if ($updateResult) {
                    // Increment total_sold for the ordered product
                    $updateTotalSoldQuery = "UPDATE products SET total_sold = total_sold + $orderQuantity WHERE id = $productId";
                    mysqli_query($conn, $updateTotalSoldQuery);

                    echo "<p>Your order for {$product['product_name']} (Quantity: $orderQuantity) has been placed and is pending approval!</p>";
                    echo "<p>Stock updated after order placement for product ID: $productId.</p>";
                } else {
                    echo "<p>Error updating stock after the order placement. Please try again.</p>";
                }
            } else {
                echo "<p>Error placing the order. Please try again.</p>";
            }
        } else {
            echo "<p>Sorry, there is insufficient stock for {$product['product_name']}.</p>";
        }
    } else {
        echo "<p>Product not found.</p>";
    }
}

$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo '<section>';
    echo '<h1>You can place order at once</h1>';
    echo '<h2>Welcome, Agent!</h2>';
    echo '<p>Product Details:</p>';
    echo '<ul>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<li>';
        echo 'Product Name: ' . $row['product_name'] . '<br>';
        echo 'Description: ' . $row['product_description'] . '<br>';
        echo 'Price: RM' . $row['product_price'] . '<br>';
        echo 'Stock: ' . $row['stock'] . '<br>';

        echo '<form method="post" action="agent.php">';
        echo '<input class="input mb-2" type="hidden" name="product_id" value="' . $row['id'] . '">';
        echo '<label>Customer Name: </label><input class="input mb-2" type="text" name="customer_name" required><br>';
        echo '<label>Customer Address: </label><input class="input mb-2" type="text" name="customer_address" required><br>';
        echo '<label>Customer Contact: </label><input class="input mb-2" type="text" name="customer_contact" required><br>';
        echo '<label>Order Quantity: </label><input class="input mb-2" type="number" name="order_quantity" min="1" max="' . $row['stock'] . '"><br>';
        echo '<button class="btn"type="submit">Place Order</button>';
        echo '</form>';

        echo '</li>';
    }
    echo '</ul>';
    echo '</section>';
} else {
    echo '<p>No products available.</p>';
}

$query = "SELECT orders.*, products.product_name
          FROM orders
          INNER JOIN products ON orders.product_id = products.id
          WHERE orders.agent_id = {$_SESSION['user_id']} AND (orders.approval_status = 'Approved' OR orders.approval_status = 'Rejected')
          ORDER BY orders.order_date DESC";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo '<section>';
    echo '<h2>Approved Orders:</h2>';
    echo '<ul>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<li>';
        echo 'Product: ' . $row['product_name'] . '<br>';
        echo 'Customer Name: ' . $row['customer_name'] . '<br>';
        echo 'Address: ' . $row['customer_address'] . '<br>';
        echo 'Contact Number: ' . $row['customer_contact'] . '<br>';
        echo 'Quantity: ' . $row['quantity'] . '<br>';
        echo 'Order Date: ' . $row['order_date'] . '<br>';
        echo 'Approval Status: ' . $row['approval_status'] . '<br>';
        echo '</li>';
        
    }
    echo '</ul>';
    echo '</section>';
} else {
    echo '<p>No approved orders for you at the moment.</p>';
}

if (isset($_SESSION['order_approved'])) {
    echo '<script>alert("Your order has been approved!");</script>';
    unset($_SESSION['order_approved']);
}
?>
<?php include 'footer.php'; ?>