<?php
session_start();
include 'connection.php';
include 'check_login.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Form 1: name, phone and bio
  if ($_POST['form'] == "info") {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);

    if ($full_name == "") {
      $error = "Name cannot be empty.";
    } else {
      $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, bio = ? WHERE id = ?");
      $stmt->bind_param("sssi", $full_name, $phone, $bio, $user_id);
      $stmt->execute();
      $message = "Your information was saved.";
    }
  }

  // Form 2: new profile picture
  if ($_POST['form'] == "photo") {
    $photo = $_FILES['photo'];
    $extension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
    $allowed = ["jpg", "jpeg", "png", "gif", "webp"];

    if ($photo['error'] != 0) {
      $error = "Please choose a photo.";
    } elseif (!in_array($extension, $allowed) || getimagesize($photo['tmp_name']) === false) {
      $error = "The photo must be a JPG, PNG, GIF or WEBP image.";
    } elseif ($photo['size'] > 2 * 1024 * 1024) {
      $error = "The photo must be 2 MB or smaller.";
    } else {
      $photo_name = uniqid("user_") . "." . $extension;
      move_uploaded_file($photo['tmp_name'], "uploads/" . $photo_name);

      // Delete the old photo
      if ($user['photo'] != "" && file_exists("uploads/" . $user['photo'])) {
        unlink("uploads/" . $user['photo']);
      }

      $stmt = $conn->prepare("UPDATE users SET photo = ? WHERE id = ?");
      $stmt->bind_param("si", $photo_name, $user_id);
      $stmt->execute();
      $message = "Your profile picture was updated.";
    }
  }

  // Load the user again so the page shows the new data
  include 'check_login.php';
}

// Data for the library card
$card_number = "BS-" . str_pad($user_id, 6, "0", STR_PAD_LEFT);
$member_since = date("d M Y", strtotime($user['created_at']));
$card_phone = $user['phone'] != "" ? $user['phone'] : "Not added";

$result = $conn->query("SELECT COUNT(*) AS total, SUM(status = 'borrowed') AS now FROM borrows WHERE user_id = $user_id");
$borrow_counts = $result->fetch_assoc();
$borrowed_total = (int) $borrow_counts['total'];
$borrowed_now = (int) $borrow_counts['now'];

// The QR code holds the card number and university ID
$qr_text = "BOOKSHELF | " . $card_number . " | " . $user['university_id'];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BOOKSHELF</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="./Css/style.css" />
    <link rel="stylesheet" href="./Css/profile.css" />
    <link rel="stylesheet" href="./Css/message.css" />
  </head>

  <body>
    <?php include 'header.php'; ?>
    <main>
      <?php if ($message != "") { ?>
        <div class="success"><?php echo $message; ?></div>
      <?php } ?>
      <?php if ($error != "") { ?>
        <div class="error"><?php echo $error; ?></div>
      <?php } ?>

      <div class="profile-main-container">
        <div class="profile-info-section">
          <h1>Your Information</h1>
          <form method="post">
            <input type="hidden" name="form" value="info" />
            <div class="info-item">
              <label for="name">Full name:</label>
              <input type="text" id="name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required />

              <label for="email">University Email:</label>
              <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly />
            </div>
            <div class="info-item">
              <label for="number">Phone Number:</label>
              <input type="tel" id="number" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" />

              <label for="registration">Registration Number:</label>
              <input type="text" id="registration" value="<?php echo htmlspecialchars($user['university_id']); ?>" readonly />
            </div>
            <div class="info-item">
              <label for="bio">Bio:</label>
              <textarea id="bio" name="bio"><?php echo htmlspecialchars($user['bio']); ?></textarea>
            </div>
            <button id="save-btn" type="submit">Save Changes</button>
          </form>
        </div>
        <div class="ppc-section">
          <div class="profile-pic-section">
            <h1>Your Profile Picture</h1>
            <img id="profile-pic-main" src="<?php echo $user_photo; ?>" alt="user-profile-main" />
            <!-- Choosing a file sends this form right away -->
            <form method="post" enctype="multipart/form-data">
              <input type="hidden" name="form" value="photo" />
              <label for="upload-btn" class="custom-upload-label">
                Upload File
              </label>
              <input type="file" id="upload-btn" name="photo" accept="image/*" onchange="this.form.submit()" />
            </form>

          </div>
          <div class="library-card-section">
            <h2>Library Card</h2>
            <div class="library-card">
              <div class="card-header">
                <img class="card-logo" src="./Assets/Images/Screenshot 2025-11-11 113932.png" alt="" />
                <div class="card-title">
                  <h3>BOOKSHELF</h3>
                  <p>Digital Library Membership Card</p>
                </div>
                <span class="card-status">Active</span>
              </div>
              <div class="card-body">
                <img class="card-photo" src="<?php echo $user_photo; ?>" alt="card-photo" />
                <dl class="card-details">
                  <dt>Card No</dt>
                  <dd><?php echo $card_number; ?></dd>
                  <dt>Full Name</dt>
                  <dd><?php echo htmlspecialchars($user['full_name']); ?></dd>
                  <dt>University ID</dt>
                  <dd><?php echo htmlspecialchars($user['university_id']); ?></dd>
                  <dt>Email</dt>
                  <dd><?php echo htmlspecialchars($user['email']); ?></dd>
                  <dt>Phone</dt>
                  <dd><?php echo htmlspecialchars($card_phone); ?></dd>
                  <dt>Member Since</dt>
                  <dd><?php echo $member_since; ?></dd>
                </dl>
              </div>
              <div class="card-footer">
                <p>
                  Borrowed now: <b><?php echo $borrowed_now; ?></b><br />
                  Total borrowed: <b><?php echo $borrowed_total; ?></b>
                </p>
                <div id="card-qr"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    <script src="./Js/index.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
      new QRCode(document.getElementById("card-qr"), {
        text: <?php echo json_encode($qr_text); ?>,
        width: 72,
        height: 72
      });
    </script>
  </body>
</html>
