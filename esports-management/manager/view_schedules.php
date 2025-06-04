<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'team_manager') {
    header("Location: ../login.php");
    exit();
}

$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT user_id FROM User WHERE email = ? AND role = 'team_manager'");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    $_SESSION['error'] = "Manager not found.";
    header("Location: manager_dashboard.php");
    exit();
}
$manager = $result->fetch_assoc();
$manager_id = $manager['user_id'];

$stmt = $conn->prepare("SELECT team_id FROM Team WHERE manager_id = ?");
$stmt->bind_param("i", $manager_id);
$stmt->execute();
$team_result = $stmt->get_result();
if ($team_result->num_rows !== 1) {
    $_SESSION['error'] = "Team not found.";
    header("Location: manager_dashboard.php");
    exit();
}
$team_data = $team_result->fetch_assoc();
$team_id = $team_data['team_id'];

$stmt = $conn->prepare("CALL GetMatchesForTeam(?)");
$stmt->bind_param("i", $team_id);
$stmt->execute();
$matches_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Match Schedules</title>
    <link rel="stylesheet" href="../css_manager/view_schedules.css">
</head>
<body>
    <div class="container">
        <h1>Match Schedules for Your Team</h1>
        <table>
            <thead>
                <tr>
                    <th>Match ID</th>
                    <th>Team 1</th>
                    <th>Team 2</th>
                    <th>Date & Time</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($match = $matches_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $match['match_id']; ?></td>
                    <td><?php echo $match['team1_id'] . " - " . $match['team1_name']; ?></td>
                    <td><?php echo $match['team2_id'] . " - " . $match['team2_name']; ?></td>
                    <td><?php echo $match['match_date']; ?></td>
                    <td><?php echo $match['result']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="../manager_dashboard.php" class="btn back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
