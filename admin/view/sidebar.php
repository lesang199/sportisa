<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SideBar</title>
    <link rel="stylesheet" href="style.css">
  <style>
    .nav-item.active {
        background-color: #e9ecef !important;
        border-radius: 5px;
    }
    .nav-item.active .nav-link,
    .nav-item.active .nav-link i,
    .nav-item.active .nav-link span {
        color: #0d6efd !important;
    }
    .sidebar-admin{
        height: 100vh;
    }
</style>
</style>
</head>
<body>
    
    <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse sidebar-admin">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">SPORTISA</h4>
                        <p class="text-muted">Quản lý</p>
                    </div>
                  <ul class="nav flex-column">
    <li class="nav-item <?php if(basename($_SERVER['PHP_SELF'])=='index.php') echo 'active'; ?>">
        <a class="nav-link text-white" href="index.php">
            <i class="fas fa-home me-2"></i><span>Tổng quan </span>
        </a>
    </li>
    <li class="nav-item <?php if(basename($_SERVER['PHP_SELF'])=='products.php') echo 'active'; ?>">
        <a class="nav-link text-white" href="products.php">
            <i class="fas fa-box me-2"></i> <span>Sản phẩm</span>
        </a>
    </li>
    <li class="nav-item <?php if(basename($_SERVER['PHP_SELF'])=='orders.php') echo 'active'; ?>">
        <a class="nav-link text-white" href="orders.php">
            <i class="fas fa-shopping-cart me-2"></i><span> Đơn hàng</span>
        </a>
    </li>
    <li class="nav-item <?php if(basename($_SERVER['PHP_SELF'])=='categories.php') echo 'active'; ?>">
        <a class="nav-link text-white" href="categories.php">
            <i class="fas fa-users me-2"></i> <span>Danh mục</span>
        </a>
    </li>
    <li class="nav-item <?php if(basename($_SERVER['PHP_SELF'])=='users.php') echo 'active'; ?>">
        <a class="nav-link text-white" href="users.php">
            <i class="fas fa-users me-2"></i> <span>Người dùng</span>
        </a>
    </li>
    <li class="nav-item <?php if(basename($_SERVER['PHP_SELF'])=='admins.php') echo 'active'; ?>">
        <a class="nav-link text-white" href="admins.php">
            <i class="fas fa-users me-2"></i> <span>Quản trị viên</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white" href="../logout.php">
            <i class="fas fa-sign-out-alt me-2"></i> <span>Đăng xuất</span>
        </a>
    </li>
</ul>
                </div>
            </div>
      <script>
    // Lấy tất cả các thẻ nav-item trong sidebar
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Xóa class active khỏi tất cả các nav-item
            navItems.forEach(i => i.classList.remove('active'));
            // Thêm class active cho nav-item được bấm
            this.classList.add('active');
        });
    });
</script>
</body>
</html>