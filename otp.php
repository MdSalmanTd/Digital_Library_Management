<?php
session_start();
include 'connection.php';

// Only people who just registered (or tried to log in unverified) can be here
if (!isset($_SESSION['pending_user_id'])) {
  header("Location: registration.php");
  exit();
}

$pending_id = (int) $_SESSION['pending_user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $pending_id");
$pending_user = $result->fetch_assoc();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // The six boxes arrive as an array: join them into one code
  $entered_otp = implode("", $_POST['otp']);

  if ($entered_otp == $pending_user['otp_code']) {
    $conn->query("UPDATE users SET is_verified = 1, otp_code = NULL WHERE id = $pending_id");
    unset($_SESSION['pending_user_id']);
    header("Location: verify.php");
    exit();
  } else {
    $error = "Wrong OTP. Please try again.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OTP - BOOKSHELF</title>
    <link rel="stylesheet" href="./Css/otp.css">
    <link rel="stylesheet" href="./Css/message.css">

  </head>
  <body>
    <div class="container">
      <div class="left">
        <div class="otp-card">
          <div class="logo">
            <img src="./Assets/Images/Screenshot 2025-11-11 113932.png" alt="BOOKSHELF Logo" />
            <h2>BOOKSHELF</h2>
          </div>

          <h3>Verification</h3>
          <p class="subtext">Check your Email for OTP</p>
          <p class="subtext small">Enter your OTP Here</p>

          <!-- XAMPP cannot send emails, so the code is shown here for testing -->
          <div class="success">Demo: your OTP is <b><?php echo $pending_user['otp_code']; ?></b></div>

          <?php if ($error != "") { ?>
            <div class="error"><?php echo $error; ?></div>
          <?php } ?>

          <form method="post">
            <div class="otp-inputs">
              <input type="text" name="otp[]" maxlength="1" inputmode="numeric" required />
              <input type="text" name="otp[]" maxlength="1" inputmode="numeric" required />
              <input type="text" name="otp[]" maxlength="1" inputmode="numeric" required />
              <input type="text" name="otp[]" maxlength="1" inputmode="numeric" required />
              <input type="text" name="otp[]" maxlength="1" inputmode="numeric" required />
              <input type="text" name="otp[]" maxlength="1" inputmode="numeric" required />
            </div>

            <button id="submit" type="submit">Sign Up</button>
          </form>

          <div class="login">
            Have an account already? <a href="./login.php">Login</a>
          </div>
        </div>
      </div>

      <div class="right"></div>
    </div>

    <script src="./Js/otp.js"></script>
  </body>
</html>
