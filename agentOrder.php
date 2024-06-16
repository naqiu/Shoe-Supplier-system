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

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0):
    $product = mysqli_fetch_assoc($result);
    ?>

    <h2>Place Order</h2>
    <h3>Product Details</h3>
    <p><img src="<?php echo $product['image']; ?>" width="200"></p>
    <p><strong>Product Name:</strong> <?php echo $product['product_name']; ?></p>
    <p><strong>Description:</strong> <?php echo $product['product_description']; ?></p>
    <p><strong>Price (RM):</strong> <?php echo $product['product_price']; ?></p>
    <p><strong>Stock:</strong> <?php echo $product['stock']; ?></p>
    
    <form method="post" action="agentOrder.php">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <h3>Customer Details</h3>
        <label>Customer Name:</label>
        <input class="input mb-2" type="text" name="customer_name" required><br>

        <label>Customer Address:</label>
        <textarea class="input mb-2" style="width:300px;"
            name="customer_address" required></textarea><br>

        <label>Customer Contact:</label>
        <input class="input mb-2" type="text" name="customer_contact" required><br>

        <label>Order Quantity:</label>
        <input class="input mb-2" type="number" name="order_quantity" min="1" max="<?php echo $product['stock']; ?>" required><br>

        <button class="btn" type="submit">Place Order</button>
    </form>
<?php else: ?>
    <p>No products available.</p>
<?php endif;
?>
<?php include 'footer.php'; ?>