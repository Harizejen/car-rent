<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-container h2 {
            margin-bottom: 25px;
            font-size: 28px;
            color: #333;
            font-weight: 600;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 12px 12px 40px; /* Add left padding for the icon */
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .input-group input:focus {
            border-color: #667eea;
            outline: none;
        }

        .input-group i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
        }

        .login-container input[type="submit"],
        .login-container .back-button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
            margin-bottom: 15px;
            transition: background-color 0.3s ease;
        }

        .login-container input[type="submit"] {
            background-color: #667eea;
            color: #fff;
        }

        .login-container input[type="submit"]:hover {
            background-color: #5a6fd1;
        }

        .login-container .back-button {
            background-color: #6c757d;
            color: #fff;
        }

        .login-container .back-button:hover {
            background-color: #5a6268;
        }

        .error-message {
            color: #ff4757;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .mt-3 {
            margin-top: 15px;
        }

        .login-container a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .login-container a:hover {
            text-decoration: underline;
        }
    </style>
    <!-- Add FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php
        // Display error message if login fails
        if (isset($_GET['error'])) {
            echo '<div class="error-message">Invalid username or password.</div>';
        }
        ?>
        <form action="loginAdmin.php" method="post" onsubmit="return validateLogin()">
            <!-- Username Field with Icon -->
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" id="username" placeholder="Username" required>
            </div>
            <!-- Password Field with Icon -->
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>
            <input type="submit" value="Login">
        </form>
        <!-- Back Button -->
        <a href="index.php" class="back-button">Back</a>
        <p class="mt-3">Don't have an account? <a href="create_admin.php">Create one</a>.</p>
    </div>

    <script>
        // Function to validate login credentials
        function validateLogin() {
            const username = document.getElementById("username").value;
            const password = document.getElementById("password").value;

            // Check if username and password are empty
            if (username === "" || password === "") {
                alert("Username and password are required!");
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }

        // Check for error parameter in the URL and show a popup
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('error')) {
            alert("Invalid username or password!");
        }
    </script>
</body>
</html>