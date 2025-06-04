<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_teams.php");
    exit();
}

$team_id = $_GET['id'];

$sql = "DELETE FROM Team WHERE team_id = $team_id";

if (mysqli_query($conn, $sql)) {
    header("Location: manage_teams.php");
    exit();
} else {
    echo "Error deleting team: " . mysqli_error($conn);
}
?>
