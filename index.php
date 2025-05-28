<?php
session_start();
include 'config/database.php';

require_once 'models/indexmodel.php';
require_once 'controllers/indexcontroller.php';

$model = new IndexModel($conn);
$controller = new IndexController($model);

// Lấy dữ liệu trang chủ
$data = $controller->loadHomepageData();

$featured_products = $data['featured_products'] ?? [];
$categories = $data['categories'] ?? [];
$brands = $data['brands'] ?? [];
$news = $data['news'] ?? [];
$error = $controller->getError();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPORTISA - Cửa hàng thể thao chính hãng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <?php require 'views/indexview.php'; ?>
</head>
<body>
    <!-- Header -->
    <header class="bg-dark text-white">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="index.php">SPORTISA</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse d-flex" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Trang chủ</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Danh mục</a>
                            <ul class="dropdown-menu">
                               <?php foreach ($categories as $category): ?>
                                    <li>
                                        <a class="dropdown-item" href="products.php?category=<?php echo $category['categories_name']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">Giới thiệu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="news.php">Tin tức</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Liên hệ</a>
                        </li>
                    </ul>
                    <div class="d-flex">
                        <a href="cart.php" class="btn btn-outline-light me-2">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge bg-danger"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                        </a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                    <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="profile.php">Tài khoản</a></li>
                                    <li><a class="dropdown-item" href="orders.php">Đơn hàng</a></li>
                                    <li><a class="dropdown-item" href="change_password.php">Đổi mật khẩu</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-light">Đăng nhập</a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4">Thể thao chính hãng</h1>
                    <p class="lead">Khám phá bộ sưu tập giày thể thao, quần áo và phụ kiện chất lượng cao từ các thương hiệu hàng đầu thế giới.</p>
                    <a href="products.php" class="btn btn-light btn-lg">Mua sắm ngay</a>
                </div>
                <div class="col-md-6">
                   
                </div>
            </div>
        </div>
    </section>
    <!-- Carousel -->
    <div class="carousel-container">
      <div class="carousel-inner-custom">
          <div class="carousel-img">
              <img src="./images/anhnen1.JPG" alt="">
              <img src="./images/anhnen2.JPG" alt="">
            <img src="./images/image.jpg" alt="">
            <img src="./images/anhnen4.jpg" alt="">
          </div>
    
        </div>
        <!-- Navigation arrows -->
         <span class="arrow-icon-left"><i class="fa-solid fa-arrow-left"></i></span>
         <span class="arrow-icon-right"><i class="fa-solid fa-arrow-right"></i></span>
    </div>

    <!-- Featured Products -->
    <section class="features py-5">
        <div class="container">
            <h2 class="text-center mb-4">Sản phẩm nổi bật</h2>
            <div class="row">
                <?php foreach ($featured_products as $product): ?>
                    <div class="col-md-3 mb-4">
                        <a href="product.php?slug=<?php echo $product['slug']; ?>" class="text-decoration-none text-dark">
                            <div class="card h-100 hover-shadow">
                                <?php if ($product['image']): ?>
                                    <img src="uploads/products/<?php echo $product['image']; ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($product['brand_name']); ?></p>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if ($product['sale_price']): ?>
                                            <span class="fw-bold text-danger"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                                            <small class="text-muted text-decoration-line-through ms-2"><?php echo number_format($product['sale_price'], 0, ',', '.'); ?> VNĐ</small>
                                        <?php else: ?>
                                            <span class="fw-bold text-danger"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="categories py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Danh mục sản phẩm</h2>
            <div class="row">
                <?php foreach ($categories as $category): ?>
                    <div class="col-12 col-sm-6 col-md-4 mb-4 d-flex align-items-stretch">
                        <div class="card w-100 h-100 shadow-sm">
                            <?php if (!empty($category['image'])): ?>
                                <img src="uploads/categories/<?php echo $category['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($category['name']); ?>">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column h-100 text-center">
                                <h3 class="card-title mb-2"><?php echo htmlspecialchars($category['name']); ?></h3>
                                <p class="card-text flex-grow-1"><?php echo htmlspecialchars($category['description']); ?></p>
                                <div class="mt-auto">
                                    <a href="category.php?slug=<?php echo $category['slug']; ?>" class="btn btn-primary">Xem sản phẩm</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


      <!-- Brands -->
    <section class="brands py-5">
    <div class="container">
        <h2 class="text-center mb-4">Thương hiệu</h2>
        <div class="row">
            <?php foreach ($brands as $brand): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 position-relative">
                        <?php if ($brand['logo']): ?>
                            <img src="./images/<?php echo $brand['logo']; ?>" 
                                 class="card-img-top p-3" 
                                 alt="<?php echo htmlspecialchars($brand['name']); ?>">
                        <?php endif; ?>
                        <div class="card-body text-center overlay">
                            <h5 class="card-title"><?php echo htmlspecialchars($brand['name']); ?></h5>
                            <a href="brand.php?slug=<?php echo $brand['slug']; ?>" class="btn btn-outline-primary">Xem sản phẩm</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    </section>

 main
        <div class="container">
            <h2 class="text-center mb-4">Tin tức</h2>
            <div class="row">
                <?php foreach ($news as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if ($item['image']): ?>
                                <img src="uploads/news/<?php echo $item['image']; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                                <p class="card-text"><?php echo substr(strip_tags($item['content']), 0, 100) . '...'; ?></p>
                                <a href="news_detail.php?slug=<?php echo $item['slug']; ?>" class="btn btn-primary">Đọc thêm</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>      -->     

    <!-- Footer -->
    <?php require 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
  const imgAll = document.querySelectorAll(".carousel-img img");
  const list = document.querySelector(".carousel-img");
  const iconLeft = document.querySelector(".arrow-icon-left");
  const iconRight = document.querySelector(".arrow-icon-right");
  const imgLength = imgAll.length;
  let currentIdx = 0;

  function updateTransform() {
    const width = imgAll[0].offsetWidth;
    list.style.transform = `translateX(${-width * currentIdx}px)`;
    list.style.transition = '0.5s ease';
  }

  function slideShow() {
    currentIdx = (currentIdx + 1) % imgLength;
    updateTransform();
  }

  let interval = setInterval(slideShow,3000);

  iconLeft.addEventListener("click", () => {
    clearInterval(interval);
    currentIdx = (currentIdx === 0) ? imgLength - 1 : currentIdx - 1;
    updateTransform();
    interval = setInterval(slideShow, 3000);
  });

  iconRight.addEventListener("click", () => {
    clearInterval(interval);
    currentIdx = (currentIdx === imgLength - 1) ? 0 : currentIdx + 1;
    updateTransform();
    interval = setInterval(slideShow, 3000);
  });

  // Resize support
  window.addEventListener('resize', updateTransform);
</script>
   
</body>
</html>
