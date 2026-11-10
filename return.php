<?php
session_start();
include 'connection.php';
include 'check_login.php';

$borrow_id = (int) $_POST['borrow_id'];

// Find the loan (it must belong to this user and not be returned yet)
$result = $conn->query("SELECT * FROM borrows WHERE id = $borrow_id AND user_id = $user_id AND status = 'borrowed'");
$borrow = $result->fetch_assoc();

if ($borrow) {
  $returned_at = date("Y-m-d H:i:s");
  $conn->query("UPDATE borrows SET status = 'returned', returned_at = '$returned_at' WHERE id = $borrow_id");

  // Put the copy back on the shelf
  $book_id = $borrow['book_id'];
  $conn->query("UPDATE books SET available_copies = available_copies + 1 WHERE id = $book_id");
}

header("Location: yourshelf.php?tab=borrowed");
exit();
?>
