<?php
session_start();
include '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'player') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
// Call the GetPlayerProfile function
$profile_sql = "SELECT GetPlayerProfile(?) AS profile";
$profile_stmt = $conn->prepare($profile_sql);
$profile_stmt->bind_param("i", $user_id);
$profile_stmt->execute();
$profile_result = $profile_stmt->get_result();
$profile_row = $profile_result->fetch_assoc();
$profile_text = $profile_row['profile'];

// Get player, team, and manager info
$sql = "
SELECT
    u.email AS player_email,
    p.username,
    p.join_date,
    p.role AS player_role,
    p.avg_rating,
    t.name AS team_name,
    t.creation_date,
    mu.email AS manager_email,
    mu.user_id AS manager_id
FROM Player p
JOIN User u ON p.user_id = u.user_id
LEFT JOIN Team t ON p.team_id = t.team_id
LEFT JOIN User mu ON t.manager_id = mu.user_id
WHERE u.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

$matchSql = "
SELECT COUNT(*) AS total_matches
FROM GameMatch
WHERE team1_id = (SELECT team_id FROM Player WHERE user_id = ?)
   OR team2_id = (SELECT team_id FROM Player WHERE user_id = ?)
";
$stmtMatch = $conn->prepare($matchSql);
$stmtMatch->bind_param("ii", $user_id, $user_id);
$stmtMatch->execute();
$matchRes = $stmtMatch->get_result();
$total_matches = $matchRes->fetch_assoc()['total_matches'] ?? 0;

$tourSql = "
SELECT COUNT(DISTINCT tournament_id) AS tournaments
FROM Stats
WHERE player_id = (SELECT player_id FROM Player WHERE user_id = ?)
";
$stmtTour = $conn->prepare($tourSql);
$stmtTour->bind_param("i", $user_id);
$stmtTour->execute();
$tourRes = $stmtTour->get_result();
$total_tournaments = $tourRes->fetch_assoc()['tournaments'] ?? 0;

$statsSql = "
SELECT 
    SUM(kills) AS total_kills, 
    SUM(deaths) AS total_deaths,
    AVG(win_rate) AS avg_win_rate
FROM Stats 
WHERE player_id = (SELECT player_id FROM Player WHERE user_id = ?)
";
$stmtStats = $conn->prepare($statsSql);
$stmtStats->bind_param("i", $user_id);
$stmtStats->execute();
$statsRes = $stmtStats->get_result();
$stats = $statsRes->fetch_assoc();

$kd_ratio = 0;
if (!empty($stats['total_deaths']) && $stats['total_deaths'] > 0) {
    $kd_ratio = round($stats['total_kills'] / $stats['total_deaths'], 2);
}

$avg_win_rate = round($stats['avg_win_rate'] ?? 0, 2);

$recentMatchesSql = "
SELECT 
    gm.match_date,
    gm.result,
    t1.name AS team1_name,
    t2.name AS team2_name,
    tr.name AS tournament_name
FROM GameMatch gm
JOIN Team t1 ON gm.team1_id = t1.team_id
JOIN Team t2 ON gm.team2_id = t2.team_id
JOIN Tournament tr ON gm.tournament_id = tr.tournament_id
WHERE (gm.team1_id = (SELECT team_id FROM Player WHERE user_id = ?)
    OR gm.team2_id = (SELECT team_id FROM Player WHERE user_id = ?))
ORDER BY gm.match_date DESC
LIMIT 5
";
$stmtRecentMatches = $conn->prepare($recentMatchesSql);
$stmtRecentMatches->bind_param("ii", $user_id, $user_id);
$stmtRecentMatches->execute();
$recentMatchesRes = $stmtRecentMatches->get_result();

$accuracy = 70;
$teamwork = 65;
$strategy = 75;
$reaction_time = 80;
$game_sense = 60;

