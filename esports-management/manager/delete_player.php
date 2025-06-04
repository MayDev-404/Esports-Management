<?php
session_start();
include '../db.php';

if (!isset($_GET['player_id'])) {
    header("Location: manage_your_team.php");
    exit();
}

$player_id = $_GET['player_id'];

$sql = "DELETE FROM player WHERE player_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $player_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Player deleted successfully!";
} else {
    $_SESSION['error'] = "Error deleting player.";
}

header("Location: manage_your_team.php");
?>
