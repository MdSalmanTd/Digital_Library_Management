<?php
session_start();
include 'connection.php';
include 'check_librarian.php';

$lib_page = "librarian_books.php";
$error = "";

// ?id=5 edits book 5, no id adds a new book
$book_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$borrowed_now = 0;

if ($book_id > 0) {
  $book = $conn->query("SELECT * FROM books WHERE id = $book_id")->fetch_assoc();
  if (!$book) {
    header("Location: librarian_books.php");
    exit();
  }
  $borrowed_now = $conn->query("SELECT COUNT(*) AS total FROM borrows WHERE book_id = $book_id AND status = 'borrowed'")->fetch_assoc()['total'];
} else {
  // Empty values for a new book
  $book = [
    "title" => "", "author" => "", "edition" => "", "published_year" => date("Y"),
    "cover" => "img.png", "category" => "", "sub_category" => "", "type" => "book",
    "rating" => "0.0", "reviews_count" => 0, "has_hardcopy" => 1, "has_ebook" => 0,
    "has_audiobook" => 0, "location" => "", "total_copies" => 1, "description" => "",
    "author_bio" => "", "read_url" => "",
  ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Keep what was typed so the form is not emptied when there is an error
  $book['title'] = trim($_POST['title']);
  $book['author'] = trim($_POST['author']);
  $book['edition'] = trim($_POST['edition']);
  $book['published_year'] = (int) $_POST['published_year'];
  $book['category'] = trim($_POST['category']);
  $book['sub_category'] = trim($_POST['sub_category']);
  $book['type'] = $_POST['type'] == "article" ? "article" : "book";
  $book['rating'] = (float) $_POST['rating'];
  $book['reviews_count'] = (int) $_POST['reviews_count'];
  $book['has_hardcopy'] = isset($_POST['has_hardcopy']) ? 1 : 0;
  $book['has_ebook'] = isset($_POST['has_ebook']) ? 1 : 0;
  $book['has_audiobook'] = isset($_POST['has_audiobook']) ? 1 : 0;
  $book['location'] = trim($_POST['location']);
  $book['total_copies'] = (int) $_POST['total_copies'];
  $book['description'] = trim($_POST['description']);
  $book['author_bio'] = trim($_POST['author_bio']);
  $book['read_url'] = trim($_POST['read_url']);

  if ($book['title'] == "" || $book['author'] == "" || $book['category'] == "") {
    $error = "Title, author and category are required.";
  } elseif ($book['rating'] < 0 || $book['rating'] > 5) {
    $error = "Rating must be between 0 and 5.";
  } elseif ($book['total_copies'] < 1) {
    $error = "A book needs at least 1 copy.";
  } elseif ($book['total_copies'] < $borrowed_now) {
    $error = "$borrowed_now copies are borrowed right now, so total copies cannot be less than $borrowed_now.";
  } elseif ($book['read_url'] != "" && !filter_var($book['read_url'], FILTER_VALIDATE_URL)) {
    $error = "The read link must be a full web address (starting with https://).";
  }

  // A new cover is optional
  $new_cover = "";
  if ($error == "" && $_FILES['cover']['error'] != 4) {
    $cover = $_FILES['cover'];
    $extension = strtolower(pathinfo($cover['name'], PATHINFO_EXTENSION));
    $allowed = ["jpg", "jpeg", "png", "gif", "webp"];

    if ($cover['error'] != 0) {
      $error = "The cover could not be uploaded. Please try again.";
    } elseif (!in_array($extension, $allowed) || getimagesize($cover['tmp_name']) === false) {
      $error = "The cover must be a JPG, PNG, GIF or WEBP image.";
    } elseif ($cover['size'] > 2 * 1024 * 1024) {
      $error = "The cover must be 2 MB or smaller.";
    } else {
      $new_cover = "covers/" . uniqid("cover_") . "." . $extension;
      move_uploaded_file($cover['tmp_name'], "Assets/Images/" . $new_cover);
    }
  }

  if ($error == "") {
    if ($new_cover != "") {
      // Delete the old uploaded cover
      if (strpos($book['cover'], "covers/") === 0 && file_exists("Assets/Images/" . $book['cover'])) {
        unlink("Assets/Images/" . $book['cover']);
      }
      $book['cover'] = $new_cover;
    }
    $available = $book['total_copies'] - $borrowed_now;

    if ($book_id > 0) {
      $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, edition = ?, published_year = ?, cover = ?, category = ?,
                                sub_category = ?, type = ?, rating = ?, reviews_count = ?, has_hardcopy = ?, has_ebook = ?,
                                has_audiobook = ?, location = ?, total_copies = ?, available_copies = ?, description = ?,
                                author_bio = ?, read_url = ? WHERE id = ?");
      $stmt->bind_param("sssissssdiiiisiisssi", $book['title'], $book['author'], $book['edition'], $book['published_year'],
        $book['cover'], $book['category'], $book['sub_category'], $book['type'], $book['rating'], $book['reviews_count'],
        $book['has_hardcopy'], $book['has_ebook'], $book['has_audiobook'], $book['location'], $book['total_copies'],
        $available, $book['description'], $book['author_bio'], $book['read_url'], $book_id);
      $stmt->execute();
      $_SESSION['message'] = "\"" . $book['title'] . "\" was saved.";
    } else {
      $stmt = $conn->prepare("INSERT INTO books (title, author, edition, published_year, cover, category, sub_category, type,
                                rating, reviews_count, has_hardcopy, has_ebook, has_audiobook, location, total_copies,
                                available_copies, description, author_bio, read_url)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("sssissssdiiiisiisss", $book['title'], $book['author'], $book['edition'], $book['published_year'],
        $book['cover'], $book['category'], $book['sub_category'], $book['type'], $book['rating'], $book['reviews_count'],
        $book['has_hardcopy'], $book['has_ebook'], $book['has_audiobook'], $book['location'], $book['total_copies'],
        $available, $book['description'], $book['author_bio'], $book['read_url']);
      $stmt->execute();
      $_SESSION['message'] = "\"" . $book['title'] . "\" was added to the library.";
    }
    header("Location: librarian_books.php");
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BOOKSHELF - Librarian</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="./Css/style.css" />
    <link rel="stylesheet" href="./Css/librarian.css" />
    <link rel="stylesheet" href="./Css/message.css" />
  </head>

  <body>
    <?php include 'header.php'; ?>
    <main>
      <?php include 'librarian_nav.php'; ?>

      <?php if ($error != "") { ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
      <?php } ?>

      <div class="lib-panel">
        <div class="lib-panel-header">
          <h2><?php echo $book_id > 0 ? "Edit Book" : "Add a New Book"; ?></h2>
          <a href="./librarian_books.php">Back to books</a>
        </div>

        <form class="lib-form" method="post" enctype="multipart/form-data">
          <div class="lib-form-cover">
            <img id="cover-preview" src="./Assets/Images/<?php echo $book['cover']; ?>" alt="Book cover" />
            <label for="cover-input" class="lib-btn blue">Choose Cover</label>
            <input type="file" id="cover-input" name="cover" accept="image/*" hidden />
          </div>

          <div class="lib-form-fields">
            <label>Title *<input type="text" name="title" maxlength="200" value="<?php echo htmlspecialchars($book['title']); ?>" required /></label>
            <label>Author *<input type="text" name="author" maxlength="150" value="<?php echo htmlspecialchars($book['author']); ?>" required /></label>
            <label>Edition<input type="text" name="edition" maxlength="50" value="<?php echo htmlspecialchars($book['edition']); ?>" placeholder="eg: Second Edition" /></label>
            <label>Published year<input type="number" name="published_year" min="1000" max="<?php echo date("Y") + 1; ?>" value="<?php echo $book['published_year']; ?>" required /></label>
            <label>Category *<input type="text" name="category" maxlength="80" value="<?php echo htmlspecialchars($book['category']); ?>" placeholder="eg: Computer Science" required /></label>
            <label>Sub category<input type="text" name="sub_category" maxlength="80" value="<?php echo htmlspecialchars($book['sub_category']); ?>" placeholder="eg: UX Design" /></label>
            <label>Type
              <select name="type">
                <option value="book" <?php if ($book['type'] == "book") echo "selected"; ?>>Book</option>
                <option value="article" <?php if ($book['type'] == "article") echo "selected"; ?>>Article / Journal</option>
              </select>
            </label>
            <label>Shelf location<input type="text" name="location" maxlength="20" value="<?php echo htmlspecialchars($book['location']); ?>" placeholder="eg: CS A-15" /></label>
            <label>Total copies<input type="number" name="total_copies" min="<?php echo max(1, $borrowed_now); ?>" value="<?php echo $book['total_copies']; ?>" required /></label>
            <label>Rating (0 - 5)<input type="number" name="rating" min="0" max="5" step="0.1" value="<?php echo $book['rating']; ?>" /></label>
            <label>Number of reviews<input type="number" name="reviews_count" min="0" value="<?php echo $book['reviews_count']; ?>" /></label>
            <label>Read online link<input type="url" name="read_url" maxlength="500" value="<?php echo htmlspecialchars($book['read_url']); ?>" placeholder="https://..." /></label>

            <div class="lib-checks">
              <label><input type="checkbox" name="has_hardcopy" <?php if ($book['has_hardcopy']) echo "checked"; ?> /> Hard copy</label>
              <label><input type="checkbox" name="has_ebook" <?php if ($book['has_ebook']) echo "checked"; ?> /> E-book</label>
              <label><input type="checkbox" name="has_audiobook" <?php if ($book['has_audiobook']) echo "checked"; ?> /> Audio book</label>
            </div>

            <label class="lib-wide">Description<textarea name="description" rows="5"><?php echo htmlspecialchars($book['description']); ?></textarea></label>
            <label class="lib-wide">About the author<textarea name="author_bio" rows="3"><?php echo htmlspecialchars($book['author_bio']); ?></textarea></label>

            <?php if ($borrowed_now > 0) { ?>
              <p class="lib-note lib-wide"><?php echo $borrowed_now; ?> copies are borrowed right now. Copies on the shelf = total copies - <?php echo $borrowed_now; ?>.</p>
            <?php } ?>

            <button class="lib-btn green lib-wide" type="submit"><?php echo $book_id > 0 ? "Save Changes" : "Add Book"; ?></button>
          </div>
        </form>
      </div>
    </main>
    <script src="./Js/index.js"></script>
    <script>
      // Show the chosen cover before saving
      document.getElementById("cover-input").addEventListener("change", function () {
        if (this.files[0]) {
          document.getElementById("cover-preview").src = URL.createObjectURL(this.files[0]);
        }
      });
    </script>
  </body>
</html>
