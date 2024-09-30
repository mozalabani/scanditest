<?php
require_once 'database/Database.php';

// Generate random products and insert them into the database
function generateRandomProducts($count = 10) {
    $db = new Database();
    $pdo = $db->getPdo(); // Access the pdo using the getter method
    
    // Sample product data arrays
    $skus = ['SKU001', 'SKU002', 'SKU003', 'SKU004', 'SKU005', 'SKU006', 'SKU007', 'SKU008', 'SKU009', 'SKU010'];
    $names = ['Product A', 'Product B', 'Product C', 'Product D', 'Product E', 'Product F', 'Product G', 'Product H', 'Product I', 'Product J'];
    $types = ['DVD', 'Book', 'Furniture'];

    for ($i = 0; $i < $count; $i++) {
        $sku = $skus[$i];
        $name = $names[$i];
        $price = rand(10, 100);
        $type = $types[array_rand($types)];

        switch ($type) {
            case 'DVD':
                $size = rand(500, 900);
                $stmt = $pdo->prepare("INSERT INTO products (sku, name, price, type, size) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$sku, $name, $price, $type, $size]);
                break;
            case 'Book':
                $weight = rand(1, 10);
                $stmt = $pdo->prepare("INSERT INTO products (sku, name, price, type, weight) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$sku, $name, $price, $type, $weight]);
                break;
            case 'Furniture':
                $height = rand(50, 200);
                $width = rand(50, 150);
                $length = rand(50, 180);
                $stmt = $pdo->prepare("INSERT INTO products (sku, name, price, type, height, width, length) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$sku, $name, $price, $type, $height, $width, $length]);
                break;
        }
    }

    echo "Random products added successfully!";
}

// Call the function to add random products
generateRandomProducts(10);
?>
