<?php
session_start();
include '../db.php';

$manager_id = $_SESSION['user_id'];
$team_id = 0;

$stmt = $conn->prepare("SELECT team_id FROM Team WHERE manager_id = ?");
$stmt->bind_param("i", $manager_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $team_id = $result->fetch_assoc()['team_id'];
}

$reportType = $_POST['reportType'];

if ($reportType == 'tournament') {
    $sql = "SELECT DISTINCT t.* FROM Tournament t
            JOIN GameMatch g ON g.tournament_id = t.tournament_id
            WHERE g.team1_id = ? OR g.team2_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $team_id, $team_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<table><tr><th>ID</th><th>Name</th><th>Start Date</th><th>End Date</th><th>Prize Pool</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['tournament_id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['start_date']}</td>
            <td>{$row['end_date']}</td>
            <td>{$row['prize_pool']}</td>
            <td>{$row['status']}</td>
        </tr>";
    }
    echo "</table>";
}

elseif ($reportType == 'team') {
    $sql = "SELECT * FROM Team WHERE team_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<table><tr><th>Team ID</th><th>Name</th><th>Manager ID</th><th>Creation Date</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['team_id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['manager_id']}</td>
            <td>{$row['creation_date']}</td>
        </tr>";
    }
    echo "</table>";
}

elseif ($reportType == 'player') {
    $sql = "SELECT p.*, s.kills, s.deaths, s.win_rate
            FROM Player p
            LEFT JOIN Stats s ON p.player_id = s.player_id
            WHERE p.team_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<table><tr><th>ID</th><th>Username</th><th>Role</th><th>Join Date</th><th>Avg Rating</th><th>Win Rate (%)</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $win_rate = $row['win_rate'] ?? 0;
        echo "<tr>
            <td>{$row['player_id']}</td>
            <td>{$row['username']}</td>
            <td>{$row['role']}</td>
            <td>{$row['join_date']}</td>
            <td>{$row['avg_rating']}</td>
            <td>" . round($win_rate, 2) . "</td>
        </tr>";
    }
    echo "</table>";
}

elseif ($reportType == 'match') {
    $sql = "SELECT * FROM GameMatch
            WHERE team1_id = ? OR team2_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $team_id, $team_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<table><tr><th>ID</th><th>Tournament ID</th><th>Team 1</th><th>Team 2</th><th>Match Date</th><th>Result</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['match_id']}</td>
            <td>{$row['tournament_id']}</td>
            <td>{$row['team1_id']}</td>
            <td>{$row['team2_id']}</td>
            <td>{$row['match_date']}</td>
            <td>{$row['result']}</td>
        </tr>";
    }
    echo "</table>";
}
?>
