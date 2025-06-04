<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tournament_id = $_POST['tournament_id'];
    $team1_id = $_POST['team1_id'];
    $team2_id = $_POST['team2_id'];
    $match_date = $_POST['match_date'];
    $result = $_POST['result'];

    $sql = "INSERT INTO GameMatch (tournament_id, team1_id, team2_id, match_date, result)
            VALUES ('$tournament_id', '$team1_id', '$team2_id', '$match_date', '$result')";

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_matches.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Match</title>
    <link rel="stylesheet" href="../css/add_match.css">
</head>
<body>
    <div class="add-match-container">
        <h2>Add New Match</h2>
        <form method="POST">
            <label>Tournament ID:</label>
            <input type="number" name="tournament_id" required>

            <label>Team 1 ID:</label>
            <input type="number" name="team1_id" required>

            <label>Team 2 ID:</label>
            <input type="number" name="team2_id" required>

            <label>Match Date:</label>
            <input type="datetime-local" name="match_date" required>

            <label>Result:</label>
            <input type="text" name="result" placeholder="e.g. Team 1 won" required>

            <button type="submit">Add Match</button>
            <a href="manage_matches.php" class="back-btn">Back</a>
        </form>
    </div>
</body>
</html>
