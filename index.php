<?php
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<h1>Welcome, <?php echo $_SESSION['username'] ?>!</h1>
<p>This is the main content of the homepage.</p>


<?php
include 'footer.php';
?>