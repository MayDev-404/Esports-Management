<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Esports Management</title>
    <link rel="stylesheet" href="css/login.css"> 
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <?php
        if (isset($_SESSION['signup_user_id'])) {
            echo "<p style='color: blue; font-weight: bold;'>Your User ID is: " . $_SESSION['signup_user_id'] . "</p>";
            unset($_SESSION['signup_user_id']);
        }

        if (isset($_SESSION['success'])) {
            echo "<p class='success-message' style='color: green; font-weight: bold;'>" . $_SESSION['success'] . "</p>";
            unset($_SESSION['success']);
        }

        if (isset($_SESSION['error'])) {
            echo "<p class='error-message' style='color: red; font-weight: bold;'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }
        ?>

        <form action="login_process.php" method="post">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
            <a href="signup.php">Don't have an account? Sign up here</a>
        </form>
    </div>
</body>
</html>
