// Login page specific JavaScript

document.addEventListener("DOMContentLoaded", () => {
  // Form validation
  const loginForm = document.getElementById("loginForm");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const emailError = document.getElementById("emailError");
  const passwordError = document.getElementById("passwordError");
  const togglePassword = document.getElementById("togglePassword");

  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      let isValid = true;

      // Reset error messages
      emailError.textContent = "";
      passwordError.textContent = "";

      // Validate email
      if (!emailInput.value) {
        emailError.textContent = "Email is required";
        isValid = false;
      } else if (!/^\S+@\S+\.\S+$/.test(emailInput.value)) {
        emailError.textContent = "Please enter a valid email address";
        isValid = false;
      }

      // Validate password
      if (!passwordInput.value) {
        passwordError.textContent = "Password is required";
        isValid = false;
      }

      if (!isValid) {
        e.preventDefault();
      }
    });
  }

  // Toggle password visibility
  if (togglePassword) {
    togglePassword.addEventListener("click", () => {
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      // Toggle eye icons
      document.querySelector(".eye-icon").classList.toggle("hidden");
      document.querySelector(".eye-off-icon").classList.toggle("hidden");
    });
  }

  // Enhanced particles for login page
  if (typeof tsParticles !== "undefined") {
    tsParticles.load("tsparticles", {
      background: {
        color: {
          value: "transparent",
        },
      },
      fpsLimit: 60,
      interactivity: {
        events: {
          onClick: { enable: true, mode: "push" },
          onHover: { enable: true, mode: "repulse" },
          resize: true,
        },
        modes: {
          push: { quantity: 4 },
          repulse: { distance: 100, duration: 0.4 },
        },
      },
      particles: {
        color: { value: "#2563eb" },
        links: {
          color: "#2563eb",
          distance: 150,
          enable: true,
          opacity: 0.2,
          width: 1,
        },
        collisions: { enable: true },
        move: {
          direction: "none",
          enable: true,
          outModes: { default: "bounce" },
          random: false,
          speed: 1,
          straight: false,
        },
        number: {
          density: { enable: true, area: 800 },
          value: 50,
        },
        opacity: { value: 0.3 },
        shape: { type: "circle" },
        size: { value: { min: 1, max: 3 } },
      },
      detectRetina: true,
    });
  }
});
