<style>
    td {
        max-width: 300px;
    }

    .flex-container {
        display: flex;
        flex-wrap: wrap;
    }

    .flex-container>a {
        width: 200px;
        background-color: #f2f2f2;
        margin: 8px;
        text-decoration: none;
        color: #000;
    }

    .flex-container>a>img {
        width: 100%;
    }

    .flex-container>a>div {
        padding: 10px;
    }

    .flex-container>a:first-child {
        margin-left: 0;
    }

    .alert-cont {
        position: fixed;
        top: 35px;
        right: 20px;
    }

    .alert {
        padding: 20px;
        background-color: #f44336;
        color: white;
        opacity: 1;
        transition: opacity 0.6s;
        margin-bottom: 15px;
        width: fit-content;
    }

    .alert.warning {
        background-color: #ff9800;
    }

    .closebtn {
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    .closebtn:hover {
        color: black;
    }
</style>
<?php

class AdminAlert {
    public function triggerAlert($productName) {
        ?>
        <div class="alert warning">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Low stock alert: <?= $productName ?> needs restocking!
        </div>
        <?php
    }
}

include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


function fetchLowStockProducts($conn)
{
    // Prepare SQL statement to fetch products with stock below restock threshold
    $stmt = $conn->prepare("
        SELECT * FROM products 
        WHERE stock < (SELECT restock_threshold FROM admin WHERE user_id = ?)
    ");

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    // Bind parameters and execute query
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    // Get result and close statement
    $result = $stmt->get_result();
    $stmt->close();

    // Return fetched products
    return $result->fetch_all(MYSQLI_ASSOC);
}

$products = fetchLowStockProducts($conn);
function fetchPendingOrders($conn)
{
    $query = "SELECT orders.*, products.product_name, users.username AS agent_username
              FROM orders
              INNER JOIN products ON orders.product_id = products.id
              INNER JOIN users ON orders.agent_id = users.id
              WHERE orders.approval_status = 'Pending'
              ORDER BY orders.order_date DESC";

    try {
        // Execute the query
        $result = mysqli_query($conn, $query);

        if (!$result) {
            throw new Exception(mysqli_error($conn));
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $conn->close();
        exit();
    }
    $conn->close(); ?>
    <h3>Orders Pending Approval:</h3>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order Date</th>
                    <th>Agent</th>
                    <th>Product</th>
                    <th>Address</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['order_date']; ?></td>
                        <td><?php echo $row['agent_username']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['customer_address']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>
                            <form method="post" action="updateOrderStatus.php">
                                <a class="btn btn-s" href="xx.php?id=<?php echo $row['id']; ?>">Details</a>
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="status" value="Approved">
                                <button class="btn btn-s" type="submit">Approve</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders pending approval.</p>
    <?php endif;
}

?>

<h1>Welcome, <?php echo $_SESSION['username'] ?>!</h1>
<?php if (!empty($products)) : ?>
        <h3>Products needing restocking:</h3>
        <div class="alert-cont">
        <?php $adminAlert = new AdminAlert(); ?>
        <?php foreach ($products as $product) : ?>
            <?php $adminAlert->triggerAlert($product['product_name']); ?>
            <?php endforeach; ?>
    </div>
        <div class="flex-container">
            <?php foreach ($products as $product) : ?>
                <a href="updateProduct.php?id=<?php echo $product['id']; ?>">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>"><br>
                    <div>
                        <b><?php echo htmlspecialchars($product['product_name']); ?></b><br>
                        Current Stock: <?php echo htmlspecialchars($product['stock']); ?><br>
                    </div>
                </a>
                
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>No products need restocking at the moment.</p>
    <?php endif; ?>

<?php
// Display orders with 'Pending' approval status
fetchPendingOrders($conn);


if (isset($_SESSION['order_approved'])) {
    echo '<script>alert("Order has been updated!");</script>';
    unset($_SESSION['order_approved']);
}
?>

<?php
include 'footer.php';
?>