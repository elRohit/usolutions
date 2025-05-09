/**
 * USOLUTIONS - Profile Page JavaScript
 * Modern, enhanced functionality
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initialize profile page functionality
  initProfilePage()
})

/**
 * Initialize all profile page functionality
 */
function initProfilePage() {
  // Initialize tab navigation
  initTabNavigation()

  // Initialize form validation
  initFormValidation()

  // Initialize password strength meter
  initPasswordStrength()

  // Initialize password toggle
  initPasswordToggle()

  // Initialize alert dismissal
  initAlertDismissal()

  // Initialize animations
  initAnimations()
}

/**
 * Initialize tab navigation
 */
function initTabNavigation() {
  const navLinks = document.querySelectorAll(".profile-nav a")
  const sections = document.querySelectorAll(".profile-section")

  // Show first section by default
  if (sections.length > 0) {
    sections[0].classList.add("active")
  }

  // Set first link as active
  if (navLinks.length > 0) {
    navLinks[0].classList.add("active")
  }

  // Add click event to each nav link
  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      // Skip for "Back to Dashboard" link
      if (this.getAttribute("href").includes("dashboard")) {
        return
      }

      e.preventDefault()

      // Get target section id
      const targetId = this.getAttribute("href").substring(1)
      const targetSection = document.getElementById(targetId)

      if (targetSection) {
        // Remove active class from all links
        navLinks.forEach((navLink) => {
          navLink.classList.remove("active")
        })

        // Add active class to clicked link
        this.classList.add("active")

        // Hide all sections
        sections.forEach((section) => {
          section.classList.remove("active")
        })

        // Show target section
        targetSection.classList.add("active")

        // Update URL hash
        history.pushState(null, null, `#${targetId}`)

        // Scroll to top of section on mobile
        if (window.innerWidth < 768) {
          targetSection.scrollIntoView({ behavior: "smooth" })
        }
      }
    })
  })

  // Check for hash in URL
  if (window.location.hash) {
    const hash = window.location.hash.substring(1)
    const hashLink = document.querySelector(`.profile-nav a[href="#${hash}"]`)

    if (hashLink) {
      hashLink.click()
    }
  }

  // Add keyboard navigation
  document.addEventListener("keydown", (e) => {
    // Only if profile page is active
    if (!document.querySelector(".profile")) return

    // Get current active link
    const activeLink = document.querySelector(".profile-nav a.active")
    if (!activeLink) return

    const links = Array.from(navLinks)
    const currentIndex = links.indexOf(activeLink)

    // Arrow down or right: next tab
    if ((e.key === "ArrowDown" || e.key === "ArrowRight") && e.altKey) {
      e.preventDefault()
      const nextIndex = (currentIndex + 1) % links.length
      links[nextIndex].click()
    }

    // Arrow up or left: previous tab
    if ((e.key === "ArrowUp" || e.key === "ArrowLeft") && e.altKey) {
      e.preventDefault()
      const prevIndex = (currentIndex - 1 + links.length) % links.length
      links[prevIndex].click()
    }
  })
}

/**
 * Initialize form validation
 */
