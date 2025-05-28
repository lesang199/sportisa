<?php
class DetailModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getOrder($order_id, $user_id) {
        $stmt = $this->conn->prepare("
            SELECT o.*, u.full_name, u.phone, u.address 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.id = ? AND o.user_id = ?
        ");
        $stmt->execute([$order_id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderDetails($order_id) {
        $stmt = $this->conn->prepare("
            SELECT od.*, p.name, p.image 
            FROM order_details od 
            JOIN products p ON od.product_id = p.id 
            WHERE od.order_id = ?
        ");
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>