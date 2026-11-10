<?php
session_start();
include 'connection.php';

$error = "";
$full_name = "";
$email = "";
$university_id = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $full_name = trim($_POST['full_name']);
  $email = trim($_POST['email']);
  $university_id = trim($_POST['university_id']);
  $password = $_POST['password'];

  // Check the form
  if (strlen($password) < 8) {
    $error = "Password must be at least 8 characters long.";
  } else {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR university_id = ?");
    $stmt->bind_param("ss", $email, $university_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
      $error = "This email or university ID is already registered.";
    }
  }

  // Check the photo
  if ($error == "") {
    $photo = $_FILES['photo'];
    $extension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
    $allowed = ["jpg", "jpeg", "png", "gif", "webp"];

    if ($photo['error'] != 0) {
      $error = "Please upload your photo.";
    } elseif (!in_array($extension, $allowed) || getimagesize($photo['tmp_name']) === false) {
      $error = "The photo must be a JPG, PNG, GIF or WEBP image.";
    } elseif ($photo['size'] > 2 * 1024 * 1024) {
      $error = "The photo must be 2 MB or smaller.";
    }
  }

  // Everything is OK: save the photo and create the account
  if ($error == "") {
    $photo_name = uniqid("user_") . "." . $extension;
    move_uploaded_file($photo['tmp_name'], "uploads/" . $photo_name);

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $otp = (string) rand(100000, 999999);

    $stmt = $conn->prepare("INSERT INTO users (full_name, email, university_id, password_hash, photo, otp_code) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $full_name, $email, $university_id, $password_hash, $photo_name, $otp);
    $stmt->execute();

    // Remember who has to enter the OTP
    $_SESSION['pending_user_id'] = $conn->insert_id;
    header("Location: otp.php");
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    />
<link rel="stylesheet" href="./Css/registration.css">
<link rel="stylesheet" href="./Css/message.css">

</head>

<body>
  <div class="container">
    <div class="left">
<div class="card">
    <div class="logo">
      <img src="./Assets/Images/Screenshot 2025-11-11 113932.png" alt="">
      <h2>BookWise</h2>
    </div>

    <h3>Create Your Library Account</h3>
    <p class="subtext">
      Please complete all fields and upload a valid university ID to gain access to the library.
    </p>

    <?php if ($error != "") { ?>
      <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="post" enctype="multipart/form-data">
      <label>Full name</label>
      <input type="text" name="full_name" placeholder="Adrian Hajdin" value="<?php echo htmlspecialchars($full_name); ?>" required />

      <label>University Email</label>
      <input type="email" name="email" placeholder="adrian@jsmastery.pro" value="<?php echo htmlspecialchars($email); ?>" required />

      <label>University ID No</label>
      <input type="text" name="university_id" placeholder="eg: 0435*******36" value="<?php echo htmlspecialchars($university_id); ?>" required />

      <label>Password</label>
      <input type="password" name="password" placeholder="At least 8 characters long" minlength="8" required />

    <input type="file" name="photo" id="upload-btn" accept="image/*" required>
    <label for="upload-btn" class="custom-upload-label">
    <i class="fa-solid fa-upload"></i>
     <span id="upload-text">Upload Photo</span>
    </label>

      <button id="submit" type="submit">
       Sign Up
     </button>
    </form>

    <div class="login">
      Have an account already? <a href="./login.php">Login</a>
    </div>
    <div class="img"></div>
  </div>
    </div>

    <div class="right"></div>
  </div>
  <script>
    // Show the chosen file name on the upload button
    document.getElementById("upload-btn").addEventListener("change", function () {
      document.getElementById("upload-text").textContent = this.files[0] ? this.files[0].name : "Upload Photo";
    });
  </script>
</body>
</html>
