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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if it's an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid CSRF token'
            ]);
            exit;
        }
    }

    if (empty($_POST['name'])) {
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'message' => 'Product name is required'
            ]);
            exit;
        }
    }

    if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 100) {
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'message' => 'Product name must be between 3 and 100 characters'
            ]);
            exit;
        }
    }

    if (empty($_POST['description'])) {
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'message' => 'Product description is required'
            ]);
            exit;
        }
    }

    if (empty($_POST['price'])) {
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'message' => 'Price is required'
            ]);
            exit;
        }
    }

    if (!is_numeric($_POST['price']) || $_POST['price'] < 0) {
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'message' => 'Price must be a positive number'
            ]);
            exit;
        }
    }

    if (empty($_POST['category_id'])) {
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'message' => 'Category is required'
            ]);
            exit;
        }
    }

    // Handle file upload
    $image_url = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/products/';

        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileInfo = pathinfo($_FILES['product_image']['name']);
        $extension = strtolower($fileInfo['extension']);

        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowedTypes)) {
            if ($isAjax) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes)
                ]);
                exit;
            }
        }

        // Validate file size (5MB max)
        $maxSize = 5 * 1024 * 1024;
        if ($_FILES['product_image']['size'] > $maxSize) {
            if ($isAjax) {
                echo json_encode([
                    'success' => false,
                    'message' => 'File size must be less than 5MB'
                ]);
                exit;
            }
        }

        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $destination)) {
            $image_url = 'uploads/products/' . $filename;
        } else {
            if ($isAjax) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to upload image'
                ]);
                exit;
            }
        }
    } elseif (empty($_POST['image_url']) && (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] === UPLOAD_ERR_NO_FILE)) {
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'message' => 'Please upload an image or provide an image URL'
            ]);
            exit;
        }
    } else {
        // Use URL if provided and no file uploaded
        $image_url = $_POST['image_url'] ?? '';
    }

    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];

    // Insert into database
    try {
        $db->insert('products', [
            'name' => $name,
            'price' => $price,
            'category_id' => $category_id,
            'image_url' => $image_url,
            'description' => $description
        ]);

        if ($isAjax) {
            echo json_encode([
                'success' => true,
                'message' => 'Product added successfully!',
                'redirect' => 'products.php'
            ]);
            exit;
        } else {
            header("Location: products.php");
            exit;
        }
    } catch (Exception $e) {
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
            exit;
        }
    }
}

// Get categories for dropdown
$categories = $db->select('categories', '*');
?>

<?php include 'components/header.php'; ?>

<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-white">Add New Product</h2>
        <p class="text-gray-400 mt-2">Fill in the details to add a new product</p>
    </div>

    <!-- Alert Message -->
    <div id="alertMessage" class="hidden mb-6 p-4 rounded-xl text-sm text-center"></div>

    <!-- Form Container -->
    <div class="glass-panel rounded-3xl overflow-hidden shadow-2xl border border-gray-700/50 p-8">
        <form id="productForm" method="POST" enctype="multipart/form-data">
            <!-- Product Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Product Name</label>
                <input type="text"
                    id="name"
                    name="name"
                    required
                    class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                    placeholder="Enter product name">
            </div>

            <!-- Price -->
            <div class="mb-6">
                <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Price ($)</label>
                <input type="number"
                    id="price"
                    name="price"
                    step="0.01"
                    required
                    class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                    placeholder="0.00">
            </div>

            <!-- Category -->
            <div class="mb-6">
                <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                <select id="category_id"
                    name="category_id"
                    required
                    class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600/50 rounded-xl text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    <option value="" class="bg-gray-800">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id']) ?>" class="bg-gray-800">
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Image Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-300 mb-2">Product Image</label>

                <!-- File Upload -->
                <div class="mb-3">
                    <label for="product_image" class="cursor-pointer">
                        <div class="border-2 border-dashed border-gray-600/50 rounded-xl p-6 text-center hover:border-blue-500/50 transition-all">
                            <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-400 text-sm">Click to upload image</p>
                            <p class="text-gray-500 text-xs mt-1">JPG, PNG, GIF, WEBP (Max: 5MB)</p>
                        </div>
                    </label>
                    <input type="file"
                        id="product_image"
                        name="product_image"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        class="hidden">
                </div>

                <!-- OR Divider -->
                <div class="relative my-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-600/50"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-900 text-gray-400">OR</span>
                    </div>
                </div>

                <!-- Image URL Alternative -->
                <div>
                    <label for="image_url" class="block text-sm font-medium text-gray-300 mb-2">Image URL</label>
                    <input type="url"
                        id="image_url"
                        name="image_url"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                        placeholder="https://example.com/image.jpg">
                </div>

                <!-- Image Preview -->
                <div id="image_preview" class="mt-3 hidden">
                    <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-xl border border-gray-600/50">
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                <textarea id="description"
                    name="description"
                    rows="4"
                    class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all resize-none"
                    placeholder="Enter product description"></textarea>
            </div>

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <!-- Submit Button -->
            <div class="flex items-center space-x-4">
                <button type="submit"
                    id="submitButton"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-500/20 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span id="buttonText">Add Product</span>
                    <svg id="loadingSpinner" class="hidden w-5 h-5 ml-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
                <a href="products.php"
                    class="px-6 py-3 bg-gray-700/50 hover:bg-gray-600/50 text-gray-300 font-semibold rounded-xl transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Image Preview and Form Submission Script -->
