<?php
session_start();
require_once '../config/database.php';

class AdminManager {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function addAdmin($data) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, full_name, phone, role) 
                                         VALUES (?, ?, ?, ?, ?, 'admin')");
            $stmt->execute([
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['full_name'],
                $data['phone']
            ]);
            return ['success' => 'Thêm quản trị viên thành công'];
        } catch(PDOException $e) {
            return ['error' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }
    
    public function updateAdmin($data) {
        try {
            $stmt = $this->conn->prepare("UPDATE users SET email = ?, full_name = ?, phone = ?, status = ? 
                                         WHERE id = ? AND role = 'admin'");
            $stmt->execute([
                $data['email'],
                $data['full_name'],
                $data['phone'],
                $data['status'],
                $data['id']
            ]);
            return ['success' => 'Cập nhật thông tin thành công'];
        } catch(PDOException $e) {
            return ['error' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }
    
    public function deleteAdmin($id, $currentUserId) {
        try {
            if ($id == $currentUserId) {
                return ['error' => 'Không thể xóa tài khoản của chính mình'];
            }
            
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
            $stmt->execute([$id]);
            return ['success' => 'Xóa quản trị viên thành công'];
        } catch(PDOException $e) {
            return ['error' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }
    
    public function getAllAdmins() {
        try {
            $stmt = $this->conn->query("SELECT * FROM users WHERE role = 'admin' ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return ['error' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }
}

// Check admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$adminManager = new AdminManager($conn);
$message = [];
$admins = [];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_admin'])) {
        $message = $adminManager->addAdmin([
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'full_name' => trim($_POST['full_name']),
            'phone' => trim($_POST['phone'])
        ]);
    } elseif (isset($_POST['update_admin'])) {
        $message = $adminManager->updateAdmin([
            'id' => intval($_POST['id']),
            'email' => trim($_POST['email']),
            'full_name' => trim($_POST['full_name']),
            'phone' => trim($_POST['phone']),
            'status' => $_POST['status']
        ]);
    }
}

// Handle delete action
if (isset($_GET['delete'])) {
    $message = $adminManager->deleteAdmin(intval($_GET['delete']), $_SESSION['user_id']);
}

// Get all admins
$result = $adminManager->getAllAdmins();
if (isset($result['error'])) {
    $message = $result;
} else {
    $admins = $result;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý quản trị viên - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../admin/view/sidebar.php'; ?> 

            <!-- Main content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Quản lý quản trị viên</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                        <i class="fas fa-plus me-2"></i>Thêm quản trị viên
                    </button>
                </div>

                <?php if (isset($message['success'])): ?>
                    <div class="alert alert-success"><?php echo $message['success']; ?></div>
                <?php endif; ?>

                <?php if (isset($message['error'])): ?>
                    <div class="alert alert-danger"><?php echo $message['error']; ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên đăng nhập</th>
                                <th>Email</th>
                                <th>Họ tên</th>
                                <th>Số điện thoại</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td><?php echo $admin['id']; ?></td>
                                    <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['phone']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $admin['status'] === 'active' ? 'success' : 'danger'; ?>">
                                            <?php echo $admin['status'] === 'active' ? 'Hoạt động' : 'Không hoạt động'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($admin['created_at'])); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editAdminModal<?php echo $admin['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($admin['id'] != $_SESSION['user_id']): ?>
                                            <a href="?delete=<?php echo $admin['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa quản trị viên này?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Edit Admin Modal -->
                                <div class="modal fade" id="editAdminModal<?php echo $admin['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Chỉnh sửa thông tin quản trị viên</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" class="form-control" name="email" 
                                                               value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Họ tên</label>
                                                        <input type="text" class="form-control" name="full_name" 
                                                               value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Số điện thoại</label>
                                                        <input type="text" class="form-control" name="phone" 
                                                               value="<?php echo htmlspecialchars($admin['phone']); ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Trạng thái</label>
                                                        <select class="form-select" name="status">
                                                            <option value="active" <?php echo $admin['status'] === 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                                            <option value="inactive" <?php echo $admin['status'] === 'inactive' ? 'selected' : ''; ?>>Không hoạt động</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit" name="update_admin" class="btn btn-primary">Cập nhật</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <div class="modal fade" id="addAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm quản trị viên mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" name="add_admin" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>