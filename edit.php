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
        echo "Profile updated successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
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

<form method="POST">
    Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"><br>
    Full Name: <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>"><br>
    <button type="submit">Update Profile</button>
</form>
