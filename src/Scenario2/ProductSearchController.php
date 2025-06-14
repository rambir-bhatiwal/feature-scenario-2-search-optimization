<?php

namespace Scenario2;

use PDO;

class ProductSearchController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function searchProducts(?string $keyword = '', array $filters = []): array
    {
        $sql = "
            SELECT p.*,
                   AVG(r.rating) as avg_rating,
                   COUNT(r.id) as review_count,
                   c.name AS category_name,
                   b.name AS brand_name
            FROM products p
            LEFT JOIN reviews r ON p.id = r.product_id
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }

        if (!empty($filters['brands'])) {
            $in = implode(',', array_fill(0, count($filters['brands']), '?'));
            $sql .= " AND p.brand_id IN ($in)";
            $params = array_merge($params, $filters['brands']);
        }

        $sql .= " GROUP BY p.id";

        if (!empty($filters['min_rating'])) {
            $sql .= " HAVING avg_rating >= ?";
            $params[] = $filters['min_rating'];
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT 50";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function facetedSearch(?string $keyword = '', array $filters = []): array
    {
        // Step 1: Get filtered products
        $products = $this->searchProducts($keyword, $filters);

        // Step 2: Build facets using same filters (reusable WHERE clause)
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $where .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        if (!empty($filters['min_price'])) {
            $where .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }

        if (!empty($filters['brands'])) {
            $in = implode(',', array_fill(0, count($filters['brands']), '?'));
            $where .= " AND p.brand_id IN ($in)";
            $params = array_merge($params, $filters['brands']);
        }

        // Step 3: Facet Queries

        // Brand Counts
        $sql1 = "
        SELECT b.id, b.name, COUNT(*) as count
        FROM products p
        JOIN brands b ON p.brand_id = b.id
        $where
        GROUP BY b.id, b.name
    ";
        $stmt1 = $this->db->prepare($sql1);
        $stmt1->execute($params);
        $brands = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        // Category Counts
        $sql2 = "
        SELECT c.id, c.name, COUNT(*) as count
        FROM products p
        JOIN categories c ON p.category_id = c.id
        $where
        GROUP BY c.id, c.name
    ";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute($params);
        $categories = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Price Buckets (histogram: every 200 range)
        $sql3 = "
        SELECT FLOOR(p.price / 200) * 200 as price_bucket, COUNT(*) as count
        FROM products p
        $where
        GROUP BY price_bucket
        ORDER BY price_bucket
    ";
        $stmt3 = $this->db->prepare($sql3);
        $stmt3->execute($params);
        $price_buckets = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        // Stock status
        $sql4 = "
        SELECT CASE 
                 WHEN stock > 0 THEN 'in_stock'
                 ELSE 'out_of_stock'
               END as stock_status,
               COUNT(*) as count
        FROM products p
        $where
        GROUP BY stock_status
    ";
        $stmt4 = $this->db->prepare($sql4);
        $stmt4->execute($params);
        $stock_status = $stmt4->fetchAll(PDO::FETCH_KEY_PAIR); // ['in_stock' => 100, 'out_of_stock' => 25]

        return [
            'products' => $products,
            'filters' => [
                'brands' => $brands,
                'categories' => $categories,
                'price_histogram' => $price_buckets,
                'availability' => $stock_status,
            ]
        ];
    }
}
