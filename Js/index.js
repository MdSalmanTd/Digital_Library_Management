const activePage = window.location.pathname;
const navLinks = document.querySelectorAll(".nav-item a");
if (navLinks && navLinks.length) {
  navLinks.forEach((link) => {
    if (link.href && link.href.includes(activePage)) {
      link.classList.add("active");
      console.log(link);
    }
  });
}

const rightbtn = document.querySelector("#next-btn");
const leftbtn = document.querySelector("#prev-btn");
const display = document.querySelector(".text-slide p");

if (rightbtn && display) {
  rightbtn.addEventListener("click", () => {
    display.textContent = `“Be yourself; everyone else is already taken.”\n― Oscar Wilde`;
  });
}

if (leftbtn && display) {
  leftbtn.addEventListener("click", () => {
    display.textContent = `“A room without books is like a body without a soul.”\n― Marcus Tullius Cicero`;
  });
}

const searchBy = document.querySelector("#search-by");
const selectWrappers = document.querySelectorAll("#select");
selectWrappers.forEach((wrapper) => {
  const displaySpan = wrapper.querySelector(".select-dropdown span");
  const optionItems = wrapper.querySelectorAll(".options-list .options");

  optionItems.forEach((item) => {
    item.addEventListener("click", () => {
      if (displaySpan) displaySpan.textContent = item.textContent;
      // Send the chosen option to search.php with the form
      if (searchBy) searchBy.value = item.textContent.toLowerCase();
    });
  });
});

// Clicking the magnifying glass submits the search form
const searchIcon = document.querySelector(".search-input i");
if (searchIcon) {
  searchIcon.addEventListener("click", () => {
    searchIcon.closest("form").submit();
  });
}

// The top bar dropdowns open on hover, and also on click (needed for touch screens)
const dropdowns = [
  { box: "#select", button: ".select-dropdown", items: ".options" },
  { box: ".langbar", button: ".lang-dropdown", items: ".lang-item" },
  { box: ".profile-container", button: ".profile", items: ".profile-item" },
];

function closeDropdowns() {
  dropdowns.forEach((d) => {
    document
      .querySelectorAll(d.box)
      .forEach((box) => box.classList.remove("open"));
  });
}

dropdowns.forEach((d) => {
  document.querySelectorAll(d.box).forEach((box) => {
    const button = box.querySelector(d.button);
    if (!button) return;

    button.addEventListener("click", (event) => {
      event.stopPropagation();
      const wasOpen = box.classList.contains("open");
      closeDropdowns();
      if (!wasOpen) box.classList.add("open");
    });

    // Choosing an option closes the list
    box.querySelectorAll(d.items).forEach((item) => {
      item.addEventListener("click", () => box.classList.remove("open"));
    });
  });
});

// Clicking anywhere else closes any open list
document.addEventListener("click", closeDropdowns);

const langwrap = document.querySelectorAll(".langbar");
langwrap.forEach((wrapper) => {
  const displaySpan = wrapper.querySelector(".lang-dropdown p");
  const optionItems = wrapper.querySelectorAll(".lang-list .lang-item");

  optionItems.forEach((item) => {
    item.addEventListener("click", () => {
      if (displaySpan) displaySpan.textContent = item.textContent;
    });
  });
});

function updateDateTime() {
  const now = new Date();

  let hours = now.getHours();
  let minutes = now.getMinutes();
  let ampm = hours >= 12 ? "PM" : "AM";

  hours = hours % 12;
  hours = hours ? hours : 12; // Convert 0 -> 12
  minutes = minutes < 10 ? "0" + minutes : minutes;

  const timeString = `${hours}:${minutes} ${ampm}`;

  const day = now.getDate().toString().padStart(2, "0");

  const monthNames = [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "May",
    "Jun",
    "Jul",
    "Aug",
    "Sep",
    "Oct",
    "Nov",
    "Dec",
  ];
  const month = monthNames[now.getMonth()];

  const year = now.getFullYear();

  const dateString = `${day}-${month}-${year}`;

  document.getElementById("time-now").textContent = timeString;
  document.getElementById("date-today").textContent = dateString;
}

setInterval(updateDateTime, 1000);

updateDateTime();
