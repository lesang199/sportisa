<?php
class ProductsController {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function getCategories() {
        return $this->model->getCategories();
    }

    public function getProducts($request) {
        $filters = [
            'category' => $request['category'] ?? '',
            'search' => $request['search'] ?? '',
            'sort' => $request['sort'] ?? 'newest',
            'price_range' => $request['price'] ?? ''
        ];
        return $this->model->getProducts($filters);
    }
}
?>