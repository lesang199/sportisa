<?php
class ProfileController {
  private $model;

  public function __construct($model) {
    $this->model = $model;
  }

  public function getProfile($email) {
    return $this->model->getUserByEmail($email);
  }
}
?>