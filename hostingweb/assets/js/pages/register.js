/**
 * USOLUTIONS - Register Page JavaScript
 */

document.addEventListener("DOMContentLoaded", () => {
  // Form validation
  initRegisterFormValidation()

  // Password visibility toggle
  initPasswordToggles()

  // Password strength meter
  initPasswordStrengthMeter()
})

/**
 * Initialize register form validation
 */
function initRegisterFormValidation() {
  const registerForm = document.querySelector(".auth-form form")

  if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
      let isValid = true
      const firstNameInput = document.getElementById("first_name")
      const lastNameInput = document.getElementById("last_name")
      const emailInput = document.getElementById("email")
      const passwordInput = document.getElementById("password")
      const confirmPasswordInput = document.getElementById("confirm_password")
      const termsCheckbox = document.getElementById("terms")

      // Reset previous error messages
      document.querySelectorAll(".error-message").forEach((el) => el.remove())

      // Validate first name
      if (!firstNameInput.value.trim()) {
        isValid = false
        showError(firstNameInput, "Please enter your first name")
      }

      // Validate last name
      if (!lastNameInput.value.trim()) {
        isValid = false
        showError(lastNameInput, "Please enter your last name")
      }

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
        showError(passwordInput, "Please enter a password")
      } else if (passwordInput.value.length < 8) {
        isValid = false
        showError(passwordInput, "Password must be at least 8 characters long")
      }

      // Validate confirm password
      if (!confirmPasswordInput.value) {
        isValid = false
        showError(confirmPasswordInput, "Please confirm your password")
      } else if (confirmPasswordInput.value !== passwordInput.value) {
        isValid = false
        showError(confirmPasswordInput, "Passwords do not match")
      }

      // Validate terms checkbox
      if (!termsCheckbox.checked) {
        isValid = false
        showError(termsCheckbox, "You must agree to the Terms of Service and Privacy Policy")
      }

      // If the form is not valid, prevent submission
      if (!isValid) {
        e.preventDefault()
      }
    })
  }
}

/**
 * Initialize password visibility toggles
 */
function initPasswordToggles() {
  const passwordInputs = [document.getElementById("password"), document.getElementById("confirm_password")]

  passwordInputs.forEach((input) => {
    if (input) {
      const toggleButton = document.createElement("button")
      toggleButton.type = "button"
      toggleButton.className = "password-toggle"
      toggleButton.innerHTML = '<i class="fas fa-eye"></i>'

      // Insert toggle button after password input
      input.parentNode.insertBefore(toggleButton, input.nextSibling)

      toggleButton.addEventListener("click", function () {
        if (input.type === "password") {
          input.type = "text"
          this.innerHTML = '<i class="fas fa-eye-slash"></i>'
        } else {
          input.type = "password"
          this.innerHTML = '<i class="fas fa-eye"></i>'
        }
      })
    }
  })
}

/**
 * Initialize password strength meter
 */
function initPasswordStrengthMeter() {
  const passwordInput = document.getElementById("password")

  if (passwordInput) {
    // Create strength meter elements
    const strengthMeter = document.createElement("div")
    strengthMeter.className = "password-strength-meter"

    const strengthBar = document.createElement("div")
    strengthBar.className = "strength-bar"

    const strengthText = document.createElement("div")
    strengthText.className = "strength-text"

    strengthMeter.appendChild(strengthBar)
    strengthMeter.appendChild(strengthText)

    // Insert after the small text that follows the password input
    const smallText = passwordInput.nextElementSibling
    if (smallText && smallText.tagName === "SMALL") {
      smallText.parentNode.insertBefore(strengthMeter, smallText.nextSibling)
    } else {
      passwordInput.parentNode.appendChild(strengthMeter)
    }

    // Update strength meter on input
    passwordInput.addEventListener("input", function () {
      const strength = calculatePasswordStrength(this.value)
      updateStrengthMeter(strengthBar, strengthText, strength)
    })
  }
}

/**
 * Calculate password strength
 * @param {string} password - The password to evaluate
 * @returns {number} - Strength score (0-4)
 */
function calculatePasswordStrength(password) {
  let score = 0

  // Length check
  if (password.length >= 8) score++
  if (password.length >= 12) score++

  // Complexity checks
  if (/[A-Z]/.test(password)) score++
  if (/[0-9]/.test(password)) score++
  if (/[^A-Za-z0-9]/.test(password)) score++

  return Math.min(4, score)
}

/**
 * Update strength meter display
 * @param {HTMLElement} bar - The strength bar element
 * @param {HTMLElement} text - The strength text element
 * @param {number} strength - The strength score (0-4)
 */
function updateStrengthMeter(bar, text, strength) {
  // Update strength bar width and color
  const strengthClasses = ["very-weak", "weak", "medium", "strong", "very-strong"]
  const strengthLabels = ["Very Weak", "Weak", "Medium", "Strong", "Very Strong"]

  // Remove all strength classes
  strengthClasses.forEach((cls) => {
    bar.classList.remove(cls)
  })

  // Add appropriate class and set width
  bar.classList.add(strengthClasses[strength])
  bar.style.width = `${(strength + 1) * 20}%`

  // Update text
  text.textContent = strengthLabels[strength]
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

  if (input.type === "checkbox") {
    // For checkboxes, append after the label
    const label = input.nextElementSibling
    if (label && label.tagName === "LABEL") {
      label.parentNode.appendChild(errorElement)
    } else {
      input.parentNode.appendChild(errorElement)
    }
  } else {
    input.parentNode.appendChild(errorElement)
  }

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

