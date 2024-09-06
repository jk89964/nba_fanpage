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

if ($match_id) {

    $sql = "DELETE FROM matches WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $match_id);
    
    if ($stmt->execute()) {

        header("Location: team.php?team_id=" . $team_id);
        exit();
    } else {
        echo "Błąd podczas usuwania meczu: " . $conn->error;
    }
}

$conn->close();
?>
