<?php
session_start();
include 'connection.php';
include 'check_librarian.php';

$lib_page = "librarian_members.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $member_id = (int) $_POST['member_id'];
  $member = $conn->query("SELECT * FROM users WHERE id = $member_id")->fetch_assoc();

  if (!$member) {
    $_SESSION['error'] = "That member does not exist.";
  } elseif ($member_id == $user_id) {
    $_SESSION['error'] = "You cannot change your own account here.";
  } elseif ($_POST['action'] == "verify") {
    $conn->query("UPDATE users SET is_verified = 1, otp_code = NULL WHERE id = $member_id");
    $_SESSION['message'] = $member['full_name'] . " is now verified.";
  } elseif ($_POST['action'] == "make_librarian") {
    $conn->query("UPDATE users SET role = 'librarian', is_verified = 1 WHERE id = $member_id");
    $_SESSION['message'] = $member['full_name'] . " is now a librarian.";
  } elseif ($_POST['action'] == "make_member") {
    $conn->query("UPDATE users SET role = 'member' WHERE id = $member_id");
    $_SESSION['message'] = $member['full_name'] . " is now a normal member.";
  } elseif ($_POST['action'] == "delete") {
    // Put their borrowed books back on the shelf, then remove the account (loans and favorites go with it)
    $conn->query("UPDATE books JOIN borrows ON books.id = borrows.book_id
                  SET books.available_copies = books.available_copies + 1
                  WHERE borrows.user_id = $member_id AND borrows.status = 'borrowed'");
    if ($member['photo'] != "" && file_exists("uploads/" . $member['photo'])) {
      unlink("uploads/" . $member['photo']);
    }
    $conn->query("DELETE FROM users WHERE id = $member_id");
    $_SESSION['message'] = $member['full_name'] . "'s account was removed.";
  }
  header("Location: librarian_members.php");
  exit();
}

// Search by name, email or university ID
$find = isset($_GET['find']) ? trim($_GET['find']) : "";
$like = "%" . $find . "%";
$stmt = $conn->prepare("SELECT users.*,
                          (SELECT COUNT(*) FROM borrows WHERE borrows.user_id = users.id AND borrows.status = 'borrowed') AS borrowed_now,
                          (SELECT COUNT(*) FROM borrows WHERE borrows.user_id = users.id) AS borrowed_total
                        FROM users
                        WHERE full_name LIKE ? OR email LIKE ? OR university_id LIKE ?
                        ORDER BY role DESC, created_at DESC");
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$members = $stmt->get_result();
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

      <div class="lib-toolbar">
        <form class="lib-find" method="get">
          <input type="text" name="find" placeholder="Find by name, email or university ID" value="<?php echo htmlspecialchars($find); ?>" />
          <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
      </div>

      <div class="lib-panel">
        <?php if ($members->num_rows == 0) { ?>
          <p class="lib-empty">No members found.</p>
        <?php } else { ?>
          <div class="lib-table-wrap">
            <table class="lib-table">
              <tr><th></th><th>Name</th><th>University ID</th><th>Joined</th><th>Status</th><th>Books</th><th></th></tr>
              <?php while ($member = $members->fetch_assoc()) { ?>
                <?php $member_photo = $member['photo'] != "" ? "./uploads/" . $member['photo'] : "./Assets/Images/b7b6bb2c53f083ca7beb2acab7ee8a29.jpg"; ?>
                <tr>
                  <td><img class="lib-avatar" src="<?php echo htmlspecialchars($member_photo); ?>" alt="" /></td>
                  <td><?php echo htmlspecialchars($member['full_name']); ?><br /><small><?php echo htmlspecialchars($member['email']); ?></small></td>
                  <td><?php echo htmlspecialchars($member['university_id']); ?></td>
                  <td><?php echo date("d-M-Y", strtotime($member['created_at'])); ?></td>
                  <td>
                    <?php if ($member['role'] == "librarian") { ?>
                      <span class="lib-badge blue">Librarian</span>
                    <?php } elseif ($member['is_verified']) { ?>
                      <span class="lib-badge green">Verified</span>
                    <?php } else { ?>
                      <span class="lib-badge grey">Not verified</span>
                    <?php } ?>
                  </td>
                  <td><?php echo $member['borrowed_now']; ?> now<br /><small><?php echo $member['borrowed_total']; ?> total</small></td>
                  <td class="lib-actions">
                    <?php if ($member['id'] == $user_id) { ?>
                      <small>This is you</small>
                    <?php } else { ?>
                      <form method="post">
                        <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>" />
                        <?php if (!$member['is_verified']) { ?>
                          <button class="lib-btn green" name="action" value="verify" type="submit">Verify</button>
                        <?php } ?>
                        <?php if ($member['role'] == "librarian") { ?>
                          <button class="lib-btn grey" name="action" value="make_member" type="submit">Make member</button>
                        <?php } else { ?>
                          <button class="lib-btn blue" name="action" value="make_librarian" type="submit" onclick="return confirm('Give this person full librarian access?');">Make librarian</button>
                        <?php } ?>
                        <button class="lib-btn red" name="action" value="delete" type="submit" onclick="return confirm('Remove this account and all of its loans? This cannot be undone.');">Remove</button>
                      </form>
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
