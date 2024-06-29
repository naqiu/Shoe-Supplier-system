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
include 'order_facade.php'; // Include the facade class

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['order_quantity']) && isset($_POST['customer_name']) && isset($_POST['customer_address']) && isset($_POST['customer_contact'])) {
    $productId = $_POST['product_id'];
    $orderQuantity = $_POST['order_quantity'];
    $customerName = $_POST['customer_name'];
    $customerAddress = $_POST['customer_address'];
    $customerContact = $_POST['customer_contact'];

    // Initialize the OrderFacade with database connection
    $orderFacade = new OrderFacade($conn);

    // Attempt to place the order
    $orderPlaced = $orderFacade->placeOrder($productId, $orderQuantity, $customerName, $customerAddress, $customerContact);

    if ($orderPlaced) {
        echo "<p>Your order has been placed and is pending approval!</p>";
    } else {
        echo "<p>Error placing the order. Please try again.</p>";
    }
}

// Display form to place an order
// Fetch product details and display form
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
        <textarea class="input mb-2" style="width:300px;" name="customer_address" required></textarea><br>

        <label>Customer Contact:</label>
        <input class="input mb-2" type="text" name="customer_contact" required><br>

        <label>Order Quantity:</label>
        <input class="input mb-2" type="number" name="order_quantity" min="1" max="<?php echo $product['stock']; ?>" required><br>

        <button class="btn" type="submit">Place Order</button>
    </form>
<?php else: ?>
    
<?php endif;
include 'footer.php';
?>
