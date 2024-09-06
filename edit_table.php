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


$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

$conference = isset($_GET['conference']) ? $_GET['conference'] : '';


$sql = "SELECT id, name, logo, wins, losses FROM teams WHERE conference = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $conference);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($_POST['teams'] as $team_id => $team_data) {
        $wins = $team_data['wins'];
        $losses = $team_data['losses'];

        $update_sql = "UPDATE teams SET wins = ?, losses = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("iii", $wins, $losses, $team_id);
        $stmt->execute();
    }


    header("Location: tabela.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj Tabelę</title>
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
        <main class="main-content">
            <h2>Edytuj Tabelę - <?php echo ucfirst($conference); ?> Conference</h2>
            <form action="edit_table.php?conference=<?php echo htmlspecialchars($conference); ?>" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Nazwa drużyny</th>
                            <th>Wygrane</th>
                            <th>Porażki</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($team = $result->fetch_assoc()): ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars($team['logo']); ?>" alt="<?php echo htmlspecialchars($team['name']); ?> Logo" class="team-logo"></td>
                                <td><?php echo htmlspecialchars($team['name']); ?></td>
                                <td><input type="text" name="teams[<?php echo $team['id']; ?>][wins]" value="<?php echo htmlspecialchars($team['wins']); ?>"></td>
                                <td><input type="text" name="teams[<?php echo $team['id']; ?>][losses]" value="<?php echo htmlspecialchars($team['losses']); ?>"></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <button type="submit">Zapisz zmiany</button>
            </form>
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
