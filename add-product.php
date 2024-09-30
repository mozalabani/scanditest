<?php
require_once 'database/Database.php';
require_once 'classes/Product.php';

$errorMessage = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $sku = $_POST['sku'] ?? '';
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $productType = $_POST['productType'] ?? '';

    // Validate the fields
    if (empty($sku) || empty($name) || empty($price) || empty($productType)) {
        $errorMessage = 'Please, submit required data';
    } else {
        // Check if SKU is unique
        $db = new Database();
        $products = $db->getAllProducts();
        foreach ($products as $product) {
            if ($product['sku'] === $sku) {
                $errorMessage = 'SKU must be unique. This SKU already exists.';
                break;
            }
        }

        // If no error, save the product
        if (!$errorMessage) {
            // Prepare data based on product type
            try {
                switch ($productType) {
                    case 'DVD':
                        $size = $_POST['size'] ?? '';
                        if (!is_numeric($size) || $size <= 0) {
                            $errorMessage = 'Please, provide a valid size in MB.';
                        } else {
                            $stmt = $db->pdo->prepare("INSERT INTO products (sku, name, price, size) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$sku, $name, $price, $size]);
                            $success = true;
                        }
                        break;
                    case 'Book':
                        $weight = $_POST['weight'] ?? '';
                        if (!is_numeric($weight) || $weight <= 0) {
                            $errorMessage = 'Please, provide a valid weight in Kg.';
                        } else {
                            $stmt = $db->pdo->prepare("INSERT INTO products (sku, name, price, weight) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$sku, $name, $price, $weight]);
                            $success = true;
                        }
                        break;
                    case 'Furniture':
                        $height = $_POST['height'] ?? '';
                        $width = $_POST['width'] ?? '';
                        $length = $_POST['length'] ?? '';
                        if (!is_numeric($height) || !is_numeric($width) || !is_numeric($length) || $height <= 0 || $width <= 0 || $length <= 0) {
                            $errorMessage = 'Please, provide valid dimensions (HxWxL in cm).';
                        } else {
                            $stmt = $db->pdo->prepare("INSERT INTO products (sku, name, price, height, width, length) VALUES (?, ?, ?, ?, ?, ?)");
                            $stmt->execute([$sku, $name, $price, $height, $width, $length]);
                            $success = true;
                        }
                        break;
                }
            } catch (Exception $e) {
                $errorMessage = 'An error occurred while saving the product.';
            }
        }
    }

    // If successful, redirect back to product list page
    if ($success) {
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <script>
        // JavaScript to handle dynamic form changes based on product type
        function handleProductTypeChange() {
            const productType = document.getElementById('productType').value;
            const dvdAttributes = document.getElementById('dvdAttributes');
            const bookAttributes = document.getElementById('bookAttributes');
            const furnitureAttributes = document.getElementById('furnitureAttributes');

            dvdAttributes.style.display = 'none';
            bookAttributes.style.display = 'none';
            furnitureAttributes.style.display = 'none';

            if (productType === 'DVD') {
                dvdAttributes.style.display = 'block';
            } else if (productType === 'Book') {
                bookAttributes.style.display = 'block';
            } else if (productType === 'Furniture') {
                furnitureAttributes.style.display = 'block';
            }
        }
    </script>
</head>
<body>
    <h1>Product Add</h1>
    <link rel="stylesheet" href="styles.css">
    <!-- Error message -->
    <?php if ($errorMessage): ?>
        <p style="color:red"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <form action="add-product.php" method="POST">
        <label for="sku">SKU</label>
        <input type="text" id="sku" name="sku" required><br>

        <label for="name">Name</label>
        <input type="text" id="name" name="name" required><br>

        <label for="price">Price ($)</label>
        <input type="text" id="price" name="price" required><br>

        <label for="productType">Product Type</label>
        <select id="productType" name="productType" onchange="handleProductTypeChange()" required>
            <option value="">Select Type</option>
            <option value="DVD">DVD</option>
            <option value="Book">Book</option>
            <option value="Furniture">Furniture</option>
        </select><br>

        <!-- DVD Specific Attributes -->
        <div id="dvdAttributes" style="display:none">
            <label for="size">Size (MB)</label>
            <input type="text" id="size" name="size"><br>
            <p>Please, provide size in MB.</p>
        </div>

        <!-- Book Specific Attributes -->
        <div id="bookAttributes" style="display:none">
            <label for="weight">Weight (Kg)</label>
            <input type="text" id="weight" name="weight"><br>
            <p>Please, provide weight in Kg.</p>
        </div>

        <!-- Furniture Specific Attributes -->
        <div id="furnitureAttributes" style="display:none">
            <label for="height">Height (cm)</label>
            <input type="text" id="height" name="height"><br>
            
            <label for="width">Width (cm)</label>
            <input type="text" id="width" name="width"><br>
            
            <label for="length">Length (cm)</label>
            <input type="text" id="length" name="length"><br>
            <p>Please, provide dimensions (HxWxL).</p>
        </div>
        <form action="index.php" method="POST" style="display:inline;">
    </form>
        <button type="submit">Save</button>
        <button type="button" onclick="window.location.href='index.php'">Cancel</button>
    </form>
</body>
</html>
