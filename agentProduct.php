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


    #search-input {
        padding-right: 30px;
    }

    .search-wrapper {
        position: relative;
        display: inline-block;
    }

    .clear-btn {
        position: absolute;
        right: 4px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        font-size: 18px;
        cursor: pointer;
    }

    .clear-btn:focus {
        outline: none;
    }
</style>
<?php
include 'header.php';

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM products";
if ($searchTerm) {
    $query .= " WHERE product_name LIKE ? OR product_description LIKE ?";
}

$stmt = $conn->prepare($query);
if ($searchTerm) {
    $likeSearchTerm = '%' . $searchTerm . '%';
    $stmt->bind_param('ss', $likeSearchTerm, $likeSearchTerm);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<h2>Available Products</h2>

<form class="search" method="get" action="">
    <div class="search-wrapper">
        <input type="text" class="input" name="search" id="search-input" placeholder="Search products"
            value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button type="button" class="clear-btn" id="clear-btn" style="display: none;">&times;</button>
    </div>
    <button type="submit" class="btn">Search</button>
</form>

<div class="flex-container">
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
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
    <?php else: ?>
        <p>No products available.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("search-input");
        const clearBtn = document.getElementById("clear-btn");
        const searchForm = document.querySelector(".search");

        searchInput.addEventListener("input", function () {
            if (searchInput.value.length > 0) {
                clearBtn.style.display = "inline";
            } else {
                clearBtn.style.display = "none";
            }
        });

        clearBtn.addEventListener("click", function () {
            searchInput.value = "";
            clearBtn.style.display = "none";
            searchForm.submit();
        });

        searchInput.dispatchEvent(new Event('input'));
    });
</script>