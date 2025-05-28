<?php
class UserController {
    private $userModel;
    private $success;
    private $error;

    public function __construct(UserModel $userModel) {
        $this->userModel = $userModel;
    }

    public function checkAdminAccess() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }
    }

    public function handleToggleStatus($user_id) {
        try {
            if ($this->userModel->toggleStatus($user_id)) {
                $this->success = 'Cập nhật trạng thái thành công';
            }
        } catch (PDOException $e) {
            $this->error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }

    public function handleDeleteUser($user_id) {
        try {
            if ($this->userModel->hasOrders($user_id)) {
                $this->error = 'Không thể xóa người dùng này vì có đơn hàng liên quan';
            } elseif ($this->userModel->delete($user_id)) {
                $this->success = 'Xóa người dùng thành công';
            }
        } catch (PDOException $e) {
            $this->error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }

    public function getUsers() {
        try {
            return $this->userModel->getAll();
        } catch (PDOException $e) {
            $this->error = 'Có lỗi xảy ra: ' . $e->getMessage();
            return [];
        }
    }

    public function getSuccessMessage() {
        return $this->success;
    }

    public function getErrorMessage() {
        return $this->error;
    }
}
?>