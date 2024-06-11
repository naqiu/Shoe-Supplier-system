<?php
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id']; // This will be passed from the form
    $productPrice = $_POST['product_price'];
    $productName = $_POST['product_name'];
    $newStock = $_POST['new_stock']; // Newly added field for updated stock

    if ( empty($productName) ||empty($productPrice) || empty($newStock)) {
        echo '<p>Please fill in all fields.</p>';
    } else {
        $supplierId = $_SESSION['user_id'];
        
        $query = "UPDATE products SET product_name = ?, product_price = ?, stock = ? WHERE id = ? AND supplier_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sdiis', $productName, $productPrice, $newStock, $productId, $supplierId);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                echo '<p>Product and Stock updated successfully!</p>';
            } else {
                echo '<p>Error updating product and stock. Please try again.</p>';
            }
        } else {
            echo '<p>Database error. Please try again later.</p>';
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<style>
    label {
        min-width: 160px;
        display: inline-block;
        vertical-align: top;
        padding-top: 9px;
    }
</style>
<section>
    <h2>Update Product Stock</h2>
    <form method="post" action="updateProduct.php">
        <label for="product_id">Product ID:</label>
        <input class="input mb-2" type="text" id="product_id" name="product_id" required><br>
        
        <label for="product_name">Product Name:</label>
        <input class="input mb-2" type="text" id="product_name" name="product_name" required><br>

        <label for="product_price">Product Price:</label>
        <input class="input mb-2" type="number" id="product_price" name="product_price" required><br>

        <label for="new_stock">New Stock Quantity:</label>
        <input class="input mb-2" type="number" id="new_stock" name="new_stock" required><br>

        <button class="btn" type="submit">Update Stock</button>
    </form>
</section>

<?php include 'footer.php'; ?>