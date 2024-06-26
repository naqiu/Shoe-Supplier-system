<?php
include 'user.php'; 

class Agent implements User {
    public function createUser($conn, $username, $password) {
        // Insert the new agent profile into the database
        $supplierId = $_SESSION['user_id'];
        $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'agent')";
        $result = mysqli_query($conn, $query);

        return $result;
    }
}

include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agentUsername = $_POST['agent_username'];
    $agentPassword = $_POST['agent_password'];

    // You may want to add more validation and error checking here

    // Create Agent using Factory Method
    $agent = new Agent();
    $result = $agent->createUser($conn, $agentUsername, $agentPassword);

    if ($result) {
        // Agent profile created successfully, redirect to viewAgents.php
        echo '<p>Agent Created Successfully</p>';    
        header('Location: viewAgents.php');  
        exit();
    } else {
        echo '<p>Agent creation failed. Please try again.</p>';
    }
}
?>
<style>
    label {
        min-width: 160px;
        display: inline-block;
        vertical-align: top;
        padding-top: 9px;
    }
</style>
<section>
    <h2>Create Agent Profile</h2>
    <form method="post" action="createAgent.php">
        <label for="agent_username">Agent Username:</label>
        <input class="input mb-2" type="text" id="agent_username" name="agent_username" required><br>

        <label for="agent_password">Agent Password:</label>
        <input class="input mb-2" type="password" id="agent_password" name="agent_password" required><br>

        <button class="btn" type="submit">Create Agent Profile</button>
    </form>
</section>

<?php include 'footer.php'; ?>