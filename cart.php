<?php
session_start();
include 'config/database.php';

require_once 'controllers/cartcontroller.php';

// Usage
$cartController = new CartController();
$cartController->handleRequest();
$total = $cartController->calculateTotal();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .cart-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .cart-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        .cart-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .quantity-input {
            width: 80px;
            text-align: center;
        }
        .summary-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .summary-card:hover {
            transform: translateY(-5px);
        }
        .empty-cart {
            text-align: center;
            padding: 3rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .empty-cart i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container py-5">
        <h1 class="mb-4 fw-bold">Giỏ hàng</h1>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3 class="mb-3">Giỏ hàng trống</h3>
                <p class="text-muted mb-4">Hãy thêm sản phẩm vào giỏ hàng của bạn</p>
                <a href="products.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <form method="POST">
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th class="text-center">Giá</th>
                                                <th class="text-center">Số lượng</th>
                                                <th class="text-end">Tổng</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                                            <tr class="cart-item">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="uploads/products/<?php echo $item['image']; ?>" 
                                                             alt="<?php echo $item['name']; ?>" 
                                                             class="cart-image me-3">
                                                        <div>
                                                            <h5 class="mb-0"><?php echo $item['name']; ?></h5>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" 
                                                           name="quantity[<?php echo $id; ?>]" 
                                                           value="<?php echo $item['quantity']; ?>" 
                                                           min="1" 
                                                           class="form-control quantity-input mx-auto">
                                                </td>
                                                <td class="text-end fw-bold">
                                                    <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ
                                                </td>
                                                <td class="text-end">
                                                    <button type="submit" 
                                                            name="remove_item" 
                                                            value="<?php echo $id; ?>" 
                                                            class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="products.php" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                                    </a>
                                    <button type="submit" name="update_cart" class="btn btn-primary">
                                        <i class="fas fa-sync me-2"></i>Cập nhật giỏ hàng
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="summary-card p-4">
                        <h4 class="mb-4 fw-bold">Tổng đơn hàng</h4>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tạm tính:</span>
                            <span class="fw-bold"><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Phí vận chuyển:</span>
                            <span class="fw-bold">
                                <?php 
                                $shipping = $total >= 1000000 ? 0 : 30000;
                                echo number_format($shipping, 0, ',', '.'); 
                                ?> VNĐ
                            </span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 mb-0">Tổng cộng:</span>
                            <span class="h5 mb-0 text-primary">
                                <?php echo number_format($total + $shipping, 0, ',', '.'); ?> VNĐ
                            </span>
                        </div>
                        <a href="checkout.php" class="btn btn-primary w-100">
                            <i class="fas fa-credit-card me-2"></i>Thanh toán
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>