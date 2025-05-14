
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SideBar</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .nav-link.active i,
    .nav-link.active span {
     color:greenyellow;
    }
    span{
        font-size: 17px
    }
    </style>
</head>
<body>
    <?php
        $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
        <div class="position-sticky pt-3">
            <div class="text-center mb-4">
                <h4 class="text-white">SPORTISA</h4>
                <p class="text-muted">Quản lý</p>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white <?php if($current_page == 'index.php') echo 'active'; ?>" href="index.php">
                        <i class="fas fa-home me-2"></i><span>Tổng quan </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php if($current_page == 'products.php') echo 'active'; ?>" href="products.php">
                        <i class="fas fa-box me-2"></i> <span>Sản phẩm</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php if($current_page == 'orders.php') echo 'active'; ?>" href="orders.php">
                        <i class="fas fa-shopping-cart me-2"></i><span> Đơn hàng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php if($current_page == 'users.php') echo 'active'; ?>" href="users.php">
                        <i class="fas fa-users me-2"></i> <span>Người dùng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php if($current_page == 'admins.php') echo 'active'; ?>" href="admins.php">
                        <i class="fas fa-users me-2"></i> <span>Quản trị viên</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php if($current_page == 'logout.php') echo 'active'; ?>" href="../logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i> <span>Đăng xuất</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</body>
</html>