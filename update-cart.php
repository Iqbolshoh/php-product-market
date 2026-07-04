<?php
session_start();
include 'db.php';

$db = new Database();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate CSRF token for security
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $cartId = (int)($_POST['cart_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $userId = $_SESSION['user']['id'] ?? null;

    // Proceed only if all required data is present
    if ($cartId && $userId && in_array($action, ['increase', 'decrease'])) {

        // Fetch the specific cart item to verify ownership and get current quantity
        $cartItem = $db->select('carts', 'id, quantity', 'id = ? AND user_id = ?', [$cartId, $userId]);

        if (!empty($cartItem)) {
            $currentQuantity = (int)$cartItem[0]['quantity'];

            if ($action === 'increase') {
                // Increment the quantity by 1
                $db->update('carts', ['quantity' => $currentQuantity + 1], 'id = ?', [$cartId]);
            } elseif ($action === 'decrease') {
                if ($currentQuantity > 1) {
                    // Decrement the quantity by 1 if it's greater than 1
                    $db->update('carts', ['quantity' => $currentQuantity - 1], 'id = ?', [$cartId]);
                } else {
                    // Remove the item completely if quantity drops below 1
                    $db->delete('carts', 'id = ?', [$cartId]);
                }
            }
        }
    }
}

// Seamlessly redirect back to the cart page
header("Location: cart.php");
exit;
