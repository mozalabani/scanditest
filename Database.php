<?php
require_once __DIR__ . '/../config.php';

class Database {
    private $conn;

    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=product_db;charset=utf8';
        $username = 'root';
        $password = '';

        try {
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Public getter method for the PDO connection
    public function getPdo() {
        return $this->conn; // Return the correct property
    }

    public function getAllProducts() {
        $stmt = $this->conn->query("SELECT * FROM products ORDER BY id ASC"); // Use $this->conn
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteProducts($skus) {
        $placeholders = implode(',', array_fill(0, count($skus), '?'));
        $stmt = $this->conn->prepare("DELETE FROM products WHERE sku IN ($placeholders)"); // Use $this->conn
        $stmt->execute($skus);
    }

    public function deleteProductById($id) {
        $sql = "DELETE FROM products WHERE id = :id"; // Use named parameters
        $stmt = $this->conn->prepare($sql); // Use $this->conn
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Bind the parameter
        return $stmt->execute(); // Execute the statement
    }
}
