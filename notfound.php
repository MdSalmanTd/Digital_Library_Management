<?php
session_start();
include 'connection.php';
include 'check_login.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Found</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="./Css/style.css">
    <link rel="stylesheet" href="./Css/notfound.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <div class="notfound-container">
            <h1>No Results</h1>
            <img src="./Assets/Images/dribbble_1.gif" alt="404 Not Found">
            <h2>Book Not Found</h2>
            <p>Sorry, the book you are looking for does not exist.</p>
            <a href="./index.php" class="home-button">Go to Home</a>
        </div>
    </main>
    <script src="./Js/index.js"></script>
</body>
</html>
