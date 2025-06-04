<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password']; 
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO user (email, password_hash, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $password, $role);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id; 
        $_SESSION['success'] = "Signup successful! Please login below.";
        $_SESSION['signup_user_id'] = $user_id;  
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: signup.php");
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <link rel="stylesheet" type="text/css" href="css/signup.css">
</head>
<body>
    <?php
    if (isset($_SESSION['error'])) {
        echo "<p style='color: red'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    ?>
    <form method="post">
        <h2>Signup</h2>
        <label>Email:</label><br>
        <input type="email" name="email">
        
        <label>Password:</label><br>
        <input type="password" name="password">
        
        <label>Select Role:</label>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="team_manager">Team Manager</option>
            <option value="player">Player</option>
            <option value="spectator">Spectator</option>
        </select>

        <button type="submit">Signup</button>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>
</body>
</html>
