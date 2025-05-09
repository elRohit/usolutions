/**
 * USOLUTIONS - Server Configuration JavaScript
 * Enhances the server configuration form with interactive features
 */

document.addEventListener("DOMContentLoaded", () => {
    // Check if we're on the server configuration page
    const configForm = document.querySelector(".config-form form")
    if (!configForm) return
  
    // Add Font Awesome if not already loaded
    if (!document.querySelector('link[href*="font-awesome"]')) {
      const fontAwesome = document.createElement("link")
      fontAwesome.rel = "stylesheet"
      fontAwesome.href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
      document.head.appendChild(fontAwesome)
    }
  
    // Initialize form validation
    initFormValidation()
  
    // Initialize password strength meter
    initPasswordStrength()
  
    // Initialize password toggle
    initPasswordToggle()
  
    // Initialize password generator
    initPasswordGenerator()
  
    // Initialize form animations
    initFormAnimations()
  })
  
  /**
   * Initialize form validation
   */
  function initFormValidation() {
    const form = document.querySelector(".config-form form")
  
    if (!form) return
  
    const hostnameInput = document.getElementById("hostname")
    const usernameInput = document.getElementById("username")
    const passwordInput = document.getElementById("password")
    const confirmPasswordInput = document.getElementById("confirm_password")
  
    if (hostnameInput) {
      hostnameInput.addEventListener("input", function () {
        validateHostname(this)
      })
  
      hostnameInput.addEventListener("blur", function () {
        validateHostname(this, true)
      })
    }
  
    if (usernameInput) {
      usernameInput.addEventListener("input", function () {
        validateUsername(this)
      })
  
      usernameInput.addEventListener("blur", function () {
        validateUsername(this, true)
      })
    }
  
    if (passwordInput) {
      passwordInput.addEventListener("input", function () {
        validatePassword(this)
  
        if (confirmPasswordInput && confirmPasswordInput.value) {
          validateConfirmPassword(confirmPasswordInput)
        }
      })
  
      passwordInput.addEventListener("blur", function () {
        validatePassword(this, true)
      })
    }
  
    if (confirmPasswordInput) {
      confirmPasswordInput.addEventListener("input", function () {
        validateConfirmPassword(this)
      })
  
      confirmPasswordInput.addEventListener("blur", function () {
        validateConfirmPassword(this, true)
      })
    }
  
    if (form) {
      form.addEventListener("submit", (e) => {
        let isValid = true
  
        if (hostnameInput) {
          isValid = validateHostname(hostnameInput, true) && isValid
        }
  
        if (usernameInput) {
          isValid = validateUsername(usernameInput, true) && isValid
        }
  
        if (passwordInput) {
          isValid = validatePassword(passwordInput, true) && isValid
        }
  
        if (confirmPasswordInput) {
          isValid = validateConfirmPassword(confirmPasswordInput, true) && isValid
        }
  
        if (!isValid) {
          e.preventDefault()
  
          // Scroll to first error
          const firstError = document.querySelector(".form-group.has-error")
          if (firstError) {
            firstError.scrollIntoView({ behavior: "smooth", block: "center" })
          }
        }
      })
    }
  }
  
  /**
   * Validate hostname
   * @param {HTMLInputElement} input - The hostname input element
   * @param {boolean} showError - Whether to show error messages
   * @returns {boolean} - Whether the hostname is valid
   */
  function validateHostname(input, showError = false) {
    if (!input) return false
  
    const value = input.value.trim()
    const formGroup = input.closest(".form-group")
  
    if (!formGroup) return false
  
    const errorElement = formGroup.querySelector(".error-message") || createErrorElement(formGroup)
  
    // Remove existing validation classes
    formGroup.classList.remove("has-error", "has-success")
  
    if (!value) {
      if (showError) {
        formGroup.classList.add("has-error")
        errorElement.textContent = "Hostname is required"
      }
      return false
    }
  
    const hostnameRegex = /^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/
  
    if (!hostnameRegex.test(value)) {
      if (showError) {
        formGroup.classList.add("has-error")
        errorElement.textContent =
          "Hostname must contain only lowercase letters, numbers, and hyphens, and cannot start or end with a hyphen"
      }
      return false
    }
  
    formGroup.classList.add("has-success")
    return true
  }
  
  /**
   * Validate username
   * @param {HTMLInputElement} input - The username input element
   * @param {boolean} showError - Whether to show error messages
   * @returns {boolean} - Whether the username is valid
   */
  function validateUsername(input, showError = false) {
    if (!input) return false
  
    const value = input.value.trim()
    const formGroup = input.closest(".form-group")
  
    if (!formGroup) return false
  
    const errorElement = formGroup.querySelector(".error-message") || createErrorElement(formGroup)
  
    // Remove existing validation classes
    formGroup.classList.remove("has-error", "has-success")
  
    if (!value) {
      if (showError) {
        formGroup.classList.add("has-error")
        errorElement.textContent = "Username is required"
      }
      return false
    }
  
    const usernameRegex = /^[a-z_]([a-z0-9_-]{0,31})$/
  
    if (!usernameRegex.test(value)) {
      if (showError) {
        formGroup.classList.add("has-error")
        errorElement.textContent =
          "Username must start with a letter or underscore, and contain only lowercase letters, numbers, underscores, and hyphens"
      }
      return false
    }
  
    formGroup.classList.add("has-success")
    return true
  }
  
  /**
   * Validate password
   * @param {HTMLInputElement} input - The password input element
   * @param {boolean} showError - Whether to show error messages
   * @returns {boolean} - Whether the password is valid
   */
  function validatePassword(input, showError = false) {
    if (!input) return false
  
    const value = input.value
    const formGroup = input.closest(".form-group")
  
    if (!formGroup) return false
  
    const errorElement = formGroup.querySelector(".error-message") || createErrorElement(formGroup)
  
    // Remove existing validation classes
    formGroup.classList.remove("has-error", "has-success")
  
    if (!value) {
      if (showError) {
        formGroup.classList.add("has-error")
        errorElement.textContent = "Password is required"
      }
      return false
    }
  
    if (value.length < 8) {
      if (showError) {
        formGroup.classList.add("has-error")
        errorElement.textContent = "Password must be at least 8 characters long"
      }
      return false
    }
  
    formGroup.classList.add("has-success")
    return true
  }
  
  /**
   * Validate confirm password
   * @param {HTMLInputElement} input - The confirm password input element
   * @param {boolean} showError - Whether to show error messages
   * @returns {boolean} - Whether the confirm password is valid
   */
  function validateConfirmPassword(input, showError = false) {
    if (!input) return false
  
    const value = input.value
    const passwordInput = document.getElementById("password")
  
    if (!passwordInput) return false
  
    const passwordValue = passwordInput.value
    const formGroup = input.closest(".form-group")
  
    if (!formGroup) return false
  
    const errorElement = formGroup.querySelector(".error-message") || createErrorElement(formGroup)
  
    // Remove existing validation classes
    formGroup.classList.remove("has-error", "has-success")
  
    if (!value) {
      if (showError) {
        formGroup.classList.add("has-error")
        errorElement.textContent = "Please confirm your password"
      }
      return false
    }
  
    if (value !== passwordValue) {
      if (showError) {
        formGroup.classList.add("has-error")
        errorElement.textContent = "Passwords do not match"
      }
      return false
    }
  
    formGroup.classList.add("has-success")
    return true
  }
  
  /**
   * Create error message element
   * @param {HTMLElement} formGroup - The form group element
   * @returns {HTMLElement} - The error message element
   */
  function createErrorElement(formGroup) {
    if (!formGroup) return null
  
    const errorElement = document.createElement("div")
    errorElement.className = "error-message"
    formGroup.appendChild(errorElement)
    return errorElement
  }
  
  /**
   * Initialize password strength meter
   */
  function initPasswordStrength() {
    const passwordInput = document.getElementById("password")
  
    if (!passwordInput) return
  
    const formGroup = passwordInput.closest(".form-group")
  
    if (!formGroup) return
  
    // Check if strength meter elements already exist
    let strengthContainer = formGroup.querySelector(".password-strength")
    let strengthMeter = formGroup.querySelector(".password-strength-meter")
    let strengthText = formGroup.querySelector(".password-strength-text")
  
    if (!strengthContainer) {
      // Create password strength elements
      strengthContainer = document.createElement("div")
      strengthContainer.className = "password-strength"
  
      strengthMeter = document.createElement("div")
      strengthMeter.className = "password-strength-meter"
  
      strengthText = document.createElement("div")
      strengthText.className = "password-strength-text"
  
      strengthContainer.appendChild(strengthMeter)
      formGroup.appendChild(strengthContainer)
      formGroup.appendChild(strengthText)
    }
  
    // Update password strength on input
    passwordInput.addEventListener("input", function () {
      updatePasswordStrength(this.value, strengthMeter, strengthText)
    })
  }
  
  /**
   * Update password strength meter
   * @param {string} password - The password to evaluate
   * @param {HTMLElement} meter - The strength meter element
   * @param {HTMLElement} text - The strength text element
   */
  function updatePasswordStrength(password, meter, text) {
    if (!meter || !text) return
  
    let strength = 0
    let feedback = ""
  
    if (password.length === 0) {
      meter.style.width = "0%"
      meter.style.backgroundColor = "#e2e8f0"
      text.textContent = ""
      return
    }
  
    // Length check
    if (password.length >= 8) {
      strength += 1
    }
  
    if (password.length >= 12) {
      strength += 1
    }
  
    // Character variety checks
    if (/[a-z]/.test(password)) {
      strength += 1
    }
  
    if (/[A-Z]/.test(password)) {
      strength += 1
    }
  
    if (/[0-9]/.test(password)) {
      strength += 1
    }
  
    if (/[^a-zA-Z0-9]/.test(password)) {
      strength += 1
    }
  
    // Set meter width and color based on strength
    const percentage = (strength / 6) * 100
    meter.style.width = `${percentage}%`
  
    if (strength < 3) {
      meter.style.backgroundColor = "var(--danger-color)" // Weak
      feedback = "Weak"
    } else if (strength < 5) {
      meter.style.backgroundColor = "var(--warning-color)" // Medium
      feedback = "Medium"
    } else {
      meter.style.backgroundColor = "var(--success-color)" // Strong
      feedback = "Strong"
    }
  
    text.textContent = feedback
  }
  
  /**
   * Initialize password toggle
   */
  function initPasswordToggle() {
    const passwordInputs = document.querySelectorAll('input[type="password"]')
  
    passwordInputs.forEach((input) => {
      if (!input) return
  
      const formGroup = input.closest(".form-group")
  
      if (!formGroup) return
  
      // Create toggle button
      const toggleButton = document.createElement("button")
      toggleButton.type = "button"
      toggleButton.className = "password-toggle"
      toggleButton.innerHTML = '<i class="fas fa-eye"></i>'
      toggleButton.setAttribute("aria-label", "Toggle password visibility")
  
      formGroup.style.position = "relative"
      formGroup.appendChild(toggleButton)
  
      // Toggle password visibility
      toggleButton.addEventListener("click", function () {
        if (input.type === "password") {
          input.type = "text"
          this.innerHTML = '<i class="fas fa-eye-slash"></i>'
        } else {
          input.type = "password"
          this.innerHTML = '<i class="fas fa-eye"></i>'
        }
  
        input.focus()
      })
    })
  }
  
  /**
   * Initialize password generator
   */
  function initPasswordGenerator() {
    const passwordInput = document.getElementById("password")
  
    if (!passwordInput) return
  
    const formGroup = passwordInput.closest(".form-group")
  
    if (!formGroup) return
  
    const confirmPasswordInput = document.getElementById("confirm_password")
  
    // Create generate button
    const generateButton = document.createElement("button")
    generateButton.type = "button"
    generateButton.className = "generate-password"
    generateButton.innerHTML = '<i class="fas fa-magic"></i>'
    generateButton.setAttribute("aria-label", "Generate secure password")
    generateButton.setAttribute("title", "Generate secure password")
  
    formGroup.appendChild(generateButton)
  
    // Generate password on click
    generateButton.addEventListener("click", () => {
      const generatedPassword = generateSecurePassword()
      passwordInput.type = "text"
      passwordInput.value = generatedPassword
  
      if (confirmPasswordInput) {
        confirmPasswordInput.value = generatedPassword
      }
  
      // Update password toggle button
      const toggleButton = formGroup.querySelector(".password-toggle")
      if (toggleButton) {
        toggleButton.innerHTML = '<i class="fas fa-eye-slash"></i>'
      }
  
      // Trigger input event to update validation and strength meter
      const inputEvent = new Event("input", { bubbles: true })
      passwordInput.dispatchEvent(inputEvent)
  
      if (confirmPasswordInput) {
        confirmPasswordInput.dispatchEvent(inputEvent)
      }
  
      // Focus on the next field or submit button
      if (confirmPasswordInput) {
        confirmPasswordInput.focus()
      } else {
        const submitButton = document.querySelector('button[type="submit"]')
        if (submitButton) {
          submitButton.focus()
        }
      }
    })
  }
  
  /**
   * Generate a secure password
   * @returns {string} - A secure random password
   */
  function generateSecurePassword() {
    const length = 12
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+"
    let password = ""
  
    // Ensure at least one character from each category
    password += getRandomChar("abcdefghijklmnopqrstuvwxyz")
    password += getRandomChar("ABCDEFGHIJKLMNOPQRSTUVWXYZ")
    password += getRandomChar("0123456789")
    password += getRandomChar("!@#$%^&*()-_=+")
  
    // Fill the rest with random characters
    for (let i = password.length; i < length; i++) {
      password += charset.charAt(Math.floor(Math.random() * charset.length))
    }
  
    // Shuffle the password
    return password
      .split("")
      .sort(() => 0.5 - Math.random())
      .join("")
  }
  
  /**
   * Get a random character from a string
   * @param {string} chars - The characters to choose from
   * @returns {string} - A random character
   */
  function getRandomChar(chars) {
    return chars.charAt(Math.floor(Math.random() * chars.length))
  }
  
  /**
   * Initialize form animations
   */
  function initFormAnimations() {
    const summaryRows = document.querySelectorAll(".summary-row")
    const formGroups = document.querySelectorAll(".form-group")
  
    // Animate summary rows
    summaryRows.forEach((row, index) => {
      if (!row) return
  
      row.style.opacity = "0"
      row.style.transform = "translateY(10px)"
  
      setTimeout(
        () => {
          row.style.transition = "opacity 0.3s ease, transform 0.3s ease"
          row.style.opacity = "1"
          row.style.transform = "translateY(0)"
        },
        100 + index * 50,
      )
    })
  
    // Animate form groups
    formGroups.forEach((group, index) => {
      if (!group) return
  
      group.style.opacity = "0"
      group.style.transform = "translateY(10px)"
  
      setTimeout(
        () => {
          group.style.transition = "opacity 0.3s ease, transform 0.3s ease"
          group.style.opacity = "1"
          group.style.transform = "translateY(0)"
        },
        300 + index * 100,
      )
    })
  }
  
  