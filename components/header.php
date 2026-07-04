<?php
$currentUser = $_SESSION['user'];
$cartItemCount = $db->count('carts', 'user_id = ?', [$currentUser['id']]) ?? 0;
?>

<!-- HEADER START -->
<header class="glass-header sticky top-0 z-50 shadow-lg shadow-black/20">
    <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">

        <!-- Logo -->
        <a href="./" class="text-2xl font-extrabold text-white tracking-wide flex items-center">
            ProductMarket<span class="text-blue-500">.uz</span>
        </a>

        <!-- Navigation Links -->
        <nav class="hidden md:flex items-center space-x-8">
            <a href="./" class="text-white font-medium hover:text-blue-400 transition-colors">Home</a>
            <a href="products.php" class="text-gray-300 hover:text-white transition-colors font-medium">Mahsulotlar</a>
        </nav>

        <!-- Action Buttons (Cart, Profile, Logout) -->
        <div class="flex items-center space-x-6">

            <!-- Shopping Cart / Card Icon -->
            <a href="cart.php" class="relative text-gray-300 hover:text-white transition-colors flex items-center group">
                <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
                <!-- Cart Item Badge -->
                <?php if ($cartItemCount > 0): ?>
                    <span class="absolute -top-2 -right-2 flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white shadow-lg">
                        <?= $cartItemCount ?>
                    </span>
                <?php endif; ?>
            </a>

            <div class="h-6 w-px bg-gray-700"></div>

            <!-- Profile Link -->
            <a href="profile.php" class="flex items-center space-x-2 text-gray-300 hover:text-white transition-colors">
                <div class="w-8 h-8 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold border border-blue-500/30">
                    <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
                </div>
                <span class="hidden lg:block font-medium text-sm"><?= htmlspecialchars($currentUser['name']) ?></span>
            </a>

            <!-- Logout Button -->
            <button onclick="handleLogout()" class="px-4 py-2 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/20 rounded-xl transition-all duration-300 font-medium text-sm flex items-center shadow-lg shadow-red-500/10 hover:shadow-red-500/30">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Log Out
            </button>
        </div>

    </div>
</header>
<!-- HEADER END -->