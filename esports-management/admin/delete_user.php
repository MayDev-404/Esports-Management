<?php
session_start();
include '../db.php';

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = $_GET['id'];

$sql = "DELETE FROM user WHERE user_id = $user_id";
if (mysqli_query($conn, $sql)) {
    header("Location: manage_users.php");
    exit();
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
?>
