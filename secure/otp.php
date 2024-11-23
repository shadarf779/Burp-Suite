<?php
session_start();

// Initialize rate limiting variables
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0; // Number of attempts
    $_SESSION['last_attempt_time'] = time(); // Timestamp of the last attempt
}

$RATE_LIMIT_WINDOW = 60; // Time window in seconds (1 minute)
$MAX_ATTEMPTS = 5; // Maximum number of attempts allowed

// Reset attempts if the time window has passed
if (time() - $_SESSION['last_attempt_time'] > $RATE_LIMIT_WINDOW) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Increment attempts
    $_SESSION['attempts'] += 1;

    // Check if attempts exceed the allowed limit
    if ($_SESSION['attempts'] > $MAX_ATTEMPTS) {
        $error_message = "Too many failed attempts. Please try again after " . ($RATE_LIMIT_WINDOW - (time() - $_SESSION['last_attempt_time'])) . " seconds.";
    } else {
        $user_otp = $_POST['otp'];

        // Validate the OTP
        if (isset($_SESSION['otp']) && $user_otp == $_SESSION['otp'] && time() <= $_SESSION['otp_expiry']) {
            $_SESSION['otp_verified'] = true;
            header("Location: reset.php");
            exit();
        } else {
            $error_message = "Invalid or expired OTP.";
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
