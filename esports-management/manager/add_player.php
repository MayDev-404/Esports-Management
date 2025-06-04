<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'team_manager') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $team_id = $_POST['team_id'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $avg_rating = $_POST['avg_rating'];
    $join_date = $_POST['join_date']; 
    $user_id = $_POST['user_id'];

    $sql = "INSERT INTO Player (username, team_id, role, avg_rating, join_date, user_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisisi", $username, $team_id, $role, $avg_rating, $join_date, $user_id);
    $stmt->execute();

    $_SESSION['success'] = "Player added successfully!";
    header("Location: manage_your_team.php");
    exit();
}

$team_id = $_GET['team_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Player</title>
    <link rel="stylesheet" href="../css_manager/add_player.css">
</head>
<body>
    <div class="container">
        <h1>Add Player</h1>
        <form action="add_player.php" method="POST">
            <input type="hidden" name="team_id" value="<?php echo $team_id; ?>">

            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="role">Role</label>
            <input type="text" id="role" name="role" required>

            <label for="avg_rating">Average Rating</label>
            <input type="number" step="0.01" id="avg_rating" name="avg_rating" required>

            <label for="join_date">Join Date</label>
            <input type="date" id="join_date" name="join_date" required>

            <label for="user_id">User ID</label>
            <input type="number" id="user_id" name="user_id" required>

            <button type="submit" class="btn">Add Player</button>
        </form>
        <br>
        <a href="manage_your_team.php" class="btn back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
