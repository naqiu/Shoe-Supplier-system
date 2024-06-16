<?php
// Include database connection
include 'db_connect.php';

// Query to retrieve inventory levels (all products) and total sold
$inventory_query = "SELECT p.product_name, p.stock, COALESCE(SUM(o.quantity), 0) AS total_sold
                    FROM products p
                    LEFT JOIN orders o ON p.id = o.product_id
                    GROUP BY p.product_name, p.stock
                    ORDER BY p.product_name ASC";
$inventory_result = mysqli_query($conn, $inventory_query);

// Query to retrieve stock movements (last 30 days)
$stock_movements_query = "SELECT p.product_name, o.quantity, o.order_date
                         FROM orders o
                         JOIN products p ON o.product_id = p.id
                         WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                         ORDER BY o.order_date DESC";
$stock_movements_result = mysqli_query($conn, $stock_movements_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Reports</title>
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
        <h1>Inventory Reports</h1>

        <h2>Inventory Levels</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Stock</th>
                    <th>Total Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($inventory_result)): ?>
                    <tr>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['stock']; ?></td>
                        <td><?php echo $row['total_sold']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Stock Movements (Last 30 Days)</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($stock_movements_result)): ?>
                    <tr>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['order_date']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="back-button">
            <a href="viewProduct.php">Back to Products</a>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>