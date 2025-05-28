<?php
session_start();
require_once '../config/database.php';

class AdminDashboard {
    private $conn;
    private $error;
    
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }
    
    public function checkAdminAccess() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: login.php");
            exit();
        }
    }
    
    public function getTotalOrders() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM orders");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->error = 'Error getting total orders: ' . $e->getMessage();
            return 0;
        }
    }
    
    public function getTotalProducts() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM products");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->error = 'Error getting total products: ' . $e->getMessage();
            return 0;
        }
    }
    
    public function getTotalUsers() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->error = 'Error getting total users: ' . $e->getMessage();
            return 0;
        }
    }
    
    public function getTotalRevenue() {
        try {
            $stmt = $this->conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'delivered'");
            return $stmt->fetchColumn() ?? 0;
        } catch (PDOException $e) {
            $this->error = 'Error getting total revenue: ' . $e->getMessage();
            return 0;
        }
    }
    
    public function getRecentOrders($limit = 5) {
        try {
            $stmt = $this->conn->prepare("SELECT o.*, u.username, u.full_name 
                                         FROM orders o 
                                         JOIN users u ON o.user_id = u.id 
                                         ORDER BY o.created_at DESC 
                                         LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = 'Error getting recent orders: ' . $e->getMessage();
            return [];
        }
    }
    
    public function getTopProducts($limit = 5) {
        try {
            $stmt = $this->conn->prepare("SELECT p.id, p.name, p.image, p.price, COUNT(oi.id) as order_count, SUM(oi.quantity) as total_quantity
                                       FROM products p
                                       JOIN order_items oi ON p.id = oi.product_id
                                       JOIN orders o ON oi.order_id = o.id
                                       WHERE o.status = 'delivered'
                                       GROUP BY p.id
                                       ORDER BY total_quantity DESC
                                       LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = 'Error getting top products: ' . $e->getMessage();
            return [];
        }
    }
    
    public function getRecentReviews($limit = 5) {
        try {
            $stmt = $this->conn->prepare("SELECT r.*, u.username, p.name as product_name
                                       FROM reviews r
                                       JOIN users u ON r.user_id = u.id
                                       JOIN products p ON r.product_id = p.id
                                       ORDER BY r.created_at DESC
                                       LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = 'Error getting recent reviews: ' . $e->getMessage();
            return [];
        }
    }
    
    public function getError() {
        return $this->error;
    }
    
    public function formatCurrency($amount) {
        return number_format($amount, 0, ',', '.') . ' VNĐ';
    }
    
    public function formatDate($dateString) {
        return date('d/m/Y H:i', strtotime($dateString));
    }
    
    public function getStatusBadge($status) {
        $statuses = [
            'pending' => ['class' => 'warning', 'text' => 'Chờ xử lý'],
            'processing' => ['class' => 'info', 'text' => 'Đang xử lý'],
            'shipped' => ['class' => 'primary', 'text' => 'Đang giao hàng'],
            'delivered' => ['class' => 'success', 'text' => 'Đã giao hàng'],
            'cancelled' => ['class' => 'danger', 'text' => 'Đã hủy']
        ];
        
        return $statuses[$status] ?? ['class' => 'secondary', 'text' => $status];
    }
}

// Usage
$dashboard = new AdminDashboard($conn);
$dashboard->checkAdminAccess();

$total_orders = $dashboard->getTotalOrders();
$total_products = $dashboard->getTotalProducts();
$total_users = $dashboard->getTotalUsers();
$total_revenue = $dashboard->getTotalRevenue();
$recent_orders = $dashboard->getRecentOrders();
$top_products = $dashboard->getTopProducts();
$recent_reviews = $dashboard->getRecentReviews();
$error = $dashboard->getError();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../admin/view/sidebar.php'; ?>    

            <!-- Main content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tổng quan</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Xuất báo cáo</button>
                        </div>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Thống kê -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng đơn hàng</h5>
                                <h2 class="card-text"><?php echo number_format($total_orders); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng sản phẩm</h5>
                                <h2 class="card-text"><?php echo number_format($total_products); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng người dùng</h5>
                                <h2 class="card-text"><?php echo number_format($total_users); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng doanh thu</h5>
                                <h2 class="card-text"><?php echo $dashboard->formatCurrency($total_revenue); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Đơn hàng mới nhất -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Đơn hàng mới nhất</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Mã đơn</th>
                                                <th>Khách hàng</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày đặt</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <tr>
                                                    <td>#<?php echo $order['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                                    <td><?php echo $dashboard->formatCurrency($order['total_amount']); ?></td>
                                                    <td>
                                                        <?php $status = $dashboard->getStatusBadge($order['status']); ?>
                                                        <span class="badge bg-<?php echo $status['class']; ?>">
                                                            <?php echo $status['text']; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo $dashboard->formatDate($order['created_at']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sản phẩm bán chạy -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Sản phẩm bán chạy</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>Số lượng bán</th>
                                                <th>Doanh thu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($top_products as $product): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if ($product['image']): ?>
                                                                <img src="../uploads/products/<?php echo $product['image']; ?>" 
                                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                                     class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                            <?php endif; ?>
                                                            <?php echo htmlspecialchars($product['name']); ?>
                                                        </div>
                                                    </td>
                                                    <td><?php echo number_format($product['total_quantity']); ?></td>
                                                    <td><?php echo $dashboard->formatCurrency($product['price'] * $product['total_quantity']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Đánh giá mới nhất -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Đánh giá mới nhất</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Người dùng</th>
                                        <th>Sản phẩm</th>
                                        <th>Đánh giá</th>
                                        <th>Nội dung</th>
                                        <th>Ngày đăng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_reviews as $review): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($review['username']); ?></td>
                                            <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                                            <td>
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                                <?php endfor; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($review['comment']); ?></td>
                                            <td><?php echo $dashboard->formatDate($review['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>