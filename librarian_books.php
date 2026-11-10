<?php
session_start();
include 'connection.php';
include 'check_librarian.php';

$lib_page = "librarian_books.php";

// Delete a book
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == "delete") {
  $book_id = (int) $_POST['book_id'];
  $book = $conn->query("SELECT * FROM books WHERE id = $book_id")->fetch_assoc();
  $active_loans = $conn->query("SELECT COUNT(*) AS total FROM borrows WHERE book_id = $book_id AND status = 'borrowed'")->fetch_assoc()['total'];

  if (!$book) {
    $_SESSION['error'] = "That book does not exist.";
  } elseif ($active_loans > 0) {
    $_SESSION['error'] = "\"" . $book['title'] . "\" is borrowed by $active_loans member(s). Mark it returned before deleting it.";
  } else {
    // Remove the uploaded cover (the books that came with the site keep theirs)
    if (strpos($book['cover'], "covers/") === 0 && file_exists("Assets/Images/" . $book['cover'])) {
      unlink("Assets/Images/" . $book['cover']);
    }
    $conn->query("DELETE FROM books WHERE id = $book_id");
    $_SESSION['message'] = "\"" . $book['title'] . "\" was deleted.";
  }
  header("Location: librarian_books.php");
  exit();
}

// Search the list by title, author or category
$find = isset($_GET['find']) ? trim($_GET['find']) : "";
if ($find != "") {
  $like = "%" . $find . "%";
  $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR category LIKE ? ORDER BY title");
  $stmt->bind_param("sss", $like, $like, $like);
  $stmt->execute();
  $books = $stmt->get_result();
} else {
  $books = $conn->query("SELECT * FROM books ORDER BY title");
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

      <div class="lib-toolbar">
        <form class="lib-find" method="get">
          <input type="text" name="find" placeholder="Find by title, author or category" value="<?php echo htmlspecialchars($find); ?>" />
          <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        <a class="lib-btn green" href="./librarian_bookform.php"><i class="fa-solid fa-plus"></i> Add Book</a>
      </div>

      <div class="lib-panel">
        <?php if ($books->num_rows == 0) { ?>
          <p class="lib-empty">No books found.</p>
        <?php } else { ?>
          <div class="lib-table-wrap">
            <table class="lib-table">
              <tr><th></th><th>Title</th><th>Category</th><th>Location</th><th>On shelf</th><th>Formats</th><th></th></tr>
              <?php while ($book = $books->fetch_assoc()) { ?>
                <tr>
                  <td><img class="lib-cover" src="./Assets/Images/<?php echo $book['cover']; ?>" alt="" /></td>
                  <td>
                    <a class="lib-link" href="./bookdetails.php?id=<?php echo $book['id']; ?>"><?php echo htmlspecialchars($book['title']); ?></a><br />
                    <small><?php echo htmlspecialchars($book['author']); ?> · <?php echo $book['published_year']; ?></small>
                  </td>
                  <td><?php echo htmlspecialchars($book['category']); ?><br /><small><?php echo htmlspecialchars($book['sub_category']); ?></small></td>
                  <td><?php echo htmlspecialchars($book['location']); ?></td>
                  <td>
                    <?php if ($book['available_copies'] > 0) { ?>
                      <span class="lib-badge green"><?php echo $book['available_copies']; ?> / <?php echo $book['total_copies']; ?></span>
                    <?php } else { ?>
                      <span class="lib-badge red">0 / <?php echo $book['total_copies']; ?></span>
                    <?php } ?>
                  </td>
                  <td class="lib-formats">
                    <?php if ($book['has_hardcopy']) { ?><i class="fa-solid fa-book" title="Hard copy"></i><?php } ?>
                    <?php if ($book['has_ebook']) { ?><i class="fa-solid fa-tablet-screen-button" title="E-book"></i><?php } ?>
                    <?php if ($book['has_audiobook']) { ?><i class="fa-solid fa-headphones" title="Audio book"></i><?php } ?>
                  </td>
                  <td class="lib-actions">
                    <a class="lib-btn blue" href="./librarian_bookform.php?id=<?php echo $book['id']; ?>">Edit</a>
                    <form method="post" onsubmit="return confirm('Delete this book? This cannot be undone.');">
                      <input type="hidden" name="action" value="delete" />
                      <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>" />
                      <button class="lib-btn red" type="submit">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php } ?>
            </table>
          </div>
        <?php } ?>
      </div>
    </main>
    <script src="./Js/index.js"></script>
  </body>
</html>
