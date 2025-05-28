<?php

include 'config/database.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$price_range = isset($_GET['price']) ? $_GET['price'] : '';

$query = "SELECT p.*, c.categories_name FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($category) {
    $query .= " AND c.categories_name = ?";
    $params[] = $category;
}
if ($search) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($price_range) {
    if ($price_range == '0-500000') {
        $query .= " AND p.price BETWEEN 0 AND 500000";
    } elseif ($price_range == '500000-1000000') {
        $query .= " AND p.price BETWEEN 500000 AND 1000000";
    } elseif ($price_range == '1000000-2000000') {
        $query .= " AND p.price BETWEEN 1000000 AND 2000000";
    } elseif ($price_range == '2000000-') {
        $query .= " AND p.price > 2000000";
    }
}
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'name':
        $query .= " ORDER BY p.name ASC";
        break;
    default:
        $query .= " ORDER BY p.created_at DESC";
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xuất HTML
if (empty($products)) {
    echo '<div class="text-center py-5">
            <h3>Không tìm thấy sản phẩm</h3>
            <p>Vui lòng thử lại với từ khóa hoặc bộ lọc khác.</p>
          </div>';
} else {
    foreach ($products as $product) {
        echo '<div class="col-md-4 mb-4">
                <div class="card product-card">
                    <img src="'.$product['image'].'" class="card-img-top" alt="'.htmlspecialchars($product['name']).'">
                    <div class="card-body">
                        <h5 class="card-title">'.htmlspecialchars($product['name']).'</h5>
                        <p class="card-text text-primary fw-bold">'.number_format($product['price'], 0, ',', '.').' VNĐ</p>
                        <div class="d-flex justify-content-between">
                            <a href="product_detail.php?id='.$product['id'].'" class="btn btn-primary">Xem chi tiết</a>
                            <button class="btn btn-outline-primary add-to-cart" data-id="'.$product['id'].'">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
              </div>';
    }
}