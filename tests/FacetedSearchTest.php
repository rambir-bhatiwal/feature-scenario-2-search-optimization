<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Scenario2/ProductSearchController.php';

use Dotenv\Dotenv;
use Scenario2\ProductSearchController;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$pdo = new PDO("mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'], $_ENV['DB_PASS']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$service = new ProductSearchController($pdo);

$response = $service->facetedSearch("Product", [
    'min_price' => 300,
    'max_price' => 800,
    'min_rating' => 3
]);

echo "🔍 Products Found: " . count($response['products']) . "\n\n";

// Filters Output
echo "📊 Brand Facets:\n";
foreach ($response['filters']['brands'] as $b) {
    echo "- {$b['name']} ({$b['count']})\n";
}

echo "\n📚 Category Facets:\n";
foreach ($response['filters']['categories'] as $c) {
    echo "- {$c['name']} ({$c['count']})\n";
}

echo "\n💸 Price Histogram:\n";
foreach ($response['filters']['price_histogram'] as $bucket) {
    echo "- ₹{$bucket['price_bucket']} to ₹" . ($bucket['price_bucket'] + 199) . ": {$bucket['count']}\n";
}

echo "\n📦 Stock:\n";
foreach ($response['filters']['availability'] as $type => $count) {
    echo "- {$type}: {$count}\n";
}
