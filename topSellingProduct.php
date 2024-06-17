<?php
include 'header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

function fetchTopSellingProduct($conn) {
    $query = "
        SELECT 
            p.product_name,
            o.product_id,
            o.total_quantity
        FROM 
            (SELECT 
                 product_id,
                 SUM(quantity) AS total_quantity
             FROM 
                 orders
             WHERE 
                 approval_status = 'Approved'
             GROUP BY 
                 product_id
             ORDER BY 
                 total_quantity DESC
             LIMIT 1) AS o
        JOIN 
            products AS p
        ON 
            o.product_id = p.id
    ";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo '<section>';
        echo '<h2>Top Selling Product:</h2>';
        echo 'Product Name: ' . $row['product_name'] . '<br>';
        echo 'Total Sold: ' . $row['total_quantity'] . '<br>';
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

<!-- Add any additional HTML, CSS, or JavaScript specific to this page if needed -->
