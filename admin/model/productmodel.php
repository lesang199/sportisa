<?php
class ProductService {
    private $conn;
    private $products = [];

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addProduct($data, $files) {
        $success = '';
        $error = '';
        $image = '';

        $name = trim($data['name']);
        $description = trim($data['description']);
        $price = floatval($data['price']);
        $category_id = intval($data['category_id']);
        $stock = intval($data['stock']);

        if (isset($files['image']) && $files['image']['error'] == 0) {
            $image = $this->handleImageUpload($files['image']);
        }

        try {
            $stmt = $this->conn->prepare("INSERT INTO products (name, description, price, category_id, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $category_id, $stock, $image]);
            $success = 'Thêm sản phẩm thành công';
        } catch(PDOException $e) {
            $error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        return compact('success', 'error');
    }

    private function handleImageUpload($imageFile) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            return '';
        }

        $new_filename = uniqid() . '.' . $ext;
        $upload_path = '../uploads/products/' . $new_filename;

        if (move_uploaded_file($imageFile['tmp_name'], $upload_path)) {
            return $new_filename;
        }

        return '';
    }

    public function deleteProduct($id) {
        $success = '';
        $error = '';
        try {
            $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([intval($id)]);
            $success = 'Xóa sản phẩm thành công';
        } catch(PDOException $e) {
            $error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        return compact('success', 'error');
    }

    public function getProducts() {
        try {
            $stmt = $this->conn->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
}
class CategoryService {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getCategories() {
        try {
            $stmt = $this->conn->query("SELECT * FROM categories ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
}
?>