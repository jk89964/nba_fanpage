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


$match_id = isset($_GET['match_id']) ? $_GET['match_id'] : null;
$team_id = isset($_GET['team_id']) ? $_GET['team_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $date = $_POST['date'];
    $team1 = $_POST['team1'];  
    $team2 = $_POST['team2'];  
    $time = $_POST['time'];
    $points = $_POST['points'];

    $sql = "UPDATE matches SET date = ?, team1_id = ?, team2_id = ?, time = ?, points = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siissi", $date, $team1, $team2, $time, $points, $match_id);
    $stmt->execute();

   
    header("Location: team.php?team_id=" . $team_id);
    exit();
}


if ($match_id) {
    $sql = "SELECT * FROM matches WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $match = $stmt->get_result()->fetch_assoc();
} else {
    echo "Nie wybrano meczu.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj Mecz</title>
</head>
<body>
    <h2>Edytuj mecz</h2>

    <form action="edit_match.php?match_id=<?php echo $match_id; ?>&team_id=<?php echo $team_id; ?>" method="POST">
        <label for="date">Data:</label>
        <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($match['date']); ?>" required><br>

        <label for="team1">Drużyna 1:</label>
        <input type="text" name="team1" id="team1" value="<?php echo htmlspecialchars($match['team1_id']); ?>" required><br>

        <label for="team2">Drużyna 2:</label>
        <input type="text" name="team2" id="team2" value="<?php echo htmlspecialchars($match['team2_id']); ?>" required><br>

        <label for="time">Czas:</label>
        <input type="time" name="time" id="time" value="<?php echo htmlspecialchars($match['time']); ?>" required><br>

        <label for="points">Punkty:</label>
        <input type="text" name="points" id="points" value="<?php echo htmlspecialchars($match['points']); ?>"><br>

        <button type="submit">Zapisz zmiany</button>
    </form>
</body>
</html>
