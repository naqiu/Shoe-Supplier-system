<style>
    table {
        max-width: 600px;
    }
</style>
<?php
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

function fetchSalesByAgent($conn)
{
    $query = "SELECT agent_id, COUNT(id) as total_orders, SUM(sales_amount) as total_sales
              FROM orders
              WHERE agent_id IS NOT NULL AND EXISTS (
                  SELECT 1 FROM products WHERE products.id = orders.product_id
              )
              GROUP BY agent_id";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0): ?>
        <h3>Sales Performance by Agent:</h3>
        <table>
            <tr>
                <th>Agent ID</th>
                <th>Total Orders</th>
                <th>Total Sales (RM)</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['agent_id']; ?></td>
                    <td><?php echo $row['total_orders']; ?></td>
                    <td>RM<?php echo $row['total_sales']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

    <?php else: ?>
        <p>No sales data available.</p>
    <?php endif;
}

// Function to fetch total sales performance
function fetchTotalPerformance($conn)
{
    $query = "SELECT COUNT(id) as total_orders, SUM(sales_amount) as total_sales
              FROM orders
              WHERE EXISTS (
                  SELECT 1 FROM products WHERE products.id = orders.product_id
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
?>
<a class="btn btn-s" href="topSellingProduct.php">Top Selling Product</a>
<?php

// Display sales information by agent
fetchSalesByAgent($conn);

// Display total sales performance
fetchTotalPerformance($conn);

include 'footer.php';
mysqli_close($conn);
?>