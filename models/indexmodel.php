<?php
class IndexModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getFeaturedProducts($limit = 8) {
        $stmt = $this->conn->query("
            SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN brands b ON p.brand_id = b.id 
            WHERE p.featured = 1 AND p.status = 'active'
            ORDER BY p.created_at DESC 
            LIMIT $limit
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveCategories() {
        $stmt = $this->conn->query("SELECT * FROM categories WHERE status = 'active'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveBrands() {
        $stmt = $this->conn->query("SELECT * FROM brands WHERE status = 'active'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLatestNews($limit = 3) {
        $stmt = $this->conn->query("
            SELECT * 
            FROM news 
            WHERE status = 'published' 
            ORDER BY created_at DESC 
            LIMIT $limit
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>