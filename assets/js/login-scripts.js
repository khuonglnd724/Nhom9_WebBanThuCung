const container = document.querySelector(".container");
const registerBtn = document.querySelector(".register-btn");
const loginBtn = document.querySelector(".login-btn");

registerBtn.addEventListener("click", () => {
  container.classList.add("active");
});

loginBtn.addEventListener("click", () => {
  container.classList.remove("active");
});

document.addEventListener("DOMContentLoaded", () => {
  const container = document.querySelector(".container");
  const registerBtn = document.querySelector(".register-btn");
  const loginBtn = document.querySelector(".login-btn");
  const forgotBtns = document.querySelectorAll(".forgot-btn");
  const backToLogin = document.querySelector(".back-to-login");

  registerBtn?.addEventListener("click", () => {
    container.classList.add("active");
    container.classList.remove("show-forgot");
  });

  loginBtn?.addEventListener("click", () => {
    container.classList.remove("active");
    container.classList.remove("show-forgot");
  });

  forgotBtns?.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      container.classList.add("show-forgot");
      container.classList.remove("active");
    });
  });

  backToLogin?.addEventListener("click", (e) => {
    e.preventDefault();
    container.classList.remove("show-forgot");
  });
});
