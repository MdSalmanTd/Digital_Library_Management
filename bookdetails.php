<?php
session_start();
include 'connection.php';
include 'check_login.php';

// Which book? e.g. bookdetails.php?id=3
$book_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$result = $conn->query("SELECT * FROM books WHERE id = $book_id");
$book = $result->fetch_assoc();

if (!$book) {
  header("Location: notfound.php");
  exit();
}

// How many people are reading this book right now
$result = $conn->query("SELECT COUNT(*) AS total FROM borrows WHERE book_id = $book_id AND status = 'borrowed'");
$currently_reading = $result->fetch_assoc()['total'];

// Has this user already borrowed it?
$result = $conn->query("SELECT id FROM borrows WHERE book_id = $book_id AND user_id = $user_id AND status = 'borrowed'");
$already_borrowed = $result->num_rows > 0;
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
    <link rel="stylesheet" href="./Css/bookdetails.css" />
    <link rel="stylesheet" href="./Css/message.css" />
  </head>

  <body>
    <?php include 'header.php'; ?>
    <main>
      <?php if (isset($_SESSION['message'])) { ?>
        <div class="error"><?php echo $_SESSION['message']; ?></div>
        <?php unset($_SESSION['message']); ?>
      <?php } ?>

      <div class="bdcontainer">
        <div class="book-details-container">
          <div class="book-cover">
            <img
              src="./Assets/Images/<?php echo $book['cover']; ?>"
              alt="Book Cover"
            />
          </div>
          <div class="book-information">
            <h2><?php echo htmlspecialchars($book['title']); ?></h2>
            <h3>by <?php echo htmlspecialchars($book['author']); ?></h3>
            <h4><?php echo htmlspecialchars($book['edition']); ?></h4>
            <div class="book-microinfo">
              <div class="star-rate">
                <?php for ($i = 1; $i <= 5; $i++) { ?>
                  <?php if ($i <= round($book['rating'])) { ?>
                    <i class="fa-solid fa-star"></i>
                  <?php } else { ?>
                    <i class="fa-regular fa-star"></i>
                  <?php } ?>
                <?php } ?>
                <span><?php echo $book['rating']; ?> (<?php echo $book['reviews_count']; ?> Reviews)</span>
              </div>
              <p><?php echo $currently_reading; ?> Currently Reading</p>
            </div>
            <div class="availability-container">

                <div class="availability">
                  <h2>Availability</h2>
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
                  <h2>Status</h2>
                  <?php if ($book['available_copies'] > 0) { ?>
                    <span class="status available">In-Shelf (<?php echo $book['available_copies']; ?> left)</span>
                  <?php } else { ?>
                    <span class="status unavailable">All copies borrowed</span>
                  <?php } ?>
                  <span class="location"
                    ><i class="fa-solid fa-location-dot" style="color: #ff0000"></i>
                    <?php echo $book['location']; ?></span
                  >
                </div>
            </div>

            <div class="book-action">
              <form action="borrow.php" method="post">
                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>" />
                <?php if ($already_borrowed) { ?>
                  <button id="borrow-btn" type="button" style="background-color: #A9A9A9" disabled>Already Borrowed</button>
                <?php } elseif ($book['available_copies'] < 1) { ?>
                  <button id="borrow-btn" type="button" style="background-color: #A9A9A9" disabled>Not Available</button>
                <?php } else { ?>
                  <button id="borrow-btn" type="submit">Borrow Book</button>
                <?php } ?>
              </form>
              <?php if ($book['read_url'] != "") { ?>
                <button class="read-btn">
                  <a href="<?php echo htmlspecialchars($book['read_url']); ?>" target="_blank">
                  Read Now
                  </a>
                </button>
              <?php } ?>
            </div>

          </div>
          <div class="about-auth">
              <h2>About the Author</h2>
              <div class="auth-name-img">
                  <h3><?php echo htmlspecialchars($book['author']); ?></h3>
                  <img src="./Assets/Images/<?php echo $book['cover']; ?>" alt="<?php echo htmlspecialchars($book['author']); ?>">
              </div>
              <p>
                  <?php echo htmlspecialchars($book['author_bio']); ?>
              </p>
          </div>
        </div>

        <div class="about-book">
            <h2>Book Details</h2>
            <div class="about-header">
              <h3>Overview</h3>
            </div>
          <div class="about-content">
            <p>
              <?php echo htmlspecialchars($book['description']); ?>
            </p>
          </div>
        </div>
      </div>
    </main>

    <script src="./Js/index.js"></script>
  </body>
</html>
