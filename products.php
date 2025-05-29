<?php
session_start();
include 'config/database.php';

// ProductsModel.php
require_once 'models/ProductsModel.php';

// ProductsController.php
require_once 'controllers/ProductsController.php';

// Khởi tạo model và controller
$productsModel = new ProductsModel($conn);
$productsController = new ProductsController($productsModel);

// Lấy dữ liệu từ request
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$price_range = isset($_GET['price']) ? $_GET['price'] : '';

// Lấy danh sách danh mục và sản phẩm
$categories = $productsController->getCategories();
$products = $productsController->getProducts($_GET);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-list li a {
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            display: block;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
        }
        .sidebar-list li a:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
        }
        .sidebar-list li a.active {
            background-color: #e9ecef;
            color: #0d6efd;
            font-weight: 600;
        }
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .form-check-label {
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .form-check:hover .form-check-label {
            color: #0d6efd;
        }
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        .product-card:hover .card-img-top {
            transform: scale(1.05);
        }
        
        .product-actions {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .product-card:hover .product-actions {
            opacity: 1;
        }
        
        .product-image-container {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container py-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <!-- Categories Filter -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-list me-2"></i>Danh mục
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-unstyled sidebar-list mb-0">
                            <li>
                                <a href="products.php" class="<?php echo !$category ? 'active' : ''; ?>">
                                    <i class="fas fa-th-large me-2"></i>Tất cả sản phẩm
                                </a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a href="products.php?category=<?php echo $cat['categories_name']; ?>" 
                                       class="<?php echo $category == $cat['categories_name'] ? 'active' : ''; ?>">
                                        <i class="fas fa-angle-right me-2"></i><?php echo $cat['name']; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Price Filter -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-tags me-2"></i>Lọc theo giá
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="products.php">
                            <input type="hidden" name="category" value="<?php echo $category; ?>">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="price" id="price1" value="0-500000" <?php echo $price_range == '0-500000' ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="price1">
                                    <i class="fas fa-money-bill-wave me-2"></i>Dưới 500.000 VNĐ
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="price" id="price2" value="500000-1000000" <?php echo $price_range == '500000-1000000' ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="price2">
                                    <i class="fas fa-money-bill-wave me-2"></i>500.000 - 1.000.000 VNĐ
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="price" id="price3" value="1000000-2000000" <?php echo $price_range == '1000000-2000000' ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="price3">
                                    <i class="fas fa-money-bill-wave me-2"></i>1.000.000 - 2.000.000 VNĐ
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" id="price4" value="2000000-" <?php echo $price_range == '2000000-' ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="price4">
                                    <i class="fas fa-money-bill-wave me-2"></i>Trên 2.000.000 VNĐ
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo $category ? ucfirst($category) : 'Tất cả sản phẩm'; ?></h2>
                    <div class="d-flex">
                        <form class="me-3" method="GET">
                            <input type="hidden" name="category" value="<?php echo $category; ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." value="<?php echo $search; ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                        <select class="form-select" onchange="window.location.href=this.value">
                            <option value="products.php?sort=newest<?php echo $category ? '&category='.$category : ''; ?>" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                            <option value="products.php?sort=price_asc<?php echo $category ? '&category='.$category : ''; ?>" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                            <option value="products.php?sort=price_desc<?php echo $category ? '&category='.$category : ''; ?>" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                            <option value="products.php?sort=name<?php echo $category ? '&category='.$category : ''; ?>" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Tên A-Z</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row g-4">
                    <?php if (empty($products)): ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h3 class="text-muted">Không tìm thấy sản phẩm</h3>
                                    <p class="text-muted">Vui lòng thử lại với từ khóa hoặc bộ lọc khác.</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-0 shadow-sm product-card">
                                    <div class="position-relative">
                                        <div class="product-image-container" style="height: 250px; overflow: hidden;">
                                            <?php if (!empty($product['image']) && file_exists('uploads/products/' . $product['image'])): ?>
                                                <img src="uploads/products/<?php echo $product['image']; ?>" 
                                                     class="card-img-top" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                     style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                            <?php else: ?>
                                                <img src="assets/images/no-image.jpg" 
                                                     class="card-img-top" 
                                                     alt="Không có hình ảnh"
                                                     style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-actions position-absolute top-0 end-0 p-2">
                                            <button class="btn btn-light btn-sm rounded-circle shadow-sm add-to-cart" 
                                                    data-id="<?php echo $product['id']; ?>"
                                                    data-bs-toggle="tooltip"
                                                    title="Thêm vào giỏ hàng">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title text-truncate mb-2">
                                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" 
                                               class="text-decoration-none text-dark">
                                                <?php echo htmlspecialchars($product['name']); ?>
                                            </a>
                                        </h5>
                                        <p class="card-text text-primary fw-bold mb-3">
                                            <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ
                                        </p>
                                        <div class="d-grid">
                                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye me-2"></i>Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <script>
                    // Khởi tạo tooltips
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    });
                </script>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Xử lý thêm vào giỏ hàng
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã thêm vào giỏ hàng');
                        // Cập nhật số lượng trong giỏ hàng
                        const cartCount = document.querySelector('.badge');
                        cartCount.textContent = data.cart_count;
                    } else {
                        alert('Có lỗi xảy ra');
                    }
                });
            });
        });
    </script>
</body>
</html> 