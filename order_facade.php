<?php

class OrderFacade {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function placeOrder($productId, $orderQuantity, $customerName, $customerAddress, $customerContact) {
        // Retrieve product details
        $product = $this->getProductById($productId);

        if ($product && $orderQuantity <= $product['stock']) {
            $approvalStatus = 'Pending';


            $insertQuery = "INSERT INTO orders (product_id, agent_id, customer_name, customer_address, customer_contact, quantity, approval_status, sales_amount)
                            VALUES ($productId, {$_SESSION['user_id']}, '$customerName', '$customerAddress', '$customerContact', $orderQuantity, '$approvalStatus', {$product['product_price']} * $orderQuantity)";
            $insertResult = mysqli_query($this->conn, $insertQuery);

            if ($insertResult) {
                // Update stock
                $newStock = $product['stock'] - $orderQuantity;
                $this->updateStock($productId, $newStock);

                // Increment total sold
                $this->incrementTotalSold($productId, $orderQuantity);

                return true; // Order successfully placed
            } else {
                return false; // Error placing order
            }
        } else {
            return false; // Insufficient stock or product not found
        }
    }

    private function getProductById($productId) {
        $query = "SELECT * FROM products WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    private function updateStock($productId, $newStock) {
        $updateQuery = "UPDATE products SET stock = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ii", $newStock, $productId);
        mysqli_stmt_execute($stmt);
    }

    private function incrementTotalSold($productId, $quantity) {
        $updateQuery = "UPDATE products SET total_sold = total_sold + ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ii", $quantity, $productId);
        mysqli_stmt_execute($stmt);
    }
}
?>
