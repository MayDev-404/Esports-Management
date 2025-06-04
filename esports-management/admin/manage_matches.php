<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT * FROM GameMatch";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Matches</title>
    <link rel="stylesheet" href="../css/manage_matches.css">
</head>
<body>
    <div class="matches-container">
        <h2>Manage Matches</h2>
        <a href="add_match.php" class="add-btn">+ Add Match</a>
        <a href="../admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
        <table border="1">
            <thead>
                <tr>
                    <th>Match ID</th>
                    <th>Tournament ID</th>
                    <th>Team 1 ID</th>
                    <th>Team 2 ID</th>
                    <th>Match Date</th>
                    <th>Result</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['match_id']}</td>
                            <td>{$row['tournament_id']}</td>
                            <td>{$row['team1_id']}</td>
                            <td>{$row['team2_id']}</td>
                            <td>{$row['match_date']}</td>
                            <td>{$row['result']}</td>
                            <td>
                                <a href='edit_match.php?id={$row['match_id']}' class='edit-btn'>Edit</a>
                                <a href='delete_match.php?id={$row['match_id']}' class='delete-btn' onclick=\"return confirm('Are you sure?');\">Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No matches found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