function initFormValidation() {
  // Personal info form validation
  const personalInfoForm = document.querySelector("#personal-info form")

  if (personalInfoForm) {
    const firstNameInput = document.getElementById("first_name")
    const lastNameInput = document.getElementById("last_name")
    const phoneInput = document.getElementById("phone")

    // Add input event listeners
    if (firstNameInput) {
      firstNameInput.addEventListener("input", function () {
        validateInput(this, (value) => {
          if (!value.trim()) return "First name is required"
          return null
        })
      })
    }

    if (lastNameInput) {
      lastNameInput.addEventListener("input", function () {
        validateInput(this, (value) => {
          if (!value.trim()) return "Last name is required"
          return null
        })
      })
    }

    if (phoneInput) {
      phoneInput.addEventListener("input", function () {
        validateInput(this, (value) => {
          if (value.trim() && !/^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/.test(value)) {
            return "Please enter a valid phone number"
          }
          return null
        })
      })
    }

    // Form submission
    personalInfoForm.addEventListener("submit", (e) => {
      let isValid = true

      // Validate first name
      if (firstNameInput) {
        const firstNameError = validateInput(firstNameInput, (value) => {
          if (!value.trim()) return "First name is required"
          return null
        })

        if (firstNameError) isValid = false
      }

      // Validate last name
      if (lastNameInput) {
        const lastNameError = validateInput(lastNameInput, (value) => {
          if (!value.trim()) return "Last name is required"
          return null
        })

        if (lastNameError) isValid = false
      }

      // Validate phone (optional)
      if (phoneInput && phoneInput.value.trim()) {
        const phoneError = validateInput(phoneInput, (value) => {
          if (!/^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/.test(value)) {
            return "Please enter a valid phone number"
          }
          return null
        })

        if (phoneError) isValid = false
      }

      if (!isValid) {
        e.preventDefault()

        // Show error notification
        showNotification("Please fix the errors in the form", "error")

        // Focus first invalid field
        const firstInvalid = personalInfoForm.querySelector(".has-error input")
        if (firstInvalid) firstInvalid.focus()
      } else {
        // Add loading state to button
        const submitBtn = personalInfoForm.querySelector('button[type="submit"]')
        if (submitBtn) {
          submitBtn.disabled = true
          submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...'
        }
      }
    })
  }

  // Password form validation
  const passwordForm = document.querySelector("#change-password form")

  if (passwordForm) {
    const currentPasswordInput = document.getElementById("current_password")
    const newPasswordInput = document.getElementById("new_password")
    const confirmPasswordInput = document.getElementById("confirm_password")

    // Add input event listeners
    if (currentPasswordInput) {
      currentPasswordInput.addEventListener("input", function () {
        validateInput(this, (value) => {
          if (!value) return "Current password is required"
          return null
        })
      })
    }

    if (newPasswordInput) {
      newPasswordInput.addEventListener("input", function () {
        validateInput(this, (value) => {
          if (!value) return "New password is required"
          if (value.length < 8) return "Password must be at least 8 characters long"

          // Also validate confirm password if it has a value
          if (confirmPasswordInput && confirmPasswordInput.value) {
            validateInput(confirmPasswordInput, (value) => {
              if (value !== newPasswordInput.value) return "Passwords do not match"
              return null
            })
          }

          return null
        })
      })
    }

    if (confirmPasswordInput) {
      confirmPasswordInput.addEventListener("input", function () {
        validateInput(this, (value) => {
          if (!value) return "Please confirm your password"
          if (value !== newPasswordInput.value) return "Passwords do not match"
          return null
        })
      })
    }

    // Form submission
    passwordForm.addEventListener("submit", (e) => {
      let isValid = true

      // Validate current password
      if (currentPasswordInput) {
        const currentPasswordError = validateInput(currentPasswordInput, (value) => {
          if (!value) return "Current password is required"
          return null
        })

        if (currentPasswordError) isValid = false
      }

      // Validate new password
      if (newPasswordInput) {
        const newPasswordError = validateInput(newPasswordInput, (value) => {
          if (!value) return "New password is required"
          if (value.length < 8) return "Password must be at least 8 characters long"
          return null
        })

        if (newPasswordError) isValid = false
      }

      // Validate confirm password
      if (confirmPasswordInput) {
        const confirmPasswordError = validateInput(confirmPasswordInput, (value) => {
          if (!value) return "Please confirm your password"
          if (value !== newPasswordInput.value) return "Passwords do not match"
          return null
        })

        if (confirmPasswordError) isValid = false
      }

      if (!isValid) {
        e.preventDefault()

        // Show error notification
        showNotification("Please fix the errors in the form", "error")

        // Focus first invalid field
        const firstInvalid = passwordForm.querySelector(".has-error input")
        if (firstInvalid) firstInvalid.focus()
      } else {
        // Add loading state to button
        const submitBtn = passwordForm.querySelector('button[type="submit"]')
        if (submitBtn) {
          submitBtn.disabled = true
          submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing Password...'
        }
      }
    })
  }
}

/**
 * Validate an input field
 * @param {HTMLElement} input - The input element to validate
 * @param {Function} validationFn - Function that returns error message or null
 * @returns {string|null} - Error message or null if valid
 */
