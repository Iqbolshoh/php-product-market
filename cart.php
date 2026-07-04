<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login/");
    exit;
}

include 'db.php';

// Fetch the current user session data
$currentUser = $_SESSION['user'];

$db = new Database();

if (isset($_GET['add'])) {
    $add = isset($_GET['add']) ? (int)$_GET['add'] : null;

    $check = $db->select('carts', '*', 'user_id = ? AND product_id = ?', [$_SESSION['user']['id'], $add])[0] ?? null;

    if ($check) {
        $db->update('carts', ['quantity' => $check['quantity'] + 1], 'id = ?', [$check['id']]);
    } else {
        $db->insert('carts', [
            'user_id' => $_SESSION['user']['id'],
            'product_id' => $add,
            'quantity' => 1
        ]);
    }

    header("Location: products.php");
}

$cartItems = $db->execute('
    SELECT c.id AS cart_id, p.id AS product_id, p.name, p.price, p.image_url, c.quantity
    FROM carts c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
', [$currentUser['id']])->fetchAll();

// Calculate total prices
$subtotal = 0;
foreach ($cartItems as $cart) {
    $subtotal += ($cart['price'] * $cart['quantity']);
}

// Additional costs (example logic)
$shippingCost = ($subtotal > 0 && $subtotal < 100) ? 15.00 : 0.00; // Free shipping over $100
$taxRate = 0.05; // 5% tax
$estimatedTax = $subtotal * $taxRate;
$orderTotal = $subtotal + $shippingCost + $estimatedTax;

?>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - ProductMarket.uz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <style>
        body {
            background-color: #0f172a;
            background-image: radial-gradient(circle at 50% 0%, #3b82f6 0%, #0f172a 60%);
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .glass-panel {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>

<body class="text-gray-200 antialiased">

    <!-- Include Header Navigation -->
    <?php include 'components/header.php'; ?>

    <main class="flex-grow max-w-7xl mx-auto w-full px-6 py-12">

        <h1 class="text-3xl font-extrabold text-white mb-8">Shopping Cart</h1>

        <?php if (empty($cartItems)): ?>

            <!-- Empty Cart State -->
            <div class="glass-panel rounded-3xl p-16 text-center shadow-2xl flex flex-col items-center justify-center">
                <div
                    class="w-24 h-24 bg-gray-800/50 rounded-full flex items-center justify-center mb-6 border border-gray-700">
                    <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Your cart is empty</h2>
                <p class="text-gray-400 mb-8">Looks like you haven't added any products to your cart yet.</p>
                <a href="products.php"
                    class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-500/30">
                    Continue Shopping
                </a>
            </div>

        <?php else: ?>

            <!-- Filled Cart State (Bento Grid Layout) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left Column: Cart Items List -->
                <div class="lg:col-span-2 space-y-6">
                    <?php foreach ($cartItems as $cart): ?>
                        <div
                            class="glass-panel rounded-2xl p-6 flex flex-col sm:flex-row items-center gap-6 hover:border-blue-500/30 transition-colors">

                            <!-- Product Image Placeholder or Actual Image -->
                            <div
                                class="w-24 h-24 bg-gray-800/80 rounded-xl flex-shrink-0 flex items-center justify-center overflow-hidden border border-gray-700/50">
                                <?php if (!empty($cart['image_url'])): ?>
                                    <img src="<?= htmlspecialchars($cart['image_url']) ?>" alt="Product"
                                        class="object-cover w-full h-full">
                                <?php else: ?>
                                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                <?php endif; ?>
                            </div>

                            <!-- Product Details -->
                            <div class="flex-grow text-center sm:text-left">
                                <h3 class="text-lg font-bold text-white mb-1"><?= htmlspecialchars($cart['name']) ?></h3>
                                <p class="text-blue-400 font-bold mb-3">$<?= number_format($cart['price'], 2) ?></p>

                                <!-- Action Form (Remove Item via POST) -->
                                <form action="delete-cart.php" method="POST" class="inline-block">
                                    <!-- Security Token -->
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <!-- Target Item ID -->
                                    <input type="hidden" name="cart_id" value="<?= htmlspecialchars($cart['cart_id']) ?>">

                                    <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition-colors flex items-center justify-center sm:justify-start bg-transparent border-none cursor-pointer">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Remove
                                    </button>
                                </form>
                            </div>

                            <!-- Quantity Controller -->
                            <div class="flex items-center space-x-3 bg-gray-900/50 rounded-xl p-1 border border-gray-700/50">
                                <div class="flex-grow text-center sm:text-left">
                                    <!-- Quantity Controller with Increase/Decrease Forms -->
                                    <div class="flex items-center space-x-2 bg-gray-900/50 rounded-xl p-1 border border-gray-700/50 w-max mt-4 sm:mt-0">

                                        <!-- Decrease Form (-) -->
                                        <form action="update-cart.php" method="POST" class="m-0 p-0 flex">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                            <input type="hidden" name="cart_id" value="<?= htmlspecialchars($cart['cart_id']) ?>">
                                            <input type="hidden" name="action" value="decrease">
                                            <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-800 hover:text-white transition-colors" <?= $cart['quantity'] <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                        </form>

                                        <!-- Display Current Quantity -->
                                        <span class="text-white font-bold w-8 text-center select-none">
                                            <?= htmlspecialchars($cart['quantity']) ?>
                                        </span>

                                        <!-- Increase Form (+) -->
                                        <form action="update-cart.php" method="POST" class="m-0 p-0 flex">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                            <input type="hidden" name="cart_id" value="<?= htmlspecialchars($cart['cart_id']) ?>">
                                            <input type="hidden" name="action" value="increase">
                                            <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                        </form>

                                    </div>

                                </div>
                            </div>

                            <!-- Item Total Amount -->
                            <div class="w-24 text-right hidden sm:block">
                                <span class="text-lg font-extrabold text-white">
                                    $<?= number_format($cart['price'] * $cart['quantity'], 2) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="lg:col-span-1">
                    <div class="glass-panel rounded-3xl p-8 sticky top-28 shadow-xl">
                        <h2 class="text-xl font-bold text-white mb-6 border-b border-gray-700/50 pb-4">Order Summary</h2>

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center text-gray-400">
                                <span>Subtotal</span>
                                <span class="text-white font-medium">$<?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="flex justify-between items-center text-gray-400">
                                <span>Shipping estimate</span>
                                <span class="text-white font-medium">
                                    <?= $shippingCost > 0 ? '$' . number_format($shippingCost, 2) : 'Free' ?>
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-gray-400">
                                <span>Tax estimate (5%)</span>
                                <span class="text-white font-medium">$<?= number_format($estimatedTax, 2) ?></span>
                            </div>
                        </div>

                        <div class="border-t border-gray-700/50 pt-6 mb-8">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-white">Order Total</span>
                                <span
                                    class="text-3xl font-extrabold text-blue-400">$<?= number_format($orderTotal, 2) ?></span>
                            </div>
                        </div>

                        <button
                            class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold text-lg rounded-xl transition-all duration-300 shadow-lg shadow-blue-500/30">
                            Checkout
                        </button>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </main>

    <!-- Include Footer Navigation -->
    <?php include 'components/footer.php'; ?>

</body>

</html>