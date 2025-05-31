<?php
session_start();
include '../config/database.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Kiểm tra ID sản phẩm
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = intval($_GET['id']);

// Lấy thông tin sản phẩm
try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: products.php");
        exit();
    }

    // Lấy thông tin chi tiết sản phẩm
    $stmt = $conn->prepare("SELECT * FROM product_details WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product_detail = $stmt->fetch(PDO::FETCH_ASSOC);

    // Lấy danh sách danh mục
    $stmt = $conn->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy danh sách thương hiệu
    $stmt = $conn->query("SELECT * FROM brands WHERE status = 'active' ORDER BY name");
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy danh sách size của sản phẩm
    $stmt = $conn->prepare("SELECT * FROM product_sizes WHERE id = ? ORDER BY size");
    $stmt->execute([$product_id]);
    $product_sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = 'Có lỗi xảy ra: ' . $e->getMessage();
}

// Xử lý cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $sale_price = !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null;
    $category_id = intval($_POST['category_id']);
    $brand_id = !empty($_POST['brand_id']) ? intval($_POST['brand_id']) : null;
    $stock = intval($_POST['stock']);
    $status = $_POST['status'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    $color = trim($_POST['color']);
    $material = trim($_POST['material']);
    $origin = trim($_POST['origin']);
    $weight = !empty($_POST['weight']) ? floatval($_POST['weight']) : null;

    // Xử lý sizes
    $sizes = isset($_POST['sizes']) ? $_POST['sizes'] : [];
    $existing_sizes = [];
    if ($product_sizes) {
        foreach ($product_sizes as $size) {
            $existing_sizes[] = $size['size'];
        }
    }

    try {
        // Xử lý upload hình ảnh mới nếu có
        $image = $product['image']; // Giữ nguyên ảnh cũ nếu không upload mới
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error = 'Chỉ chấp nhận file ảnh (jpg, jpeg, png, gif)';
            } else {
                // Tạo tên file mới
                $new_filename = uniqid() . '.' . $ext;
                $upload_dir = '../uploads/products/';
                
                // Kiểm tra và tạo thư mục nếu chưa tồn tại
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $upload_path = $upload_dir . $new_filename;

                // Upload file
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // Xóa ảnh cũ nếu tồn tại
                    if ($product['image'] && file_exists($upload_dir . $product['image'])) {
                        unlink($upload_dir . $product['image']);
                    }
                    $image = $new_filename;
                } else {
                    $error = 'Không thể upload file. Vui lòng kiểm tra quyền ghi thư mục.';
                }
            }
        }

        if (!isset($error)) {
            // Cập nhật bảng products
            $stmt = $conn->prepare("
                UPDATE products 
                SET name = ?, slug = ?, description = ?, price = ?, sale_price = ?, 
                    category_id = ?, brand_id = ?, stock = ?, status = ?, featured = ?, image = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $name, $slug, $description, $price, $sale_price, 
                $category_id, $brand_id, $stock, $status, $featured, $image, $product_id
            ]);

            // Cập nhật hoặc thêm mới thông tin chi tiết sản phẩm
            if ($product_detail) {
                $stmt = $conn->prepare("
                    UPDATE product_details 
                    SET color = ?, material = ?, origin = ?, weight = ?, category_id = ?
                    WHERE product_id = ?
                ");
                $stmt->execute([$color, $material, $origin, $weight, $category_id, $product_id]);
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO product_details 
                    (product_id, color, material, origin, weight, category_id)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$product_id, $color, $material, $origin, $weight, $category_id]);
            }

            // Xử lý cập nhật sizes
            // Xóa tất cả sizes cũ
            $stmt = $conn->prepare("DELETE FROM product_sizes WHERE id = ?");
            $stmt->execute([$product_id]);

            // Thêm sizes mới
            if (!empty($sizes)) {
                $stmt = $conn->prepare("INSERT INTO product_sizes (id, size) VALUES (?, ?)");
                foreach ($sizes as $size) {
                    if (!empty(trim($size))) {
                        $stmt->execute([$product_id, trim($size)]);
                    }
                }
            }

            $success = 'Cập nhật sản phẩm thành công';
            
            // Cập nhật lại thông tin sản phẩm
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            // Cập nhật lại thông tin chi tiết
            $stmt = $conn->prepare("SELECT * FROM product_details WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product_detail = $stmt->fetch(PDO::FETCH_ASSOC);

            // Cập nhật lại danh sách size
            $stmt = $conn->prepare("SELECT * FROM product_sizes WHERE id = ? ORDER BY size");
            $stmt->execute([$product_id]);
            $product_sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) {
        $error = 'Có lỗi xảy ra: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm - SPORTISA</title>
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
                    <h1 class="h2">Chỉnh sửa sản phẩm</h1>
                    <a href="products.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Tên sản phẩm</label>
                                        <input type="text" class="form-control" name="name" 
                                               value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Mô tả</label>
                                        <textarea class="form-control" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Giá gốc</label>
                                                <input type="number" class="form-control" name="price" 
                                                       value="<?php echo $product['price']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Giá khuyến mãi</label>
                                                <input type="number" class="form-control" name="sale_price" 
                                                       value="<?php echo $product['sale_price']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Danh mục</label>
                                                <select class="form-select" name="category_id" required>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?php echo $category['id']; ?>" 
                                                                <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($category['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Thương hiệu</label>
                                                <select class="form-select" name="brand_id">
                                                    <option value="">Chọn thương hiệu</option>
                                                    <?php foreach ($brands as $brand): ?>
                                                        <option value="<?php echo $brand['id']; ?>" 
                                                                <?php echo $brand['id'] == $product['brand_id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($brand['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Số lượng</label>
                                                <input type="number" class="form-control" name="stock" 
                                                       value="<?php echo $product['stock']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Trạng thái</label>
                                                <select class="form-select" name="status">
                                                    <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                                    <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>Không hoạt động</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Thông tin chi tiết sản phẩm -->
                                    <h4 class="mt-4 mb-3">Thông tin chi tiết</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Màu sắc</label>
                                                <input type="text" class="form-control" name="color" 
                                                       value="<?php echo htmlspecialchars($product_detail['color'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Chất liệu</label>
                                                <input type="text" class="form-control" name="material" 
                                                       value="<?php echo htmlspecialchars($product_detail['material'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Xuất xứ</label>
                                                <input type="text" class="form-control" name="origin" 
                                                       value="<?php echo htmlspecialchars($product_detail['origin'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Trọng lượng (kg)</label>
                                                <input type="number" step="0.01" class="form-control" name="weight" 
                                                       value="<?php echo $product_detail['weight'] ?? ''; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Size Management -->
                                    <div class="mb-3">
                                        <label class="form-label">Kích thước</label>
                                        <div id="sizes-container">
                                            <?php 
                                            if ($product_sizes) {
                                                foreach ($product_sizes as $index => $size) {
                                                    echo '<div class="input-group mb-2 size-input-group">
                                                            <input type="text" class="form-control" name="sizes[]" 
                                                                   value="' . htmlspecialchars($size['size']) . '" placeholder="Nhập kích thước">
                                                            <button type="button" class="btn btn-danger remove-size">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                          </div>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <button type="button" class="btn btn-secondary btn-sm" id="add-size">
                                            <i class="fas fa-plus me-2"></i>Thêm kích thước
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="featured" id="featured" 
                                                   <?php echo $product['featured'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="featured">Sản phẩm nổi bật</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Hình ảnh sản phẩm</label>
                                        <?php if ($product['image']): ?>
                                            <div class="mb-2">
                                                <img src="../uploads/products/<?php echo $product['image']; ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                     class="img-thumbnail" style="max-width: 200px;">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" name="image" accept="image/*">
                                        <small class="text-muted">Để trống nếu không muốn thay đổi ảnh</small>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật sản phẩm
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý thêm size mới
            document.getElementById('add-size').addEventListener('click', function() {
                const container = document.getElementById('sizes-container');
                const newSizeInput = document.createElement('div');
                newSizeInput.className = 'input-group mb-2 size-input-group';
                newSizeInput.innerHTML = `
                    <input type="text" class="form-control" name="sizes[]" placeholder="Nhập kích thước">
                    <button type="button" class="btn btn-danger remove-size">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                container.appendChild(newSizeInput);
            });

            // Xử lý xóa size
            document.getElementById('sizes-container').addEventListener('click', function(e) {
                if (e.target.closest('.remove-size')) {
                    e.target.closest('.size-input-group').remove();
                }
            });
        });
    </script>
</body>
</html>
