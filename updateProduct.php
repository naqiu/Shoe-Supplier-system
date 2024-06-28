<?php
include 'header.php';
include 'exception.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = $_POST['product_name'];
    $productDescription = $_POST['product_description'];
    $productPrice = $_POST['product_price'];
    $stock = $_POST['stock'];
    $newImage = $_FILES["image"]["name"];

    if ($newImage) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($newImage);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $target_file = $_POST['current_image'];
    }

    try {
        if ($stock < 0) {
            throw new NegativeStockException("Stock cannot be a negative value.");
        }

        $stmt = $conn->prepare("UPDATE products SET product_name = ?, product_description = ?, product_price = ?, stock = ?, image = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("ssdisi", $productName, $productDescription, $productPrice, $stock, $target_file, $id);

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
    
        header('Location: viewProduct.php');
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }

    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
} else {
    try {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows == 0) {
            throw new ProductNotFoundException("Product not found");
        }
    
        $product = $result->fetch_assoc();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $conn->close();
        exit();
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
<div>
    <h2>Update Product</h2>
    <form action="updateProduct.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">

        <label>Product Name:</label>
        <input class="input mb-2" type="text" name="product_name" value="<?php echo $product['product_name']; ?>"
            required><br>

        <label>Description:</label>
        <textarea class="input mb-2" style="width:300px;"
            name="product_description"><?php echo $product['product_description']; ?></textarea><br>

        <label>Price (RM):</label>
        <input class="input mb-2" type="number" step="0.01" name="product_price" value="<?php echo $product['product_price']; ?>"
            required><br>

        <label>Stock:</label>
        <input class="input mb-2" type="number" name="stock" value="<?php echo $product['stock']; ?>" required><br>

        <label>Image:</label>
        <img src="<?php echo $product['image']; ?>" width="100"><br>
        <input type="hidden" name="current_image" value="<?php echo $product['image']; ?>">
        <label>New Image:</label>
        <label for="file-upload" class="btn btn-s btn-upload mb-2">
        <input id="file-upload" type="file" name="image" id="image">
            Upload
        </label>
        <br>
        
        <button class="btn" type="submit">Update Product</button>
    </form>
</div>

<?php include 'footer.php'; ?>