<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'spectator') {
    header("Location: ../login.php");
    exit();
}

$sql = "
SELECT 
    t.team_id,
    t.name AS team_name,
    t.creation_date,
    u.email AS manager_email,
    (SELECT COUNT(*) FROM Player p WHERE p.team_id = t.team_id) AS total_players,
    (SELECT COUNT(*) FROM GameMatch gm 
        WHERE gm.team1_id = t.team_id OR gm.team2_id = t.team_id) AS total_matches
FROM Team t
LEFT JOIN User u ON t.manager_id = u.user_id
ORDER BY t.name
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Teams</title>
    <link rel="stylesheet" href="../css_spectator/view_teams.css">
</head>
<body>
<div class="teams-container">
    <h2>All Esports Teams</h2>
    <table>
        <thead>
            <tr>
                <th>Team Name</th>
                <th>Manager Email</th>
                <th>Created On</th>
                <th>Total Players</th>
                <th>Total Matches</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['team_name']) ?></td>
                <td><?= htmlspecialchars($row['manager_email'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($row['creation_date']) ?></td>
                <td><?= $row['total_players'] ?></td>
                <td><?= $row['total_matches'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <a href="../spectator_dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
