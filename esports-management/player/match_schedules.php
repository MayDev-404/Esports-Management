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

$matchSql = "
    SELECT gm.match_id, gm.match_date, t1.name AS team1, t2.name AS team2, gm.result
    FROM GameMatch gm
    JOIN Team t1 ON gm.team1_id = t1.team_id
    JOIN Team t2 ON gm.team2_id = t2.team_id
    WHERE gm.team1_id = ? OR gm.team2_id = ?
    ORDER BY gm.match_date DESC
";
$stmtMatch = $conn->prepare($matchSql);
$stmtMatch->bind_param("ii", $player_team['team_id'], $player_team['team_id']);
$stmtMatch->execute();
$matchResult = $stmtMatch->get_result();

$stmt->close();
$stmtMatch->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Match Schedules</title>
    <link rel="stylesheet" href="../css_player/match_schedules.css">
</head>
<body>
<div class="match-schedules-container">
    <h2>Your Match Schedules</h2>
    <?php if ($matchResult->num_rows > 0): ?>
        <table class="match-table">
            <thead>
                <tr>
                    <th>Match Date</th>
                    <th>Opponent Team</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($match = $matchResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($match['match_date']) ?></td>
                        <td>
                            <?php
                                if ($match['team1'] == $player_team['team_name']) {
                                    echo htmlspecialchars($match['team2']);
                                } else {
                                    echo htmlspecialchars($match['team1']);
                                }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($match['result'] ?? 'N/A') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No matches found for your team.</p>
    <?php endif; ?>

    <a href="../player_dashboard.php" class="button">← Back to Dashboard</a>
</div>
</body>
</html>
