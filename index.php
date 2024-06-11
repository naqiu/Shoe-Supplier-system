<?php
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<div class="flex-ctn" style="flex-grow: 1">
<h1>Welcome, <?php echo $_SESSION['username'] ?>!</h1>
<p>This is the main content of the homepage.</p>

</div>
<?php
include 'footer.php';
?>