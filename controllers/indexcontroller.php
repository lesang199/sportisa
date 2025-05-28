<?php
class IndexController {
    private $model;
    private $error;

    public function __construct(IndexModel $model) {
        $this->model = $model;
    }

    public function loadHomepageData() {
        try {
            return [
                'featured_products' => $this->model->getFeaturedProducts(),
                'categories' => $this->model->getActiveCategories(),
                'brands' => $this->model->getActiveBrands(),
                'news' => $this->model->getLatestNews()
            ];
        } catch (PDOException $e) {
            $this->error = 'Có lỗi xảy ra: ' . $e->getMessage();
            return [];
        }
    }

    public function getError() {
        return $this->error;
    }
}
?>