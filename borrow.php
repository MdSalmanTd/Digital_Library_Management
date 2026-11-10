<?php
session_start();
include 'connection.php';
include 'check_login.php';

$book_id = (int) $_POST['book_id'];

// Has the user already borrowed this book?
$result = $conn->query("SELECT id FROM borrows WHERE user_id = $user_id AND book_id = $book_id AND status = 'borrowed'");
$already_borrowed = $result->num_rows > 0;

// How many books does the user have right now? (limit is 5)
$result = $conn->query("SELECT COUNT(*) AS total FROM borrows WHERE user_id = $user_id AND status = 'borrowed'");
$active_loans = $result->fetch_assoc()['total'];

if ($already_borrowed) {
  $_SESSION['message'] = "You have already borrowed this book.";
  header("Location: bookdetails.php?id=$book_id");
  exit();
}

if ($active_loans >= 5) {
  $_SESSION['message'] = "You can borrow up to 5 books at a time. Please return a book first.";
  header("Location: bookdetails.php?id=$book_id");
  exit();
}

// Take one copy off the shelf (only works if a copy is left)
$conn->query("UPDATE books SET available_copies = available_copies - 1 WHERE id = $book_id AND available_copies > 0");

if ($conn->affected_rows == 0) {
  $_SESSION['message'] = "Sorry, no copies of this book are available right now.";
  header("Location: bookdetails.php?id=$book_id");
  exit();
}

// Save the loan: 14 days to return it
$borrowed_at = date("Y-m-d H:i:s");
$due_date = date("Y-m-d", strtotime("+14 days"));

$stmt = $conn->prepare("INSERT INTO borrows (user_id, book_id, borrowed_at, due_date) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $user_id, $book_id, $borrowed_at, $due_date);
$stmt->execute();

header("Location: yourshelf.php?tab=borrowed");
exit();
?>
