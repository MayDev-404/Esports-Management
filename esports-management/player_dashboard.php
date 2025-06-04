<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'player') {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

$email = $_SESSION['email'];
$query = "SELECT p.*, t.name as team_name, t.logo as team_logo, u.email 
          FROM Player p 
          JOIN User u ON p.user_id = u.user_id 
          LEFT JOIN Team t ON p.team_id = t.team_id 
          WHERE u.email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$player = $result->fetch_assoc();

$query = "SELECT gm.*, t1.name as team1_name, t2.name as team2_name, tr.name as tournament_name 
          FROM GameMatch gm 
          JOIN Team t1 ON gm.team1_id = t1.team_id 
          JOIN Team t2 ON gm.team2_id = t2.team_id 
          JOIN Tournament tr ON gm.tournament_id = tr.tournament_id 
          WHERE (t1.team_id = ? OR t2.team_id = ?) AND gm.match_date > NOW() 
          ORDER BY gm.match_date ASC LIMIT 3";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $player['team_id'], $player['team_id']);
$stmt->execute();
$upcoming_matches = $stmt->get_result();

$query = "SELECT AVG(s.kills) as avg_kills, AVG(s.deaths) as avg_deaths, s.win_rate 
          FROM Stats s 
          WHERE s.player_id = ? GROUP BY s.player_id";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $player['player_id']);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esportify | Player Dashboard</title>
    <link rel="stylesheet" href="css/player_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="logo">
                <span>Esportify</span>
            </div>
            
            <div class="player-info">
                <div class="avatar">
                    <img src="<?php echo !empty($player['avatar']) ? $player['avatar'] : 'images/logo.png'; ?>" alt="Player Avatar">
                </div>
                <h3><?php echo $player['username']; ?></h3>
                <p class="role"><?php echo $player['role']; ?></p>
                <p class="team"><?php echo $player['team_name'] ?? 'No Team'; ?></p>
            </div>
            
            <nav class="nav-menu">
                <ul>
                    <li class="active">
                        <a href="#"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="player/view_profile.php"><i class="fas fa-user"></i> My Profile</a>
                    </li>
                    <li>
                        <a href="player/view_tournaments.php"><i class="fas fa-trophy"></i> Tournaments</a>
                    </li>
                    <li>
                        <a href="player/match_schedules.php"><i class="fas fa-gamepad"></i> Match Schedule</a>
                    </li>
                    <li class="logout">
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <header>
                <div class="toggle-menu">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="header-right">
                    <div class="user-dropdown">
                        <span><?php echo $player['username']; ?></span>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="welcome-message">
                    <h1>Welcome back, <span><?php echo $player['username']; ?></span></h1>
                    <h3>Your User ID is <span><?php echo $player['user_id']; ?></span></h3>
                    <p>Here's your latest stats and upcoming matches</p>
                </div>

                <div class="stats-overview">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-crosshairs"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Avg. Kills</h3>
                            <h2><?php echo number_format($stats['avg_kills'] ?? 0, 1); ?></h2>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-skull"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Avg. Deaths</h3>
                            <h2><?php echo number_format($stats['avg_deaths'] ?? 0, 1); ?></h2>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Win Rate</h3>
                            <h2><?php echo number_format($stats['win_rate'] ?? 0, 1); ?>%</h2>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Rating</h3>
                            <h2><?php echo number_format($player['avg_rating'] ?? 0, 2); ?></h2>
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="grid-item upcoming-matches">
                        <div class="section-header">
                            <h2>Upcoming Matches</h2>
                            <a href="player/match_schedules.php" class="view-all">View All</a>
                        </div>
                        
                        <div class="matches-list">
                            <?php if ($upcoming_matches->num_rows > 0): ?>
                                <?php while($match = $upcoming_matches->fetch_assoc()): ?>
                                    <div class="match-card">
                                        <div class="match-header">
                                            <span class="tournament"><?php echo $match['tournament_name']; ?></span>
                                            <span class="date"><?php echo date('M d, H:i', strtotime($match['match_date'])); ?></span>
                                        </div>
                                        <div class="teams">
                                            <div class="team">
                                                <span class="team-name"><?php echo $match['team1_name']; ?></span>
                                            </div>
                                            <div class="vs">VS</div>
                                            <div class="team">
                                                <span class="team-name"><?php echo $match['team2_name']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="no-data">No upcoming matches scheduled</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="grid-item team-info">
                        <div class="section-header">
                            <h2>My Team</h2>
                            <a href="player/view_profile.php" class="view-all">View Details</a>
                        </div>
                        
                        <?php if (!empty($player['team_id'])): ?>
                            <div class="team-card">
                                <div class="team-header">
                                    <div class="team-logo">
                                        <img src="<?php echo !empty($player['team_logo']) ? $player['team_logo'] : 'img/default_team_logo.png'; ?>" alt="Team Logo">
                                    </div>
                                    <div class="team-name">
                                        <h3><?php echo $player['team_name']; ?></h3>
                                        <p>Member since <?php echo date('M Y', strtotime($player['join_date'])); ?></p>
                                    </div>
                                </div>
                                <div class="team-status">
                                    <div class="status-item">
                                        <span class="label">Role</span>
                                        <span class="value"><?php echo $player['role']; ?></span>
                                    </div>
                                    <div class="status-item">
                                        <span class="label">Status</span>
                                        <span class="value active">Active</span>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="no-data">You are not currently on a team</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/player_dashboard.js"></script>
</body>
</html>