<?php
session_start();


$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "nba_fanpage";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$team_id = isset($_GET['team_id']) ? $_GET['team_id'] : null;

if ($team_id) {

    $sql = "SELECT name, logo FROM teams WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $team = $result->fetch_assoc();
    } else {
        echo "Nie znaleziono drużyny.";
        exit();
    }
} else {
    echo "Nie wybrano drużyny.";
    exit();
}

$matches_sql = "SELECT * FROM matches WHERE team1_id = ? OR team2_id = ?";
$matches_stmt = $conn->prepare($matches_sql);
$matches_stmt->bind_param("ii", $team_id, $team_id);
$matches_stmt->execute();
$matches_result = $matches_stmt->get_result();


$isAdmin = isset($_SESSION['username']) && $_SESSION['username'] === 'admin';

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($team['name']); ?> - Strona drużyny</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Strona Główna</a></li>
                <li><a href="zawodnicy.php">Zawodnicy</a></li>
                <li><a href="tabela.php">Tabela</a></li>
            </ul>
        </nav>
        <div class="header-right">
            <?php if (isset($_SESSION['username'])): ?>
                <span>Witaj, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php">Wyloguj się</a>
            <?php else: ?>
                <a href="login.php">Zaloguj się</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
  
        <aside class="left-sidebar">
            <div class="schedule-container">
                <h3>Drużyny</h3>
                <ul class="schedule-list">
                    <?php
             
                    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
                    $teams_sql = "SELECT id, name, logo FROM teams";
                    $teams_result = $conn->query($teams_sql);

                    if ($teams_result->num_rows > 0): 
                        while($team_sidebar = $teams_result->fetch_assoc()): ?>
                            <li>
                                <a href="team.php?team_id=<?php echo htmlspecialchars($team_sidebar['id']); ?>" class="team-link">
                                    <div class="game-info">
                                        <img src="<?php echo htmlspecialchars($team_sidebar['logo']); ?>" alt="<?php echo htmlspecialchars($team_sidebar['name']); ?> Logo" class="team-logo">
                                        <span class="team-name"><?php echo htmlspecialchars($team_sidebar['name']); ?></span>
                                    </div>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li>Brak drużyn w bazie danych.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </aside>


        <main class="main-content">
            <div class="team-header">
                <img src="<?php echo htmlspecialchars($team['logo']); ?>" alt="<?php echo htmlspecialchars($team['name']); ?> Logo" class="team-page-logo">
                <h2><?php echo htmlspecialchars($team['name']); ?></h2>
            </div>
            <h3>Regular Season</h3>

      
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Opponent</th>
                        <th>Time</th>
                        <th>Points</th>
                        <?php if ($isAdmin): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($matches_result->num_rows > 0): ?>
                        <?php while ($match = $matches_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($match['date']); ?></td>
                                <td>
                                    <?php 
                                   
                                    if ($match['team1_id'] == $team_id) {
                                        $opponent_id = $match['team2_id'];
                                    } else {
                                        $opponent_id = $match['team1_id'];
                                    }

                              
                                    $opponent_sql = "SELECT name FROM teams WHERE id = ?";
                                    $opponent_stmt = $conn->prepare($opponent_sql);
                                    $opponent_stmt->bind_param("i", $opponent_id);
                                    $opponent_stmt->execute();
                                    $opponent_result = $opponent_stmt->get_result();
                                    if ($opponent_result->num_rows > 0) {
                                        $opponent = $opponent_result->fetch_assoc();
                                        echo htmlspecialchars($opponent['name']);
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($match['time']); ?></td>
                                <td><?php echo htmlspecialchars($match['points']); ?></td>
                                <?php if ($isAdmin): ?>
                                    <td>
                                        <a href="edit_match.php?match_id=<?php echo $match['id']; ?>&team_id=<?php echo $team_id; ?>">Edytuj</a> | 
                                        <a href="delete_match.php?match_id=<?php echo $match['id']; ?>&team_id=<?php echo $team_id; ?>" onclick="return confirm('Czy na pewno chcesz usunąć ten mecz?')">Usuń</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Brak meczów w bazie danych.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

     
            <?php if ($isAdmin): ?>
                <a href="add_match.php?team_id=<?php echo $team_id; ?>" class="add-match-btn">Dodaj mecz</a>
            <?php endif; ?>
        </main>
    </div>

    <footer>
        <p>&copy; 2024 Strona Fanowska NBA</p>
    </footer>
</body>
</html>
