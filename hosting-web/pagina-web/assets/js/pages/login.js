/**
 * USOLUTIONS - Login Page JavaScript
 */

document.addEventListener("DOMContentLoaded", () => {
  // Form validation
  initLoginFormValidation()

  // Password visibility toggle
  initPasswordToggle()
})

/**
 * Initialize login form validation
 */
function initLoginFormValidation() {
  const loginForm = document.querySelector(".auth-form form")

  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      let isValid = true
      const emailInput = document.getElementById("email")
      const passwordInput = document.getElementById("password")

      // Reset previous error messages
      document.querySelectorAll(".error-message").forEach((el) => el.remove())

      // Validate email
      if (!emailInput.value.trim()) {
        isValid = false
        showError(emailInput, "Please enter your email")
      } else if (!isValidEmail(emailInput.value.trim())) {
        isValid = false
        showError(emailInput, "Please enter a valid email address")
      }

      // Validate password
      if (!passwordInput.value) {
        isValid = false
        showError(passwordInput, "Please enter your password")
      }

      // If the form is not valid, prevent submission
      if (!isValid) {
        e.preventDefault()
      }
    })
  }
}

/**
 * Initialize password visibility toggle
 */
function initPasswordToggle() {
  const passwordInput = document.getElementById("password")
  const toggleButton = document.createElement("button")
  toggleButton.type = "button"
  toggleButton.className = "password-toggle"
  toggleButton.innerHTML = '<i class="fas fa-eye"></i>'

  if (passwordInput) {
    // Insert toggle button after password input
    passwordInput.parentNode.insertBefore(toggleButton, passwordInput.nextSibling)

    toggleButton.addEventListener("click", function () {
      if (passwordInput.type === "password") {
        passwordInput.type = "text"
        this.innerHTML = '<i class="fas fa-eye-slash"></i>'
      } else {
        passwordInput.type = "password"
        this.innerHTML = '<i class="fas fa-eye"></i>'
      }
    })
  }
}

/**
 * Show error message for an input
 * @param {HTMLElement} input - The input element
 * @param {string} message - The error message
 */
function showError(input, message) {
  const errorElement = document.createElement("div")
  errorElement.className = "error-message"
  errorElement.textContent = message

  input.parentNode.appendChild(errorElement)
  input.classList.add("error")
}

/**
 * Validate email format
 * @param {string} email - The email to validate
 * @returns {boolean} - Whether the email is valid
 */
function isValidEmail(email) {
  const re =
    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  return re.test(email)
}

