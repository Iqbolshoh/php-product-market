<?php
session_start();
include 'db.php';
$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token for security
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $cartId = $_POST['cart_id'] ?? null;
    $userId = $_SESSION['user']['id'] ?? null;

    if ($cartId && $userId) {
        $db->delete('carts', 'id = ? AND user_id = ?', [$cartId, $userId]);
    }
}

// Redirect back to the cart page seamlessly
header("Location: cart.php");
exit;
