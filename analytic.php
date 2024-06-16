<?php
// Include database connection
include 'db_connect.php';

// Define how many results you want per page
$results_per_page = 5;

// Determine which page number visitor is currently on
$page_stock = isset($_GET['page_stock']) ? (int)$_GET['page_stock'] : 1;
$page_orders = isset($_GET['page_orders']) ? (int)$_GET['page_orders'] : 1;

// Determine the SQL LIMIT starting number for the results on the displaying page
$start_stock = ($page_stock - 1) * $results_per_page;
$start_orders = ($page_orders - 1) * $results_per_page;

// Query to retrieve sales trends (last 30 days) with overall sales price
$sales_query = "SELECT DATE(order_date) AS sale_date, SUM(quantity) AS total_quantity, SUM(sales_amount) AS total_sales_price
               FROM orders
               WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
               GROUP BY DATE(order_date)
               ORDER BY DATE(order_date) DESC";
$sales_result = mysqli_query($conn, $sales_query);
$sales_data = [];
$sales_labels = [];
$quantity_data = [];

while ($row = mysqli_fetch_assoc($sales_result)) {
    $sales_data[] = $row['total_sales_price'];
    $sales_labels[] = $row['sale_date'];
    $quantity_data[] = $row['total_quantity'];
}

// Query to retrieve stock levels (top 10 products by stock) with pagination
$stock_query = "SELECT product_name, stock
               FROM products
               ORDER BY stock DESC
               LIMIT $start_stock, $results_per_page";
$stock_result = mysqli_query($conn, $stock_query);

// Query to get the total number of stock records
$total_stock_query = "SELECT COUNT(*) AS total FROM products";
$total_stock_result = mysqli_query($conn, $total_stock_query);
$total_stock_row = mysqli_fetch_assoc($total_stock_result);
$total_stock_pages = ceil($total_stock_row['total'] / $results_per_page);

// Query to retrieve order histories (last 10 orders) with pagination
$orders_query = "SELECT orders.order_date, products.product_name, orders.quantity, orders.sales_amount, users.username AS agent_name
                FROM orders
                JOIN products ON orders.product_id = products.id
                JOIN users ON orders.agent_id = users.id
                ORDER BY orders.order_date DESC
                LIMIT $start_orders, $results_per_page";
$orders_result = mysqli_query($conn, $orders_query);

// Query to get the total number of order records
$total_orders_query = "SELECT COUNT(*) AS total FROM orders";
$total_orders_result = mysqli_query($conn, $total_orders_query);
$total_orders_row = mysqli_fetch_assoc($total_orders_result);
$total_orders_pages = ceil($total_orders_row['total'] / $results_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytical Dashboard</title>
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
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .pagination a:hover {
            background-color: #45a049;
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
        #salesChart {
            max-width: 100%;
            height: 400px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Analytical Dashboard</h1>

        <h2>Sales Trends (Last 30 Days)</h2>
        <canvas id="salesChart"></canvas>

        <script>
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($sales_labels); ?>,
                    datasets: [
                        {
                            label: 'Total Sales Amount (RM)',
                            data: <?php echo json_encode($sales_data); ?>,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Total Quantity Sold',
                            data: <?php echo json_encode($quantity_data); ?>,
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>

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

        <div class="pagination">
            <?php for ($page = 1; $page <= $total_stock_pages; $page++): ?>
                <a href="analytic.php?page_stock=<?php echo $page; ?>"><?php echo $page; ?></a>
            <?php endfor; ?>
        </div>

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

        <div class="pagination">
            <?php for ($page = 1; $page <= $total_orders_pages; $page++): ?>
                <a href="analytic.php?page_orders=<?php echo $page; ?>"><?php echo $page; ?></a>
            <?php endfor; ?>
        </div>

        <div class="back-button">
            <a href="admin.php">Back to Admin Page</a>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>
