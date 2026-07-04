<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login/");
    exit;
}

if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../");
    exit;
}

include '../db.php';
$db = new Database();

$products = $db->select('products', '*');
?>


<?php include 'components/header.php'; ?>

<!-- Main Content -->
<div class="max-w-6xl mx-auto">

    <!-- Page Header & Action -->
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-extrabold text-white">Products Management</h2>
        <a href="add_product.php" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-500/20 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Product
        </a>
    </div>

    <!-- Table Container -->
    <div class="glass-panel rounded-3xl overflow-hidden shadow-2xl border border-gray-700/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-800/50 text-gray-400 uppercase text-xs tracking-widest">
                        <th class="px-8 py-5">ID</th>
                        <th class="px-8 py-5">Image</th>
                        <th class="px-8 py-5">Product Name</th>
                        <th class="px-8 py-5">Price</th>
                        <th class="px-8 py-5">Category</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    <?php foreach ($products as $product): ?>
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-8 py-5 font-mono text-gray-400"><?= htmlspecialchars($product['id']) ?></td>
                            <td class="px-8 py-5">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="<?= htmlspecialchars(!filter_var($product['image_url'], FILTER_VALIDATE_URL) ? '../' . $product['image_url'] : $product['image_url']) ?>"
                                        class="w-14 h-14 object-cover rounded-xl border-2 border-gray-600/50 hover:border-blue-500/50 transition-all shadow-lg">
                                <?php else: ?>
                                    <div class="w-14 h-14 bg-gray-700/50 rounded-xl flex items-center justify-center border-2 border-gray-600/50">
                                        <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-8 py-5 font-semibold text-white"><?= htmlspecialchars($product['name']) ?></td>
                            <td class="px-8 py-5 text-blue-400 font-bold">$<?= number_format($product['price'], 2) ?></td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-gray-700/50 rounded-lg text-xs text-gray-300">
                                    ID: <?= htmlspecialchars($product['category_id']) ?>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right space-x-4">
                                <a href="edit_product.php?id=<?= urlencode($product['id']) ?>"
                                    class="text-blue-400 hover:text-blue-300 transition-colors font-medium text-sm">
                                    Edit
                                </a>
                                <a href="delete_product.php?id=<?= urlencode($product['id']) ?>"
                                    onclick="return confirm('Are you sure you want to delete this product?');"
                                    class="text-red-400 hover:text-red-300 transition-colors font-medium text-sm">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-12 text-gray-500">
                No products added yet.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'components/footer.php'; ?>