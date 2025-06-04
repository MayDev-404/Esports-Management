<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT * FROM team";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Teams</title>
    <link rel="stylesheet" href="../css/manage_teams.css">
</head>
<body>
    <div class="team-container">
        <h2>Manage Teams</h2>
        <a href="add_team.php" class="add-btn">+ Add Team</a>
        <a href="../admin_dashboard.php" class="back-btn">← Back to Dashboard</a>

        <table>
            <thead>
                <tr>
                    <th>Team ID</th>
                    <th>Team Name</th>
                    <th>Logo</th>
                    <th>Manager ID</th>
                    <th>Creation Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['team_id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['logo']}</td>
                                <td>{$row['manager_id']}</td>
                                <td>{$row['creation_date']}</td>
                                <td>
                                    <a href='edit_team.php?id={$row['team_id']}' class='edit-btn'>Edit</a>
                                    <a href='delete_team.php?id={$row['team_id']}' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this team?');\">Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No teams found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
