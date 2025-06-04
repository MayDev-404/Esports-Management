<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'team_manager') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['player_id'])) {
    $player_id = $_GET['player_id'];
    $sql = "SELECT * FROM Player WHERE player_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $player_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $player = $result->fetch_assoc();
    } else {
        $_SESSION['error'] = "Player not found!";
        header("Location: manage_your_team.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = trim($_POST['username']);
        $role = trim($_POST['role']);
        $join_date = $_POST['join_date'];
        $avg_rating = $_POST['avg_rating'];

        $sql_update = "UPDATE Player SET username = ?, role = ?, join_date = ?, avg_rating = ? WHERE player_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssi", $username, $role, $join_date, $avg_rating, $player_id);

        if ($stmt_update->execute()) {
            $_SESSION['success'] = "Player updated successfully!";
            header("Location: manage_your_team.php");
            exit();
        } else {
            $_SESSION['error'] = "Error updating player.";
        }

        $stmt_update->close();
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid player ID.";
    header("Location: manage_your_team.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Player</title>
    <link rel="stylesheet" href="../css_manager/edit_player.css">
</head>
<body>

    <div class="container">
        <h1>Edit Player</h1>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($player['username']); ?>" required>

            <label for="role">Role:</label>
            <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($player['role']); ?>" required>

            <label for="join_date">Join Date:</label>
            <input type="date" id="join_date" name="join_date" value="<?php echo htmlspecialchars($player['join_date']); ?>" required>

            <label for="avg_rating">Avg. Rating:</label>
            <input type="number" id="avg_rating" name="avg_rating" step="0.01" min="0" max="5" value="<?php echo htmlspecialchars($player['avg_rating']); ?>" required>

            <input type="submit" value="Update Player">
        </form>
        <br>
        <a href="manage_your_team.php" class="btn back-btn">Back to Dashboard</a>
    </div>

</body>
</html>
