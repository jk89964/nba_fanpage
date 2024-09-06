<?php
session_start();


if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$articleId = $input['article_id'];
$action = $input['action'];
$username = $_SESSION['username'];

$servername = "localhost";
$dbUsername = "root"; 
$dbPassword = "";
$dbname = "nba_fanpage";


$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($action === "like") {
    $stmt = $conn->prepare("INSERT INTO likes (username, article_id, liked) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE liked = 1");
} else {
    $stmt = $conn->prepare("UPDATE likes SET liked = 0 WHERE username = ? AND article_id = ?");
}

$stmt->bind_param("si", $username, $articleId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Operation failed']);
}

$stmt->close();
$conn->close();
?>
