<?php
class AuthService {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function loginAdmin($email, $password) {
        $admin = $this->getAdminByEmail($email);

        if ($admin && password_verify($password, $admin['password'])) {
            $this->setAdminSession($admin);
            return true;
        }

        return false;
    }

    private function getAdminByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function setAdminSession($admin) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['role'] = $admin['role'];
    }

    public function isAdminLoggedIn() {
        return isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';
    }
}
?>