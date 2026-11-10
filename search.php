<?php
session_start();
include 'connection.php';
include 'check_login.php';

// What did the user type, and where should we look?
$search_q = isset($_GET['q']) ? trim($_GET['q']) : "";
$search_by = isset($_GET['by']) ? $_GET['by'] : "all";

if ($search_by == "title") {
  $column = "title";
} elseif ($search_by == "author") {
  $column = "author";
} elseif ($search_by == "subject") {
  $column = "CONCAT_WS(' ', category, sub_category)";
} elseif ($search_by == "text") {
  $column = "description";
} else {
  $search_by = "all";
  $column = "CONCAT_WS(' ', title, author, category, sub_category, description)";
}

$like = "%" . $search_q . "%";
$stmt = $conn->prepare("SELECT * FROM books WHERE $column LIKE ? ORDER BY title");
$stmt->bind_param("s", $like);
$stmt->execute();
$books = $stmt->get_result();

// IDs of the books this user has marked as favorite
$favorite_ids = [];
$result = $conn->query("SELECT book_id FROM favorites WHERE user_id = $user_id");
while ($row = $result->fetch_assoc()) {
  $favorite_ids[] = $row['book_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Search - BOOKSHELF</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="./Css/style.css" />
    <link rel="stylesheet" href="./Css/search.css" />
    <link rel="stylesheet" href="./Css/notfound.css">
  </head>
  <body>
    <?php include 'header.php'; ?>
    <main>
      <div class="search-results">
        <div class="library-table">
          <div class="library-title">
            <div class="title">Title</div>
            <div class="ratings">Ratings</div>
            <div class="category">Category</div>
            <div class="availability">Availability</div>
            <div class="status-location">Status</div>
            <div></div>
            <div></div>
          </div>
          <div class="library-body">
            <?php while ($book = $books->fetch_assoc()) { ?>
              <div class="book-body">
                <div class="book-info">
                  <img src="./Assets/Images/<?php echo $book['cover']; ?>" alt="Book Cover" />
                  <div>
                    <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                    <p><?php echo htmlspecialchars($book['author']); ?>, <?php echo $book['published_year']; ?></p>
                  </div>
                </div>
                <div class="ratings"><?php echo $book['rating']; ?>/5</div>
                <div class="category">
                  <?php echo htmlspecialchars($book['category']); ?><br /><span class="sub-cat"><?php echo htmlspecialchars($book['sub_category']); ?></span>
                </div>
                <div class="availability">
                  <div class="avail-item">
                    <?php if ($book['has_hardcopy']) { ?>
                      <i class="fa-solid fa-circle-check" style="color: #42bb4e"></i>
                    <?php } else { ?>
                      <i class="fa-solid fa-circle-xmark" style="color: red"></i>
                    <?php } ?>
                    <span>Hard Copy</span>
                  </div>
                  <div class="avail-item">
                    <?php if ($book['has_ebook']) { ?>
                      <i class="fa-solid fa-circle-check" style="color: #42bb4e"></i>
                    <?php } else { ?>
                      <i class="fa-solid fa-circle-xmark" style="color: red"></i>
                    <?php } ?>
                    <span>E-Book</span>
                  </div>
                  <div class="avail-item">
                    <?php if ($book['has_audiobook']) { ?>
                      <i class="fa-solid fa-circle-check" style="color: #42bb4e"></i>
                    <?php } else { ?>
                      <i class="fa-solid fa-circle-xmark" style="color: red"></i>
                    <?php } ?>
                    <span>Audio Book</span>
                  </div>
                </div>

                <div class="status-location">
                  <?php if ($book['available_copies'] > 0) { ?>
                    <span class="status available">In-Shelf</span>
                  <?php } else { ?>
                    <span class="status unavailable">Borrowed</span>
                  <?php } ?>
                  <span class="location"><i class="fa-solid fa-location-dot" style="color: #ff0000"></i> <?php echo $book['location']; ?></span>
                </div>
                <a class="favorite" href="favorite.php?id=<?php echo $book['id']; ?>" title="Add to favorites">
                  <?php if (in_array($book['id'], $favorite_ids)) { ?>
                    <i class="fa-solid fa-heart" style="color: red"></i>
                  <?php } else { ?>
                    <i class="fa-solid fa-heart" style="color: grey"></i>
                  <?php } ?>
                </a>
                <button class="preview" onclick="location.href='bookdetails.php?id=<?php echo $book['id']; ?>'">Preview</button>
              </div>
            <?php } ?>

            <?php if ($books->num_rows == 0) { ?>
              <div class="notfound-container">
                <h1>No Results</h1>
                <img src="./Assets/Images/dribbble_1.gif" alt="404 Not Found">
                <h2>Book Not Found</h2>
                <p>Sorry, the book you are looking for does not exist.</p>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </main>
    <script src="./Js/index.js"></script>
    <script src="./Js/search.js"></script>
  </body>
</html>
