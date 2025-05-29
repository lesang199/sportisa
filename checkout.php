<?php
session_start();
include 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra giỏ hàng
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Lấy thông tin người dùng
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Tính tổng tiền
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
$shipping = $total >= 1000000 ? 0 : 30000;
$grand_total = $total + $shipping;

// Xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];
    $shipping_address = $_POST['shipping_address'];
    $notes = $_POST['notes'];

    try {
        // Bắt đầu transaction
        $conn->beginTransaction();

        // Tạo đơn hàng
        $query = "INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address, notes, created_at) 
                  VALUES (?, ?, 'pending', ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $grand_total, $payment_method, $shipping_address, $notes]);
        $order_id = $conn->lastInsertId();

        // Thêm chi tiết đơn hàng
        foreach ($_SESSION['cart'] as $item) {
            // Kiểm tra số lượng tồn kho
            $check_stock_query = "SELECT stock FROM products WHERE id = ?";
            $check_stmt = $conn->prepare($check_stock_query);
            $check_stmt->execute([$item['id']]);
            $stock = $check_stmt->fetchColumn();

            if ($stock < $item['quantity']) {
                throw new Exception("Sản phẩm " . $item['name'] . " không đủ số lượng tồn kho");
            }

            // Thêm chi tiết đơn hàng
            $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);

            // Cập nhật số lượng tồn kho
            $query = "UPDATE products SET stock = stock - ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$item['quantity'], $item['id']]);
        }

        // Commit transaction
        $conn->commit();

        // Xóa giỏ hàng
        unset($_SESSION['cart']);

        // Chuyển hướng đến trang cảm ơn
        header("Location: thank_you.php?order_id=" . $order_id);
        exit();

    } catch (Exception $e) {
        // Rollback transaction nếu có lỗi
        $conn->rollBack();
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .checkout-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .checkout-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            border: none;
        }
        .checkout-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        .checkout-card .card-body {
            padding: 1.5rem;
        }
        .form-control:read-only {
            background-color: #f8f9fa;
        }
        .order-summary {
            position: sticky;
            top: 2rem;
        }
        .order-summary .table {
            margin-bottom: 1rem;
        }
        .order-summary .table td {
            padding: 0.5rem 0;
        }
        .btn-checkout {
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .payment-method-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="checkout-container">
        <div class="checkout-header text-center">
            <h1 class="mb-3"><i class="fas fa-shopping-cart me-2"></i>Thanh toán</h1>
            <p class="mb-0">Vui lòng kiểm tra thông tin đơn hàng của bạn</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-card">
                    <div class="card-header">
                        <i class="fas fa-user me-2"></i>Thông tin giao hàng
                    </div>
                    <div class="card-body">
                        <form method="POST" action="checkout.php" id="checkoutForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Họ tên</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="shipping_address" class="form-label">Địa chỉ giao hàng</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Phương thức thanh toán</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="cod">
                                        <i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng (COD)
                                    </option>
                                    <option value="bank">
                                        <i class="fas fa-university"></i> Chuyển khoản ngân hàng
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Ghi chú (Tùy chọn)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Nhập ghi chú của bạn..."></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="checkout-card order-summary">
                    <div class="card-header">
                        <i class="fas fa-receipt me-2"></i>Đơn hàng của bạn
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-end">Tổng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['cart'] as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td class="text-center"><?php echo $item['quantity']; ?></td>
                                            <td class="text-end"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-end"><strong>Tạm tính:</strong></td>
                                        <td class="text-end"><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end"><strong>Phí vận chuyển:</strong></td>
                                        <td class="text-end">
                                            <?php if ($shipping == 0): ?>
                                                <span class="text-success">Miễn phí</span>
                                            <?php else: ?>
                                                <?php echo number_format($shipping, 0, ',', '.'); ?> VNĐ
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td colspan="2" class="text-end"><strong>Tổng cộng:</strong></td>
                                        <td class="text-end"><strong class="text-primary"><?php echo number_format($grand_total, 0, ',', '.'); ?> VNĐ</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="submit" form="checkoutForm" class="btn btn-primary btn-checkout w-100">
                            <i class="fas fa-check-circle me-2"></i>Hoàn tất đặt hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
