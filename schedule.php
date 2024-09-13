<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "nba_fanpage";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$team_id = isset($_GET['team_id']) ? $_GET['team_id'] : null;

if (!$team_id) {
    header("Location: index.php");
    exit();
}


$team_sql = "SELECT * FROM teams WHERE id = ?";
$stmt = $conn->prepare($team_sql);
$stmt->bind_param("i", $team_id);
$stmt->execute();
$team_result = $stmt->get_result();
$team = $team_result->fetch_assoc();

if (!$team) {
    header("Location: index.php");
    exit();
}


$games_sql = "SELECT * FROM games WHERE team_id = ? ORDER BY date";
$stmt = $conn->prepare($games_sql);
$stmt->bind_param("i", $team_id);
$stmt->execute();
$games_result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $team['name']; ?> Harmonogram</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Strona Główna</a></li>
                <li><a href="kalendarz.php">Kalendarz</a></li>
                <li><a href="zawodnicy.php">Zawodnicy</a></li>
                <li><a href="tabela.php">Tabela</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <main class="main-content">
            <h2>Harmonogram meczów <?php echo $team['name']; ?></h2>

            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Przeciwnik</th>
                        <th>Godzina</th>
                        <th>Punkty</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php while ($game = $games_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('D, M j', strtotime($game['date'])); ?></td>
                            <td><?php echo $game['opponent']; ?></td>
                            <td><?php echo date('g:i A', strtotime($game['time'])); ?></td>
                            <td><?php echo $game['points']; ?></td>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>

</body>
</html>

<?php
$conn->close();
?>
