<?php
include 'header.php';

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

<p>Your role is: <?php echo htmlspecialchars($_SESSION['role']); ?></p>

<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
<?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
<h2>Update Username</h2>
<form method="post" action="">
    <label for="username">New Username:</label>
    <input class="input" type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
    <button class="btn" type="submit" name="update_username">Update</button>
</form>

<?php include 'footer.php'; ?>