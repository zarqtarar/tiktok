<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <style>
        /* Global reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #333; /* Dark gray background */
            background: linear-gradient(135deg, #2e2e2e, #434343); /* Black-gray gradient */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #f2f2f2;
            text-align: center;
        }

        /* Container for the sign-in form */
        .container {
            background-color: white;
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2575fc;
            font-size: 30px;
            margin-bottom: 20px;
        }

        /* Form Labels */
        label {
            font-size: 16px;
            text-align: left;
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        /* Input Fields */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            color: #333;
            transition: border-color 0.3s ease-in-out;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #2575fc;
            outline: none;
        }

        /* Submit Button */
        input[type="submit"] {
            width: 100%;
            padding: 14px;
            background-color: #2575fc;
            color: white;
            font-size: 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        input[type="submit"]:hover {
            background-color: #1d63d7;
        }

        /* Error Message */
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 15px;
        }

        /* Footer Links */
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #333;
        }

        .footer a {
            color: #2575fc;
            text-decoration: none;
            font-weight: bold;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Responsive Styles */
        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 26px;
            }

            input[type="submit"] {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Sign In</h2>

        <!-- Display Error Message if there is an issue with the login -->
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?php
                if ($_GET['error'] == 'invalid') {
                    echo "Invalid username or password.";
                } else {
                    echo "An unknown error occurred.";
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- Sign In Form -->
        <form action="signin_process.php" method="POST">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br>

            <input type="submit" value="Sign In">
        </form>

        <div class="footer">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>

</body>

</html>
