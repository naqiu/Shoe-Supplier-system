<?php
class NegativeStockException extends Exception {
    public function errorMessage() {
        return "Error: " . $this->getMessage();
    }
}

class ProductNotFoundException extends Exception {
    public function errorMessage() {
        return "Error: " . $this->getMessage();
    }
}
?>