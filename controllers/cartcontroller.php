<?php
class CartController {
    private $cart;

    public function __construct() {
        $this->initializeCart();
    }

    private function initializeCart() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $this->cart = &$_SESSION['cart'];
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['update_cart'])) {
                $this->updateCartItems();
            } elseif (isset($_POST['remove_item'])) {
                $this->removeCartItem();
            }
        }
    }

    private function updateCartItems() {
        if (isset($_POST['quantity'])) {
            foreach ($_POST['quantity'] as $id => $quantity) {
                if ($quantity <= 0) {
                    $this->removeItem($id);
                } else {
                    $this->updateItemQuantity($id, $quantity);
                }
            }
        }
    }

    private function removeCartItem() {
        $id = $_POST['remove_item'];
        $this->removeItem($id);
    }

    private function updateItemQuantity($id, $quantity) {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['quantity'] = $quantity;
        }
    }

    private function removeItem($id) {
        unset($this->cart[$id]);
    }

    public function calculateTotal() {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
}
?>