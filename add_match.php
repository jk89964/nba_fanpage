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


$team1_id = isset($_GET['team_id']) ? $_GET['team_id'] : null;


$team1_sql = "SELECT id, name FROM teams WHERE id = ?";
$team1_stmt = $conn->prepare($team1_sql);
$team1_stmt->bind_param("i", $team1_id);
$team1_stmt->execute();
$team1_result = $team1_stmt->get_result();

if ($team1_result->num_rows > 0) {
    $team1 = $team1_result->fetch_assoc();
} else {
    die("Nie znaleziono drużyny.");
}


$teams_sql = "SELECT id, name FROM teams WHERE id != ?";
$teams_stmt = $conn->prepare($teams_sql);
$teams_stmt->bind_param("i", $team1_id);
$teams_stmt->execute();
$teams_result = $teams_stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $team1 = $_POST['team1'];  
    $team2 = $_POST['team2'];  
    $date = $_POST['date'];
    $time = $_POST['time'];

    $sql = "INSERT INTO matches (team1_id, team2_id, date, time) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $team1, $team2, $date, $time);

    if ($stmt->execute()) {
        header("Location: team.php?team_id=" . $team1);
        exit();
    } else {
        echo "Błąd podczas dodawania meczu: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj Mecz - <?php echo htmlspecialchars($team1['name']); ?></title>
</head>
<body>
    <h1>Dodaj Nowy Mecz dla <?php echo htmlspecialchars($team1['name']); ?></h1>
    <form method="POST">
        <label for="team1">Drużyna 1 (Gospodarz):</label>
        <select name="team1" required>
            <option value="<?php echo $team1['id']; ?>" selected><?php echo htmlspecialchars($team1['name']); ?></option>
        </select><br>

        <label for="team2">Drużyna 2 (Gość):</label>
        <select name="team2" required>
            <?php while ($team = $teams_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($team['id']); ?>">
                    <?php echo htmlspecialchars($team['name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <label for="date">Data:</label>
        <input type="date" name="date" required><br>

        <label for="time">Czas:</label>
        <input type="time" name="time" required><br>

        <button type="submit">Dodaj Mecz</button>
    </form>
</body>
</html>
