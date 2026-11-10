<?php
session_start();
include 'connection.php';
include 'check_login.php';

// AI Buddy: a simple helper that answers from the library database.
// It looks for keywords in the question (recommend, genre, summarize).
$question = isset($_GET['ask']) ? trim($_GET['ask']) : "";
$answer = "";
$answer_books = [];   // books to show as links under the answer
$answer_genres = [];  // genres to show as links under the answer

if ($question != "") {
  $q = strtolower($question);

  if (str_contains($q, "recommend")) {
    $answer = "Here are the highest rated books in our library right now:";
    $result = $conn->query("SELECT * FROM books ORDER BY rating DESC LIMIT 5");
    while ($row = $result->fetch_assoc()) {
      $answer_books[] = $row;
    }

  } elseif (str_contains($q, "genre") || str_contains($q, "explore")) {
    $answer = "These are the genres you can explore. Click one to see its books:";
    $result = $conn->query("SELECT category, COUNT(*) AS total FROM books GROUP BY category ORDER BY category");
    while ($row = $result->fetch_assoc()) {
      $answer_genres[] = $row;
    }

  } elseif (str_contains($q, "summar")) {
    // Look for a book title inside the question
    $result = $conn->query("SELECT * FROM books");
    while ($row = $result->fetch_assoc()) {
      if (str_contains($q, strtolower($row['title']))) {
        $answer = "Summary of \"" . $row['title'] . "\" by " . $row['author'] . ": " . $row['description'];
        $answer_books[] = $row;
        break;
      }
    }
    if ($answer == "") {
      $answer = "Which book should I summarize? Type for example: \"Summarize Origin\". Here are some books you can ask about:";
      $result = $conn->query("SELECT * FROM books ORDER BY title LIMIT 10");
      while ($row = $result->fetch_assoc()) {
        $answer_books[] = $row;
      }
    }

  } else {
    // Anything else: search the books
    $like = "%" . $question . "%";
    $stmt = $conn->prepare("SELECT * FROM books WHERE CONCAT_WS(' ', title, author, category, sub_category, description) LIKE ? LIMIT 5");
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $answer_books[] = $row;
    }

    if (count($answer_books) > 0) {
      $answer = "I found these books for you:";
    } else {
      $answer = "Sorry, I couldn't find anything about that. Try asking me to recommend books, explore genres, or summarize a book.";
    }
  }
}
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
    <link rel="stylesheet" href="./Css/aihelper.css" />
  </head>

  <body>
    <?php include 'header.php'; ?>
    <main>
      <div class="ai-container">
        <div class="greet">
          <h1>Hello, <?php echo htmlspecialchars($first_name); ?></h1>
          <p>How can I assist you today?</p>
        </div>

        <div class="aicards">
            <div class="aicard1 aicard" onclick="location.href='aihelper.php?ask=Summarize a book'">
                <h3>Summarize a Book</h3>
                <div class="iconbg">
                    <i class="fa-solid fa-book-open"></i>
                </div>
            </div>
            <div class="aicard2 aicard" onclick="location.href='aihelper.php?ask=Explore new genres'">
                <h3>Explore New Genres</h3>
                <div class="iconbg">
                    <i class="fa-solid fa-compass"></i>
            </div>
        </div>
        <div class="aicard3 aicard" onclick="location.href='aihelper.php?ask=Recommend me books'">
            <h3>Get Book Recommendations</h3>
            <div class="iconbg">
                <i class="fa-solid fa-lightbulb"></i>
            </div>
          </div>
        </div>

        <form class="searchbox" action="aihelper.php" method="get">
            <input type="text" name="ask" placeholder="Ask me anything..." value="<?php echo htmlspecialchars($question); ?>" />
            <button type="submit" class="sendicon">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>

        <div class="chat">
          <?php if ($answer != "") { ?>
            <div class="aioverview" style="display: block">
              <h2>AI Overview</h2>
              <div class="AiAnswer">
                  <p><b>You asked:</b> <?php echo htmlspecialchars($question); ?></p>
                  <p><?php echo htmlspecialchars($answer); ?></p>

                  <ul class="ai-list">
                    <?php foreach ($answer_books as $book) { ?>
                      <li>
                        <a href="bookdetails.php?id=<?php echo $book['id']; ?>"><?php echo htmlspecialchars($book['title']); ?></a>
                        by <?php echo htmlspecialchars($book['author']); ?> (<?php echo $book['rating']; ?>/5)
                      </li>
                    <?php } ?>
                    <?php foreach ($answer_genres as $genre) { ?>
                      <li>
                        <a href="search.php?by=subject&q=<?php echo urlencode($genre['category']); ?>"><?php echo htmlspecialchars($genre['category']); ?></a>
                        (<?php echo $genre['total']; ?> books)
                      </li>
                    <?php } ?>
                  </ul>
              </div>
            </div>
          <?php } ?>
        </div>
    </div>
</main>
    <script src="./Js/index.js"></script>
  </body>
  </html>
