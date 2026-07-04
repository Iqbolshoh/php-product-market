<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login/");
    exit;
}

include 'db.php';
$db = new Database();

// Ensure CSRF token exists in the session to prevent empty token errors
$db->generate_csrf_token();

$currentUser = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        }

        .glass-panel {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>

<body class="min-h-screen text-gray-200 p-6 flex flex-col">

    <!-- Top Navigation Bar -->
    <nav class="glass-panel w-full max-w-5xl mx-auto rounded-2xl px-6 py-4 flex justify-between items-center mb-10 shadow-lg shadow-black/20">
        <div class="text-xl font-bold text-white tracking-wide">
            Product Market<span class="text-blue-500">.uz</span>
        </div>
        <div class="flex items-center space-x-6">
            <a href="profile.php" class="text-gray-300 hover:text-white transition-colors font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profile
            </a>
            <button onclick="handleLogout()" class="px-5 py-2 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/20 rounded-xl transition-all duration-300 font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Log Out
            </button>
        </div>
    </nav>

    <!-- Main Dashboard Content -->
    <main class="flex-grow flex items-center justify-center">
        <div class="glass-panel p-10 rounded-3xl text-center shadow-2xl max-w-lg w-full relative overflow-hidden">
            <!-- Background Glow inside the panel -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/20 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="w-20 h-20 mx-auto bg-gradient-to-tr from-blue-600 to-blue-400 rounded-full flex items-center justify-center text-3xl font-bold text-white shadow-lg shadow-blue-500/30 mb-6">
                    <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
                </div>
                <h1 class="text-3xl font-bold mb-2 text-white">Welcome back!</h1>
                <p class="text-gray-400 mb-6 text-lg"><?= htmlspecialchars($currentUser['name']) ?></p>
                <p class="text-sm text-gray-500 border-t border-gray-700/50 pt-6">
                    Use the navigation bar above to manage your profile settings or log out of the system safely.
                </p>
            </div>
        </div>
    </main>

    <!-- Global CSRF Token for JavaScript actions -->
    <input type="hidden" id="global_csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

    <script>
        // Perform a secure logout via POST request
        async function handleLogout() {
            const csrfToken = document.getElementById('global_csrf_token').value;
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);

            try {
                // Ensure the endpoint matches your actual file name exactly
                const response = await fetch('logout.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = 'login/';
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Logout error:', error);
                alert('An error occurred during logout.');
            }
        }
    </script>
</body>

</html>