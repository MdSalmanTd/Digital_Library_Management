<?php
// If the user is not logged in, send them to the login page
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Load the logged in user's details
$user_id = (int) $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

// The account no longer exists, so log out
if (!$user) {
  session_destroy();
  header("Location: login.php");
  exit();
}

// Use the uploaded photo, or the default picture if there is none
if ($user['photo'] != "") {
  $user_photo = "./uploads/" . $user['photo'];
} else {
  $user_photo = "./Assets/Images/b7b6bb2c53f083ca7beb2acab7ee8a29.jpg";
}

$first_name = explode(" ", $user['full_name'])[0];
?>
