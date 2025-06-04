<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'spectator') {
    header("Location: ../login.php");
    exit();
}

// Database connection
$db_host = "localhost"; 
$db_user = "root";       
$db_pass = "";           
$db_name = "esports_management"; 

/*
$servername = "sql308.infinityfree.com";  
$username = "if0_38801586";         
$password = "esports62042";             
$dbname = "if0_38801586_esports_management";
*/

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tournament_query = "SELECT COUNT(*) as tournament_count FROM Tournament WHERE status != 'completed'";
$tournament_result = $conn->query($tournament_query);
$tournament_count = $tournament_result->fetch_assoc()['tournament_count'];

$match_query = "SELECT COUNT(*) as match_count FROM GameMatch WHERE match_date <= NOW() AND result IS NULL";
$match_result = $conn->query($match_query);
$match_count = $match_result->fetch_assoc()['match_count'];

$team_query = "SELECT COUNT(*) as team_count FROM Team";
$team_result = $conn->query($team_query);
$team_count = $team_result->fetch_assoc()['team_count'];

$stream_query = "SELECT COUNT(*) as stream_count FROM Stream s 
                JOIN GameMatch m ON s.match_id = m.match_id 
                WHERE m.match_date <= NOW() AND m.result IS NULL";
$stream_result = $conn->query($stream_query);
$stream_count = $stream_result->fetch_assoc()['stream_count'];

$upcoming_matches_query = "SELECT m.match_id, m.match_date, m.result, 
                           t1.name as team1_name, t1.logo as team1_logo, 
                           t2.name as team2_name, t2.logo as team2_logo,
                           trn.name as tournament_name
                           FROM GameMatch m
                           JOIN Team t1 ON m.team1_id = t1.team_id
                           JOIN Team t2 ON m.team2_id = t2.team_id
                           JOIN Tournament trn ON m.tournament_id = trn.tournament_id
                           WHERE m.match_date >= NOW()
                           ORDER BY m.match_date
                           LIMIT 2";
$upcoming_matches_result = $conn->query($upcoming_matches_query);

$top_teams_query = "SELECT t.team_id, t.name, t.logo, 
                    COUNT(CASE WHEN m.team1_id = t.team_id AND m.result LIKE 'team1%' THEN 1 
                              WHEN m.team2_id = t.team_id AND m.result LIKE 'team2%' THEN 1 END) as wins,
                    COUNT(CASE WHEN m.team1_id = t.team_id AND m.result LIKE 'team2%' THEN 1 
                              WHEN m.team2_id = t.team_id AND m.result LIKE 'team1%' THEN 1 END) as losses
                    FROM Team t
                    LEFT JOIN GameMatch m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
                    WHERE m.result IS NOT NULL
                    GROUP BY t.team_id
                    ORDER BY wins DESC
                    LIMIT 3";
$top_teams_result = $conn->query($top_teams_query);

$star_players_query = "SELECT p.player_id, p.username, p.role, t.name as team_name, t.logo as team_logo,
                      AVG(s.kills) / NULLIF(AVG(s.deaths), 0) as kd_ratio,
                      AVG(s.win_rate) as avg_win_rate
                      FROM Player p
                      JOIN Team t ON p.team_id = t.team_id
                      JOIN Stats s ON p.player_id = s.player_id
                      GROUP BY p.player_id
                      ORDER BY kd_ratio DESC, avg_win_rate DESC
                      LIMIT 2";
