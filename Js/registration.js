function regisButton() {
  document.querySelectorAll("#submit").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      window.location.href = "./otp.html";
    });
  });
}

document.addEventListener("DOMContentLoaded", regisButton);