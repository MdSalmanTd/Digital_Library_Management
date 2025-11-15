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



const selectWrappers = document.querySelectorAll("#select");
selectWrappers.forEach((wrapper) => {
  const displaySpan = wrapper.querySelector(".select-dropdown span");
  const optionItems = wrapper.querySelectorAll(".options-list .options");

  optionItems.forEach((item) => {
    item.addEventListener("click", () => {
      if (displaySpan) displaySpan.textContent = item.textContent;
    });
  });
});


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
    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
  ];
  const month = monthNames[now.getMonth()];

  const year = now.getFullYear();

  const dateString = `${day}-${month}-${year}`;


  document.getElementById("time-now").textContent = timeString;
  document.getElementById("date-today").textContent = dateString;
}


setInterval(updateDateTime, 1000);

updateDateTime();



