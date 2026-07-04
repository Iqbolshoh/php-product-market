<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ProductMarket.uz</title>
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
            /* Layout adjustments for Sidebar */
            display: flex;
            height: 100vh;
            overflow: hidden;
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

        /* Custom scrollbar for the main content area */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.5);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.8);
        }
    </style>
</head>

<body class="text-gray-200 antialiased">

    <!-- SIDEBAR (ASIDE) START -->
    <aside class="glass-panel w-72 flex flex-col h-full border-r border-gray-700/50 flex-shrink-0 z-20 hidden md:flex">

        <!-- Sidebar Header / Logo -->
        <div class="h-20 flex items-center px-8 border-b border-gray-700/50">
            <a href="index.php" class="text-2xl font-extrabold text-white tracking-wide flex items-center">
                Admin<span class="text-blue-500">Panel</span>
            </a>
        </div>

        <?php
        // Get the current file name (e.g., 'index.php', 'categories.php')
        $currentPage = basename($_SERVER['PHP_SELF']);

        // Define the menu items and their SVG paths
        $menuItems = [
            'index.php' => [
                'label' => 'Dashboard',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>'
            ],
            'categories.php' => [
                'label' => 'Categories',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>'
            ],
            'products.php' => [
                'label' => 'Products',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>'
            ]
        ];
        ?>

        <!-- Sidebar Navigation -->
        <nav class="flex-1 px-4 py-8 space-y-3 overflow-y-auto">
            <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Menu</p>

            <?php foreach ($menuItems as $url => $item): ?>
                <?php
                // Check if this menu item is the current page
                $isActive = ($currentPage === $url);

                // Assign CSS classes based on active state
                $linkClasses = $isActive
                    ? 'bg-blue-600/20 text-blue-400 border-blue-500/20'
                    : 'text-gray-400 border-transparent hover:bg-white/5 hover:text-white';

                $iconClasses = $isActive
                    ? 'text-blue-400'
                    : 'text-gray-500 group-hover:text-blue-400 transition-colors';
                ?>

                <a href="<?= htmlspecialchars($url) ?>" class="flex items-center px-4 py-3 rounded-xl border transition-all font-medium group <?= $linkClasses ?>">
                    <svg class="w-5 h-5 mr-3 <?= $iconClasses ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <?= $item['icon'] ?>
                    </svg>
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Sidebar Footer (Profile / Settings) -->
        <div class="p-4 border-t border-gray-700/50">
            <div class="flex items-center px-4 py-3 bg-gray-800/50 rounded-xl border border-gray-700/50">
                <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold mr-3 shadow-lg shadow-blue-500/30">
                    <?= strtoupper(substr($_SESSION['user']['name'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-white truncate"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?></p>
                    <p class="text-xs text-gray-400 truncate">Administrator</p>
                </div>
            </div>
        </div>
    </aside>
    <!-- SIDEBAR END -->

    <!-- MAIN CONTENT WRAPPER START -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative">

        <!-- Background Glow Element for aesthetic appeal -->
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Top Navigation Area -->
        <header class="glass-header h-20 flex items-center justify-between px-8 shrink-0 z-10">
            <!-- Mobile Menu Toggle Button (Visible only on small screens) -->
            <button class="md:hidden text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Dashboard Title -->
            <h1 class="text-xl font-bold text-white hidden md:block">Overview</h1>

            <!-- Quick Actions -->
            <div class="flex items-center space-x-4">
                <a href="../" target="_blank" class="text-sm font-medium text-gray-400 hover:text-blue-400 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    View Store
                </a>
                <div class="w-px h-6 bg-gray-700"></div>
                <form action="../logout.php" method="POST" class="m-0 p-0">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <button type="submit" class="text-sm font-medium text-red-400 hover:text-red-300 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Scrollable Main Content Area -->
        <main class="flex-1 overflow-y-auto p-6 md:p-8 z-10">