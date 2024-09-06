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


$east_sql = "SELECT id, name, logo, wins, losses FROM teams WHERE conference = 'East' ORDER By wins DESC, losses ASC";
$east_result = $conn->query($east_sql);


$west_sql = "SELECT id, name, logo, wins, losses FROM teams WHERE conference = 'West' ORDER By wins DESC, losses ASC";
$west_result = $conn->query($west_sql);

$teams_sql = "SELECT id, name, logo FROM teams";
$teams_result = $conn->query($teams_sql);

$isAdmin = isset($_SESSION['username']) && $_SESSION['username'] === 'admin';


?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona Fanowska NBA - Tabela</title>
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

<div class="conference-table">
    <h2>Western Conference</h2>
    <table>
        <thead>
            <tr>
                <th>Logo</th>
                <th>Nazwa drużyny</th>
                <th>Wygrane</th>
                <th>Przegrane</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($west_team = $west_result->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?php echo $west_team['logo']; ?>" alt="<?php echo $west_team['name']; ?> Logo"></td>
                    <td><?php echo $west_team['name']; ?></td>
                    <td><?php echo $west_team['wins']; ?></td>
                    <td><?php echo $west_team['losses']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php if ($isAdmin): ?>
    <a href="edit_table.php?conference=west" class="edit-button">Edytuj tabelę</a>
    <?php endif; ?>
</div>


<div class="conference-table">
    <h2>Eastern Conference</h2>
    <table>
        <thead>
            <tr>
                <th>Logo</th>
                <th>Nazwa drużyny</th>
                <th>Wygrane</th>
                <th>Przegrane</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($east_team = $east_result->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?php echo $east_team['logo']; ?>" alt="<?php echo $east_team['name']; ?> Logo"></td>
                    <td><?php echo $east_team['name']; ?></td>
                    <td><?php echo $east_team['wins']; ?></td>
                    <td><?php echo $east_team['losses']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php if ($isAdmin): ?>
    <a href="edit_table.php?conference=east" class="edit-button">Edytuj tabelę</a>
    <?php endif; ?>
</div>

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
