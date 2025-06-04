<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'team_manager') {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

$manager_id = $_SESSION['user_id'];

$query_team = "SELECT t.team_id, t.name, COUNT(p.player_id) AS player_count
               FROM Team t 
               LEFT JOIN Player p ON t.team_id = p.team_id 
               WHERE t.manager_id = ?
               GROUP BY t.team_id";
$stmt_team = $conn->prepare($query_team);
$stmt_team->bind_param("i", $manager_id);
$stmt_team->execute();
$result_team = $stmt_team->get_result();
$team = $result_team->fetch_assoc();

$query_matches = "SELECT gm.match_id, gm.match_date, t1.name AS team1, t2.name AS team2, 
                  gm.team1_id, gm.team2_id, gm.result
                  FROM GameMatch gm
                  JOIN Team t1 ON gm.team1_id = t1.team_id
                  JOIN Team t2 ON gm.team2_id = t2.team_id
                  WHERE (gm.team1_id = ? OR gm.team2_id = ?) AND gm.match_date > NOW()
                  ORDER BY gm.match_date LIMIT 3";
$stmt_matches = $conn->prepare($query_matches);
$stmt_matches->bind_param("ii", $team['team_id'], $team['team_id']);
$stmt_matches->execute();
$result_matches = $stmt_matches->get_result();

$query_stats = "SELECT 
                SUM(CASE WHEN 
                    (gm.team1_id = ? AND gm.result = 'team1_win') OR 
                    (gm.team2_id = ? AND gm.result = 'team2_win') 
                    THEN 1 ELSE 0 END) AS wins,
                SUM(CASE WHEN 
                    (gm.team1_id = ? AND gm.result = 'team2_win') OR 
                    (gm.team2_id = ? AND gm.result = 'team1_win') 
                    THEN 1 ELSE 0 END) AS losses,
                SUM(CASE WHEN gm.result = 'draw' THEN 1 ELSE 0 END) AS draws
                FROM GameMatch gm
                WHERE (gm.team1_id = ? OR gm.team2_id = ?) AND gm.result IS NOT NULL";
$stmt_stats = $conn->prepare($query_stats);
$stmt_stats->bind_param("iiiiii", $team['team_id'], $team['team_id'], $team['team_id'], $team['team_id'], $team['team_id'], $team['team_id']);
$stmt_stats->execute();
$result_stats = $stmt_stats->get_result();
$stats = $result_stats->fetch_assoc();

$wins = $stats['wins'] ?? 0;
$losses = $stats['losses'] ?? 0;
$draws = $stats['draws'] ?? 0;

$query_top_players = "SELECT p.player_id, p.username, p.role, p.avg_rating 
                      FROM Player p
                      WHERE p.team_id = ?
                      ORDER BY p.avg_rating DESC
                      LIMIT 3";
$stmt_top_players = $conn->prepare($query_top_players);
$stmt_top_players->bind_param("i", $team['team_id']);
$stmt_top_players->execute();
$result_top_players = $stmt_top_players->get_result();

$query_tournaments = "SELECT t.tournament_id, t.name, t.start_date, 
                     (SELECT COUNT(*) + 1 FROM Prize p2 
                      WHERE p2.tournament_id = t.tournament_id 
                      AND p2.amount > p1.amount) AS placement
                     FROM Tournament t
                     JOIN Prize p1 ON t.tournament_id = p1.tournament_id
                     WHERE p1.team_id = ? AND t.end_date < NOW()
                     ORDER BY t.end_date DESC
                     LIMIT 3";
$stmt_tournaments = $conn->prepare($query_tournaments);
$stmt_tournaments->bind_param("i", $team['team_id']);
$stmt_tournaments->execute();
$result_tournaments = $stmt_tournaments->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esports Team Manager Dashboard</title>
    <link rel="stylesheet" href="css/manager_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/manager_dashboard.js" defer></script>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="team-logo">
                <div class="logo-placeholder"></div>
                <h2><?php echo $team['name'] ?? 'Team Manager'; ?></h2>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="manager_dashboard.php"><i class="icon dashboard-icon"></i>Dashboard</a></li>
                    <li><a href="manager/manage_your_team.php"><i class="icon team-icon"></i>Manage Team</a></li>
                    <li><a href="manager/view_schedules.php"><i class="icon schedule-icon"></i>Schedules</a></li>
                    <li><a href="manager/reports.php"><i class="icon reports-icon"></i>Reports</a></li>
                    <li class="logout"><a href="logout.php"><i class="icon logout-icon"></i>Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="dashboard-header">
                <div class="welcome-section">
                    <h1>Welcome, <span class="manager-name"><?php echo $_SESSION['email']; ?></span></h1>
                    <h3>Your User ID is <span class="manager-name"><?php echo $_SESSION['user_id']; ?></span></h3>
                    <p class="current-date"><?php echo date("l, F j, Y"); ?></p>
                </div>
                <div class="quick-actions">
                    <button class="action-button" id="addPlayerBtn">Add Player</button>
                </div>
            </header>

            <div class="dashboard-grid">
                <section class="dashboard-card team-stats">
                    <div class="card-header">
                        <h2>Team Statistics</h2>
                    </div>
                    <div class="card-content">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $team['player_count'] ?? 0; ?></div>
                            <div class="stat-label">Players</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $wins; ?></div>
                            <div class="stat-label">Wins</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $losses; ?></div>
                            <div class="stat-label">Losses</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $draws; ?></div>
                            <div class="stat-label">Draws</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value win-ratio"><?php echo ($wins + $losses > 0) ? round(($wins / ($wins + $losses)) * 100) : 0; ?>%</div>
                            <div class="stat-label">Win Rate</div>
                        </div>
                    </div>
                </section>

                <section class="dashboard-card upcoming-matches">
                    <div class="card-header">
                        <h2>Upcoming Matches</h2>
                        <a href="manager/view_schedules.php" class="view-all">View All</a>
                    </div>
                    <div class="card-content match-list">
                        <?php if ($result_matches && $result_matches->num_rows > 0): ?>
                            <?php while ($match = $result_matches->fetch_assoc()): ?>
                                <div class="match-item">
                                    <div class="match-date">
                                        <div class="date"><?php echo date("M j", strtotime($match['match_date'])); ?></div>
                                        <div class="time"><?php echo date("g:i A", strtotime($match['match_date'])); ?></div>
                                    </div>
                                    <div class="match-teams">
                                        <div class="team <?php echo ($match['team1_id'] == $team['team_id']) ? 'our-team' : ''; ?>">
                                            <?php echo $match['team1']; ?>
                                        </div>
                                        <div class="vs">VS</div>
                                        <div class="team <?php echo ($match['team2_id'] == $team['team_id']) ? 'our-team' : ''; ?>">
                                            <?php echo $match['team2']; ?>
                                        </div>
                                    </div>
                                    <div class="match-status">
                                        <span class="status-indicator scheduled">Scheduled</span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="no-matches">No upcoming matches scheduled</div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="dashboard-card team-performance">
                    <div class="card-header">
                        <h2>Performance Analytics</h2>
                    </div>
                    <div class="card-content">
                        <div class="chart-container">
                            <canvas id="teamPerformanceChart"></canvas>
                        </div>
                    </div>
                </section>

                <section class="dashboard-card top-players">
                    <div class="card-header">
                        <h2>Top Performers</h2>
                        <a href="manager/manage_your_team.php" class="view-all">View All</a>
                    </div>
                    <div class="card-content player-list">
                        <?php if ($result_top_players && $result_top_players->num_rows > 0): ?>
                            <?php while ($player = $result_top_players->fetch_assoc()): ?>
                                <div class="player-item">
                                    <div class="player-avatar">
                                        <div class="avatar-placeholder"></div>
                                    </div>
                                    <div class="player-info">
                                        <div class="player-name"><?php echo $player['username']; ?></div>
                                        <div class="player-position"><?php echo $player['role']; ?></div>
                                    </div>
                                    <div class="player-rating">
                                        <div class="rating-value"><?php echo number_format($player['avg_rating'], 1); ?></div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="no-players">No player data available</div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="dashboard-card recent-tournaments">
                    <div class="card-header">
                        <h2>Recent Tournaments</h2>
                        <a href="manager/reports.php" class="view-all">View All</a>
                    </div>
                    <div class="card-content tournament-list">
                        <?php if ($result_tournaments && $result_tournaments->num_rows > 0): ?>
                            <?php while ($tournament = $result_tournaments->fetch_assoc()): ?>
                                <div class="tournament-item">
                                    <div class="tournament-info">
                                        <div class="tournament-name"><?php echo $tournament['name']; ?></div>
                                        <div class="tournament-date"><?php echo date("M j, Y", strtotime($tournament['start_date'])); ?></div>
                                    </div>
                                    <div class="tournament-placement placement-<?php echo $tournament['placement']; ?>">
                                        <?php 
                                        switch ($tournament['placement']) {
                                            case 1:
                                                echo "1<sup>st</sup>";
                                                break;
                                            case 2:
                                                echo "2<sup>nd</sup>";
                                                break;
                                            case 3:
                                                echo "3<sup>rd</sup>";
                                                break;
                                            default:
                                                echo $tournament['placement'] . "<sup>th</sup>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="no-tournaments">No tournament data available</div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>