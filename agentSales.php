<style>
    td {
        padding: 5px;
    }
</style>
<?php
include 'header.php'; 

$query = "SELECT orders.*, products.product_name
          FROM orders
          INNER JOIN products ON orders.product_id = products.id
          WHERE orders.agent_id = {$_SESSION['user_id']} AND (orders.approval_status = 'Approved' OR orders.approval_status = 'Rejected')
          ORDER BY orders.order_date DESC";

$result = mysqli_query($conn, $query);
$totalSales = 0;

if ($result && mysqli_num_rows($result) > 0): ?>
    <h2>Sales For <?php echo $_SESSION['username'] ?></h2>
    <table>
        <thead>
            <tr>
                <th>Order Date</th>
                <th>Product</th>
                <th>Customer Name</th>
                <th>Quantity</th>
                <th>Sales Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['order_date']; ?></td>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['sales_amount']; ?></td>
                </tr>
                <?php $totalSales += $row['sales_amount']; ?>
            <?php endwhile; ?>
        </tbody>
    </table>
    <h3>Total Sales For <?php echo $_SESSION['username'] ?>: RM<?php echo number_format($totalSales, 2); ?></h3>
<?php else: ?>
    <p>No approved orders for you at the moment.</p>
<?php endif;

include 'footer.php'; ?>