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

$categories = $db->select('categories', '*');
?>

<?php include 'components/header.php'; ?>

<div class="max-w-5xl mx-auto">

    <!-- Page Header & Action -->
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-extrabold text-white">Categories</h2>
        <a href="add_category.php" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-500/20 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Category
        </a>
    </div>

    <!-- Table Container (Glassmorphism) -->
    <div class="glass-panel rounded-3xl overflow-hidden shadow-2xl border border-gray-700/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-800/50 text-gray-400 uppercase text-xs tracking-widest">
                        <th class="px-8 py-5">ID</th>
                        <th class="px-8 py-5">Category Name</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    <?php foreach ($categories as $category): ?>
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-8 py-5 font-mono text-gray-400"><?= htmlspecialchars($category['id']) ?></td>
                            <td class="px-8 py-5 font-semibold text-white"><?= htmlspecialchars($category['name']) ?></td>
                            <td class="px-8 py-5 text-right space-x-3">
                                <!-- Edit Button -->
                                <a href="edit_category.php?id=<?= urlencode($category['id']) ?>"
                                    class="inline-flex items-center text-blue-400 hover:text-blue-300 transition-colors font-medium text-sm">
                                    Edit
                                </a>

                                <!-- Delete Button (POST request is recommended here as well for security) -->
                                <a href="delete_category.php?id=<?= urlencode($category['id']) ?>"
                                    onclick="return confirm('Are you sure you want to delete this category?');"
                                    class="inline-flex items-center text-red-400 hover:text-red-300 transition-colors font-medium text-sm">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($categories)): ?>
            <div class="text-center py-12 text-gray-500">
                No categories found.
            </div>
        <?php endif; ?>
    </div>

</div>

<?php include 'components/footer.php'; ?>