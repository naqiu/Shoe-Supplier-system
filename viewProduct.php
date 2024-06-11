<?php
include 'header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$query = "SELECT * FROM products WHERE supplier_id = ?";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    $supplierId = $_SESSION['user_id'];
    mysqli_stmt_bind_param($stmt, 'i', $supplierId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo '<section>';
        echo '<h2>Your Products</h2>';
        echo '<table border="1">';
        echo '<tr><th>Product Name</th><th>Description</th><th>Price</th><th>Stock</th><th>Action</th></tr>';
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['product_name'] . '</td>';
            echo '<td>' . $row['product_description'] . '</td>';
            echo '<td>' . $row['product_price'] . '</td>';
            echo '<td>' . $row['stock'] . '</td>';
            echo '<td>';
            echo '<form method="post" action="deleteProduct.php">';
            echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
            echo '<button type="submit">Delete</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '</section>';
    } else {
        echo '<p>No products found.</p>';
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo '<p>Database error. Please try again later.</p>';
}

?>
<li><a href="createProduct.php">Create Product</a></li>
<li><a href="updateProduct.php">Update Product Stocks</a></li>
<?php include 'footer.php'; ?>