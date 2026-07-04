<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login/");
    exit;
}

include 'db.php';
$db = new Database();

$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;

$categories = $db->select('categories', '*');
if ($categoryId) {
    $products = $db->select('products', '*', 'category_id = ?', [$categoryId]);
} else {
    $products = $db->select('products', '*');
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProductMarket.uz - Home</title>
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

        .glass-header {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>

<body class="text-gray-200 antialiased">

    <?php include 'components/header.php'; ?>

    <!-- MAIN CONTENT START -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-6 py-12">

        <!-- Categories Filter Section -->
        <div class="mb-10">
            <h2 class="text-xl font-bold text-white mb-4">Categories</h2>
            <div class="flex flex-wrap gap-3">

                <!-- 'All Categories' Option -->
                <a href="products.php" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-lg shadow-blue-500/30 transition-all border border-blue-500">
                    All
                </a>

                <!-- Dynamic Categories Loop -->
                <?php foreach ($categories as $category): ?>
                    <a href="products.php?category=<?= $category['id'] ?>"
                        class="px-6 py-2.5 glass-panel rounded-xl text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 hover:border-blue-500/50 transition-all">
                        <?= htmlspecialchars($category['name']) ?>
                    </a>
                <?php endforeach; ?>

            </div>
        </div>

        <!-- Products Grid Layout -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

            <?php if (empty($products)): ?>
                <p class="text-gray-400 col-span-full text-center">No products found in this category.</p>
            <?php else: ?>
                <!-- Dynamic Products Loop -->
                <?php foreach ($products as $product): ?>

                    <!-- Product Card -->
                    <div class="glass-panel rounded-2xl p-5 hover:border-blue-500/50 hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-300 group flex flex-col">

                        <!-- Image Container -->
                        <div class="w-full h-48 bg-gray-800/50 rounded-xl mb-4 flex items-center justify-center p-4 group-hover:bg-gray-800 transition-colors overflow-hidden relative">
                            <img
                                src="<?= htmlspecialchars($product['image_url']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-500">
                        </div>

                        <!-- Product Info Content -->
                        <div class="flex-grow">
                            <h3 class="text-lg font-bold text-white mb-2 line-clamp-2">
                                <?= htmlspecialchars($product['name']) ?>
                            </h3>

                            <!-- Price Display Logic -->
                            <div class="flex items-center gap-3 mb-5 flex-wrap">
                                <?php if (!empty($product['discount_price'])): ?>
                                    <!-- Show Discounted Price -->
                                    <span class="text-2xl font-extrabold text-blue-400">
                                        $<?= number_format($product['discount_price'], 2) ?>
                                    </span>
                                    <!-- Show Old Price Strikethrough -->
                                    <del class="text-sm font-medium text-gray-500">
                                        $<?= number_format($product['price'], 2) ?>
                                    </del>
                                <?php else: ?>
                                    <!-- Show Regular Price Only -->
                                    <span class="text-2xl font-extrabold text-white">
                                        $<?= number_format($product['price'], 2) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Add to Cart Action -->
                        <a href="cart.php?add=<?= $product['id'] ?>" class="text-sm font-medium">
                            <button class="w-full py-2.5 mt-auto bg-white/5 hover:bg-blue-600 border border-white/10 hover:border-blue-500 text-white font-medium rounded-xl transition-all duration-300 shadow-sm hover:shadow-blue-500/30 flex items-center justify-center gap-2">
                                <!-- Shopping Cart Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Add to Cart
                            </button>
                        </a>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </main>
    <!-- MAIN CONTENT END -->

    <?php include 'components/footer.php'; ?>

</body>

</html>