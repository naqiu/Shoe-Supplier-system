<?php
include 'header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = $_POST['product_name'];
    $productDescription = $_POST['product_description'];
    $productPrice = $_POST['product_price'];
    $stock = $_POST['stock'];

    // upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    try {
        $stmt = $conn->prepare("INSERT INTO products (product_name, product_description, product_price, stock, image) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("ssdis", $productName, $productDescription, $productPrice, $stock, $target_file);
    
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
    
        echo "<p>Product created successfully!</p>";
    } catch (Exception $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }

    $stmt->close();
    $conn->close();
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
<div>
    <h2>Add Product</h2>
    <form action="addProduct.php" method="post" enctype="multipart/form-data">

        <label>Product Name:</label>
        <input class="input mb-2" type="text" name="product_name" required><br>

        <label>Description:</label>
        <textarea class="input mb-2" style="width:300px;" name="product_description"></textarea><br>

        <label>Price (RM):</label>
        <input class="input mb-2" type="number" step="0.01" name="product_price" required><br>

        <label>Stock:</label>
        <input class="input mb-2" type="number" name="stock" required><br>

        <label>Image:</label>
        <label for="file-upload" class="btn btn-s btn-upload mb-2">
        <input id="file-upload" type="file" name="image" id="image">
            Upload
        </label>
        <br>

        <button class="btn" type="submit">Add Product</button>
    </form>
</div>

<?php include 'footer.php'; ?>