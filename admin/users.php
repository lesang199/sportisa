<?php
session_start();
include '../config/database.php';


require_once '../admin/controller/userscontroller.php';
require_once '../admin/model/usersmodel.php';



$userModel = new UserModel($conn);
$userController = new UserController($userModel);

// Kiểm tra quyền admin
$userController->checkAdminAccess();

// Xử lý hành động
if (isset($_GET['toggle_status'])) {
    $userController->handleToggleStatus(intval($_GET['toggle_status']));
}

if (isset($_GET['delete'])) {
    $userController->handleDeleteUser(intval($_GET['delete']));
}

// Lấy danh sách người dùng
$users = $userController->getUsers();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
   
  
</head>
<body>
    <?php require '../admin/view/usersview.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
