<?php
session_start();
require '../config/database.php';

class OrderController {
    private $conn;
    private $order_id;
    private $order;
    private $order_items;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->checkAdmin();
        $this->getOrderId();
        $this->loadOrder();
        $this->loadOrderItems();
    }

    private function checkAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ../login.php");
            exit();
        }
    }

    private function getOrderId() {
        if (!isset($_GET['id'])) {
            header("Location: orders.php");
            exit();
        }
        $this->order_id = $_GET['id'];
    }

    private function loadOrder() {
        $query = "SELECT o.*, u.username, u.email, u.phone 
                  FROM orders o 
                  JOIN users u ON o.user_id = u.id 
                  WHERE o.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->order_id]);
        $this->order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$this->order) {
            header("Location: orders.php");
            exit();
        }
    }

    private function loadOrderItems() {
        $query = "SELECT oi.*, p.name, p.image, p.slug 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->order_id]);
        $this->order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderIdValue() {
        return $this->order_id;
    }

    public function getOrder() {
        return $this->order;
    }

    public function getOrderItems() {
        return $this->order_items;
    }

    public function formatPrice($price) {
        return number_format($price, 0, ',', '.');
    }

    public function getShippingFee() {
        return ($this->order['total_amount'] >= 1000000) ? 'Miễn phí' : '30,000 VNĐ';
    }

    public function getPaymentMethod() {
        return ($this->order['payment_method'] == 'cod') ? 
               'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng';
    }

    public function getOrderDate() {
        return date('d/m/Y H:i', strtotime($this->order['created_at']));
    }
}

// Initialize the controller
$orderController = new OrderController($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?php echo $orderController->getOrderIdValue(); ?> - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../admin/view/sidebar.php'; ?> 
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h4>Chi tiết đơn hàng #<?php echo $orderController->getOrderIdValue(); ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5>Danh sách sản phẩm</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Sản phẩm</th>
                                                        <th>Đơn giá</th>
                                                        <th>Số lượng</th>
                                                        <th>Thành tiền</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($orderController->getOrderItems() as $item): ?>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="img-thumbnail me-2" style="width: 50px;">
                                                                    <div>
                                                                        <a href="../product.php?slug=<?php echo $item['slug']; ?>" class="text-decoration-none">
                                                                            <?php echo $item['name']; ?>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><?php echo $orderController->formatPrice($item['price']); ?> VNĐ</td>
                                                            <td><?php echo $item['quantity']; ?></td>
                                                            <td><?php echo $orderController->formatPrice($item['price'] * $item['quantity']); ?> VNĐ</td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3" class="text-end"><strong>Tạm tính:</strong></td>
                                                        <td><?php echo $orderController->formatPrice($orderController->getOrder()['total_amount']); ?> VNĐ</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="text-end"><strong>Phí vận chuyển:</strong></td>
                                                        <td>
                                                            <span class="<?php echo ($orderController->getOrder()['total_amount'] >= 1000000) ? 'text-success' : ''; ?>">
                                                                <?php echo $orderController->getShippingFee(); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                                        <td><strong><?php echo $orderController->formatPrice($orderController->getOrder()['total_amount']); ?> VNĐ</strong></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5>Thông tin đơn hàng</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="orders.php">
                                            <div class="mb-3">
                                                <label class="form-label">Trạng thái</label>
                                                <select name="status" class="form-select">
                                                    <option value="pending" <?php echo $orderController->getOrder()['status'] == 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                                                    <option value="processing" <?php echo $orderController->getOrder()['status'] == 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                                                    <option value="shipped" <?php echo $orderController->getOrder()['status'] == 'shipped' ? 'selected' : ''; ?>>Đang giao hàng</option>
                                                    <option value="delivered" <?php echo $orderController->getOrder()['status'] == 'delivered' ? 'selected' : ''; ?>>Đã giao hàng</option>
                                                    <option value="cancelled" <?php echo $orderController->getOrder()['status'] == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="order_id" value="<?php echo $orderController->getOrderIdValue(); ?>">
                                            <button type="submit" class="btn btn-primary">Cập nhật trạng thái</button>
                                        </form>
                                        <hr>
                                        <p><strong>Ngày đặt hàng:</strong> <?php echo $orderController->getOrderDate(); ?></p>
                                        <p><strong>Phương thức thanh toán:</strong> 
                                            <?php echo $orderController->getPaymentMethod(); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h5>Thông tin khách hàng</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php $order = $orderController->getOrder(); ?>
                                        <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                                        <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                                        <p><strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                                        <?php if (!empty($order['notes'])): ?>
                                            <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['notes']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>