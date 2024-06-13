<style>
    .flex-container {
        display: flex;
        flex-wrap: wrap;
    }

    .flex-container>a {
        width: 200px;
        background-color: #f2f2f2;
        margin: 8px;
        text-decoration: none;
        color: #000;
    }

    .flex-container>a>img {
        width: 100%;
    }

    .flex-container>a>div {
        padding: 10px;
    }

    .flex-container>a:first-child {
        margin-left: 0;
    }
</style>

<?php
include 'header.php';

$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0): ?>
    <h2>Available Products</h2>

    <div class="flex-container">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <a href="agentOrder.php?id=<?php echo $row['id']; ?>">
                <img src="<?php echo $row['image']; ?>"><br>
                <div>
                    <b><?php echo $row['product_name']; ?></b><br>
                    Current Stock: <?php echo $row['stock']; ?><br>
                    RM<?php echo $row['product_price']; ?>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p>No products available.</p>
<?php endif;
?>
<?php include 'footer.php'; ?>