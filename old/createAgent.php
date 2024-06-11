<!-- Add the following styles at the top of each PHP file (e.g., agent.php, changePassword.php) after the opening PHP tag -->

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f0f0f0;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #333;
        color: white;
        text-align: center;
        padding: 10px;
    }

    section {
        background-color: #fff;
        padding: 20px;
        margin: 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    ul {
        list-style-type: none;
        padding: 0;
    }

    li {
        margin-bottom: 10px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 10px;
    }

    form {
        margin-top: 20px;
    }

    button {
        background-color: #4caf50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    button:hover {
        background-color: #45a049;
    }

    input {
        padding: 8px;
        margin: 5px 0;
        box-sizing: border-box;
    }

    h2 {
        color: #333;
    }

    footer {
        background-color: #333;
        color: white;
        text-align: center;
        padding: 10px;
        position: fixed;
        bottom: 0;
        width: 100%;
    }
</style>

<?php
session_start();

// Add your database connection details
$host = 'localhost';
$users = 'root';
$password = '';
$database = 'distribution_system';

$conn = mysqli_connect($host, $users, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form for creating an agent profile is submitted
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

include 'header.php';
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


<form method="post" action="logout.php">
        <button type="submit">Logout</button>
    </form>
    
<?php include 'footer.php'; ?>