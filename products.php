
<?php
session_start();
include 'config/database.php';

// Lấy danh mục từ URL
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$price_range = isset($_GET['price']) ? $_GET['price'] : '';

// Lấy danh sách danh mục
$query_categories = "SELECT * FROM categories";
$stmt_categories = $conn->prepare($query_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Xây dựng câu truy vấn
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

// Sắp xếp
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

// Thực hiện truy vấn
$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .input-group {
            width: 300px;
        }
        .input-group input {
            border-color: none;
            user-select: none;
           
        }
        .input-group input:focus {
            box-shadow: none;
            border-color:none ;
        }
        .input-group button {
            background-color: #007bff;
            border: none;
        }
        .input-group button:hover {
            transform: translate(0);
            background-color: #0056b3;
        }
   
     .search-sort{
        width: 65%;
     }
     .form-search{
        width: 100%;
     }
     .form-select{
        width: 50%;
     }
     .form-control:focus{
      border-color: none;
      box-shadow: none; 
     }
     .sidebar-list li a{
        text-decoration: none;
        color: #000000b8;
         font-size: 17px;
        font-weight: 600;
     }
     /* ul li a:hover{
        text-decoration: none;
        color: #007bff;
       
     } */
     .form-check-label{
        font-size: 17px;
        font-weight: 600;
        color: #000000b8;
     }
     .form-check{
        display: flex;
        align-items: center;
        gap: 10px;
     }
   

    </style>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
 <div class="container py-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Danh mục</h5>
                    </div>
                    <div class="card-body">
                       <ul class="list-unstyled sidebar-list" id="category-list">
    <li class="mb-2">
        <a href="#" data-category="" class="<?php echo !$category ? 'text-primary fw-bold' : ''; ?>">Tất cả sản phẩm</a>
    </li>
    <?php foreach ($categories as $cat): ?>
        <li class="mb-2">
            <a href="#" data-category="<?php echo $cat['categories_name']; ?>" class="<?php echo $category == $cat['categories_name'] ? 'text-primary fw-bold' : '';  ?>">
                <?php echo $cat['name']; ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Lọc theo giá</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="products.php">
                            <input type="hidden" name="category" value="<?php echo $category; ?>">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="price" id="price1" value="0-500000" <?php echo $price_range == '0-500000' ? 'checked' : ''; ?> >
                                <label class="form-check-label" for="price1">Dưới 500.000 VNĐ</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="price" id="price2" value="500000-1000000" <?php echo $price_range == '500000-1000000' ? 'checked' : ''; ?> >
                                <label class="form-check-label" for="price2">500.000 - 1.000.000 VNĐ</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="price" id="price3" value="1000000-2000000" <?php echo $price_range == '1000000-2000000' ? 'checked' : ''; ?> >
                                <label class="form-check-label" for="price3">1.000.000 - 2.000.000 VNĐ</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" id="price4" value="2000000-" <?php echo $price_range == '2000000-' ? 'checked' : ''; ?> >
                                <label class="form-check-label" for="price4">Trên 2.000.000 VNĐ</label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="title-products">Tất cả sản phẩm</h2>
                    <div class="d-flex">
                        <form class="me-3" method="GET" id="search-form">
                            <input type="hidden" name="category" value="<?php echo $category; ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." value="<?php echo $search; ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                        <select class="form-select"  id="sort-select">
                            <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                           <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                          <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                          <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Tên A-Z</option>
                        </select>
           
                    </div>
                </div>

                <div class="row" id="product-container">
                    <?php include 'fetch_products.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Xử lý thêm vào giỏ hàng
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã thêm vào giỏ hàng');
                        // Cập nhật số lượng trong giỏ hàng
                        const cartCount = document.querySelector('.badge');
                        cartCount.textContent = data.cart_count;
                    } else {
                        alert('Có lỗi xảy ra');
                    }
                });
            });
        });
        function fetchProducts(params = {}) {
    // Lấy các giá trị lọc hiện tại nếu chưa truyền vào
    if (!params.hasOwnProperty('category')) {
        params.category = document.querySelector('#category-list a.text-primary, #category-list a.fw-bold')?.getAttribute('data-category') || '';
    }
    if (!params.hasOwnProperty('search')) {
        params.search = document.querySelector('input[name="search"]').value;
    }
    if (!params.hasOwnProperty('sort')) {
        params.sort = document.getElementById('sort-select').value;
    }
    if (!params.hasOwnProperty('price')) {
        params.price = document.querySelector('input[name="price"]:checked')?.value || '';
    }

    // Gửi AJAX
    const url = 'fetch_products.php?' + new URLSearchParams(params).toString();
    fetch(url)
        .then(res => res.text())
        .then(html => {
            document.getElementById('product-container').innerHTML = html;
        });
}

// Chọn danh mục
function getCurrentFilters() {
    return {
        category: document.querySelector('#category-list a.text-primary, #category-list a.fw-bold')?.getAttribute('data-category') || '',
        search: document.querySelector('input[name="search"]').value,
        sort: document.getElementById('sort-select').value,
        price: document.querySelector('input[name="price"]:checked')?.value || ''
    };
}

// Lọc giá
document.querySelectorAll('input[name="price"]').forEach(radio => {
    radio.addEventListener('change', function(e) {
        e.preventDefault();
        const filters = getCurrentFilters();
        filters.price = this.value;
        fetchProducts(filters);
    });
});

// Tìm kiếm
document.getElementById('search-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const filters = getCurrentFilters();
    filters.search = this.search.value;
    fetchProducts(filters);
});

// Sắp xếp
document.getElementById('sort-select').addEventListener('change', function(e) {
    e.preventDefault();
    const filters = getCurrentFilters();
    filters.sort = this.value;
    fetchProducts(filters);
});

// Chọn danh mục
document.getElementById('category-list').addEventListener('click', function(e) {
    if (e.target.tagName === 'A') {
        e.preventDefault();
        document.querySelectorAll('#category-list a').forEach(a => a.classList.remove('text-primary', 'fw-bold'));
        e.target.classList.add('text-primary', 'fw-bold');
          // Reset lọc giá
        document.querySelectorAll('input[name="price"]').forEach(radio => radio.checked = false);

        const filters = getCurrentFilters();
        filters.category = e.target.getAttribute('data-category');
        fetchProducts(filters);
        let titleProducts = document.querySelector('.title-products');
        titleProducts.innerText = e.target.innerText;
    }
});
      
    </script>

</body>
</html> 