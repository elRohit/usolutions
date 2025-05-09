/**
 * USOLUTIONS - Contact Page JavaScript
 */

document.addEventListener("DOMContentLoaded", () => {
    // Form validation
    initContactFormValidation()
  
    // Map initialization (if needed)
    // initMap();
  
    // Info items animation
    initInfoItemsAnimation()
  })
  
  /**
   * Initialize contact form validation
   */
  function initContactFormValidation() {
    const contactForm = document.querySelector(".contact-form form")
  
    if (contactForm) {
      contactForm.addEventListener("submit", (e) => {
        let isValid = true
        const nameInput = document.getElementById("name")
        const emailInput = document.getElementById("email")
        const subjectInput = document.getElementById("subject")
        const messageInput = document.getElementById("message")
  
        // Reset previous error messages
        document.querySelectorAll(".error-message").forEach((el) => el.remove())
  
        // Validate name
        if (!nameInput.value.trim()) {
          isValid = false
          showError(nameInput, "Please enter your name")
        }
  
        // Validate email
        if (!emailInput.value.trim()) {
          isValid = false
          showError(emailInput, "Please enter your email")
        } else if (!isValidEmail(emailInput.value.trim())) {
          isValid = false
          showError(emailInput, "Please enter a valid email address")
        }
  
        // Validate subject
        if (!subjectInput.value.trim()) {
          isValid = false
          showError(subjectInput, "Please enter a subject")
        }
  
        // Validate message
        if (!messageInput.value.trim()) {
          isValid = false
          showError(messageInput, "Please enter your message")
        }
  
        // If the form is not valid, prevent submission
        if (!isValid) {
          e.preventDefault()
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
  
  /**
   * Initialize info items animation
   */
  function initInfoItemsAnimation() {
    const infoItems = document.querySelectorAll(".info-item")
  
    if (infoItems.length > 0) {
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
              // Add delay based on index for staggered animation
              setTimeout(() => {
                entry.target.classList.add("animate")
              }, index * 100)
            }
          })
        },
        { threshold: 0.2 },
      )
  
      infoItems.forEach((item) => {
        observer.observe(item)
      })
    }
  }
  
  