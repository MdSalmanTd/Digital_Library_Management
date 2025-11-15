const borrowBtn = document.getElementById("borrow-btn");

borrowBtn.addEventListener("click", () => {
  borrowBtn.textContent = "Requested";
  borrowBtn.disabled = true;
  borrowBtn.style.backgroundColor = "#A9A9A9";
});