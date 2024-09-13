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
$order_by = 'players.name ASC';
if (isset($_GET['sort_by'])) {
    if ($_GET['sort_by'] == 'team') {
        $order_by = 'teams.name ASC';
    } elseif ($_GET['sort_by'] == 'position') {
        $order_by = 'players.position ASC';
    }
}
$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $search_query = "AND players.name LIKE '%$search%'";
}

$players_sql = "SELECT players.id, players.name AS player_name, players.position, players.number, teams.name AS team_name, teams.logo 
                FROM players 
                INNER JOIN teams ON players.team_id = teams.id
                WHERE 1=1 $search_query
                ORDER BY $order_by";
$players_result = $conn->query($players_sql);

$teams_sql = "SELECT id, name, logo FROM teams";
$teams_result = $conn->query($teams_sql);

$isAdmin = isset($_SESSION['username']) && $_SESSION['username'] === 'admin';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona Fanowska NBA - Zawodnicy</title>
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
                <span>Witaj, <?php echo $_SESSION['username']; ?>!</span>
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
                    <?php while($team = $teams_result->fetch_assoc()): ?>
                        <li>
                            <a href="team.php?team_id=<?php echo $team['id']; ?>" class="team-link">
                                <div class="game-info">
                                    <img src="<?php echo $team['logo']; ?>" alt="<?php echo $team['name']; ?> Logo" class="team-logo">
                                    <span class="team-name"><?php echo $team['name']; ?></span>
                                </div>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </aside>


        <main class="main-content">
            <div class="title-bar">
    <h2>Zawodnicy</h2>

    <form action="zawodnicy.php" method="GET" class="search-form">
        <input type="text" name="search" placeholder="Wyszukaj zawodnika..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        <button type="submit">Szukaj</button>
    </form>
    <div class="sort-buttons">
        <a href="zawodnicy.php?sort_by=team" class="sort-button">Sortuj wg Drużyny</a>
        <a href="zawodnicy.php?sort_by=position" class="sort-button">Sortuj wg Pozycji</a>
    </div>
</div>
            <table>
                <thead>
                    <tr>
                        <th>Logo Drużyny</th>
                        <th>Nazwa Zawodnika</th>
                        <th>Numer</th>
                        <th>Pozycja</th>
                        <th>Drużyna</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($player = $players_result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="<?php echo $player['logo']; ?>" alt="<?php echo $player['team_name']; ?> Logo"></td>
                            <td><?php echo $player['player_name']; ?></td>
                            <td><?php echo $player['number']; ?></td>
                            <td><?php echo $player['position']; ?></td>
                            <td><?php echo $player['team_name']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            
        </main>
    </div>

    <footer>
        <p>&copy; 2024 Strona Fanowska NBA</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
