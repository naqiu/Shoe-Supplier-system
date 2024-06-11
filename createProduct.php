<?php
include 'header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['product_name'];
    $productDescription = $_POST['product_description'];
    $productPrice = $_POST['product_price'];
    $productStock = $_POST['product_stock']; // Newly added

    if (empty($productName) || empty($productDescription) || empty($productPrice) || empty($productStock)) {
        echo '<p>Please fill in all fields.</p>';
    } else {
        $supplierId = $_SESSION['user_id'];

        $query = "INSERT INTO products (product_name, product_description, product_price, supplier_id, stock) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssdii', $productName, $productDescription, $productPrice, $supplierId, $productStock);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                echo '<p>Product created successfully!</p>';
            } else {
                echo '<p>Error creating product. Please try again.</p>';
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
    <h2>Create Product</h2>
    <form method="post" action="createProduct.php">
        <label for="product_name">Product Name:</label>
        <input class="input mb-2" type="text" id="product_name" name="product_name" required><br>

        <label for="product_description">Product Description:</label>
        <textarea style="width:300px;" class="input mb-2" id="product_description" name="product_description" required></textarea><br>

        <label for="product_price">Product Price:</label>
        <input class="input mb-2" type="number" id="product_price" name="product_price" required><br>

        <label for="product_stock">Product Stock:</label> <!-- Newly added -->
        <input class="input mb-2" type="number" id="product_stock" name="product_stock" required><br>

        <button class="btn" type="submit">Create Product</button>
    </form>
</section>

<?php include 'footer.php'; ?>