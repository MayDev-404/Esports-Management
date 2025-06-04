<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

$dashboardStats = [
    'users' => 0,
    'teams' => 0,
    'matches' => 0,
    'tournaments' => 0
];

$userQuery = "SELECT COUNT(*) as total FROM user";
if ($result = $conn->query($userQuery)) {
    $dashboardStats['users'] = $result->fetch_assoc()['total'];
}

$teamQuery = "SELECT COUNT(*) as total FROM team";
if ($result = $conn->query($teamQuery)) {
    $dashboardStats['teams'] = $result->fetch_assoc()['total'];
}

$matchQuery = "SELECT COUNT(*) as total FROM gamematch";
if ($result = $conn->query($matchQuery)) {
    $dashboardStats['matches'] = $result->fetch_assoc()['total'];
}

$tournamentQuery = "SELECT COUNT(*) as total FROM tournament";
if ($result = $conn->query($tournamentQuery)) {
    $dashboardStats['tournaments'] = $result->fetch_assoc()['total'];
}

$upcomingMatches = [];
$upcomingQuery = "SELECT gm.match_id, t1.name as team1, t2.name as team2, gm.match_date 
                 FROM gamematch gm
                 INNER JOIN team t1 ON gm.team1_id = t1.team_id
                 INNER JOIN team t2 ON gm.team2_id = t2.team_id
                 WHERE gm.match_date > NOW()
                 ORDER BY gm.match_date ASC
                 LIMIT 5";
if ($result = $conn->query($upcomingQuery)) {
    while ($row = $result->fetch_assoc()) {
        $upcomingMatches[] = $row;
    }
}

$topTeams = [];
$topTeamsQuery = "SELECT t.name, 
                  COUNT(CASE WHEN gm.team1_id = t.team_id AND gm.result = 'team1_win' THEN 1 
                             WHEN gm.team2_id = t.team_id AND gm.result = 'team2_win' THEN 1 END) as wins,
                  COUNT(CASE WHEN gm.team1_id = t.team_id AND gm.result = 'team2_win' THEN 1 
                             WHEN gm.team2_id = t.team_id AND gm.result = 'team1_win' THEN 1 END) as losses
                  FROM team t
                  LEFT JOIN gamematch gm ON t.team_id = gm.team1_id OR t.team_id = gm.team2_id
                  WHERE gm.result IS NOT NULL
                  GROUP BY t.team_id
                  ORDER BY wins DESC, losses ASC
                  LIMIT 4";
                  
if ($result = $conn->query($topTeamsQuery)) {
    while ($row = $result->fetch_assoc()) {
        $topTeams[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Esports Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>
    <div class="dashboard-loader">
        <div class="loader-content">
            <div class="loader-logo">
                <i class="fas fa-gamepad"></i>
            </div>
            <div class="loader-text">Loading Dashboard</div>
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="header-top">
                <div class="logo-container">
                    <i class="fas fa-gamepad logo-icon"></i>
                    <h1>Esportify</h1>
                </div>
                <div class="user-actions">
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                    </div>
                    <button class="mode-toggle" id="mode-toggle">
                        <i class="fas fa-sun"></i>
                    </button>
                </div>
            </div>
            <div class="header-welcome">
                <h2>Welcome to your Admin Command Center</h2>
                <p>Manage your esports platform, track statistics, and monitor upcoming events</p>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="nav-column">
                <div class="admin-profile">
                    <div class="profile-picture">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="profile-info">
                        <h3>Admin Portal</h3>
                        <p>ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
                    </div>
                </div>

                <a href="admin/manage_users.php" class="nav-card fade-in">
                    <div class="card-icon"><i class="fas fa-users"></i></div>
                    <div class="card-content">
                        <h3>Manage Users</h3>
                        <p>Add, edit, delete users</p>
                    </div>
                </a>
                
                <a href="admin/manage_teams.php" class="nav-card fade-in">
                    <div class="card-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="card-content">
                        <h3>Manage Teams</h3>
                        <p>Create, edit, remove teams</p>
                    </div>
                </a>
                
                <a href="admin/manage_matches.php" class="nav-card fade-in">
                    <div class="card-icon"><i class="fas fa-gamepad"></i></div>
                    <div class="card-content">
                        <h3>Manage Matches</h3>
                        <p>Schedule, update, delete matches</p>
                    </div>
                </a>
                
                <a href="logout.php" class="nav-card logout-card fade-in">
                    <div class="card-icon"><i class="fas fa-power-off"></i></div>
                    <div class="card-content">
                        <h3>Logout</h3>
                        <p>End admin session</p>
                    </div>
                </a>
            </div>

            <div class="dashboard-column">
                <div class="widget stats-widget fade-in">
                    <h2><i class="fas fa-chart-line"></i> Platform Overview</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                            <div class="stat-info">
                                <h4>Users</h4>
                                <p class="counter"><?php echo number_format($dashboardStats['users']); ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-shield-alt"></i></div>
                            <div class="stat-info">
                                <h4>Teams</h4>
                                <p class="counter"><?php echo number_format($dashboardStats['teams']); ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-gamepad"></i></div>
                            <div class="stat-info">
                                <h4>Matches</h4>
                                <p class="counter"><?php echo number_format($dashboardStats['matches']); ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-trophy"></i></div>
                            <div class="stat-info">
                                <h4>Tournaments</h4>
                                <p class="counter"><?php echo number_format($dashboardStats['tournaments']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="widgets-row">
                    <div class="widget upcoming-matches fade-in">
                        <h2><i class="fas fa-calendar-alt"></i> Upcoming Matches</h2>
                        <?php if (empty($upcomingMatches)): ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-xmark"></i>
                                <p>No upcoming matches scheduled</p>
                            </div>
                        <?php else: ?>
                            <div class="match-list">
                                <?php foreach ($upcomingMatches as $match): ?>
                                    <div class="match-item">
                                        <div class="match-teams">
                                            <span class="team team1"><?php echo htmlspecialchars($match['team1']); ?></span>
                                            <span class="vs">VS</span>
                                            <span class="team team2"><?php echo htmlspecialchars($match['team2']); ?></span>
                                        </div>
                                        <div class="match-date">
                                            <i class="far fa-clock"></i>
                                            <?php echo date('M d, H:i', strtotime($match['match_date'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="widget top-teams fade-in">
                        <h2><i class="fas fa-medal"></i> Top Teams</h2>
                        <?php if (empty($topTeams)): ?>
                            <div class="empty-state">
                                <i class="fas fa-shield-xmark"></i>
                                <p>No team statistics available yet</p>
                            </div>
                        <?php else: ?>
                            <div class="teams-list">
                                <?php foreach ($topTeams as $index => $team): ?>
                                    <div class="team-item">
                                        <div class="rank">#<?php echo $index + 1; ?></div>
                                        <div class="team-name"><?php echo htmlspecialchars($team['name']); ?></div>
                                        <div class="team-record">
                                            <span class="wins"><?php echo $team['wins']; ?>W</span>
                                            <span class="separator">-</span>
                                            <span class="losses"><?php echo $team['losses']; ?>L</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-footer">
            <p>&copy; <?php echo date('Y'); ?> Esports Arena Management System</p>
        </div>
    </div>

    <script src="js/admin_dashboard.js"></script>
</body>
</html>