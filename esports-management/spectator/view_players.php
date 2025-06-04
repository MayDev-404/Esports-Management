<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'spectator') {
    header("Location: ../login.php");
    exit();
}

$players = [];
$selected_team_id = isset($_GET['team_id']) ? $_GET['team_id'] : '';

$teams_result = $conn->query("SELECT team_id, name FROM Team");

if (!empty($selected_team_id)) {
    $stmt = $conn->prepare("
        SELECT 
            p.username, p.role, p.join_date, p.avg_rating,
            COALESCE(SUM(s.kills), 0) AS total_kills,
            COALESCE(SUM(s.deaths), 0) AS total_deaths,
            COALESCE(AVG(s.win_rate), 0) AS avg_win_rate
        FROM Player p
        LEFT JOIN Stats s ON p.player_id = s.player_id
        WHERE p.team_id = ?
        GROUP BY p.player_id
    ");
    $stmt->bind_param("i", $selected_team_id);
    $stmt->execute();
    $players = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Players</title>
    <link rel="stylesheet" href="../css_spectator/view_players.css">
</head>
<body>
<div class="container">
    <h2>Select a Team to View Players</h2>
    <form method="GET" action="">
        <select name="team_id" onchange="this.form.submit()">
            <option value="">-- Select Team --</option>
            <?php while ($team = $teams_result->fetch_assoc()): ?>
                <option value="<?= $team['team_id'] ?>" <?= ($team['team_id'] == $selected_team_id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($team['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if (!empty($selected_team_id) && $players->num_rows > 0): ?>
        <h3>Players in Selected Team</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Join Date</th>
                    <th>Avg Rating</th>
                    <th>Total Kills</th>
                    <th>Total Deaths</th>
                    <th>Avg Win Rate (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($player = $players->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($player['username']) ?></td>
                        <td><?= htmlspecialchars($player['role']) ?></td>
                        <td><?= htmlspecialchars($player['join_date']) ?></td>
                        <td><?= htmlspecialchars($player['avg_rating']) ?></td>
                        <td><?= htmlspecialchars($player['total_kills']) ?></td>
                        <td><?= htmlspecialchars($player['total_deaths']) ?></td>
                        <td><?= htmlspecialchars($player['avg_win_rate']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif (!empty($selected_team_id)): ?>
        <p>No players found for this team.</p>
    <?php endif; ?>

    <a href="../spectator_dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
