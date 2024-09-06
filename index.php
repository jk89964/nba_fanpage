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


$teams_sql = "SELECT id, name, logo FROM teams";
$teams_result = $conn->query($teams_sql);


$likedArticles = [];
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $likes_sql = "SELECT article_id FROM likes WHERE username='$username' AND liked=1";
    $likes_result = $conn->query($likes_sql);

    while ($row = $likes_result->fetch_assoc()) {
        $likedArticles[] = $row['article_id'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona Fanowska NBA</title>
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
                    <?php if ($teams_result->num_rows > 0): ?>
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
                    <?php else: ?>
                        <li>Brak drużyn w bazie danych.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </aside>


        <main class="main-content">
        <div class="content-box">
    <img src="news/lebronmvp.webp" alt="News Image" class="box-image">
    <div class="box-content">
        <h3>Lebron zdobył tytuł MVP w zeszłym roku</h3>
        <button class="like-btn <?php if (in_array(1, $likedArticles)) echo 'liked'; ?>" data-article-id="1" data-liked="<?php if (in_array(1, $likedArticles)) echo 'true'; else echo 'false'; ?>">Lubię to</button>
    </div>
</div>

<div class="content-box">
    <img src="news/nbafantasy.webp" alt="Stats Image" class="box-image">
    <div class="box-content">
        <h3>NBA Fantasy</h3>
        <p>Patrząc na rozgrywających zmierzających w kierunku 2024-25, jest jasne, że absolutnie nie brakuje talentu. Poniżej znajduje się 35 najlepszych rozgrywających NBA sklasyfikowanych według poziomów.</p>
        <button class="like-btn <?php if (in_array(2, $likedArticles)) echo 'liked'; ?>" data-article-id="2" data-liked="<?php if (in_array(2, $likedArticles)) echo 'true'; else echo 'false'; ?>">Lubię to</button>
    </div>
</div>

<div class="content-box">
    <img src="images/history.jpg" alt="History Image" class="box-image">
    <div class="box-content">
        <h3>Historia Ligi</h3>
        <p>Poznaj historię NBA, największe momenty i legendarne mecze.</p>
        <button class="like-btn <?php if (in_array(3, $likedArticles)) echo 'liked'; ?>" data-article-id="3" data-liked="<?php if (in_array(3, $likedArticles)) echo 'true'; else echo 'false'; ?>">Lubię to</button>
    </div>
</div>

        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>
