<?php
session_start();
include 'connection.php';
include 'check_login.php';

// Greeting changes with the time of day
$hour = date("G");
if ($hour < 12) {
  $greeting = "Good Morning";
} elseif ($hour < 17) {
  $greeting = "Good Afternoon";
} else {
  $greeting = "Good Evening";
}

// Newest books first
$new_books = $conn->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 9");

// Most borrowed books first
$popular_books = $conn->query("SELECT books.*, (SELECT COUNT(*) FROM borrows WHERE borrows.book_id = books.id) AS times_borrowed
                               FROM books ORDER BY times_borrowed DESC, reviews_count DESC LIMIT 12");

// Best rated books first
$recommended_books = $conn->query("SELECT * FROM books ORDER BY rating DESC LIMIT 12");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BOOKSHELF</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="./Css/style.css" />
  </head>

  <body>
    <?php include 'header.php'; ?>
    <main>
      <h1><?php echo $greeting; ?>, <span><?php echo htmlspecialchars($first_name); ?></span></h1>

      <div class="up-slider">
        <div class="text-slide">
          <h2>Today’s Quote</h2>
          <p>
            “There is more treasure in books than in all the pirate’s loot on
            Treasure Island.” -Walt Disney
          </p>
          <div class="slide-buttons">
            <button id="prev-btn">
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button id="next-btn">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </div>
        <div class="arr-slide">
          <p>New Arrivals</p>
          <div class="arr-img">
            <?php while ($book = $new_books->fetch_assoc()) { ?>
              <a href="./bookdetails.php?id=<?php echo $book['id']; ?>">
                <img src="./Assets/Images/<?php echo $book['cover']; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" />
              </a>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="popular-books">
        <div class="popular-header">
          <h2>Popular Books</h2>
          <a href="./allbooks.php">Show All</a>
        </div>
        <div class="book-cards">
          <?php while ($book = $popular_books->fetch_assoc()) { ?>
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

      <div class="recommended-books">
        <div class="popular-header">
          <h2>Recommended Books</h2>
          <a href="./allbooks.php">Show All</a>
        </div>
        <div class="book-cards">
          <?php while ($book = $recommended_books->fetch_assoc()) { ?>
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
