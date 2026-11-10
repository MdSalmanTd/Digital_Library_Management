<?php
session_start();
include 'connection.php';
include 'check_login.php';

// Clicking the heart adds the book to favorites, clicking again removes it
$book_id = (int) $_GET['id'];

$result = $conn->query("SELECT * FROM favorites WHERE user_id = $user_id AND book_id = $book_id");

if ($result->num_rows > 0) {
  $conn->query("DELETE FROM favorites WHERE user_id = $user_id AND book_id = $book_id");
} else {
  $book_exists = $conn->query("SELECT id FROM books WHERE id = $book_id")->num_rows > 0;
  if ($book_exists) {
    $conn->query("INSERT INTO favorites (user_id, book_id) VALUES ($user_id, $book_id)");
  }
}

// Go back to the page the heart was clicked on
if (isset($_SERVER['HTTP_REFERER'])) {
  header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
  header("Location: search.php");
}
exit();
?>
