<?php
session_start();
include 'config/database.php';

class OrderCancellation {
    private $conn;
    private $userId;
    private $orderId;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->checkAuthentication();
        $this->checkRequestMethod();
        $this->getRequestData();
    }

    private function checkAuthentication() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            exit();
        }
        $this->userId = $_SESSION['user_id'];
    }

    private function checkRequestMethod() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
            exit();
        }
    }

    private function getRequestData() {
        $data = json_decode(file_get_contents('php://input'), true);
        $this->orderId = $data['order_id'] ?? null;

        if (!$this->orderId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng']);
            exit();
        }
    }

    public function processCancellation() {
        try {
            $this->conn->beginTransaction();

            $this->validateOrder();
            $this->updateOrderStatus();
            $this->restoreProductStock();

            $this->conn->commit();
            echo json_encode(['success' => true, 'message' => 'Đã hủy đơn hàng thành công']);

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Lỗi hủy đơn hàng: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function validateOrder() {
        $query = "SELECT status FROM orders WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->orderId, $this->userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new Exception('Đơn hàng không tồn tại hoặc không thuộc về bạn');
        }

        if ($order['status'] !== 'pending') {
            throw new Exception('Chỉ có thể hủy đơn hàng đang chờ xử lý');
        }
    }

    private function updateOrderStatus() {
        $query = "UPDATE orders SET status = 'cancelled' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->orderId]);
    }

    private function restoreProductStock() {
        $query = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $query = "UPDATE products SET stock = stock + ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }
    }
}

// Usage
$orderCancellation = new OrderCancellation($conn);
$orderCancellation->processCancellation();