<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};port={$_ENV['DB_PORT']};charset=utf8", $_ENV['DB_USER'], $_ENV['DB_PASS']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



// Truncate all
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("TRUNCATE TABLE reviews");
$pdo->exec("TRUNCATE TABLE products");
$pdo->exec("TRUNCATE TABLE brands");
$pdo->exec("TRUNCATE TABLE categories");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

// Brands
$brands = ['Apple', 'Samsung', 'Sony', 'LG', 'Nike', 'Adidas', 'Levi\'s', 'Zara', 'Dell', 'HP'];
foreach ($brands as $brand) {
    $pdo->prepare("INSERT INTO brands (name) VALUES (?)")->execute([$brand]);
}

// Categories
$categories = ['Electronics', 'Fashion', 'Home', 'Books', 'Toys', 'Fitness', 'Appliances', 'Office', 'Garden', 'Automotive'];
foreach ($categories as $category) {
    $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$category]);
}

// Products
for ($i = 1; $i <= 1200; $i++) {
    $name = "Product $i";
    $desc = "Description for Product $i";
    $brand_id = rand(1, count($brands));
    $category_id = rand(1, count($categories));
    $price = rand(100, 1000);
    $stock = rand(0, 50);

    $stmt = $pdo->prepare("INSERT INTO products (name, description, brand_id, category_id, price, stock) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $desc, $brand_id, $category_id, $price, $stock]);
}

// Reviews
for ($i = 0; $i < 1000; $i++) {
    $product_id = rand(1, 200);
    $rating = rand(1, 5);
    $pdo->prepare("INSERT INTO reviews (product_id, rating) VALUES (?, ?)")->execute([$product_id, $rating]);
}

echo "✅ Scenario 2 seed complete: 10 brands, 10 categories, 200 products, 1000 reviews.\n";
