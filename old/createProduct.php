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

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'distribution_system';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['product_name'];
    $productDescription = $_POST['product_description'];
    $productPrice = $_POST['product_price'];
    $productStock = $_POST['product_stock']; // Newly added

    if (empty($productName) || empty($productDescription) || empty($productPrice) || empty($productStock)) {
        echo '<p>Please fill in all fields.</p>';
    } else {
        $supplierId = $_SESSION['user_id'];
        
        $query = "INSERT INTO products (product_name, product_description, product_price, supplier_id, stock) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssdii', $productName, $productDescription, $productPrice, $supplierId, $productStock);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                echo '<p>Product created successfully!</p>';
            } else {
                echo '<p>Error creating product. Please try again.</p>';
            }
        } else {
            echo '<p>Database error. Please try again later.</p>';
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<section>
    <h2>Create Product</h2>
    <form method="post" action="createProduct.php">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required><br>

        <label for="product_description">Product Description:</label>
        <textarea id="product_description" name="product_description" required></textarea><br>

        <label for="product_price">Product Price:</label>
        <input type="number" id="product_price" name="product_price" required><br>

        <label for="product_stock">Product Stock:</label> <!-- Newly added -->
        <input type="number" id="product_stock" name="product_stock" required><br>

        <button type="submit">Create Product</button>
    </form>
</section>

<form method="post" action="logout.php">
    <button type="submit">Logout</button>
</form>

<?php include 'footer.php'; ?>