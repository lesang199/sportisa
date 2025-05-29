<?php
session_start();
include 'config/database.php';
include 'header.php';

// Lấy danh sách sản phẩm nổi bật
try {
    $stmt = $conn->query("SELECT p.*, c.name as category_name, b.name as brand_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         LEFT JOIN brands b ON p.brand_id = b.id 
                         WHERE p.featured = 1 AND p.status = 'active'
                         ORDER BY p.created_at DESC 
                         LIMIT 4");
    $featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy danh sách thương hiệu
    $stmt = $conn->query("SELECT * FROM brands WHERE status = 'active'");
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy tin tức mới nhất
    
} catch(PDOException $e) {
    $error = 'Có lỗi xảy ra: ' . $e->getMessage();
}
?>


<!-- Carousel -->
<div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
       
    
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="images/niketheme.JPG" class="d-block w-100" alt="Slide 1">
        </div>
        <div class="carousel-item">
            <img src="images/bstheme.JPG" class="d-block w-100" alt="Slide 2">
        </div>
        
       
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- Featured Products -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">Sản phẩm nổi bật</h2>
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-md-3">
                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <div class="position-relative overflow-hidden">
                                <?php if ($product['image']): ?>
                                    <img src="uploads/products/<?php echo $product['image']; ?>" 
                                         class="card-img-top product-image" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php endif; ?>
                                
                            </div>
                            <div class="card-body">
                                <h5 class="card-title text-dark mb-2"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted small mb-2"><?php echo htmlspecialchars($product['brand_name']); ?></p>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-danger fs-5"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

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
</style>



<?php
include 'footer.php';
?>
