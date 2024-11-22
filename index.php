<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container text-center mt-5">
        <h1 class="mb-4">Welcome to Our Website</h1>

        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- User is logged in -->
            <p class="lead">Welcome back, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong>!</p>
            <p>You can edit your account details or log out below:</p>
            <div class="d-flex justify-content-center mt-4">
                <a href="edit.php" class="btn btn-warning mx-2">Edit Account</a>
                <a href="logout.php" class="btn btn-danger mx-2">Logout</a>
            </div>
        <?php else: ?>
            <!-- User is not logged in -->
            <p class="lead">Please register or log in to continue.</p>
            <div class="d-flex justify-content-center mt-4">
                <a href="register.php" class="btn btn-primary mx-2">Register</a>
                <a href="login.php" class="btn btn-success mx-2">Login</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
