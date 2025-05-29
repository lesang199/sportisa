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
    <?php require_once 'views/productsview.php'; ?>
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
                                <a href="#" class="category-link <?php echo !$category ? 'active' : ''; ?>" data-category="">
                                    <i class="fas fa-th-large me-2"></i>Tất cả sản phẩm
                                </a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a href="#" class="category-link <?php echo $category == $cat['categories_name'] ? 'active' : ''; ?>" 
                                       data-category="<?php echo $cat['categories_name']; ?>">
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
                        <div class="form-check mb-3">
                            <input class="form-check-input price-filter" type="radio" name="price" id="price1" value="0-500000" <?php echo $price_range == '0-500000' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="price1">
                                <i class="fas fa-money-bill-wave me-2"></i>Dưới 500.000 VNĐ
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input price-filter" type="radio" name="price" id="price2" value="500000-1000000" <?php echo $price_range == '500000-1000000' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="price2">
                                <i class="fas fa-money-bill-wave me-2"></i>500.000 - 1.000.000 VNĐ
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input price-filter" type="radio" name="price" id="price3" value="1000000-2000000" <?php echo $price_range == '1000000-2000000' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="price3">
                                <i class="fas fa-money-bill-wave me-2"></i>1.000.000 - 2.000.000 VNĐ
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input price-filter" type="radio" name="price" id="price4" value="2000000-" <?php echo $price_range == '2000000-' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="price4">
                                <i class="fas fa-money-bill-wave me-2"></i>Trên 2.000.000 VNĐ
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input price-filter" type="radio" name="price" id="priceAll" value="" <?php echo !$price_range ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="priceAll">
                                <i class="fas fa-times-circle me-2"></i>Tất cả giá
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo $category ? ucfirst($category) : 'Tất cả sản phẩm'; ?></h2>
                    <div class="d-flex">
                        <form class="me-3" method="GET" id="searchForm">
                            <input type="hidden" name="category" id="currentCategory" value="<?php echo $category; ?>">
                            <input type="hidden" name="price" id="currentPrice" value="<?php echo $price_range; ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" name="search" placeholder="Tìm kiếm..." value="<?php echo $search; ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                        <select class="form-select" id="sortSelect">
                            <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                            <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                            <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                            <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Tên A-Z</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row g-4" id="productsContainer">
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
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Cập nhật URL trình duyệt mà không tải lại trang
        function updateBrowserURL(params) {
            const queryString = Object.keys(params)
                .filter(key => params[key] !== '')
                .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
                .join('&');
            
            const newUrl = window.location.pathname + (queryString ? `?${queryString}` : '');
            window.history.pushState({ path: newUrl }, '', newUrl);
        }
        
        // Gửi yêu cầu AJAX để lấy sản phẩm
        function fetchProducts(params = {}) {
            // Tạo URL với các tham số
            const queryString = Object.keys(params)
                .filter(key => params[key] !== '')
                .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
                .join('&');
            
            fetch(`ajax_search.php?${queryString}`)
                .then(response => response.text())
                .then(html => {
                    // Cập nhật danh sách sản phẩm
                    document.getElementById('productsContainer').innerHTML = html;
                    
                    // Khởi tạo lại tooltip và sự kiện thêm vào giỏ hàng
                    initTooltipsAndCart();
                    
                    // Cập nhật tiêu đề danh mục
                    updateCategoryTitle(params.category || '');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        
        // Cập nhật tiêu đề danh mục
        function updateCategoryTitle(category) {
            const titleElement = document.querySelector('.col-md-9 h2');
            if (titleElement) {
                titleElement.textContent = category ? 
                    category.charAt(0).toUpperCase() + category.slice(1) : 
                    'Tất cả sản phẩm';
            }
        }
        
        // Khởi tạo tooltip và sự kiện thêm vào giỏ hàng
        function initTooltipsAndCart() {
            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
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
                            const cartCount = document.querySelector('.badge');
                            if (cartCount) cartCount.textContent = data.cart_count;
                        } else {
                            alert('Có lỗi xảy ra');
                        }
                    });
                });
            });
        }
        
        // Xử lý khi trang được tải
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo ban đầu
            initTooltipsAndCart();
            
            // Lấy các tham số hiện tại
            const currentParams = {
                category: '<?php echo $category; ?>',
                search: '<?php echo $search; ?>',
                sort: '<?php echo $sort; ?>',
                price: '<?php echo $price_range; ?>'
            };
            
            // Xử lý tìm kiếm
            document.getElementById('searchForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                currentParams.search = document.getElementById('searchInput').value;
                currentParams.category = document.getElementById('currentCategory').value;
                currentParams.price = document.getElementById('currentPrice').value;
                
                fetchProducts(currentParams);
                updateBrowserURL(currentParams);
            });
            
            // Xử lý sắp xếp
            document.getElementById('sortSelect').addEventListener('change', function() {
                currentParams.sort = this.value;
                fetchProducts(currentParams);
                updateBrowserURL(currentParams);
            });
            
            // Xử lý lọc danh mục
            document.querySelectorAll('.category-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Cập nhật active state
                    document.querySelectorAll('.category-link').forEach(el => {
                        el.classList.remove('active');
                    });
                    this.classList.add('active');
                    
                    // Cập nhật tham số
                    currentParams.category = this.dataset.category;
                    document.getElementById('currentCategory').value = currentParams.category;
                    
                    fetchProducts(currentParams);
                    updateBrowserURL(currentParams);
                });
            });
            
            // Xử lý lọc giá
            document.querySelectorAll('.price-filter').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        currentParams.price = this.value;
                        document.getElementById('currentPrice').value = currentParams.price;
                        
                        fetchProducts(currentParams);
                        updateBrowserURL(currentParams);
                    }
                });
            });
            
            // Xử lý khi người dùng nhấn nút back/forward
            window.addEventListener('popstate', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const params = {
                    category: urlParams.get('category') || '',
                    search: urlParams.get('search') || '',
                    sort: urlParams.get('sort') || 'newest',
                    price: urlParams.get('price') || ''
                };
                
                // Cập nhật UI
                document.getElementById('searchInput').value = params.search;
                document.getElementById('sortSelect').value = params.sort;
                document.getElementById('currentCategory').value = params.category;
                document.getElementById('currentPrice').value = params.price;
                
                // Cập nhật radio button giá
                document.querySelectorAll('.price-filter').forEach(radio => {
                    radio.checked = (radio.value === params.price);
                });
                
                // Cập nhật active state danh mục
                document.querySelectorAll('.category-link').forEach(link => {
                    link.classList.toggle('active', link.dataset.category === params.category);
                });
                
                // Lấy lại sản phẩm
                fetchProducts(params);
            });
        });
    </script>
</body>
</html>