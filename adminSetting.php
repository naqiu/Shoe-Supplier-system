<?php
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $restock_threshold = isset($_POST['restock_threshold']) ? intval($_POST['restock_threshold']) : null;
    
    // Validate restock_threshold
    if ($restock_threshold === null || $restock_threshold < 0) {
        die("Invalid restock threshold value.");
    }
    
    try {
        // Check if admin record already exists for user
        $stmt = $conn->prepare("SELECT COUNT(*) FROM admin WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        
        // Determine whether to insert or update based on count
        if ($count > 0) {
            $stmt = $conn->prepare("UPDATE admin SET restock_threshold = ? WHERE user_id = ?");
        } else {
            $stmt = $conn->prepare("INSERT INTO admin (restock_threshold,user_id) VALUES (?, ?)");
        }
        
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        
        // Bind parameters and execute
        $stmt->bind_param("ii", $restock_threshold, $user_id);
        $stmt->execute();
    
        echo "Admin data inserted/updated successfully.";
    
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    
    // Close statement
    if (isset($stmt)) {
        $stmt->close();
    }
}
else {
    // Fetch current restock_threshold for display
    try {
        $stmt = $conn->prepare("SELECT restock_threshold FROM admin WHERE user_id = ?");
        
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();
        
        // If record found, fetch and display current threshold
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($restock_threshold);
            $stmt->fetch();
        } else {
            // If no record found, initialize $restock_threshold
            $restock_threshold = null;
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    
    // Close connection
    $conn->close();
}
?>

<h2>Admin Setting</h2>
<form action="adminSetting.php" method="post">
    <label for="restock_threshold">Restock Threshold:</label>
    <input class="input" type="number" id="restock_threshold" name="restock_threshold" value="<?php echo isset($restock_threshold) ? $restock_threshold : ''; ?>">
    <br>
    <button class="btn" type="submit">Submit</button>
</form>

<?php
include 'footer.php';
?>
