<?php
require_once 'config/database.php';
session_start();


// Model class
require_once 'models/profilemodel.php';


// Controller class
require_once 'controllers/profilecontroller.php';

// Kiểm tra session email
if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit;
}

try {
  $profileModel = new ProfileModel($conn);
  $profileController = new ProfileController($profileModel);
  $user = $profileController->getProfile($_SESSION['email']);
} catch (PDOException $e) {
  echo '<div class="alert alert-danger text-center">Có lỗi xảy ra: ' . $e->getMessage() . '</div>';
  exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thông tin cá nhân</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-body{
      padding: 50px
    }
  </style>
</head>
<?php include 'header.php'; ?>
<body class="bg-light">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow position-relative">
        <!-- Header -->
        <div class="card-header bg-primary text-white text-center">
          <h3>Thông tin cá nhân</h3>
        </div>

        

        <!-- Body -->
        <?php if (!empty($user)): ?>
          <?php foreach ($user as $userAct): ?>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item"><strong>Họ và tên:</strong> <?= htmlspecialchars($userAct['full_name']) ?></li>
              <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($userAct['email']) ?></li>
              <li class="list-group-item"><strong>Số điện thoại:</strong> <?= htmlspecialchars($userAct['phone']) ?></li>
              <li class="list-group-item"><strong>Địa chỉ:</strong> <?= htmlspecialchars($userAct['address']) ?></li>
            </ul>

            <!-- Buttons -->
            <div class="text-center mt-4">
              <a href="edit_profile.php" class="btn btn-warning me-2">Chỉnh sửa</a>
              <a href="logout.php" class="btn btn-danger me-2">Đăng xuất</a>
              <a href="index.php" class="btn btn-secondary">Quay lại trang chủ</a>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="card-body text-center">
            <p class="text-danger">Không tìm thấy thông tin người dùng.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>