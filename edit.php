<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You need to log in to edit your profile.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];

    $query = "UPDATE users SET email = ?, fullname = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $email, $fullname, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
} else {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Edit Profile</h2>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success text-center"><?= htmlspecialchars($success_message) ?></div>
                <?php elseif (!empty($error_message)): ?>
                    <div class="alert alert-danger text-center"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>

                <form method="POST" class="p-4 border rounded shadow-sm bg-light">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
