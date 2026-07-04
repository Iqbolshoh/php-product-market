<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login/");
    exit;
}

include 'db.php';
$db = new Database();
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

        <!-- Welcome Hero Section -->
        <div class="glass-panel rounded-3xl p-10 mb-10 relative overflow-hidden flex flex-col md:flex-row items-center justify-between">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 max-w-2xl">
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight">
                    Discover Top Products on <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-600">ProductMarket.uz</span>
                </h1>
                <p class="text-gray-400 text-lg mb-8">
                    Browse our extensive catalog of products and add them directly to your card. Fast, secure, and reliable.
                </p>
                <div class="flex space-x-4">
                    <a href="products.php" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-500/30">
                        Mahsulotlarni Ko'rish
                    </a>
                </div>
            </div>
        </div>

    </main>
    <!-- MAIN CONTENT END -->

    <?php include 'components/footer.php'; ?>

</body>

</html>