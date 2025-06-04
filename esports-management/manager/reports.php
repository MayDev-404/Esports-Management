<?php
session_start();
include '../db.php';

$manager_id = $_SESSION['user_id'];
$team_query = $conn->prepare("SELECT team_id FROM Team WHERE manager_id = ?");
$team_query->bind_param("i", $manager_id);
$team_query->execute();
$team_result = $team_query->get_result();
$team_id = ($team_result->num_rows > 0) ? $team_result->fetch_assoc()['team_id'] : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
    <link rel="stylesheet" href="../css_manager/reports.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="report-container">
        <h1>Reports</h1>
        <div class="report-controls">
            <select id="reportType">
                <option value="">Select Report Type</option>
                <option value="tournament">Tournament Report</option>
                <option value="team">Team Report</option>
                <option value="player">Player Report</option>
                <option value="match">Match Report</option>
            </select>
            <button onclick="generateReport()">Generate Report</button>
            <a href="../manager_dashboard.php" class="back-button">← Back to Dashboard</a>
        </div>

        <div id="reportResults"></div>
        <canvas id="performanceChart" style="display: none;"></canvas>
    </div>

    <script src="../js/reports.js"></script>
</body>
</html>
