<?php
// File: models/CheckoutModel.php

class CheckoutModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy thông tin người dùng theo user_id
    public function getUserById($user_id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo đơn hàng và các chi tiết đơn hàng
    public function createOrder($user_id, $grand_total, $payment_method, $shipping_address, $notes, $cart) {
        try {
            // Bắt đầu transaction
            $this->conn->beginTransaction();

            // Chèn đơn hàng
            $query = "INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address, notes, created_at) 
                      VALUES (?, ?, 'pending', ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id, $grand_total, $payment_method, $shipping_address, $notes]);
            $order_id = $this->conn->lastInsertId();

            // Duyệt từng sản phẩm trong giỏ hàng để thêm vào chi tiết đơn hàng
            foreach ($cart as $item) {
                // Kiểm tra số lượng tồn kho
                $check_stock_query = "SELECT stock FROM products WHERE id = ?";
                $check_stmt = $this->conn->prepare($check_stock_query);
                $check_stmt->execute([$item['id']]);
                $stock = $check_stmt->fetchColumn();

                if ($stock < $item['quantity']) {
                    throw new Exception("Sản phẩm " . $item['name'] . " không đủ số lượng tồn kho");
                }

                // Thêm chi tiết đơn hàng
                $query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                          VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);

                // Cập nhật số lượng tồn kho
                $query = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$item['quantity'], $item['id']]);
            }

            // Commit transaction
            $this->conn->commit();

            return $order_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e; // Bắt lỗi để controller xử lý
        }
    }
}
