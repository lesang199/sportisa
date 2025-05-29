<?php
session_start();
include 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra order_id
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['order_id'];

// Lấy thông tin đơn hàng
$query = "SELECT o.*, u.username, u.email 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cảm ơn bạn đã đặt hàng - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .thank-you-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem 1rem;
        }
        .thank-you-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 2rem;
            text-align: center;
        }
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 1.5rem;
        }
        .order-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #dee2e6;
        }
        .order-info:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="thank-you-container">
        <div class="thank-you-card">
            <i class="fas fa-check-circle success-icon"></i>
            <h1 class="mb-4">Cảm ơn bạn đã đặt hàng!</h1>
            <p class="lead mb-4">Đơn hàng của bạn đã được đặt thành công và đang được xử lý.</p>
            
            <div class="order-details">
                <div class="order-info">
                    <span>Mã đơn hàng:</span>
                    <strong>#<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></strong>
                </div>
                <div class="order-info">
                    <span>Tổng tiền:</span>
                    <strong class="text-primary"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</strong>
                </div>
                <div class="order-info">
                    <span>Phương thức thanh toán:</span>
                    <strong><?php echo $order['payment_method'] == 'cod' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản ngân hàng'; ?></strong>
                </div>
                <div class="order-info">
                    <span>Địa chỉ giao hàng:</span>
                    <strong><?php echo htmlspecialchars($order['shipping_address']); ?></strong>
                </div>
            </div>

            <div class="mt-4">
                <p class="text-muted mb-4">Chúng tôi sẽ gửi email xác nhận đến địa chỉ: <?php echo htmlspecialchars($order['email']); ?></p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Về trang chủ
                    </a>
                    <a href="products.php" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 