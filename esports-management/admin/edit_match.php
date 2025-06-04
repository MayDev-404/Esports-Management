<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$match_id = $_GET['id'] ?? null;

if (!$match_id) {
    echo "Invalid match ID.";
    exit();
}

$sql = "SELECT * FROM GameMatch WHERE match_id = $match_id";
$result = $conn->query($sql);

if ($result->num_rows !== 1) {
    echo "Match not found.";
    exit();
}

$match = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tournament_id = $_POST['tournament_id'];
    $team1_id = $_POST['team1_id'];
    $team2_id = $_POST['team2_id'];
    $match_date = $_POST['match_date'];
    $result_text = $_POST['result'];

    $update_sql = "UPDATE GameMatch SET 
                    tournament_id = '$tournament_id', 
                    team1_id = '$team1_id', 
                    team2_id = '$team2_id', 
                    match_date = '$match_date', 
                    result = '$result_text' 
                   WHERE match_id = $match_id";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: manage_matches.php");
        exit();
    } else {
        echo "Error updating match: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Match</title>
    <link rel="stylesheet" href="../css/edit_match.css">
</head>
<body>
    <div class="edit-match-container">
        <h2>Edit Match</h2>
        <form method="POST">
            <label>Tournament ID:</label>
            <input type="number" name="tournament_id" value="<?= $match['tournament_id'] ?>" required>

            <label>Team 1 ID:</label>
            <input type="number" name="team1_id" value="<?= $match['team1_id'] ?>" required>

            <label>Team 2 ID:</label>
            <input type="number" name="team2_id" value="<?= $match['team2_id'] ?>" required>

            <label>Match Date:</label>
            <input type="datetime-local" name="match_date" value="<?= date('Y-m-d\TH:i', strtotime($match['match_date'])) ?>" required>

            <label>Result:</label>
            <input type="text" name="result" value="<?= $match['result'] ?>" required>

            <button type="submit">Update Match</button>
            <a href="manage_matches.php" class="back-btn"> Back</a>
        </form>
    </div>
</body>
</html>
