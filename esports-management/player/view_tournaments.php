<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'player') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT p.team_id, t.name AS team_name
    FROM Player p
    JOIN Team t ON p.team_id = t.team_id
    WHERE p.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$player_team = $result->fetch_assoc();

$tournamentSql = "
    SELECT DISTINCT t.tournament_id, t.name, t.start_date, t.end_date, t.status
    FROM Tournament t
    JOIN GameMatch gm ON gm.tournament_id = t.tournament_id
    WHERE gm.team1_id = ? OR gm.team2_id = ?
    ORDER BY t.start_date DESC
";
$stmtTournaments = $conn->prepare($tournamentSql);
$stmtTournaments->bind_param("ii", $player_team['team_id'], $player_team['team_id']);
$stmtTournaments->execute();
$tournamentResult = $stmtTournaments->get_result();

$stmt->close();
$stmtTournaments->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Tournaments</title>
    <link rel="stylesheet" href="../css_player/view_tournaments.css">
</head>
<body>
<div class="tournaments-container">
    <h2>Tournaments Involved</h2>
    <?php if ($tournamentResult->num_rows > 0): ?>
        <table class="tournament-table">
            <thead>
                <tr>
                    <th>Tournament Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($tournament = $tournamentResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($tournament['name']) ?></td>
                        <td><?= htmlspecialchars($tournament['start_date']) ?></td>
                        <td><?= htmlspecialchars($tournament['end_date']) ?></td>
                        <td><?= htmlspecialchars($tournament['status']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tournaments found for your team.</p>
    <?php endif; ?>

    <a href="../player_dashboard.php" class="button">← Back to Dashboard</a>
</div>
</body>
</html>
