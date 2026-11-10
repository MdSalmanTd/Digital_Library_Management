<?php
// Only librarians may open the librarian pages. Everyone else goes back home.
include 'check_login.php';

if ($user['role'] != "librarian") {
  header("Location: index.php");
  exit();
}
?>
