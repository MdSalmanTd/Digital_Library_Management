function otpbutton() {
  document.querySelectorAll("#submit").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      window.location.href = "./verify.html";
    });
  });
}

document.addEventListener("DOMContentLoaded", otpbutton);

const inputs = document.querySelectorAll(".otp-inputs input");

inputs.forEach((input, index) => {
  
  input.addEventListener("input", () => {
    if (input.value.length === 1 && index < inputs.length - 1) {
      inputs[index + 1].focus();
    }
  });

  input.addEventListener("keydown", (e) => {
    if (e.key === "Backspace" && input.value === "" && index > 0) {
      inputs[index - 1].focus();
    }
  });
});
