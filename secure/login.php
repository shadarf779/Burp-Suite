<?php
require '../.db.php';
session_start();

// Configuration
$RATE_LIMIT_WINDOW = 60; // Time window in seconds (e.g., 1 minute)
$MAX_GLOBAL_REQUESTS = 5; // Maximum allowed requests globally
$REQUEST_LOG_FILE = 'global_requests.log'; // File to log requests

// Helper function to log a new request
function log_request($log_file) {
    $current_time = time();
    $requests = file_exists($log_file) ? file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

    // Add the new request timestamp
    $requests[] = $current_time;

    // Prune expired requests
    $requests = array_filter($requests, function($timestamp) use ($current_time) {
        return $timestamp > ($current_time - $GLOBALS['RATE_LIMIT_WINDOW']);
    });

    // Write back to the log file
    file_put_contents($log_file, implode("\n", $requests) . "\n");

    return count($requests);
}

// Check the current number of requests
$current_requests = log_request($REQUEST_LOG_FILE);

if ($current_requests > $MAX_GLOBAL_REQUESTS) {
    die("Too many login requests globally. Please try again later.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php"); // Redirect to index page
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
    mysqli_stmt_close($stmt);
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
                   <!-- forget password ? -->
                   <div class="text-center mt-3">
                        <a href="forget-password.php">Forget Password?</a>
                    </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