$star_players_result = $conn->query($star_players_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esportify | Spectator Hub</title>
    <link rel="stylesheet" href="css/spectator_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-loader" id="loader">
        <div class="loader-content">
            <div class="loader-logo"><i class="fas fa-gamepad"></i></div>
            <div class="loader-text">Loading Esportify</div>
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="dashboard-sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-gamepad"></i>
                    <span>ESPORTIFY</span>
                </div>
            </div>
            
            <div class="user-profile">
                <div class="avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-info">
                    <p class="username"><?php echo $_SESSION['email']; ?></p>
                    <p class="user-role">ID  <?php echo $_SESSION['user_id']; ?></p>
                    <span class="user-role">Spectator</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
                        <a href="#"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="spectator/view_matches.php"><i class="fas fa-trophy"></i> Matches</a>
                    </li>
                    <li>
                        <a href="spectator/view_teams.php"><i class="fas fa-users"></i> Teams</a>
                    </li>
                    <li>
                        <a href="spectator/view_players.php"><i class="fas fa-user-astronaut"></i> Players</a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <button id="themeToggle" class="theme-toggle">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>Welcome to Esportify</h1>
                <p>Your hub for esports tournaments, teams, and players</p>
            </div>
            
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Active Tournaments</h3>
                        <p class="stat-value"><?php echo $tournament_count; ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Ongoing Matches</h3>
                        <p class="stat-value"><?php echo $match_count; ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Active Teams</h3>
                        <p class="stat-value"><?php echo $team_count; ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-broadcast-tower"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Live Streams</h3>
                        <p class="stat-value"><?php echo $stream_count; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="content-grid">
                <div class="content-card featured-matches">
                    <div class="card-header">
                        <h2><i class="fas fa-gamepad"></i> Featured Matches</h2>
                        <a href="spectator/view_matches.php" class="view-all">View All</a>
                    </div>
                    <div class="card-content">
                        <?php
                        if ($upcoming_matches_result->num_rows > 0) {
                            while ($match = $upcoming_matches_result->fetch_assoc()) {
                                $matchDate = new DateTime($match['match_date']);
                                $isLive = (new DateTime() >= $matchDate) && (new DateTime() <= $matchDate->modify('+3 hours'));
                                $statusClass = $isLive ? 'live' : 'upcoming';
                                $statusText = $isLive ? 'LIVE' : $matchDate->format('H:i') . ' GMT';
                                
                                $scoreDisplay = '';
                                if ($isLive && $match['result']) {
                                    $scoreParts = explode(':', $match['result']);
                                    if (count($scoreParts) > 1) {
                                        $scores = explode('-', $scoreParts[1]);
                                        $team1Score = $scoreParts[0] == 'team1' ? $scores[0] : $scores[1];
                                        $team2Score = $scoreParts[0] == 'team1' ? $scores[1] : $scores[0];
                                        $scoreDisplay = "<span>{$team1Score}</span><span class='score-divider'>:</span><span>{$team2Score}</span>";
                                    }
                                } else {
                                    $scoreDisplay = "<span>VS</span>";
                                }
                        ?>
                        <div class="match">
                            <div class="match-status <?php echo $statusClass; ?>"><?php echo $statusText; ?></div>
                            <div class="match-teams">
                                <div class="team">
                                    <!-- <img src="<?php echo $team1Logo; ?>" alt="<?php echo $match['team1_name']; ?>" class="team-logo"> -->
                                    <span><?php echo $match['team1_name']; ?></span>
                                </div>
                                <div class="match-score">
                                    <?php echo $scoreDisplay; ?>
                                </div>
                                <div class="team">
                                    <!-- <img src="<?php echo $team2Logo; ?>" alt="<?php echo $match['team2_name']; ?>" class="team-logo"> -->
                                    <span><?php echo $match['team2_name']; ?></span>
                                </div>
                            </div>
                            <div class="match-info">
                                <span class="tournament-name"><?php echo $match['tournament_name']; ?></span>
                                <?php if ($isLive): ?>
                                    <?php
                                    $stream_check_query = "SELECT url FROM Stream WHERE match_id = " . $match['match_id'];
                                    $stream_check_result = $conn->query($stream_check_query);
                                    if ($stream_check_result->num_rows > 0) {
                                        $stream_url = $stream_check_result->fetch_assoc()['url'];
                                    ?>
                                        <a href="<?php echo $stream_url; ?>" target="_blank" class="watch-btn">
                                            <i class="fas fa-play-circle"></i> Watch
                                        </a>
                                    <?php } else { ?>
                                        <span class="no-stream">No Stream</span>
                                    <?php } ?>
                                <?php else: ?>
                                    <a href="#" class="remind-btn" data-match-id="<?php echo $match['match_id']; ?>">
                                        <i class="far fa-bell"></i> Remind
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<div class="no-data">No upcoming matches scheduled</div>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="content-card top-teams">
                    <div class="card-header">
                        <h2><i class="fas fa-users"></i> Top Teams</h2>
                        <a href="spectator/view_teams.php" class="view-all">View All</a>
                    </div>
                    <div class="card-content">
                        <?php
                        if ($top_teams_result->num_rows > 0) {
                            $rank = 1;
                            while ($team = $top_teams_result->fetch_assoc()) {
                                $teamLogo = !empty($team['logo']) ? 'uploads/team_logos/' . $team['logo'] : 'img/placeholder-team.png';
                        ?>
                        <div class="team-ranking">
                            <div class="rank"><?php echo $rank++; ?></div>
                            <div class="team-info">
                                <!-- <img src="<?php echo $teamLogo; ?>" alt="<?php echo $team['name']; ?>" class="team-logo"> -->
                                <span class="team-name"><?php echo $team['name']; ?></span>
                            </div>
                            <div class="team-record">
                                <span class="wins"><?php echo $team['wins']; ?></span>
                                <span class="record-divider">-</span>
                                <span class="losses"><?php echo $team['losses']; ?></span>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<div class="no-data">No team data available</div>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="content-card featured-players">
                    <div class="card-header">
                        <h2><i class="fas fa-user-astronaut"></i> Star Players</h2>
                        <a href="spectator/view_players.php" class="view-all">View All</a>
                    </div>
                    <div class="card-content">
                        <?php
                        if ($star_players_result->num_rows > 0) {
                            while ($player = $star_players_result->fetch_assoc()) {
                                $playerAvatar = 'img/player-default.png';
                                
                                $kdRatio = number_format($player['kd_ratio'], 1);
                                $winRate = number_format($player['avg_win_rate'], 0);
                        ?>
                        <div class="player-card">
                            <!-- <img src="<?php echo $playerAvatar; ?>" alt="<?php echo $player['username']; ?>" class="player-image"> -->
                            <div class="player-info">
                                <h3 class="player-name"><?php echo $player['username']; ?></h3>
                                <p class="player-team"><?php echo $player['team_name']; ?> - <?php echo $player['role']; ?></p>
                                <div class="player-stats">
                                    <div class="stat">
                                        <span class="stat-label">K/D</span>
                                        <span class="stat-value"><?php echo $kdRatio; ?></span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-label">Win Rate</span>
                                        <span class="stat-value"><?php echo $winRate; ?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<div class="no-data">No player data available</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/spectator_dashboard.js"></script>
</body>
</html>

<?php
$conn->close();
?>