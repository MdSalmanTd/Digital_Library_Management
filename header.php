<?php
// Shared sidebar + top bar. Needs $user, $user_photo and $first_name from check_login.php.
// The search page sets $search_q and $search_by so the box keeps what was typed.
if (!isset($search_q)) {
  $search_q = "";
}
if (!isset($search_by)) {
  $search_by = "all";
}
?>
    <nav class="sidebar">
      <div class="logo">
        <img src="./Assets/Images/Screenshot 2025-11-11 113932.png" alt="" />
        <h2>BOOKSHELF</h2>
      </div>
      <ul class="nav-links">
        <li class="nav-item">
          <a href="./index.php">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="./search.php">
            <i class="fa-solid fa-magnifying-glass"></i>
            <span>Search</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="./yourshelf.php">
            <i class="fa-solid fa-swatchbook"></i>
            <span>My Self</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="./aihelper.php">
            <i class="fa-brands fa-codepen"></i>
            <span>AiBuddy</span>
          </a>
        </li>
      </ul>
    </nav>

    <header class="top-nav">
      <form class="searchbar" action="search.php" method="get">
        <div id="select">
          <div class="select-dropdown">
            <span><?php echo htmlspecialchars(strtoupper($search_by)); ?></span>
            <i class="fa-solid fa-caret-down"></i>
          </div>

          <ul class="options-list">
            <li class="options">All</li>
            <li class="options">Author</li>
            <li class="options">Title</li>
            <li class="options">Text</li>
            <li class="options">Subject</li>
          </ul>
        </div>
        <input type="hidden" name="by" id="search-by" value="<?php echo htmlspecialchars($search_by); ?>" />

        <div class="search-input">
          <input type="text" name="q" placeholder="Search" value="<?php echo htmlspecialchars($search_q); ?>" />
          <i class="fa-solid fa-magnifying-glass"></i>
        </div>
      </form>
      <div class="langbar">
        <div class="lang-dropdown">
          <p>Language</p>
          <i class="fa-solid fa-caret-down"></i>
        </div>
        <ul class="lang-list">
          <li class="lang-item">English</li>
        </ul>
      </div>
      <div class="time">
        <div class="time-display">
          <i class="fa-regular fa-clock"></i>
          <p id="time-now">00:00 AM</p>
        </div>
        <div class="date-display">
          <i class="fa-regular fa-calendar"></i>
          <p id="date-today">01-Jan-1970</p>
        </div>
      </div>
      <div class="profile-container">
        <div class="profile">
          <img id="profile-pic" src="<?php echo $user_photo; ?>" alt="user-profile" />
          <p><?php echo htmlspecialchars($first_name); ?></p>
          <i class="fa-solid fa-caret-down"></i>
        </div>
        <ul class="profile-options">
          <li class="profile-item">
            <a href="./profile.php">View Profile</a>
          </li>
          <li class="profile-item">
            <a href="./logout.php">Logout</a>
          </li>
        </ul>
      </div>
    </header>
