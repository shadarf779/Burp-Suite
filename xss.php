<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome User Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
            text-align: center;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 10px 0;
        }
        h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        .container {
            margin: 20px auto;
            padding: 20px;
            max-width: 600px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
        .highlight {
            font-weight: bold;
            color: #007BFF;
        }
        footer {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #555;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to User Details Lab</h1>
    </header>
    <div class="container">
        <p><strong>Your User-Agent is:</strong></p>
        <p class="highlight">
            <?php
            // Reflecting User-Agent without sanitization
            echo $_SERVER['HTTP_USER_AGENT'];
            ?>
        </p>
    </div>
    <footer>
        <p>Lab for XSS testing. Use responsibly in a controlled environment.</p>
    </footer>
</body>
</html>
