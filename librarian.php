<?php
session_start();
include 'connection.php';
include 'check_librarian.php';

$lib_page = "librarian.php";
$today = date("Y-m-d");

// Numbers for the cards at the top
$books_row = $conn->query("SELECT COUNT(*) AS titles, SUM(total_copies) AS copies, SUM(available_copies) AS available FROM books")->fetch_assoc();
$members_row = $conn->query("SELECT COUNT(*) AS total, SUM(is_verified = 0) AS unverified FROM users WHERE role = 'member'")->fetch_assoc();
$loans_row = $conn->query("SELECT SUM(status = 'borrowed') AS active, SUM(status = 'borrowed' AND due_date < '$today') AS overdue FROM borrows")->fetch_assoc();

$stats = [
  ["Book titles", (int) $books_row['titles'], "fa-book"],
  ["Copies on shelf", (int) $books_row['available'] . " / " . (int) $books_row['copies'], "fa-layer-group"],
  ["Members", (int) $members_row['total'], "fa-users"],
  ["Not verified", (int) $members_row['unverified'], "fa-user-clock"],
  ["Books out now", (int) $loans_row['active'], "fa-right-from-bracket"],
  ["Overdue", (int) $loans_row['overdue'], "fa-triangle-exclamation"],
];

$loan_sql = "SELECT borrows.*, books.title, users.full_name, users.university_id
             FROM borrows
             JOIN books ON books.id = borrows.book_id
             JOIN users ON users.id = borrows.user_id";

$overdue_loans = $conn->query($loan_sql . " WHERE borrows.status = 'borrowed' AND borrows.due_date < '$today' ORDER BY borrows.due_date LIMIT 10");
$recent_loans = $conn->query($loan_sql . " ORDER BY borrows.borrowed_at DESC LIMIT 8");
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

      <div class="lib-stats">
        <?php foreach ($stats as $stat) { ?>
          <div class="lib-stat">
            <i class="fa-solid <?php echo $stat[2]; ?>"></i>
            <p><?php echo $stat[0]; ?></p>
            <h2><?php echo $stat[1]; ?></h2>
          </div>
        <?php } ?>
      </div>

      <div class="lib-panel">
        <div class="lib-panel-header">
          <h2>Overdue books</h2>
          <a href="./librarian_loans.php?filter=overdue">See all</a>
        </div>
        <?php if ($overdue_loans->num_rows == 0) { ?>
          <p class="lib-empty">No overdue books. Everyone is on time.</p>
        <?php } else { ?>
          <div class="lib-table-wrap">
            <table class="lib-table">
              <tr><th>Book</th><th>Member</th><th>Due</th><th>Late by</th></tr>
              <?php while ($loan = $overdue_loans->fetch_assoc()) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($loan['title']); ?></td>
                  <td><?php echo htmlspecialchars($loan['full_name']); ?><br /><small><?php echo htmlspecialchars($loan['university_id']); ?></small></td>
                  <td><?php echo date("d-M-Y", strtotime($loan['due_date'])); ?></td>
                  <td class="lib-late"><?php echo (int) ((strtotime($today) - strtotime($loan['due_date'])) / 86400); ?> days</td>
                </tr>
              <?php } ?>
            </table>
          </div>
        <?php } ?>
      </div>

      <div class="lib-panel">
        <div class="lib-panel-header">
          <h2>Latest loans</h2>
          <a href="./librarian_loans.php?filter=all">See all</a>
        </div>
        <?php if ($recent_loans->num_rows == 0) { ?>
          <p class="lib-empty">Nobody has borrowed a book yet.</p>
        <?php } else { ?>
          <div class="lib-table-wrap">
            <table class="lib-table">
              <tr><th>Book</th><th>Member</th><th>Borrowed</th><th>Status</th></tr>
              <?php while ($loan = $recent_loans->fetch_assoc()) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($loan['title']); ?></td>
                  <td><?php echo htmlspecialchars($loan['full_name']); ?></td>
                  <td><?php echo date("d-M-Y h:i A", strtotime($loan['borrowed_at'])); ?></td>
                  <td>
                    <?php if ($loan['status'] == "returned") { ?>
                      <span class="lib-badge grey">Returned</span>
                    <?php } elseif ($loan['due_date'] < $today) { ?>
                      <span class="lib-badge red">Overdue</span>
                    <?php } else { ?>
                      <span class="lib-badge green">Borrowed</span>
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
