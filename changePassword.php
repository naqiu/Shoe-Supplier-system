<?php
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if (isset($_GET['id'])) {
    $agentId = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch agent information based on the provided ID
    $query = "SELECT * FROM users WHERE id = $agentId AND role = 'Agent'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $agent = mysqli_fetch_assoc($result);

        // Handle password change form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);

            // Update the agent's password
            $updateQuery = "UPDATE users SET password = '$newPassword' WHERE id = $agentId";
            $updateResult = mysqli_query($conn, $updateQuery);

            if ($updateResult) {
                echo '<script>alert("Password updated successfully!");</script>';
            } else {
                echo '<script>alert("Error updating password!");</script>';
            }
        }
?>

<section>
    <h2>Change Password for <?php echo $agent['username']; ?></h2>
    <form method="post" action="">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required>

        <button type="submit">Change Password</button>
    </form>
</section>

<?php
    } else {
        echo '<p>Agent not found.</p>';
    }
} else {
    echo '<p>Agent ID not provided.</p>';
}

include 'footer.php';
mysqli_close($conn);
?>
