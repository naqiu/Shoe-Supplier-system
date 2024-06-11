<?php
include 'header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['new_approval_status'])) {
    $orderId = $_POST['id'];
    $newApprovalStatus = $_POST['new_approval_status'];

    // Update the approval status in the database
    $updateQuery = "UPDATE orders SET approval_status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $newApprovalStatus, $orderId);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            // Approval status updated successfully
            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            // Set a session variable to indicate approval
            $_SESSION['order_approved'] = true;

            header('Location: supplier.php');
            exit();
        } else {
            echo '<p>Error updating approval status. Please try again.</p>';
        }
    } else {
        echo '<p>Database error. Please try again later.</p>';
    }
} else {
    // Redirect if accessed without proper POST data
    header('Location: supplier.php');
    exit();
}
?>