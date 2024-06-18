<?php
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Function to fetch sales report data
function fetchSalesReport($conn) {
    // Fetch total sales amount and number of orders
    $totalSalesQuery = "SELECT COUNT(id) as total_orders, SUM(sales_amount) as total_sales FROM orders";
    $totalSalesResult = mysqli_query($conn, $totalSalesQuery);
    $totalSalesData = mysqli_fetch_assoc($totalSalesResult);

    // Fetch sales by product
    $salesByProductQuery = "SELECT products.product_name, SUM(orders.sales_amount) as total_sales, COUNT(orders.id) as total_orders 
                            FROM orders 
                            JOIN products ON orders.product_id = products.id 
                            GROUP BY products.id";
    $salesByProductResult = mysqli_query($conn, $salesByProductQuery);

    // Fetch sales by agent
    $salesByAgentQuery = "SELECT agent_id, SUM(sales_amount) as total_sales, COUNT(id) as total_orders FROM orders GROUP BY agent_id";
    $salesByAgentResult = mysqli_query($conn, $salesByAgentQuery);

    // Fetch sales trends (monthly)
    $salesTrendsQuery = "SELECT DATE_FORMAT(order_date, '%Y-%m') as month, SUM(sales_amount) as total_sales, COUNT(id) as total_orders 
                         FROM orders 
                         GROUP BY DATE_FORMAT(order_date, '%Y-%m')";
    $salesTrendsResult = mysqli_query($conn, $salesTrendsQuery);

    // Return all data as an associative array
    return [
        'totalSalesData' => $totalSalesData,
        'salesByProductResult' => $salesByProductResult,
        'salesByAgentResult' => $salesByAgentResult,
        'salesTrendsResult' => $salesTrendsResult,
    ];
}

$salesReportData = fetchSalesReport($conn);

?>
<h2>Sales Report</h2>

<section>
    <h3>Total Sales Performance</h3>
    <p>Total Orders: <?php echo $salesReportData['totalSalesData']['total_orders']; ?></p>
    <p>Total Sales: RM<?php echo $salesReportData['totalSalesData']['total_sales']; ?></p>
</section>

<section>
    <h3>Sales by Product</h3>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Total Orders</th>
            <th>Total Sales (RM)</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($salesReportData['salesByProductResult'])): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td><?php echo htmlspecialchars($row['total_orders']); ?></td>
                <td>RM<?php echo htmlspecialchars($row['total_sales']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>

<section>
    <h3>Sales by Agent</h3>
    <table>
        <tr>
            <th>Agent ID</th>
            <th>Total Orders</th>
            <th>Total Sales (RM)</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($salesReportData['salesByAgentResult'])): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['agent_id']); ?></td>
                <td><?php echo htmlspecialchars($row['total_orders']); ?></td>
                <td>RM<?php echo htmlspecialchars($row['total_sales']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>

<section>
    <h3>Sales Trends (Monthly)</h3>
    <table>
        <tr>
            <th>Month</th>
            <th>Total Orders</th>
            <th>Total Sales (RM)</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($salesReportData['salesTrendsResult'])): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['month']); ?></td>
                <td><?php echo htmlspecialchars($row['total_orders']); ?></td>
                <td>RM<?php echo htmlspecialchars($row['total_sales']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>

<?php
include 'footer.php';
mysqli_close($conn);
?>
