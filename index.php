<?php
require_once 'database/Database.php';
require_once 'classes/Product.php';

$db = new Database();
$products = $db->getAllProducts(); // Fetch all products from the database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['massDelete']) && !empty($_POST['delete'])) {
        $deleteIds = $_POST['delete']; // Get IDs of products to delete
        foreach ($deleteIds as $id) {
            $db->deleteProductById($id); // Delete each selected product
        }
        header("Location: index.php"); // Redirect to refresh the product list without any alerts
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Product List</h1>

<!-- Button container for positioning -->
<div class="button-container">
    <form action="index.php" method="POST" style="display:inline;">
    </form>
    <a href="add-product.php">
        <button>Add Product</button>
    </a>
    <button type="submit" name="massDelete">Mass Delete</button>
</div>

<!-- Product List with checkboxes for Mass Delete -->
<form action="index.php" method="POST">
    <div class="product-list">
        <?php if ($products): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <input type="checkbox" name="delete[]" value="<?= htmlspecialchars($product['id']) ?>" class="delete-checkbox">
                    <p>SKU: <?= htmlspecialchars($product['sku']) ?></p>
                    <p>Name: <?= htmlspecialchars($product['name']) ?></p>
                    <p>Price: $<?= htmlspecialchars($product['price']) ?></p>
                    <p>
                        <?php if ($product['size']): ?>
                            Size: <?= htmlspecialchars($product['size']) ?> MB
                        <?php elseif ($product['weight']): ?>
                            Weight: <?= htmlspecialchars($product['weight']) ?> Kg
                        <?php elseif ($product['height'] && $product['width'] && $product['length']): ?>
                            Dimensions: <?= htmlspecialchars($product['height']) ?>x<?= htmlspecialchars($product['width']) ?>x<?= htmlspecialchars($product['length']) ?> cm
                        <?php endif; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products available.</p>
        <?php endif; ?>
        </div>
</body>
</html>
