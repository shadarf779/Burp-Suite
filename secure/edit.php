<?php
require '../.db.php';
session_start();

// Initialize rate-limiting variables
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0; // Number of login attempts
    $_SESSION['last_attempt_time'] = time(); // Timestamp of the last login attempt
}

$RATE_LIMIT_WINDOW = 60; // Time window in seconds (e.g., 1 minute)
$MAX_ATTEMPTS = 5; // Maximum allowed attempts within the time window

// Reset attempts if the time window has passed
if (time() - $_SESSION['last_attempt_time'] > $RATE_LIMIT_WINDOW) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Increment login attempts
    $_SESSION['login_attempts'] += 1;

    // Check if the number of attempts exceeds the limit
    if ($_SESSION['login_attempts'] > $MAX_ATTEMPTS) {
        $remaining_time = $RATE_LIMIT_WINDOW - (time() - $_SESSION['last_attempt_time']);
        $error_message = "Too many login attempts. Please try again after $remaining_time seconds.";
    } else {
        // Proceed with login validation
        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            // Reset login attempts upon successful login
            $_SESSION['login_attempts'] = 0;

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php"); // Redirect to index page
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Login</h2>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($error_message) ?></div>
                    <?php endif; ?>
                    <form method="POST" class="p-4 border rounded shadow-sm bg-light">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="forget-password.php">Forget Password?</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success text-center">
                        You are already logged in. <a href="index.php" class="alert-link">Go to homepage</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
