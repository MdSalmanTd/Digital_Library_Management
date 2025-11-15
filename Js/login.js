
function loginbutton() {
  document.querySelectorAll("#submit").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      window.location.href = "./index.html";
    });
  });
}

document.addEventListener("DOMContentLoaded", loginbutton);