function validateInput(input, validationFn) {
  const formGroup = input.closest(".form-group")
  if (!formGroup) return null

  const errorElement = formGroup.querySelector(".error-message") || createErrorElement(formGroup)
  const value = input.value

  // Remove existing validation classes
  formGroup.classList.remove("has-error", "has-success")

  // Get validation result
  const errorMessage = validationFn(value)

  if (errorMessage) {
    formGroup.classList.add("has-error")
    errorElement.textContent = errorMessage
    return errorMessage
  } else {
    formGroup.classList.add("has-success")
    return null
  }
}

/**
 * Create error message element
 * @param {HTMLElement} formGroup - The form group element
 * @returns {HTMLElement} - The error message element
 */
function createErrorElement(formGroup) {
  const errorElement = document.createElement("div")
  errorElement.className = "error-message"
  formGroup.appendChild(errorElement)
  return errorElement
}

/**
 * Initialize password strength meter
 */
function initPasswordStrength() {
  const passwordInput = document.getElementById("new_password")

  if (!passwordInput) return

  // Create strength meter elements if they don't exist
  const formGroup = passwordInput.closest(".form-group")
  if (!formGroup) return

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
    strengthText.innerHTML =
      '<span class="password-strength-label">Password strength:</span><span class="password-strength-value"></span>'

    strengthContainer.appendChild(strengthMeter)

    // Insert after password input
    const small = formGroup.querySelector("small")
    if (small) {
      formGroup.insertBefore(strengthContainer, small)
      formGroup.insertBefore(strengthText, small)
    } else {
      formGroup.appendChild(strengthContainer)
      formGroup.appendChild(strengthText)
    }
  }

  // Update password strength on input
  passwordInput.addEventListener("input", function () {
    updatePasswordStrength(this.value, strengthMeter, strengthText.querySelector(".password-strength-value"))
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

  // Calculate strength score
  let score = 0
  const feedback = ""

  if (password.length === 0) {
    meter.style.width = "0%"
    meter.style.backgroundColor = "#e1e5eb"
    text.textContent = ""
    text.className = "password-strength-value"
    return
  }

  // Length check
  if (password.length >= 8) {
    score += 1
  }

  if (password.length >= 12) {
    score += 1
  }

  // Character variety checks
  if (/[a-z]/.test(password)) {
    score += 1
  }

  if (/[A-Z]/.test(password)) {
    score += 1
  }

  if (/[0-9]/.test(password)) {
    score += 1
  }

  if (/[^a-zA-Z0-9]/.test(password)) {
    score += 1
  }

  // Set meter width and color based on strength
  const percentage = (score / 6) * 100
  meter.style.width = `${percentage}%`

  // Update text and color
  if (score < 3) {
    meter.style.backgroundColor = "var(--profile-danger)"
    text.textContent = "Weak"
    text.className = "password-strength-value weak"
  } else if (score < 5) {
    meter.style.backgroundColor = "var(--profile-warning)"
    text.textContent = "Medium"
    text.className = "password-strength-value medium"
  } else {
    meter.style.backgroundColor = "var(--profile-success)"
    text.textContent = "Strong"
    text.className = "password-strength-value strong"
  }
}

/**
 * Initialize password toggle
 */
function initPasswordToggle() {
  const passwordInputs = document.querySelectorAll('input[type="password"]')

  passwordInputs.forEach((input) => {
    // Create toggle button if it doesn't exist
    const formGroup = input.closest(".form-group")
    if (!formGroup) return

    let toggleButton = formGroup.querySelector(".password-toggle")

    if (!toggleButton) {
      toggleButton = document.createElement("button")
      toggleButton.type = "button"
      toggleButton.className = "password-toggle"
      toggleButton.innerHTML = '<i class="fas fa-eye"></i>'
      toggleButton.setAttribute("aria-label", "Toggle password visibility")

      formGroup.appendChild(toggleButton)
    }

    // Add toggle functionality
    toggleButton.addEventListener("click", function () {
      const type = input.getAttribute("type") === "password" ? "text" : "password"
      input.setAttribute("type", type)

      // Update icon
      this.innerHTML = type === "password" ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>'
    })
  })
}

/**
 * Initialize alert dismissal
 */
function initAlertDismissal() {
  const alerts = document.querySelectorAll(".alert")

  alerts.forEach((alert) => {
    // Add close button if it doesn't exist
    if (!alert.querySelector(".alert-close")) {
      const closeButton = document.createElement("button")
      closeButton.className = "alert-close"
      closeButton.innerHTML = '<i class="fas fa-times"></i>'
      closeButton.setAttribute("aria-label", "Close alert")

      alert.appendChild(closeButton)

      // Add click event
      closeButton.addEventListener("click", () => {
        alert.style.opacity = "0"
        setTimeout(() => {
          alert.remove()
        }, 300)
      })

      // Auto-dismiss after 5 seconds
      setTimeout(() => {
        if (document.body.contains(alert)) {
          closeButton.click()
        }
      }, 5000)
    }
  })
}

/**
 * Show notification
 * @param {string} message - The notification message
 * @param {string} type - The notification type (success or error)
 */
function showNotification(message, type = "success") {
  // Create notification element
  const notification = document.createElement("div")
  notification.className = `alert alert-${type}`
  notification.innerHTML = `<p>${message}</p>`

  // Add close button
  const closeButton = document.createElement("button")
  closeButton.className = "alert-close"
  closeButton.innerHTML = '<i class="fas fa-times"></i>'
  closeButton.setAttribute("aria-label", "Close notification")

  notification.appendChild(closeButton)

  // Add to page
  const container = document.querySelector(".profile-content")
  if (container) {
    container.insertBefore(notification, container.firstChild)

    // Add click event
    closeButton.addEventListener("click", () => {
      notification.style.opacity = "0"
      setTimeout(() => {
        notification.remove()
      }, 300)
    })

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
      if (document.body.contains(notification)) {
        closeButton.click()
      }
    }, 5000)
  }
}

