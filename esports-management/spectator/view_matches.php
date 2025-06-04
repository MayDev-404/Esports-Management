<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'spectator') {
    header("Location: ../login.php");
    exit();
}

$sql = "
SELECT 
    gm.match_id,
    gm.match_date,
    gm.result,
    gm.team1_id,
    gm.team2_id,
    t1.name AS team1_name,
    t2.name AS team2_name,
    s.platform,
    s.url
FROM GameMatch gm
JOIN Team t1 ON gm.team1_id = t1.team_id
JOIN Team t2 ON gm.team2_id = t2.team_id
LEFT JOIN Stream s ON gm.match_id = s.match_id
ORDER BY gm.match_date DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Matches</title>
    <link rel="stylesheet" href="../css_spectator/view_matches.css">
</head>
<body>
<div class="matches-container">
    <h2>Live & Past Matches</h2>
    <table>
        <thead>
            <tr>
                <th>Match ID</th>
                <th>Teams</th>
                <th>Match Date</th>
                <th>Result</th>
                <th>Watch</th>
                <th>Prize</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $prizeAmount = 'N/A';
                    if (strtolower(trim($row['result'])) !== 'pending') {
                        $prizeQuery = $conn->prepare("SELECT amount FROM Prize WHERE match_id = ? LIMIT 1");
                        $prizeQuery->bind_param("i", $row['match_id']);
                        $prizeQuery->execute();
                        $prizeResult = $prizeQuery->get_result();
                        if ($prizeRow = $prizeResult->fetch_assoc()) {
                            $prizeAmount = '₹' . number_format($prizeRow['amount']);
                        }
                        $prizeQuery->close();
                    }
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['match_id']) ?></td>
                    <td><?= htmlspecialchars($row['team1_name']) ?> vs <?= htmlspecialchars($row['team2_name']) ?></td>
                    <td><?= htmlspecialchars($row['match_date']) ?></td>
                    <td><?= htmlspecialchars($row['result']) ?></td>
                    <td>
                        <?php if (strtolower($row['result']) !== 'pending' && !empty($row['url'])): ?>
                            <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank">Watch</a>
                        <?php else: ?>
                            <span style="color: gray;">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $prizeAmount ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="../spectator_dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
