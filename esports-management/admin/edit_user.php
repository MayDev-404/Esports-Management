<?php
session_start();
include '../db.php';

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = $_GET['id'];

$sql = "SELECT * FROM user WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT); // Securely hash the password
        $update_sql = "UPDATE user SET email='$email', role='$role', password_hash='$password_hash' WHERE user_id=$user_id";
    } else {
        $update_sql = "UPDATE user SET email='$email', role='$role' WHERE user_id=$user_id";
    }

    if (mysqli_query($conn, $update_sql)) {
        $success_message = "User updated successfully!";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);
    } else {
        $error_message = "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/edit_user.css">
</head>
<body>
    <div class="edit-user-container">
        <h2>Edit User</h2>

        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="admin" <?php if ($user['role'] == 'admin') echo "selected"; ?>>Admin</option>
                <option value="team_manager" <?php if ($user['role'] == 'team_manager') echo "selected"; ?>>Team Manager</option>
                <option value="player" <?php if ($user['role'] == 'player') echo "selected"; ?>>Player</option>
                <option value="spectator" <?php if ($user['role'] == 'spectator') echo "selected"; ?>>Spectator</option>
            </select>

            <label>New Password (leave blank to keep current):</label>
            <input type="text" name="password" placeholder="Enter new password">

            <button type="submit">Update User</button>
            <a href="manage_users.php" class="back-link">← Back to Manage Users</a>
        </form>
    </div>
</body>
</html>
