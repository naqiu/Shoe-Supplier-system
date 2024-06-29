<?php
// Include database connection
include 'header.php';

// Define how many results you want per page
$results_per_page = 5;

// Determine which page number visitor is currently on
$page_stock = isset($_GET['page_stock']) ? (int) $_GET['page_stock'] : 1;
$page_orders = isset($_GET['page_orders']) ? (int) $_GET['page_orders'] : 1;

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

// Strategy Pattern Implementation for Order Histories
// Strategy interface
interface OrderSortStrategy
{
    public function sort(array $data): array;
}

// Concrete strategy for sorting by order date
class SortByOrderDateStrategy implements OrderSortStrategy
{
    public function sort(array $data): array
    {
        usort($data, function ($a, $b) {
            return strcmp($b['order_date'], $a['order_date']);
        });
        return $data;
    }
}

// Concrete strategy for sorting by product name
class SortByProductNameStrategy implements OrderSortStrategy
{
    public function sort(array $data): array
    {
        usort($data, function ($a, $b) {
            return strcmp($a['product_name'], $b['product_name']);
        });
        return $data;
    }
}

// Concrete strategy for sorting by quantity
class SortByQuantityStrategy implements OrderSortStrategy
{
    public function sort(array $data): array
    {
        usort($data, function ($a, $b) {
            return $b['quantity'] - $a['quantity'];
        });
        return $data;
    }
}

// Concrete strategy for sorting by sales amount
class SortBySalesAmountStrategy implements OrderSortStrategy
{
    public function sort(array $data): array
    {
        usort($data, function ($a, $b) {
            return $b['sales_amount'] - $a['sales_amount'];
        });
        return $data;
    }
}

// Context that uses a strategy
class OrderSortContext
{
    private $strategy;

    public function __construct(OrderSortStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function setStrategy(OrderSortStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function sort(array $data): array
    {
        return $this->strategy->sort($data);
    }
}

// Query to retrieve order histories (last 10 orders) with pagination
$orders_query = "SELECT orders.order_date, products.product_name, orders.quantity, orders.sales_amount, users.username AS agent_name
                FROM orders
                JOIN products ON orders.product_id = products.id
                JOIN users ON orders.agent_id = users.id
                LIMIT $start_orders, $results_per_page";
$orders_result = mysqli_query($conn, $orders_query);

$order_histories = [];
while ($row = mysqli_fetch_assoc($orders_result)) {
    $order_histories[] = $row;
}

// Query to get the total number of order records
$total_orders_query = "SELECT COUNT(*) AS total FROM orders";
$total_orders_result = mysqli_query($conn, $total_orders_query);
$total_orders_row = mysqli_fetch_assoc($total_orders_result);
$total_orders_pages = ceil($total_orders_row['total'] / $results_per_page);

// Use the strategy pattern for sorting order histories
if (isset($_GET['sort_order'])) {
    switch ($_GET['sort_order']) {
        case 'product_name':
            $orderSortStrategy = new SortByProductNameStrategy();
            break;
        case 'quantity':
            $orderSortStrategy = new SortByQuantityStrategy();
            break;
        case 'sales_amount':
            $orderSortStrategy = new SortBySalesAmountStrategy();
            break;
        case 'order_date':
        default:
            $orderSortStrategy = new SortByOrderDateStrategy();
            break;
    }
} else {
    $orderSortStrategy = new SortByOrderDateStrategy();
}

$orderSortContext = new OrderSortContext($orderSortStrategy);
$sortedOrderHistories = $orderSortContext->sort($order_histories);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Analytical Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        td {
            padding: 5px;
        }

        .container {
            max-width: 900px;
            margin-left: 0;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            transition: background-color 0.3s ease;
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
                <a class="btn" href="analytic.php?page_stock=<?php echo $page; ?>"><?php echo $page; ?></a>
            <?php endfor; ?>
        </div>

        <h2>Order Histories (Last 10 Orders)</h2>
        <div style="padding-bottom: 6px; display: block;">
            <label for="sortOrder">Sort by:</label>
            <select id="sortOrder" onchange="sortOrders()">
                <option value="order_date">Order Date</option>
                <option value="product_name">Product Name</option>
                <option value="quantity">Quantity</option>
                <option value="sales_amount">Sales Amount</option>
            </select>
        </div>
        <script>
            function sortOrders() {
                var sortOrder = document.getElementById("sortOrder").value;
                window.location.href = "analytic.php?sort_order=" + sortOrder;
            }
        </script>
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
                <?php foreach ($sortedOrderHistories as $row): ?>
                    <tr>
                        <td><?php echo $row['order_date']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>RM <?php echo number_format($row['sales_amount'], 2); ?></td>
                        <td><?php echo $row['agent_name']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php for ($page = 1; $page <= $total_orders_pages; $page++): ?>
                <a class="btn" href="analytic.php?page_orders=<?php echo $page; ?>"><?php echo $page; ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>