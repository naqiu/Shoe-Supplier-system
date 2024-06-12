<style>
    th {
        padding: 5px;
        font-size: 16px;
        font-weight: 700;
        text-align: center;
        cursor: pointer;
        border: none;
        background-color: var(--primary);
        color: #fff;
        text-decoration: none;
    }
</style>
<?php
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

function fetchAgents($conn)
{
    $query = "SELECT * FROM users WHERE role = 'Agent'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo '<section>';
        echo '<h2>Registered Agents:</h2>';
        echo '<table>';
        echo '<tr><th class="px-3">Agent ID</th><th class="px-3">Username</th><th>Action</th></tr>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['username'] . '</td>';
            echo '<td><a class="btn btn-s" href="changePassword.php?id=' . $row['id'] . '">Change Password</a></td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</section>';
    } else {
        echo '<p>No agents registered.</p>';
    }
}

?>
<a class="btn btn-s" href="createAgent.php">Add Agent</a>
<?php
// Display the list of agents
fetchAgents($conn);

include 'footer.php';
mysqli_close($conn);
?>