<?php
// File: controllers/CheckoutController.php

class CheckoutController {
    private $conn;
    private $model;
    private $error = '';

    public function __construct($conn, CheckoutModel $model) {
        $this->conn = $conn;
        $this->model = $model;
    }

    // Kiểm tra đăng nhập và giỏ hàng
    public function validateSession() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: cart.php");
            exit();
        }
    }

    // Tính tổng tiền trong giỏ hàng
    public function calculateTotal($cart) {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    // Tính phí vận chuyển dựa trên tổng tiền
    public function calculateShipping($total) {
        return $total >= 1000000 ? 0 : 30000;
    }

    // Xử lý thanh toán đơn hàng
    public function processCheckout($postData, $cart) {
        // Lấy dữ liệu từ POST
        $payment_method = $postData['payment_method'];
        $shipping_address = $postData['shipping_address'];
        $notes = $postData['notes'];

        // Tính tổng tiền và phí vận chuyển
        $total = $this->calculateTotal($cart);
        $shipping = $this->calculateShipping($total);
        $grand_total = $total + $shipping;

        try {
            // Tạo đơn hàng thông qua model
            $order_id = $this->model->createOrder($_SESSION['user_id'], $grand_total, $payment_method, $shipping_address, $notes, $cart);

            // Xóa giỏ hàng sau khi thanh toán thành công
            unset($_SESSION['cart']);

            // Chuyển hướng đến trang cảm ơn (thank_you.php)
            header("Location: thank_you.php?order_id=" . $order_id);
            exit();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function getError() {
        return $this->error;
    }

    // Lấy thông tin người dùng (nếu cần hiển thị ở trang checkout)
    public function getUserInfo() {
        return $this->model->getUserById($_SESSION['user_id']);
    }
}
