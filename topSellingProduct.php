<?php
include 'header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

function fetchTopSellingProduct($conn) {
    // Add error handling for the query
    $query = "SELECT * FROM products ORDER BY total_sold DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Error executing query: " . mysqli_error($conn);
        return;
    }

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo '<section>';
        echo '<h2>Top Selling Product:</h2>';
        echo 'Product Name: ' . htmlspecialchars($row['product_name']) . '<br>';
        echo 'Total Sold: ' . htmlspecialchars($row['total_sold']) . '<br>';
        echo '</section>';
    } else {
        echo '<p>No products available.</p>';
    }
}

// Display the top-selling product
fetchTopSellingProduct($conn);

include 'footer.php';
mysqli_close($conn);
?>

<?php
// Additional debugging script to check data in the products table
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Error executing query: " . mysqli_error($conn);
} else {
    echo '<h3>Products Table Data:</h3>';
    echo '<table border="1">';
    echo '<tr><th>ID</th><th>Product Name</th><th>Total Sold</th></tr>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['product_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['total_sold']) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}
?>

<!-- Add any additional HTML, CSS, or JavaScript specific to this page if needed -->
