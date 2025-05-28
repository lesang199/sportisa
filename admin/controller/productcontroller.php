<?php
class ProductController {
    private $authService;
    private $productService;
    private $categoryService;
    private $success = '';
    private $error = '';

    public function __construct($conn) {
        $this->authService = new AuthService();
        $this->productService = new ProductService($conn);
        $this->categoryService = new CategoryService($conn);

        $this->authService->checkAdmin();
        $this->handleRequests();
    }

    private function handleRequests() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
            $result = $this->productService->addProduct($_POST, $_FILES);
            $this->success = $result['success'];
            $this->error = $result['error'];
        } elseif (isset($_GET['delete'])) {
            $result = $this->productService->deleteProduct($_GET['delete']);
            $this->success = $result['success'];
            $this->error = $result['error'];
        }
    }

    public function getProducts() {
        return $this->productService->getProducts();
    }

    public function getCategories() {
        return $this->categoryService->getCategories();
    }

    public function getSuccessMessage() {
        return $this->success;
    }

    public function getErrorMessage() {
        return $this->error;
    }

    public function formatPrice($price) {
        return number_format($price, 0, ',', '.');
    }

    public function formatDate($date) {
        return date('d/m/Y H:i', strtotime($date));
    }
}
?>