<script>
    const form = document.getElementById('productForm');
    const alertMessage = document.getElementById('alertMessage');
    const productImage = document.getElementById('product_image');
    const imageUrl = document.getElementById('image_url');
    const imagePreview = document.getElementById('image_preview');
    const previewImg = imagePreview.querySelector('img');
    const submitButton = document.getElementById('submitButton');
    const buttonText = document.getElementById('buttonText');
    const loadingSpinner = document.getElementById('loadingSpinner');

    // Preview uploaded file
    productImage.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('hidden');
                imageUrl.value = ''; // Clear URL input when file is selected
            }
            reader.readAsDataURL(file);
        }
    });

    // Preview URL image
    imageUrl.addEventListener('input', function() {
        if (this.value) {
            previewImg.src = this.value;
            imagePreview.classList.remove('hidden');
            productImage.value = ''; // Clear file input when URL is entered
        } else if (!productImage.files[0]) {
            imagePreview.classList.add('hidden');
        }
    });

    // Error handling for broken images
    previewImg.addEventListener('error', function() {
        if (imageUrl.value) {
            alertMessage.textContent = 'Invalid image URL. Please check the link.';
            alertMessage.className = 'mb-6 p-4 rounded-xl text-sm text-center bg-red-500/20 text-red-400 border border-red-500/20';
            alertMessage.classList.remove('hidden');
        }
        imagePreview.classList.add('hidden');
    });

    // Form Submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        alertMessage.classList.add('hidden');

        // Show loading state
        submitButton.disabled = true;
        buttonText.textContent = 'Adding...';
        loadingSpinner.classList.remove('hidden');

        const formData = new FormData(form);

        try {
            const response = await fetch('', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                alertMessage.textContent = result.message;
                alertMessage.className = 'mb-6 p-4 rounded-xl text-sm text-center bg-green-500/20 text-green-400 border border-green-500/20';
                alertMessage.classList.remove('hidden');

                // Reset form
                form.reset();
                imagePreview.classList.add('hidden');

                // Redirect after success
                setTimeout(() => {
                    window.location.href = result.redirect || 'products.php';
                }, 1000);
            } else {
                alertMessage.textContent = result.message;
                alertMessage.className = 'mb-6 p-4 rounded-xl text-sm text-center bg-red-500/20 text-red-400 border border-red-500/20';
                alertMessage.classList.remove('hidden');
            }
        } catch (error) {
            alertMessage.textContent = 'An error occurred. Please try again.';
            alertMessage.className = 'mb-6 p-4 rounded-xl text-sm text-center bg-red-500/20 text-red-400 border border-red-500/20';
            alertMessage.classList.remove('hidden');
        } finally {
            // Reset loading state
            submitButton.disabled = false;
            buttonText.textContent = 'Add Product';
            loadingSpinner.classList.add('hidden');
        }
    });
</script>

<?php include 'components/footer.php'; ?>