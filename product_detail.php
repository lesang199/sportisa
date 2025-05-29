<?php
session_start();
include 'config/database.php';

// Kiểm tra ID sản phẩm
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = $_GET['id'];

// Lấy thông tin sản phẩm
$query = "SELECT 
            p.id,
            p.name,
            p.price,
            p.image,
            p.description,
            p.stock,
            pd.color,
            pd.material,
            pd.origin,
            pd.weight,
            pd.category_id,
            c.name AS category_name
          FROM products p
          JOIN product_details pd ON p.id = pd.product_id
          JOIN categories c ON pd.category_id = c.id
          WHERE p.id = ?";

$stmt = $conn->prepare($query);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: products.php");
    exit();
}

// Lấy các size có sẵn cho sản phẩm từ bảng product_details và product_sizes
$query = "SELECT DISTINCT ps.size
          FROM product_sizes ps
          JOIN product_details pd ON ps.id = pd.product_id
          WHERE pd.product_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$product_id]);
$sizes = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Lấy sản phẩm liên quan cùng danh mục
$query = "SELECT p.* 
          FROM products p
          JOIN product_details pd ON p.id = pd.product_id
          WHERE pd.category_id = ? AND p.id != ?
          LIMIT 4";
$stmt = $conn->prepare($query);
$stmt->execute([$product['category_id'], $product_id]);
$related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        .product-image {
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        .product-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .product-card:hover .product-overlay {
            opacity: 1;
        }
        .product-actions {
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-actions {
            transform: translateY(0);
        }
        .main-product-image {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .main-product-image:hover {
            transform: scale(1.02);
        }
        .product-info {
            background: #fff;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .policy-card {
            background: #f8f9fa;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }
        .policy-card:hover {
            transform: translateY(-3px);
        }
        .size-options {
            margin-bottom: 1rem;
        }
        .size-option .btn {
            min-width: 60px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .size-option .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .btn-check:checked + .btn {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container py-5">
        <div class="row g-4">
            <!-- Hình ảnh sản phẩm -->
            <div class="col-md-6">
                <div class="position-relative">
                    <img src="uploads/products/<?php echo $product['image']; ?>" 
                         class="img-fluid main-product-image" 
                         alt="<?php echo $product['name']; ?>">
                </div>
            </div>

            <!-- Thông tin sản phẩm -->
            <div class="col-md-6">
                <div class="product-info">
                    <h1 class="mb-3 fw-bold"><?php echo $product['name']; ?></h1>
                    
                    <h2 class="text-primary mb-4 fw-bold">
                        <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ
                    </h2>
                    <p class="mb-4 text-muted"><?php echo $product['description']; ?></p>

                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">Thông số kỹ thuật:</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><strong>Danh mục:</strong> <?php echo ucfirst($product['category_name']); ?></li>
                            <li class="mb-2"><strong>Màu sắc:</strong> <?php echo $product['color']; ?></li>
                            <li class="mb-2"><strong>Trọng lượng:</strong> <?php echo $product['weight']; ?></li>
                            <li class="mb-2"><strong>Xuất xứ:</strong> <?php echo $product['origin']; ?></li>
                            <li class="mb-2"><strong>Chất liệu:</strong> <?php echo $product['material']; ?></li>
                            <li class="mb-2">
                                <strong>Tình trạng:</strong> 
                                <?php if ($product['stock'] > 0): ?>
                                    <span class="badge bg-success">Còn hàng</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Hết hàng</span>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>

                    <!-- Chọn size -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-3">Chọn size:</label>
                        <div class="size-options d-flex flex-wrap gap-2">
                            <?php foreach ($sizes as $size): ?>
                                <div class="size-option">
                                    <input type="radio" class="btn-check" name="size" id="size_<?php echo htmlspecialchars($size); ?>" value="<?php echo htmlspecialchars($size); ?>" required>
                                    <label class="btn btn-outline-primary" for="size_<?php echo htmlspecialchars($size); ?>">
                                        <?php echo htmlspecialchars($size); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Tăng/giảm số lượng -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="input-group me-3" style="width: 150px;">
                            <button class="btn btn-outline-primary" type="button" id="decrease">-</button>
                            <input type="number" class="form-control text-center" value="1" min="1" max="<?php echo $product['stock']; ?>" id="quantity">
                            <button class="btn btn-outline-primary" type="button" id="increase">+</button>
                        </div>
                        <button class="btn btn-primary me-2 add-to-cart" data-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                        </button>
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>

                    <div class="policy-card p-3">
                        <h5 class="fw-bold mb-3">Chính sách bán hàng</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Miễn phí vận chuyển cho đơn hàng trên 1.000.000 VNĐ</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Đổi trả trong 7 ngày</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Bảo hành 12 tháng</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Hỗ trợ 24/7</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sản phẩm liên quan -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4 fw-bold">Sản phẩm liên quan</h3>
                <div class="row g-4">
                    <?php foreach ($related_products as $related): ?>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <div class="position-relative">
                                <div class="product-image-container" style="height: 250px; overflow: hidden;">
                                    <img src="uploads/products/<?php echo $related['image']; ?>" 
                                         class="card-img-top product-image" 
                                         alt="<?php echo $related['name']; ?>"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title text-dark mb-2"><?php echo $related['name']; ?></h5>
                                <p class="card-text text-danger fw-bold mb-3">
                                    <?php echo number_format($related['price'], 0, ',', '.'); ?> VNĐ
                                </p>
                                <div class="d-flex justify-content-between">
                                    <a href="product_detail.php?id=<?php echo $related['id']; ?>" class="btn btn-primary">Xem chi tiết</a>
                                    <button class="btn btn-outline-primary add-to-cart" data-id="<?php echo $related['id']; ?>">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Xử lý tăng/giảm số lượng
        document.getElementById('increase').addEventListener('click', function() {
            const input = document.getElementById('quantity');
            const max = parseInt(input.getAttribute('max'));
            if (parseInt(input.value) < max) {
                input.value = parseInt(input.value) + 1;
            }
        });

        document.getElementById('decrease').addEventListener('click', function() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        });

        // Thêm vào giỏ hàng
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.dataset.id;
                const quantity = document.getElementById('quantity').value;
                const size = document.querySelector('input[name="size"]:checked')?.value;

                if (!size) {
                    alert("Vui lòng chọn size trước khi thêm vào giỏ hàng.");
                    return;
                }

                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + encodeURIComponent(productId) + 
                          '&quantity=' + encodeURIComponent(quantity) +
                          '&size=' + encodeURIComponent(size)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã thêm vào giỏ hàng');
                        const cartCount = document.querySelector('.badge');
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;
                        }
                    } else {
                        alert('Có lỗi xảy ra');
                    }
                });
            });
        });
    </script>
</body>
</html>