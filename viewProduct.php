<style>
    .btn-s {
        padding: 6px 10px !important;
        font-size: 12px !important;
        border-radius: 8px !important;
        background-color: #ffffff !important;
        color: #000 !important;
        border: 2px solid #000 !important;
    }

    th {
        padding: 5px;
        font-size: 16px;
        font-weight: 700;
        text-align: center;
        cursor: pointer;
        border: none;
        background-color: var(--primary);
        color: #fff;
        text-decoration: none;
    }
    form{
        margin-bottom: 0 !important;
    }
</style>
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
        echo '<h2>Products</h2>';
        echo '<table>';
        echo '<tr><th class="px-3">Product Name</th><th class="px-3">Description</th><th class="px-3">Price</th><th class="px-3">Stock</th><th class="px-3">Action</th></tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['product_name'] . '</td>';
            echo '<td>' . $row['product_description'] . '</td>';
            echo '<td>' . $row['product_price'] . '</td>';
            echo '<td>' . $row['stock'] . '</td>';
            echo '<td>';
            echo '<form method="post" action="deleteProduct.php">';
            echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
            echo '<button class="btn btn-s" type="submit">Delete</button>';
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
<p><a class="btn btn-s" href="createProduct.php">Create Product</a></p>
<p><a class="btn btn-s" href="updateProduct.php">Update Product Stocks</a></p>
<?php include 'footer.php'; ?>