<?php
class DetailController {
    private $model;
    public $order;
    public $order_details;
    public $order_id;
    public $error;

    public function __construct($conn) {
        $this->model = new DetailModel($conn);
        $this->error = null;
        $this->handle();
    }

    private function handle() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        if (!isset($_GET['id'])) {
            header("Location: orders.php");
            exit();
        }

        $this->order_id = intval($_GET['id']);
        $user_id = $_SESSION['user_id'];

        try {
            $this->order = $this->model->getOrder($this->order_id, $user_id);
            if (!$this->order) {
                header("Location: orders.php");
                exit();
            }
            $this->order_details = $this->model->getOrderDetails($this->order_id);
        } catch(PDOException $e) {
            $this->error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }
}
?>