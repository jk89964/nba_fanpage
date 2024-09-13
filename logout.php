<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wylogowano</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .logout-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logout-container h1 {
            color: #007bff;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .logout-container p {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .logout-container a {
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
        }
        .logout-container a:hover {
            background-color: #0056b3;
        }
    </style>
    <script>

        setTimeout(function(){
            window.location.href = 'index.php';
        }, 5000);
    </script>
</head>
<body>
    <div class="logout-container">
        <h1>Wylogowano pomyślnie</h1>
        <p>Zostałeś wylogowany. Za chwilę nastąpi przekierowanie na stronę główną.</p>
        <a href="index.php">Wróć na stronę główną</a>
    </div>
</body>
</html>
