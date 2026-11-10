<?php
session_start();
include 'connection.php';
include 'check_login.php';

$books = $conn->query("SELECT * FROM books ORDER BY title");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BOOKSHELF</title>

    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    />
    <link rel="stylesheet" href="./Css/style.css" />
    <link rel="stylesheet" href="./Css/allbooks.css">
  </head>

  <body>
    <?php include 'header.php'; ?>
    <main>

      <div class="popular-books">
        <div class="popular-header">
          <h2>All Books</h2>
        </div>
        <div class="book-cards">
          <?php while ($book = $books->fetch_assoc()) { ?>
            <div class="book-card">
              <a href="./bookdetails.php?id=<?php echo $book['id']; ?>">
                <img src="./Assets/Images/<?php echo $book['cover']; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" />
              </a>
              <h3><?php echo htmlspecialchars($book['title']); ?></h3>
              <p><?php echo htmlspecialchars($book['author']); ?></p>
            </div>
          <?php } ?>
        </div>
      </div>
    </main>

    <script src="./Js/index.js"></script>
  </body>
</html>
