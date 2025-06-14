<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Scenario2/ProductSearchController.php';

use Dotenv\Dotenv;
use Scenario2\ProductSearchController;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$db = new PDO(
    "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']}",
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$search = new ProductSearchController($db);

$results = $search->searchProducts("Product", [
    'min_price' => 300,
    'max_price' => 800,
    'brands' => [1, 3, 5],
    'min_rating' => 3
]);

echo "🔍 Found " . count($results) . " results:\n";
foreach ($results as $r) {
    echo "- {$r['name']} | ₹{$r['price']} | Rating: " . round($r['avg_rating'], 1) . "\n";
}
