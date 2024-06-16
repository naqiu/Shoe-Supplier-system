<style>
    td {
        max-width: 300px;
    }
</style>
<?php
include 'header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $stmt = $conn->prepare("SELECT * FROM products");
    if (!$stmt) {
        throw new Exception($conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
    $conn->close();
    exit();
}
?>
<h2>Products</h2>
<div>
    <a class="btn btn-s mb-3" href="addProduct.php">Add Product</a>
    <a class="btn btn-s mb-3" href="inventoryReports.php">Inventory Reports</a>
</div>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['product_name']; ?></td>
            <td><?php echo $row['product_description']; ?></td>
            <td>RM<?php echo $row['product_price']; ?></td>
            <td><?php echo $row['stock']; ?></td>
            <td><img src="<?php echo $row['image']; ?>" width="100"></td>
            <td>
                <a class="btn btn-s" href="updateProduct.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a class="btn btn-s" href="deleteProduct.php?id=<?php echo $row['id']; ?>">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<?php include 'footer.php'; ?>
