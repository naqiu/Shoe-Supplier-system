<?php
include 'header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

function fetchTopSellingProduct($conn, $supplierId) {
    $query = "SELECT * FROM products WHERE supplier_id = $supplierId ORDER BY total_sold DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo '<section>';
        echo '<h2>Top Selling Product:</h2>';
        echo 'Product Name: ' . $row['product_name'] . '<br>';
        echo 'Total Sold: ' . $row['total_sold'] . '<br>';
        echo '</section>';
    } else {
        echo '<p>No products available.</p>';
    }
}

// Display the top-selling product
fetchTopSellingProduct($conn, $_SESSION['user_id']);

include 'footer.php';
mysqli_close($conn);
?>

<!-- Add any additional HTML, CSS, or JavaScript specific to this page if needed -->
