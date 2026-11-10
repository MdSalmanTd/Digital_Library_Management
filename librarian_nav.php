<?php
// Tabs at the top of every librarian page. Each page sets $lib_page first.
$lib_tabs = [
  "librarian.php" => "Dashboard",
  "librarian_books.php" => "Books",
  "librarian_members.php" => "Members",
  "librarian_loans.php" => "Loans",
];
?>
<h1>Librarian <span class="color">Panel</span></h1>
<ul class="lib-tabs">
  <?php foreach ($lib_tabs as $file => $label) { ?>
    <li><a href="./<?php echo $file; ?>" class="<?php if ($file == $lib_page) echo 'active'; ?>"><?php echo $label; ?></a></li>
  <?php } ?>
</ul>

<?php if (isset($_SESSION['message'])) { ?>
  <div class="success"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
  <?php unset($_SESSION['message']); ?>
<?php } ?>
<?php if (isset($_SESSION['error'])) { ?>
  <div class="error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
  <?php unset($_SESSION['error']); ?>
<?php } ?>
