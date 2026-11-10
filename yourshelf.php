<?php
session_start();
include 'connection.php';
include 'check_login.php';

// The tabs at the top of the page
$tabs = [
  "all" => "All Books",
  "favorites" => "Favorites",
  "borrowed" => "Borrowed Books",
  "ebooks" => "E-Books",
  "audio" => "Audio Books",
  "articles" => "Articles & Journals",
];

$tab = isset($_GET['tab']) ? $_GET['tab'] : "all";
if (!isset($tabs[$tab])) {
  $tab = "all";
}

// Every book this user has borrowed, with the loan details
$borrow_sql = "SELECT books.*, borrows.id AS borrow_id, borrows.borrowed_at, borrows.due_date,
                      borrows.returned_at, borrows.status
               FROM borrows JOIN books ON books.id = borrows.book_id
               WHERE borrows.user_id = $user_id";

if ($tab == "borrowed") {
  $sql = $borrow_sql . " AND borrows.status = 'borrowed' ORDER BY borrows.due_date";
} elseif ($tab == "favorites") {
  $sql = "SELECT books.* FROM favorites JOIN books ON books.id = favorites.book_id
          WHERE favorites.user_id = $user_id ORDER BY favorites.created_at DESC";
} elseif ($tab == "ebooks") {
  $sql = "SELECT * FROM books WHERE has_ebook = 1 ORDER BY title";
} elseif ($tab == "audio") {
  $sql = "SELECT * FROM books WHERE has_audiobook = 1 ORDER BY title";
} elseif ($tab == "articles") {
  $sql = "SELECT * FROM books WHERE type = 'article' ORDER BY title";
} else {
  $sql = $borrow_sql . " ORDER BY borrows.borrowed_at DESC";
}

$books = $conn->query($sql);
$today = date("Y-m-d");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BOOKSHELF</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="./Css/style.css" />
    <link rel="stylesheet" href="./Css/yourshelf.css" />
  </head>

  <body>
    <?php include 'header.php'; ?>
    <main>
      <h1>Your <span class="color">Shelf</span></h1>
      <div class="shelf-options">
        <ul id="mainMenu" class="menu">
          <?php foreach ($tabs as $key => $label) { ?>
            <li class="shelf-item <?php if ($key == $tab) echo 'active'; ?>" onclick="location.href='yourshelf.php?tab=<?php echo $key; ?>'"><?php echo $label; ?></li>
          <?php } ?>
        </ul>

        <!-- More menu -->
        <div class="more-container">
          <button id="moreBtn" class="more-btn">⋯</button>
          <ul id="moreMenu" class="more-dropdown"></ul>
        </div>
      </div>

      <div class="shelf-cards">
        <?php while ($book = $books->fetch_assoc()) { ?>
          <div class="shelf-card">
            <div class="sc-left">
              <a href="./bookdetails.php?id=<?php echo $book['id']; ?>">
                <img src="./Assets/Images/<?php echo $book['cover']; ?>" alt="Book Cover" />
              </a>
              <h3><?php echo htmlspecialchars($book['title']); ?></h3>
              <p><?php echo htmlspecialchars($book['author']); ?></p>
            </div>
            <div class="sc-right">
              <?php if (isset($book['borrow_id'])) { ?>
                <!-- A book the user borrowed -->
                <h3>Borrowed on</h3>
                <p><?php echo date("d-M-Y h:i A", strtotime($book['borrowed_at'])); ?></p>

                <?php if ($book['status'] == "borrowed") { ?>
                  <h3>Submission Due</h3>
                  <p><?php echo date("d-M-Y", strtotime($book['due_date'])); ?></p>

                  <?php if ($book['due_date'] < $today) { ?>
                    <button class="borrow-btn" style="background-color: red">Overdue</button>
                  <?php } else { ?>
                    <button class="borrow-btn">Borrowed</button>
                  <?php } ?>
                  <form action="return.php" method="post">
                    <input type="hidden" name="borrow_id" value="<?php echo $book['borrow_id']; ?>" />
                    <button class="return-btn" type="submit">Return</button>
                  </form>
                <?php } else { ?>
                  <h3>Returned on</h3>
                  <p><?php echo date("d-M-Y h:i A", strtotime($book['returned_at'])); ?></p>
                  <button class="borrow-btn" style="background-color: #777777">Returned</button>
                <?php } ?>

              <?php } else { ?>
                <!-- Favorites, e-books, audio books -->
                <h3>Category</h3>
                <p><?php echo htmlspecialchars($book['category']); ?></p>

                <h3>Location</h3>
                <p><?php echo $book['location']; ?></p>

                <?php if ($book['available_copies'] > 0) { ?>
                  <button class="borrow-btn" onclick="location.href='bookdetails.php?id=<?php echo $book['id']; ?>'">In-Shelf</button>
                <?php } else { ?>
                  <button class="borrow-btn" style="background-color: #777777" onclick="location.href='bookdetails.php?id=<?php echo $book['id']; ?>'">Not Available</button>
                <?php } ?>
                <?php if ($tab == "favorites") { ?>
                  <button class="return-btn" onclick="location.href='favorite.php?id=<?php echo $book['id']; ?>'">Remove</button>
                <?php } ?>
              <?php } ?>
            </div>
          </div>
        <?php } ?>

        <?php if ($books->num_rows == 0) { ?>
          <p class="empty-shelf">Nothing here yet. Find a book on the <a href="./search.php">Search</a> page.</p>
        <?php } ?>
      </div>
    </main>
    <script src="./Js/yourshelf.js"></script>
    <script src="./Js/index.js"></script>
  </body>
</html>
