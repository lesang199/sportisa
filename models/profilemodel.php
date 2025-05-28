<?php
class ProfileModel {
  private $conn;

  public function __construct($conn) {
    $this->conn = $conn;
  }

  public function getUserByEmail($email) {
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
?>