<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $logo = $_POST['logo'];
    $manager_id = $_POST['manager_id'];
    $creation_date = $_POST['creation_date'];

    $sql = "INSERT INTO Team (name, logo, manager_id, creation_date)
            VALUES ('$name', '$logo', '$manager_id', '$creation_date')";

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_teams.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Team</title>
    <link rel="stylesheet" href="../css/add_team.css">
</head>
<body>
    <div class="add-team-container">
        <h2>Add New Team</h2>
        <form method="POST">
            <label>Team Name:</label>
            <input type="text" name="name" required>

            <label>Logo (filename):</label>
            <input type="text" name="logo" placeholder="e.g. team1.png">

            <label>Manager ID:</label>
            <input type="number" name="manager_id" required>

            <label>Creation Date:</label>
            <input type="date" name="creation_date" required>

            <button type="submit">Add Team</button>
            <a href="manage_teams.php" class="back-btn">← Back</a>
        </form>
    </div>
</body>
</html>
