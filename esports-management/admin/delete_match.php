<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $match_id = $_GET['id'];

    $sql = "DELETE FROM GameMatch WHERE match_id = $match_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_matches.php");
        exit();
    } else {
        echo "Error deleting match: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
