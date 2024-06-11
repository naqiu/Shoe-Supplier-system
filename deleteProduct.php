<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $supplierId = $_SESSION['user_id'];

    // Check if the product belongs to the logged-in user before deleting
    $checkQuery = "SELECT * FROM products WHERE id = ? AND supplier_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);

    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, 'ii', $productId, $supplierId);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            // Product belongs to the logged-in user, proceed with deletion
            $deleteQuery = "DELETE FROM products WHERE id = ?";
            $deleteStmt = mysqli_prepare($conn, $deleteQuery);

            if ($deleteStmt) {
                mysqli_stmt_bind_param($deleteStmt, 'i', $productId);
                $deleteResult = mysqli_stmt_execute($deleteStmt);

                if ($deleteResult) {
                    echo '<p>Product deleted successfully!</p>';
                } else {
                    echo '<p>Error deleting product. Please try again.</p>';
                }

                mysqli_stmt_close($deleteStmt);
            } else {
                echo '<p>Database error. Please try again later.</p>';
            }
        } else {
            echo '<p>You do not have permission to delete this product.</p>';
        }

        mysqli_stmt_close($checkStmt);
    } else {
        echo '<p>Database error. Please try again later.</p>';
    }
}

header('Location: viewProduct.php');
exit();
?>