/**
 * Initialize animations
 */
function initAnimations() {
  // Animate form groups on page load
  const formGroups = document.querySelectorAll(".form-group")

  formGroups.forEach((group, index) => {
    group.style.opacity = "0"
    group.style.transform = "translateY(20px)"

    setTimeout(
      () => {
        group.style.transition = "opacity 0.3s ease, transform 0.3s ease"
        group.style.opacity = "1"
        group.style.transform = "translateY(0)"
      },
      100 + index * 50,
    )
  })

  // Animate buttons
  const buttons = document.querySelectorAll('button[type="submit"]')

  buttons.forEach((button) => {
    button.style.opacity = "0"
    button.style.transform = "translateY(20px)"

    setTimeout(() => {
      button.style.transition = "opacity 0.3s ease, transform 0.3s ease"
      button.style.opacity = "1"
      button.style.transform = "translateY(0)"
    }, 500)
  })
}

/**
 * Handle form submission success
 * @param {HTMLElement} form - The form element
 * @param {string} message - Success message to display
 */
function handleFormSuccess(form, message) {
  // Reset form if needed
  form.reset()

  // Show success notification
  showNotification(message, "success")

  // Reset button state
  const submitBtn = form.querySelector('button[type="submit"]')
  if (submitBtn) {
    submitBtn.disabled = false
    submitBtn.innerHTML = submitBtn.getAttribute("data-original-text") || "Submit"
  }
}

/**
 * Handle form submission error
 * @param {HTMLElement} form - The form element
 * @param {string} message - Error message to display
 */
function handleFormError(form, message) {
  // Show error notification
  showNotification(message, "error")

  // Reset button state
  const submitBtn = form.querySelector('button[type="submit"]')
  if (submitBtn) {
    submitBtn.disabled = false
    submitBtn.innerHTML = submitBtn.getAttribute("data-original-text") || "Submit"
  }

  // Shake form
  form.style.animation = "shake 0.5s ease"
  setTimeout(() => {
    form.style.animation = ""
  }, 500)
}

// Save original button text for reset
document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll('button[type="submit"]')
  buttons.forEach((button) => {
    button.setAttribute("data-original-text", button.innerHTML)
  })
})

