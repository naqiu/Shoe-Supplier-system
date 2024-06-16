<?php
// Include database connection
include 'db_connect.php';

// Query to retrieve sales trends (last 30 days) with overall sales price
$sales_query = "SELECT DATE(order_date) AS sale_date, SUM(quantity) AS total_quantity, SUM(sales_amount) AS total_sales_price
               FROM orders
               WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
               GROUP BY DATE(order_date)
               ORDER BY DATE(order_date) DESC";
$sales_result = mysqli_query($conn, $sales_query);

// Prepare data for chart.js
$sales_dates = [];
$sales_quantities = [];
$sales_prices = [];

while ($row = mysqli_fetch_assoc($sales_result)) {
    $sales_dates[] = $row['sale_date'];
    $sales_quantities[] = $row['total_quantity'];
    $sales_prices[] = $row['total_sales_price'];
}

// Reverse arrays to display in chronological order (oldest to newest)
$sales_dates = array_reverse($sales_dates);
$sales_quantities = array_reverse($sales_quantities);
$sales_prices = array_reverse($sales_prices);

// Query to retrieve stock levels (top 10 products by stock)
$stock_query = "SELECT product_name, stock
               FROM products
               ORDER BY stock DESC
               LIMIT 10";
$stock_result = mysqli_query($conn, $stock_query);

// Query to retrieve order histories (last 10 orders)
$orders_query = "SELECT orders.order_date, products.product_name, orders.quantity, orders.sales_amount, users.username AS agent_name
                FROM orders
                JOIN products ON orders.product_id = products.id
                JOIN users ON orders.agent_id = users.id
                ORDER BY orders.order_date DESC
                LIMIT 10";
$orders_result = mysqli_query($conn, $orders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytical Dashboard</title>
    <!-- Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f2f2f2;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .back-button {
            text-align: center;
            margin-top: 20px;
        }
        .back-button a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .back-button a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Analytical Dashboard</h1>

        <!-- Sales Trends Bar Chart -->
        <h2>Sales Trends (Last 30 Days)</h2>
        <canvas id="salesChart" width="400" height="200"></canvas>

        <!-- Stock Levels (Top 10 Products) Table -->
        <h2>Stock Levels (Top 10 Products)</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($stock_result)): ?>
                    <tr>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['stock']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Order Histories (Last 10 Orders) Table -->
        <h2>Order Histories (Last 10 Orders)</h2>
        <table>
            <thead>
                <tr>
                    <th>Order Date</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Sales Amount</th>
                    <th>Agent</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($orders_result)): ?>
                    <tr>
                        <td><?php echo $row['order_date']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>RM <?php echo number_format($row['sales_amount'], 2); ?></td>
                        <td><?php echo $row['agent_name']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Back Button -->
        <div class="back-button">
            <a href="admin.php">Back to Admin Page</a>
        </div>
    </div>

    <!-- JavaScript to render Sales Trends Bar Chart -->
    <script>
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($sales_dates); ?>,
                datasets: [{
                    label: 'Total Quantity Sold',
                    data: <?php echo json_encode($sales_quantities); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Total Sales Amount (RM)',
                    data: <?php echo json_encode($sales_prices); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>

    <?php include 'footer.php'; ?>

</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>