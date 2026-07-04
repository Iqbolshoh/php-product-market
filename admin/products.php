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
?>


<?php include 'components/header.php'; ?>

<h1>Mahsulotlar</h1>

<?php include 'components/footer.php'; ?>