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

// Check if the user is logged in as a supplier
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'header.php';

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'distribution_system';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to fetch and display agents
function fetchAgents($conn) {
    $query = "SELECT * FROM users WHERE role = 'Agent'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo '<section>';
        echo '<h2>Registered Agents:</h2>';
        echo '<table>';
        echo '<tr><th>Agent ID</th><th>Username</th><th>Action</th></tr>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['username'] . '</td>';
            echo '<td><a href="changePassword.php?id=' . $row['id'] . '">Change Password</a></td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</section>';
    } else {
        echo '<p>No agents registered.</p>';
    }
}

// Display the list of agents
fetchAgents($conn);

include 'footer.php';
mysqli_close($conn);
?>
