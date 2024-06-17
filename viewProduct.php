<style>
    .search {
        display: inline-block;
        margin-left: auto;
        float: right;
    }

    .input {
        font-size: 12px !important;
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
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

try {
    if ($searchTerm) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE product_name LIKE ? OR product_description LIKE ?");
        $s = '%' . $searchTerm . '%';
        $stmt->bind_param('ss', $s, $s);
    } else {
        $stmt = $conn->prepare("SELECT * FROM products");
    }

    if (!$stmt) {
        throw new Exception($conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
    $conn->close();
    exit();
}
?>
<h2>Products</h2>
<div>
    <a class="btn btn-s mb-3" href="addProduct.php">Add Product</a>
    <a class="btn btn-s mb-3" href="inventoryReports.php">Inventory Reports</a>
    <form class="search" method="get" action="">
    <div class="search-wrapper">
        <input type="text" class="input" id="search-input" name="search" placeholder="Search products" value="<?php echo $searchTerm; ?>">
        <button type="button" class="clear-btn" id="clear-btn" >&times;</button>
        </div>
        <button type="submit" class="btn btn-s">Search</button>
    </form>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['product_name']; ?></td>
            <td><?php echo $row['product_description']; ?></td>
            <td>RM<?php echo $row['product_price']; ?></td>
            <td><?php echo $row['stock']; ?></td>
            <td><img src="<?php echo $row['image']; ?>" width="100"></td>
            <td>
                <a class="btn btn-s" href="updateProduct.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a class="btn btn-s" href="deleteProduct.php?id=<?php echo $row['id']; ?>">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("search-input");
    const clearBtn = document.getElementById("clear-btn");
    const searchForm = document.querySelector(".search");

    searchInput.addEventListener("input", function() {
        if (searchInput.value.length > 0) {
            clearBtn.style.display = "inline";
        } else {
            clearBtn.style.display = "none";
        }
    });

    clearBtn.addEventListener("click", function() {
        searchInput.value = "";
        clearBtn.style.display = "none";
        searchForm.submit();
    });

    // Trigger input event on page load to set initial state
    searchInput.dispatchEvent(new Event('input'));
});
</script>
<?php include 'footer.php'; ?>