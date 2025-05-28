<?php
class ProductsModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getCategories() {
        $query = "SELECT * FROM categories";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProducts($filters = []) {
        $query = "SELECT p.*, c.categories_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
        $params = [];

        if (!empty($filters['category'])) {
            $query .= " AND c.categories_name = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['search'])) {
            $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        if (!empty($filters['price_range'])) {
            switch ($filters['price_range']) {
                case '0-500000':
                    $query .= " AND p.price BETWEEN 0 AND 500000";
                    break;
                case '500000-1000000':
                    $query .= " AND p.price BETWEEN 500000 AND 1000000";
                    break;
                case '1000000-2000000':
                    $query .= " AND p.price BETWEEN 1000000 AND 2000000";
                    break;
                case '2000000-':
                    $query .= " AND p.price > 2000000";
                    break;
            }
        }

        switch ($filters['sort'] ?? 'newest') {
            case 'price_asc':
                $query .= " ORDER BY p.price ASC";
                break;
            case 'price_desc':
                $query .= " ORDER BY p.price DESC";
                break;
            case 'name':
                $query .= " ORDER BY p.name ASC";
                break;
            default:
                $query .= " ORDER BY p.created_at DESC";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>