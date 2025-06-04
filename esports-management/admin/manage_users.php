<?php
session_start();
include '../db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT user_id, email, role FROM user";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/manage_users.css">
</head>
<body>
    <div class="container">
        <h2 class="page-title">Manage Users</h2>
        <a href="../admin_dashboard.php" class="back-btn">← Back to Dashboard</a>

        <table class="styled-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                        echo "<td>
                            <a class='edit-btn' href='edit_user.php?id=" . $row['user_id'] . "'>Edit</a>
                            <a class='delete-btn' href='delete_user.php?id=" . $row['user_id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No users found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
