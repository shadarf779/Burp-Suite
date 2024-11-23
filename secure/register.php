<?php
require '../.db.php';
session_start();

// Generate a random session-based token for extra validation
if (!isset($_SESSION['captcha_token'])) {
    $_SESSION['captcha_token'] = bin2hex(random_bytes(16)); // Unique token
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if CAPTCHA is solved
    if (empty($_POST['captcha_response']) || $_POST['captcha_response'] !== 'solved') {
        echo "<div class='alert alert-danger text-center'>CAPTCHA validation failed. Please solve the puzzle.</div>";
        exit();
    }

    // Process the registration form
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];

    $query = "INSERT INTO users (username, password, email, fullname) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $email, $fullname);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['user_id'] = mysqli_insert_id($conn); // Store the new user's ID
        $_SESSION['username'] = $username;
        header("Location: index.php"); // Redirect to index page
        exit();
    } else {
        echo "<div class='alert alert-danger text-center'>Error: " . mysqli_error($conn) . "</div>";
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #puzzle-container {
            width: 300px;
            height: 300px;
            border: 2px solid #ced4da;
            margin: 20px auto;
            position: relative;
            background-color: #f8f9fa;
        }
        #start, #goal, #item {
            width: 60px;
            height: 60px;
            position: absolute;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            font-weight: bold;
            border-radius: 50%;
            user-select: none;
        }
        #start {
            background-color: #d1ecf1;
            left: 10px;
            top: 10px;
        }
        #goal {
            background-color: #d4edda;
            right: 10px;
            bottom: 10px;
        }
        #item {
            background-color: #fff3cd;
            border: 2px solid #ffeeba;
            cursor: grab;
            left: 10px;
            top: 10px;
        }
        .solved {
            border-color: #28a745;
            background-color: #c3e6cb;
        }
        #verified-message {
            display: none;
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Register</h2>
                <form method="POST" class="p-4 border rounded shadow-sm">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullname" name="fullname">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div id="puzzle-container">
                        <div id="start">Start</div>
                        <div id="goal">Goal</div>
                        <div id="item" draggable="true">Item</div>
                    </div>
                    <div id="verified-message">CAPTCHA Verified!</div>
                    <input type="hidden" name="captcha_response" id="captcha-response" value="unsolved">
                    <input type="hidden" name="captcha_token" value="<?= htmlspecialchars($_SESSION['captcha_token']) ?>">
                    <button type="submit" class="btn btn-primary w-100" id="submit-btn" disabled>Register</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const item = document.getElementById("item");
        const goal = document.getElementById("goal");
        const puzzleContainer = document.getElementById("puzzle-container");
        const verifiedMessage = document.getElementById("verified-message");
        const submitBtn = document.getElementById("submit-btn");
        const captchaResponse = document.getElementById("captcha-response");

        let isDragging = false;

        // Drag the item
        item.addEventListener("dragstart", (e) => {
            isDragging = true;
            e.dataTransfer.setData("text", "item");
        });

        // Prevent default dragover behavior for the goal
        goal.addEventListener("dragover", (e) => {
            e.preventDefault();
        });

        // Drop the item into the goal
        goal.addEventListener("drop", (e) => {
            e.preventDefault();
            if (isDragging) {
                item.style.left = `${goal.offsetLeft}px`;
                item.style.top = `${goal.offsetTop}px`;
                item.classList.add("solved");

                captchaResponse.value = "solved"; // Mark CAPTCHA as solved
                submitBtn.disabled = false; // Enable the submit button

                // Remove puzzle and show verified message
                puzzleContainer.style.display = "none";
                verifiedMessage.style.display = "block";
            }
        });
    </script>
</body>
</html>
