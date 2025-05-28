<?php
class UserModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function toggleStatus($user_id) {
        $stmt = $this->conn->prepare("
            UPDATE users 
            SET status = CASE 
                WHEN status = 'active' THEN 'inactive' 
                ELSE 'active' 
            END 
            WHERE id = ?
        ");
        return $stmt->execute([$user_id]);
    }

    public function hasOrders($user_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function delete($user_id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        return $stmt->execute([$user_id]);
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM users ORDER BY created_at ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>