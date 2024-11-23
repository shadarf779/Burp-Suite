<?php
session_start();

// Initialize session variables
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0; // Number of failed attempts
    $_SESSION['last_attempt_time'] = 0; // Timestamp of the last failed attempt
    $_SESSION['block_time'] = 60; // Initial block time (1 minute)
}

$current_time = time();

// Check if the user is currently blocked
if ($current_time - $_SESSION['last_attempt_time'] < $_SESSION['block_time']) {
    $remaining_time = $_SESSION['block_time'] - ($current_time - $_SESSION['last_attempt_time']);
    die("Too many failed attempts. You are blocked for another $remaining_time seconds.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_otp = $_POST['otp'];

    // Validate the OTP
    if (isset($_SESSION['otp']) && $user_otp == $_SESSION['otp'] && $current_time <= $_SESSION['otp_expiry']) {
        // Success: Reset attempts and block time
        $_SESSION['attempts'] = 0;
        $_SESSION['block_time'] = 60; // Reset to initial block time
        $_SESSION['otp_verified'] = true;
        header("Location: reset.php");
        exit();
    } else {
        // Failed attempt
        $_SESSION['attempts']++;
        $_SESSION['last_attempt_time'] = $current_time;

        // Increase block time exponentially
        $_SESSION['block_time'] = pow(2, $_SESSION['attempts']) * 60;

        $error_message = "Invalid or expired OTP. You have " . (5 - $_SESSION['attempts']) . " attempts remaining.";

        // If max attempts are exceeded, block the user
        if ($_SESSION['attempts'] >= 5) {
            die("Too many failed attempts. You are now blocked for " . ($_SESSION['block_time'] / 60) . " minutes.");
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Verify OTP</h2>
                <div class="alert alert-info">An OTP has been sent to your email. Please enter the OTP below to verify your identity.</div>
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                <form method="POST" class="p-4 border rounded shadow-sm">
                    <div class="mb-3">
                        <label for="otp" class="form-label">Enter OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
