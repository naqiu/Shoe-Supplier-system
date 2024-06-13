<style>
    table {
        max-width: 600px;
    }
</style>
<?php
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
try {
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'Agent'");
    
    if (!$stmt) {
        throw new Exception($conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $conn->close();
    exit();
}
$conn->close();
?>
<h2>Registered Agents</h2>
<a class="btn btn-s mb-3" href="createAgent.php">Add Agent</a>
<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Agent ID</th>
                <th>Username</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td>
                        <a class="btn btn-s" href="changePassword.php?id=<?php echo $row['id']; ?>">Change Password</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No agents registered.</p>
<?php endif; 

include 'footer.php';
?>
