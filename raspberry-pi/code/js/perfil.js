// Perfil page specific JavaScript

document.addEventListener("DOMContentLoaded", () => {
  // Tab functionality
  const tabButtons = document.querySelectorAll(".tab-button");
  const tabContents = document.querySelectorAll(".tab-content");

  tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
      // Remove active class from all buttons and contents
      tabButtons.forEach((btn) => btn.classList.remove("active"));
      tabContents.forEach((content) => content.classList.remove("active"));

      // Add active class to clicked button
      button.classList.add("active");

      // Show corresponding tab content
      const tabId = button.getAttribute("data-tab");
      document.getElementById(`${tabId}-tab`).classList.add("active");
    });
  });

  // Form validation for profile update
  const profileForm = document.getElementById("profileForm");
  if (profileForm) {
    profileForm.addEventListener("submit", (e) => {
      const email = document.getElementById("email").value;

      // Validate email
      if (!/^\S+@\S+\.\S+$/.test(email)) {
        e.preventDefault();
        alert("Please enter a valid email address.");
      }
    });
  }

  // Form validation for password change
  const passwordForm = document.getElementById("passwordForm");
  if (passwordForm) {
    passwordForm.addEventListener("submit", (e) => {
      const currentPassword = document.getElementById("current_password").value;
      const newPassword = document.getElementById("new_password").value;
      const confirmPassword = document.getElementById("confirm_password").value;

      // Validate password length
      if (newPassword.length < 6) {
        e.preventDefault();
        alert("The new password must be at least 6 characters long.");
        return;
      }

      // Validate password match
      if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert("The new passwords do not match.");
        return;
      }
    });
  }

  // Auto-hide alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert");
  if (alerts.length > 0) {
    setTimeout(() => {
      alerts.forEach((alert) => {
        alert.style.opacity = "0";
        setTimeout(() => {
          alert.style.display = "none";
        }, 300);
      });
    }, 5000);
  }
});
