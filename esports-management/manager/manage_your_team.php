<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'team_manager') {
    header("Location: ../login.php");
    exit();
}

$manager_email = $_SESSION['email'];
$sql = "SELECT user_id FROM User WHERE email = ? AND role = 'team_manager'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $manager_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $manager_data = $result->fetch_assoc();
    $manager_id = $manager_data['user_id'];
} else {
    $_SESSION['error'] = "Manager not found!";
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT * FROM Team WHERE manager_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $manager_id);
$stmt->execute();
$team_result = $stmt->get_result();

if ($team_result->num_rows === 1) {
    $team_data = $team_result->fetch_assoc();
    $team_name = $team_data['name'];
    $team_id = $team_data['team_id'];
} else {
    $_SESSION['error'] = "No team found for the manager!";
    header("Location: manager_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Your Team</title>
    <link rel="stylesheet" href="../css_manager/manage_your_team.css">
</head>
<body>
    <div class="container">
        <h1>Manage Your Team</h1>
        <p>Welcome, Team Manager! You are managing the team: <strong><?php echo $team_name; ?></strong></p>
        
        <div class="actions">
            <a href="add_player.php?team_id=<?php echo $team_id; ?>" class="btn">Add Player</a>
            <a href="../manager_dashboard.php" class="btn back-btn">Back to Dashboard</a>
        </div>

        <h2>Players:</h2>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Average Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM Player WHERE team_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $team_id);
                $stmt->execute();
                $players_result = $stmt->get_result();

                while ($player = $players_result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$player['username']}</td>
                            <td>{$player['role']}</td>
                            <td>{$player['avg_rating']}</td>
                            <td>
                                <a href='edit_player.php?player_id={$player['player_id']}' class='btn'>Edit</a>
                                <a href='delete_player.php?player_id={$player['player_id']}' class='btn' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
