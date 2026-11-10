<?php
session_start();
include 'connection.php';

// Already logged in? Go straight to the home page
if (isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

$error = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $found_user = $stmt->get_result()->fetch_assoc();

  if (!$found_user || !password_verify($password, $found_user['password_hash'])) {
    $error = "Wrong email or password.";
  } elseif ($found_user['is_verified'] == 0) {
    // Not verified yet: make a new OTP and send them to the OTP page
    $otp = (string) rand(100000, 999999);
    $stmt = $conn->prepare("UPDATE users SET otp_code = ? WHERE id = ?");
    $stmt->bind_param("si", $otp, $found_user['id']);
    $stmt->execute();

    $_SESSION['pending_user_id'] = $found_user['id'];
    header("Location: otp.php");
    exit();
  } else {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $found_user['id'];
    header("Location: index.php");
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="./Css/login.css">
  <link rel="stylesheet" href="./Css/message.css">
</head>
<body>
  <div class="container">
    <div class="left">
      <div class="card">
    <div class="logo"><img src="./Assets/Images/Screenshot 2025-11-11 113932.png" alt="">BookWise</div>
    <h2>Welcome Back to the BookWise</h2>
    <p>Access the vast collection of resources, and stay updated</p>

    <?php if ($error != "") { ?>
      <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="post">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="adrian@jsmastery.pro" value="<?php echo htmlspecialchars($email); ?>" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="At least 8 characters long" required>

      <button id="submit" type="submit">Login</button>
    </form>

    <div class="footer-text">
      Don't have an account already? <a href="./registration.php">Register here</a>
    </div>
  </div>
    </div>

    <div class="right">
    </div>
  </div>
</body>
</html>
