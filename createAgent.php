<?php
include 'header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agentUsername = $_POST['agent_username'];
    $agentPassword = $_POST['agent_password'];

    // You may want to add more validation and error checking here

    // Insert the new agent profile into the database
    $supplierId = $_SESSION['user_id'];
    $query = "INSERT INTO users (username, password, role) VALUES ('$agentUsername', '$agentPassword', 'agent')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Agent profile created successfully, redirect to supplier.php
        echo '<p>Agent Created Successfully</p>';      
        exit();
    } else {
        echo '<p>Agent has been made. Please try again.</p>';
    }
}


?>

<!-- Add your create agent profile form here -->
<section>
    <h2>Create Agent Profile</h2>
    <form method="post" action="createAgent.php">
        <label for="agent_username">Agent Username:</label>
        <input type="text" id="agent_username" name="agent_username" required><br>

        <label for="agent_password">Agent Password:</label>
        <input type="password" id="agent_password" name="agent_password" required><br>

        <button type="submit">Create Agent Profile</button>
    </form>
</section>

<?php include 'footer.php'; ?>