<?php
session_start();
include 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra ID đơn hàng
if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_GET['id']);

// Lấy thông tin đơn hàng
try {
    $stmt = $conn->prepare("
        SELECT o.*, u.full_name, u.phone, u.address 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header("Location: orders.php");
        exit();
    }

    // Lấy chi tiết đơn hàng
    try {
        $stmt = $conn->prepare("
            SELECT od.*, p.name, p.image 
            FROM order_items od 
            JOIN products p ON od.product_id = p.id 
            WHERE od.order_id = ?
        ");
        $stmt->execute([$order_id]);
        $order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($order_details)) {
            $error = 'Không tìm thấy chi tiết đơn hàng';
        }
    } catch(PDOException $e) {
        $error = 'Lỗi khi lấy chi tiết đơn hàng: ' . $e->getMessage();
        $order_details = [];
    }
} catch(PDOException $e) {
    $error = 'Có lỗi xảy ra: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?php echo $order_id; ?> - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tài khoản của tôi</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="profile.php" class="text-decoration-none">
                                    <i class="fas fa-user me-2"></i>Thông tin cá nhân
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="orders.php" class="text-decoration-none active">
                                    <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                                </a>
                            </li>
                            <li>
                                <a href="logout.php" class="text-decoration-none text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Chi tiết đơn hàng #<?php echo $order_id; ?></h5>
                            <a href="orders.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Thông tin người nhận</h6>
                                <p class="mb-1"><?php echo htmlspecialchars($order['full_name']); ?></p>
                                <p class="mb-1"><?php echo htmlspecialchars($order['phone']); ?></p>
                                <p class="mb-0"><?php echo htmlspecialchars($order['address']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6>Thông tin đơn hàng</h6>
                                <p class="mb-1">Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                <p class="mb-1">Trạng thái: 
                                    <span class="badge bg-<?php 
                                        echo $order['status'] === 'pending' ? 'warning' : 
                                            ($order['status'] === 'processing' ? 'info' : 
                                            ($order['status'] === 'completed' ? 'success' : 'danger')); 
                                    ?>">
                                        <?php 
                                        echo $order['status'] === 'pending' ? 'Chờ xử lý' : 
                                            ($order['status'] === 'processing' ? 'Đang xử lý' : 
                                            ($order['status'] === 'completed' ? 'Hoàn thành' : 'Đã hủy')); 
                                        ?>
                                    </span>
                                </p>
                                <p class="mb-0">Phương thức thanh toán: <?php echo $order['payment_method']; ?></p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php else: ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_details as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="uploads/products/<?php echo $item['image']; ?>" 
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                         class="me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo number_format($item['price']); ?>đ</td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td class="text-end"><?php echo number_format($item['price'] * $item['quantity']); ?>đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tổng tiền:</strong></td>
                                        <td class="text-end"><strong><?php echo number_format($order['total_amount']); ?>đ</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
