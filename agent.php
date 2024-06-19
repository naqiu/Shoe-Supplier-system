<style>
    label {
        min-width: 160px;
        display: inline-block;
        vertical-align: top;
        padding-top: 9px;
    }
    td {
        padding: 5px;
    }
</style>
<?php
include 'header.php';
include 'db_connect.php';  // Ensure this path is correct



// Check if the user is logged in as an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agent') {
    header('Location: login.php');
    exit();
}

// Get the current agent's information
$user_id = $_SESSION['user_id'];
$query = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_username'])) {
    $new_username = $_POST['username'];

    // Check if the username already exists
    $check_query = "SELECT id FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('s', $new_username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error = "Username already taken.";
    } else {
        // Update the username
        $update_query = "UPDATE users SET username = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('si', $new_username, $user_id);

        if ($update_stmt->execute()) {
            $_SESSION['username'] = $new_username; // Update session username
            $success = "Username updated successfully.";
        } else {
            $error = "Failed to update username.";
        }
    }
}

?>

<h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
<p>Your role is: <?php echo htmlspecialchars($_SESSION['role']); ?></p>

<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
<?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
<h2>Update Username</h2>
<form method="post" action="">
    <label for="username">New Username:</label>
    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
    <button type="submit" name="update_username">Update</button>
</form>

<?php
$query = "SELECT orders.*, products.product_name
          FROM orders
          INNER JOIN products ON orders.product_id = products.id
          WHERE orders.agent_id = {$_SESSION['user_id']} AND (orders.approval_status = 'Approved' OR orders.approval_status = 'Rejected')
          ORDER BY orders.order_date DESC";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0): ?>
    <h3>Approved Orders:</h3>
    <table>
        <thead>
            <tr>
                <th>Order Date</th>
                <th>Product</th>
                <th>Customer Name</th>
                <th>Address</th>
                <th>Contact Number</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['customer_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['customer_contact']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No approved orders for you at the moment.</p>
<?php endif;

if (isset($_SESSION['order_approved'])) {
    echo '<script>alert("Your order has been approved!");</script>';
    unset($_SESSION['order_approved']);
}
?>
<?php include 'footer.php'; ?>