$ratingPercentage = ($profile['avg_rating'] ?? 0) * 20;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Player Profile - <?= htmlspecialchars($profile['username'] ?? 'Player') ?></title>
    <link rel="stylesheet" href="../css_player/view_profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Orbitron:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="profile-container">
    <div class="profile-header">
        <div class="username"><?= htmlspecialchars($profile['username'] ?? 'Player') ?></div>
        <div class="role-badge"><?= htmlspecialchars($profile['player_role'] ?? 'Player') ?></div>
        <h2>Player Profile</h2>
    </div>
    
    <div class="profile-content">
        <div class="profile-section">
            <h2>Player Information</h2>
            <?php if (!empty($profile_text)): ?>
                <pre><?= htmlspecialchars($profile_text) ?></pre>
            <?php endif; ?>
            <p><strong>Username:</strong> <span><?= htmlspecialchars($profile['username'] ?? 'N/A') ?></span></p>
            <p><strong>Email:</strong> <span><?= htmlspecialchars($profile['player_email'] ?? 'N/A') ?></span></p>
            <p><strong>Role:</strong> <span><?= htmlspecialchars($profile['player_role'] ?? 'N/A') ?></span></p>
            <p><strong>Join Date:</strong> <span><?= htmlspecialchars($profile['join_date'] ?? 'N/A') ?></span></p>
            <p><strong>Rating:</strong> <span><?= htmlspecialchars(number_format($profile['avg_rating'] ?? 0, 2)) ?>/5.00</span></p>
            
            <div class="rating-meter">
                <div class="rating-fill" style="width: <?= $ratingPercentage ?>%;"></div>
                <div class="rating-value"><?= htmlspecialchars(number_format($profile['avg_rating'] ?? 0, 2)) ?>/5.00</div>
            </div>
            
            <div class="profile-meta">
                <div class="meta-item">
                    <div class="meta-value"><?= $total_matches ?></div>
                    <div class="meta-label">Matches</div>
                </div>
                <div class="meta-item">
                    <div class="meta-value"><?= $total_tournaments ?></div>
                    <div class="meta-label">Tournaments</div>
                </div>
                <div class="meta-item">
                    <div class="meta-value"><?= number_format($kd_ratio, 2) ?></div>
                    <div class="meta-label">K/D Ratio</div>
                </div>
            </div>
        </div>
        
        <div class="profile-section team-section">
            <h2>Team Information</h2>
            <p><strong>Team Name:</strong> <span><?= htmlspecialchars($profile['team_name'] ?? 'N/A') ?></span></p>
            <p><strong>Team Creation Date:</strong> <span><?= htmlspecialchars($profile['creation_date'] ?? 'N/A') ?></span></p>
            <p><strong>Manager Email:</strong> <span><?= htmlspecialchars($profile['manager_email'] ?? 'N/A') ?></span></p>
        </div>
        
        <div class="profile-section stats-section">
            <h2>Player Statistics</h2>
            <p><strong>Total Kills:</strong> <span><?= number_format($stats['total_kills'] ?? 0) ?></span></p>
            <p><strong>Total Deaths:</strong> <span><?= number_format($stats['total_deaths'] ?? 0) ?></span></p>
            <p><strong>K/D Ratio:</strong> <span><?= number_format($kd_ratio, 2) ?></span></p>
            <p><strong>Win Rate:</strong> <span><?= number_format($avg_win_rate, 2) ?>%</span></p>
            <p><strong>Total Matches Played:</strong> <span><?= $total_matches ?></span></p>
            <p><strong>Total Tournaments:</strong> <span><?= $total_tournaments ?></span></p>
            
            <div class="stats-chart-container">
                <div class="chart-title">Performance Analytics</div>
                <div class="stats-chart">
                    <canvas id="statsChart"></canvas>
                </div>
            </div>
            
            <div class="radar-chart-container">
                <div class="chart-title">Skill Analysis</div>
                <canvas id="radarChart"></canvas>
            </div>
        </div>
        
        <!-- Recent Matches Section
        <div class="profile-section">
            <h2>Recent Matches</h2>
            <div class="match-history">
                <?php if ($recentMatchesRes->num_rows > 0): ?>
                    <?php while ($match = $recentMatchesRes->fetch_assoc()): 
                        $resultClass = "";
                        if (strpos(strtolower($match['result']), 'win') !== false) {
                            $resultClass = "result-win";
                        } elseif (strpos(strtolower($match['result']), 'loss') !== false || strpos(strtolower($match['result']), 'lose') !== false) {
                            $resultClass = "result-loss";
                        } else {
                            $resultClass = "result-draw";
                        }
                    ?>
                        <div class="match-item">
                            <div class="match-date"><?= htmlspecialchars(date('M d, Y', strtotime($match['match_date']))) ?></div>
                            <div class="match-teams">
                                <?= htmlspecialchars($match['team1_name']) ?> vs <?= htmlspecialchars($match['team2_name']) ?>
                            </div>
                            <div class="match-tournament"><?= htmlspecialchars($match['tournament_name']) ?></div>
                            <div class="match-result <?= $resultClass ?>"><?= htmlspecialchars($match['result']) ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No recent matches found.</p>
                <?php endif; ?>
            </div>
        </div> -->
        
        <div class="profile-section achievements-section">
            <h2>Achievements</h2>
            <div class="achievements-grid">
                <div class="achievement-badge">
                    <div class="badge-icon"><i class="fas fa-trophy"></i></div>
                    <div class="badge-name">Tournament Champion</div>
                    <div class="badge-desc">Won a tournament</div>
                </div>
                <div class="achievement-badge">
                    <div class="badge-icon"><i class="fas fa-fire"></i></div>
                    <div class="badge-name">Killing Spree</div>
                    <div class="badge-desc">5+ kills in a match</div>
                </div>
                <div class="achievement-badge">
                    <div class="badge-icon"><i class="fas fa-medal"></i></div>
                    <div class="badge-name">MVP</div>
                    <div class="badge-desc">Match MVP award</div>
                </div>
                <div class="achievement-badge">
                    <div class="badge-icon"><i class="fas fa-star"></i></div>
                    <div class="badge-name">All-Star</div>
                    <div class="badge-desc">Top rated player</div>
                </div>
            </div>
        </div>
        
        <a href="../player_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('statsChart').getContext('2d');
        const data = {
            labels: ['Kills', 'Deaths', 'K/D Ratio', 'Win Rate'],
            datasets: [{
                label: 'Player Stats',
                data: [
                    <?= ($stats['total_kills'] ?? 0) ?>, 
                    <?= ($stats['total_deaths'] ?? 0) ?>, 
                    <?= $kd_ratio ?>, 
                    <?= $avg_win_rate ?>
                ],
                backgroundColor: [
                    'rgba(33, 150, 243, 0.6)',
                    'rgba(255, 87, 34, 0.6)',
                    'rgba(123, 31, 162, 0.6)',
                    'rgba(76, 175, 80, 0.6)'
                ],
                borderColor: [
                    'rgba(33, 150, 243, 1)',
                    'rgba(255, 87, 34, 1)',
                    'rgba(123, 31, 162, 1)',
                    'rgba(76, 175, 80, 1)'
                ],
                borderWidth: 2
            }]
        };
        
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        };
        
        new Chart(ctx, {
            type: 'bar',
            data: data,
            options: options
        });
        
        // Radar chart
        const radarCtx = document.getElementById('radarChart').getContext('2d');
        
        const radarData = {
            labels: ['Accuracy', 'Teamwork', 'Strategy', 'Reaction Time', 'Game Sense'],
            datasets: [{
                label: 'Skills',
                data: [
                    <?= $accuracy ?>, 
                    <?= $teamwork ?>, 
                    <?= $strategy ?>, 
                    <?= $reaction_time ?>, 
                    <?= $game_sense ?>
                ],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
            }]
        };
        
        const radarOptions = {
            responsive: true,
            maintainAspectRatio: false,
            elements: {
                line: {
                    borderWidth: 3
                }
            },
            scales: {
                r: {
                    angleLines: {
                        color: 'rgba(255, 255, 255, 0.2)'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.2)'
                    },
                    pointLabels: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)',
                        backdropColor: 'transparent',
                        font: {
                            size: 10
                        }
                    },
                    suggestedMin: 0,
                    suggestedMax: 100
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        };
        
        new Chart(radarCtx, {
            type: 'radar',
            data: radarData,
            options: radarOptions
        });
    });
</script>
</body>
</html>