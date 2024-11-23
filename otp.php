<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_otp = $_POST['otp'];

    if (isset($_SESSION['otp']) && $user_otp == $_SESSION['otp'] && time() <= $_SESSION['otp_expiry']) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset.php");
        exit();
    } else {
        $error_message = "Invalid or expired OTP.";
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
                    <div class="alert alert-success">    An OTP has been sent to your email. Please enter the OTP below to verify your identity.</div>
 
            
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                <form method="POST" class="p-4 border rounded shadow-sm">
                    <div class="mb-3">
                        <label for="otp" class="form-label">Enter OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" required>
                    </div>
                    <?php echo $_SESSION['otp']; ?>
                    <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
