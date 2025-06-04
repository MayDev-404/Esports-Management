<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_teams.php");
    exit();
}

$team_id = $_GET['id'];

$sql = "SELECT * FROM Team WHERE team_id = $team_id";
$result = mysqli_query($conn, $sql);
$team = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $logo = $_POST['logo'];
    $manager_id = $_POST['manager_id'];
    $creation_date = $_POST['creation_date'];

    $update_sql = "UPDATE Team SET 
                    name = '$name',
                    logo = '$logo',
                    manager_id = '$manager_id',
                    creation_date = '$creation_date'
                    WHERE team_id = $team_id";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: manage_teams.php");
        exit();
    } else {
        echo "Error updating team: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Team</title>
    <link rel="stylesheet" href="../css/edit_team.css">
</head>
<body>
    <div class="edit-team-container">
        <h2>Edit Team</h2>
        <form method="POST">
            <label>Team Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($team['name']); ?>" required>

            <label>Logo:</label>
            <input type="text" name="logo" value="<?php echo htmlspecialchars($team['logo']); ?>">

            <label>Manager ID:</label>
            <input type="number" name="manager_id" value="<?php echo htmlspecialchars($team['manager_id']); ?>" required>

            <label>Creation Date:</label>
            <input type="date" name="creation_date" value="<?php echo $team['creation_date']; ?>" required>

            <button type="submit">Update Team</button>
            <a href="manage_teams.php" class="back-btn">← Back</a>
        </form>
    </div>
</body>
</html>
