<?php
session_start();
include 'connection.php';
include 'check_librarian.php';

$lib_page = "librarian_loans.php";
$today = date("Y-m-d");

$filters = [
  "active" => "Borrowed now",
  "overdue" => "Overdue",
  "returned" => "Returned",
  "all" => "All loans",
];
$filter = isset($_GET['filter']) && isset($filters[$_GET['filter']]) ? $_GET['filter'] : "active";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $borrow_id = (int) $_POST['borrow_id'];
  $borrow = $conn->query("SELECT * FROM borrows WHERE id = $borrow_id AND status = 'borrowed'")->fetch_assoc();

  if (!$borrow) {
    $_SESSION['error'] = "That loan is already returned or does not exist.";
  } elseif ($_POST['action'] == "return") {
    $returned_at = date("Y-m-d H:i:s");
    $conn->query("UPDATE borrows SET status = 'returned', returned_at = '$returned_at' WHERE id = $borrow_id");
    // Put the copy back on the shelf
    $conn->query("UPDATE books SET available_copies = available_copies + 1 WHERE id = " . (int) $borrow['book_id']);
    $_SESSION['message'] = "The book was marked as returned.";
  } elseif ($_POST['action'] == "extend") {
    // 7 more days, counted from today if the book is already late
    $from = $borrow['due_date'] < $today ? $today : $borrow['due_date'];
    $new_due = date("Y-m-d", strtotime($from . " +7 days"));
    $conn->query("UPDATE borrows SET due_date = '$new_due' WHERE id = $borrow_id");
    $_SESSION['message'] = "The new due date is " . date("d-M-Y", strtotime($new_due)) . ".";
  }
  header("Location: librarian_loans.php?filter=" . $filter);
  exit();
}

$sql = "SELECT borrows.*, books.title, books.cover, users.full_name, users.university_id
        FROM borrows
        JOIN books ON books.id = borrows.book_id
        JOIN users ON users.id = borrows.user_id";

if ($filter == "active") {
  $sql .= " WHERE borrows.status = 'borrowed' ORDER BY borrows.due_date";
} elseif ($filter == "overdue") {
  $sql .= " WHERE borrows.status = 'borrowed' AND borrows.due_date < '$today' ORDER BY borrows.due_date";
} elseif ($filter == "returned") {
  $sql .= " WHERE borrows.status = 'returned' ORDER BY borrows.returned_at DESC";
} else {
  $sql .= " ORDER BY borrows.borrowed_at DESC";
}
$loans = $conn->query($sql);
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

      <ul class="lib-filters">
        <?php foreach ($filters as $key => $label) { ?>
          <li><a href="./librarian_loans.php?filter=<?php echo $key; ?>" class="<?php if ($key == $filter) echo 'active'; ?>"><?php echo $label; ?></a></li>
        <?php } ?>
      </ul>

      <div class="lib-panel">
        <?php if ($loans->num_rows == 0) { ?>
          <p class="lib-empty">No loans here.</p>
        <?php } else { ?>
          <div class="lib-table-wrap">
            <table class="lib-table">
              <tr><th></th><th>Book</th><th>Member</th><th>Borrowed</th><th>Due</th><th>Status</th><th></th></tr>
              <?php while ($loan = $loans->fetch_assoc()) { ?>
                <tr>
                  <td><img class="lib-cover" src="./Assets/Images/<?php echo $loan['cover']; ?>" alt="" /></td>
                  <td><?php echo htmlspecialchars($loan['title']); ?></td>
                  <td><?php echo htmlspecialchars($loan['full_name']); ?><br /><small><?php echo htmlspecialchars($loan['university_id']); ?></small></td>
                  <td><?php echo date("d-M-Y", strtotime($loan['borrowed_at'])); ?></td>
                  <td><?php echo date("d-M-Y", strtotime($loan['due_date'])); ?></td>
                  <td>
                    <?php if ($loan['status'] == "returned") { ?>
                      <span class="lib-badge grey">Returned</span><br /><small><?php echo date("d-M-Y", strtotime($loan['returned_at'])); ?></small>
                    <?php } elseif ($loan['due_date'] < $today) { ?>
                      <span class="lib-badge red">Overdue</span><br /><small><?php echo (int) ((strtotime($today) - strtotime($loan['due_date'])) / 86400); ?> days late</small>
                    <?php } else { ?>
                      <span class="lib-badge green">Borrowed</span>
                    <?php } ?>
                  </td>
                  <td class="lib-actions">
                    <?php if ($loan['status'] == "borrowed") { ?>
                      <form method="post">
                        <input type="hidden" name="borrow_id" value="<?php echo $loan['id']; ?>" />
                        <button class="lib-btn green" name="action" value="return" type="submit">Mark returned</button>
                        <button class="lib-btn blue" name="action" value="extend" type="submit">+7 days</button>
                      </form>
                    <?php } ?>
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
