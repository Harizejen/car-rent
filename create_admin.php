<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account</title>
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

        .create-account-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .create-account-container h2 {
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

        .create-account-container input[type="submit"],
        .create-account-container .back-button {
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

        .create-account-container input[type="submit"] {
            background-color: #667eea;
            color: #fff;
        }

        .create-account-container input[type="submit"]:hover {
            background-color: #5a6fd1;
        }

        .create-account-container .back-button {
            background-color: #6c757d;
            color: #fff;
        }

        .create-account-container .back-button:hover {
            background-color: #5a6268;
        }

        .error-message {
            color: #ff4757;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .mt-3 {
            margin-top: 15px;
        }

        .create-account-container a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .create-account-container a:hover {
            text-decoration: underline;
        }
    </style>
    <!-- Add FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="create-account-container">
        <h2>Create Admin Account</h2>
        <?php
        // Display error message if account creation fails
        if (isset($_GET['error'])) {
            echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
        }
        // Display success message if account creation succeeds
        if (isset($_GET['success'])) {
            echo '<div class="success-message">Admin account created successfully!</div>';
        }
        ?>
        <form action="process_create_admin.php" method="post" onsubmit="return validatePassword()">
            <!-- Username Field with Icon -->
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <!-- Password Field with Icon -->
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>
            <!-- Confirm Password Field with Icon -->
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
            </div>
            <!-- Email Field with Icon -->
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <input type="submit" value="Create Account">
        </form>
        <!-- Back Button -->
        <a href="adminLogin.php" class="back-button">Back</a>
    </div>

    <script>
        // Validate if password and confirm password match
        function validatePassword() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</body>
</html>