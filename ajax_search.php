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

// Lấy sản phẩm dựa trên tham số GET
$products = $productsController->getProducts($_GET);

// Trả về HTML của danh sách sản phẩm
if (empty($products)): ?>
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