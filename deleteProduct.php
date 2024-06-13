<?php
include 'header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

try {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    if (!$stmt) {
        throw new Exception($conn->error);
    }
    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    echo "Record deleted successfully";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$stmt->close();
$conn->close();

header('Location: viewProduct.php');
exit();
?>