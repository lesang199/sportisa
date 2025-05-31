<?php
session_start();
require_once '../config/database.php';

class CategoryManager {
    private $conn;
    private $categories;
    private $error;
    private $success;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
        $this->checkAdmin();
        $this->handleRequests();
        $this->loadCategories();
    }

    private function checkAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: login.php");
            exit();
        }
    }

    private function handleRequests() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
            $this->addCategory();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
            $this->updateCategory();
        }
        
        if (isset($_GET['delete'])) {
            $this->deleteCategory();
        }
    }

    private function addCategory() {
        $name = trim($_POST['name']);
        $slug = strtolower(str_replace(' ', '-', $name));
        $description = trim($_POST['description']);
        $status = $_POST['status'];

        try {
            $stmt = $this->conn->prepare("INSERT INTO categories (name, slug, description, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $description, $status]);
            $this->success = 'Thêm danh mục thành công';
        } catch(PDOException $e) {
            $this->error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }

    private function updateCategory() {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $slug = strtolower(str_replace(' ', '-', $name));
        $description = trim($_POST['description']);
        $status = $_POST['status'];

        try {
            $stmt = $this->conn->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $status, $id]);
            $this->success = 'Cập nhật danh mục thành công';
        } catch(PDOException $e) {
            $this->error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }

    private function deleteCategory() {
        $id = intval($_GET['delete']);

        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
            $stmt->execute([$id]);
            $has_products = $stmt->fetchColumn() > 0;

            if ($has_products) {
                $this->error = 'Không thể xóa danh mục này vì có sản phẩm liên quan';
            } else {
                $stmt = $this->conn->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute([$id]);
                $this->success = 'Xóa danh mục thành công';
            }
        } catch(PDOException $e) {
            $this->error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }

    private function loadCategories() {
        try {
            $stmt = $this->conn->query("
                SELECT * FROM categories 
                ORDER BY name
            ");
            $this->categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $this->error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }

    public function render() {
        ?>
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Quản lý danh mục - SPORTISA</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <link href="../css/style.css" rel="stylesheet">
        </head>
        <body>
            <div class="container-fluid">
                <div class="row">
                    <?php include '../admin/view/sidebar.php'; ?> 

                    <!-- Main content -->
                    <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                            <h1 class="h2">Quản lý danh mục</h1>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="fas fa-plus me-2"></i>Thêm danh mục
                            </button>
                        </div>

                        <?php if ($this->success): ?>
                            <div class="alert alert-success"><?php echo $this->success; ?></div>
                        <?php endif; ?>

                        <?php if ($this->error): ?>
                            <div class="alert alert-danger"><?php echo $this->error; ?></div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên danh mục</th>
                                        <th>Mô tả</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($this->categories as $category): ?>
                                        <tr>
                                            <td><?php echo $category['id']; ?></td>
                                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                                            <td><?php echo htmlspecialchars($category['description']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $category['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo $category['status'] === 'active' ? 'Hoạt động' : 'Không hoạt động'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($category['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editCategoryModal<?php echo $category['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="?delete=<?php echo $category['id']; ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Edit Category Modal -->
                                        <div class="modal fade" id="editCategoryModal<?php echo $category['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Chỉnh sửa danh mục</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Tên danh mục</label>
                                                                <input type="text" class="form-control" name="name" 
                                                                       value="<?php echo htmlspecialchars($category['name']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Mô tả</label>
                                                                <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Trạng thái</label>
                                                                <select class="form-select" name="status">
                                                                    <option value="active" <?php echo $category['status'] === 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                                                    <option value="inactive" <?php echo $category['status'] === 'inactive' ? 'selected' : ''; ?>>Không hoạt động</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit" name="update_category" class="btn btn-primary">Cập nhật</button>
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

            <!-- Add Category Modal -->
            <div class="modal fade" id="addCategoryModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Thêm danh mục mới</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Tên danh mục</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-select" name="status">
                                        <option value="active">Hoạt động</option>
                                        <option value="inactive">Không hoạt động</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" name="add_category" class="btn btn-primary">Thêm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }
}

// Usage
try {
    // Sử dụng biến $conn đã được tạo từ database.php
    $categoryManager = new CategoryManager($conn);
    $categoryManager->render();
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>