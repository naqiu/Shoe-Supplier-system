<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo "Error: Required parameters missing.";
    exit();
}

$id = mysqli_real_escape_string($conn, $_POST['id']);
$new_approval_status = mysqli_real_escape_string($conn, $_POST['status']);

$query = "UPDATE orders SET approval_status = '$new_approval_status' WHERE id = $id";

try {
    $result = mysqli_query($conn, $query);

    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }

    header('Location: supplier.php');
    exit();
} catch (Exception $e) {
    echo "Error updating approval status: " . $e->getMessage();
}

mysqli_close($conn);
?>
Explanatio