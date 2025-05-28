<?php
class LoginHandler {
    private $authService;
    private $errorMessage = '';

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function handleRequest() {
        if ($this->authService->isAdminLoggedIn()) {
            $this->redirectToAdmin();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        }
    }

    private function handleLogin() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->errorMessage = 'Vui lòng điền đầy đủ thông tin';
            return;
        }

        if ($this->authService->loginAdmin($email, $password)) {
            $this->redirectToAdmin();
        } else {
            $this->errorMessage = 'Email hoặc mật khẩu không đúng';
        }
    }

    private function redirectToAdmin() {
        header("Location: index.php");
        exit();
    }

    public function getError() {
        return $this->errorMessage;
    }
}
?>