document.addEventListener("DOMContentLoaded", () => {
  // Get all payment method radio buttons
  const paymentMethods = document.querySelectorAll('input[name="payment_method"]')

  // Add event listeners to each payment method
  paymentMethods.forEach((method) => {
    method.addEventListener("change", function () {
      // Remove active class from all labels
      document.querySelectorAll(".payment-method label").forEach((label) => {
        label.classList.remove("active")
      })

      // Add active class to selected label
      if (this.checked) {
        this.nextElementSibling.classList.add("active")
      }
    })
  })

  // Form validation
  const paymentForm = document.querySelector(".payment-form form")
  if (paymentForm) {
    paymentForm.addEventListener("submit", (e) => {
      // Check if a payment method is selected
      const selectedMethod = document.querySelector('input[name="payment_method"]:checked')
      if (!selectedMethod) {
        e.preventDefault()

        // Create error message if it doesn't exist
        let errorDiv = document.querySelector(".error-messages")
        if (!errorDiv) {
          errorDiv = document.createElement("div")
          errorDiv.className = "error-messages"
          const ul = document.createElement("ul")
          errorDiv.appendChild(ul)

          // Insert before the form
          paymentForm.parentNode.insertBefore(errorDiv, paymentForm)
        }

        // Add error message
        const ul = errorDiv.querySelector("ul")
        ul.innerHTML = "<li>Please select a payment method</li>"

        // Scroll to error message
        errorDiv.scrollIntoView({ behavior: "smooth", block: "start" })
      }
    })
  }
})

document.addEventListener("DOMContentLoaded", () => {
  // Get all payment method radio buttons
  const paymentMethods = document.querySelectorAll('input[name="payment_method"]')
  const paymentForm = document.querySelector(".payment-form form")
  const summaryRows = document.querySelectorAll(".summary-row")

  // Add staggered animation to summary rows
  if (summaryRows.length > 0) {
    summaryRows.forEach((row, index) => {
      row.style.opacity = "0"
      row.style.transform = "translateY(20px)"
      row.style.transition = "opacity 0.3s ease, transform 0.3s ease"

      setTimeout(
        () => {
          row.style.opacity = "1"
          row.style.transform = "translateY(0)"
        },
        100 + index * 50,
      )
    })
  }

  // Add animation to payment methods
  const paymentMethodLabels = document.querySelectorAll(".payment-method label")
  if (paymentMethodLabels.length > 0) {
    paymentMethodLabels.forEach((label, index) => {
      label.style.opacity = "0"
      label.style.transform = "translateY(20px)"
      label.style.transition = "opacity 0.3s ease, transform 0.3s ease"

      setTimeout(
        () => {
          label.style.opacity = "1"
          label.style.transform = "translateY(0)"
        },
        300 + index * 100,
      )
    })
  }

  // Add event listeners to each payment method
  paymentMethods.forEach((method) => {
    method.addEventListener("change", function () {
      // Remove active class from all labels
      document.querySelectorAll(".payment-method label").forEach((label) => {
        label.classList.remove("active")
      })

      // Add active class to selected label
      if (this.checked) {
        const label = this.nextElementSibling
        label.classList.add("active")

        // Add animation to the selected payment method
        label.style.transform = "translateY(-3px)"

        // Animate the icon
        const icon = label.querySelector(".payment-icon")
        if (icon) {
          icon.style.transform = "scale(1.1)"
          setTimeout(() => {
            icon.style.transform = "scale(1)"
          }, 300)
        }
      }
    })

    // Check if any method is already selected (e.g., after form submission)
    if (method.checked) {
      const label = method.nextElementSibling
      label.classList.add("active")
    }
  })

  // Add keyboard navigation for better accessibility
  paymentMethodLabels.forEach((label) => {
    label.addEventListener("keydown", function (e) {
      // Space or Enter key
      if (e.keyCode === 32 || e.keyCode === 13) {
        e.preventDefault()
        const radio = this.previousElementSibling
        radio.checked = true

        // Trigger the change event
        const event = new Event("change")
        radio.dispatchEvent(event)
      }
    })

    // Add tabindex for keyboard navigation
    label.setAttribute("tabindex", "0")
  })

  // Enhanced form validation
  if (paymentForm) {
    paymentForm.addEventListener("submit", (e) => {
      // Check if a payment method is selected
      const selectedMethod = document.querySelector('input[name="payment_method"]:checked')
      if (!selectedMethod) {
        e.preventDefault()

        // Create error message if it doesn't exist
        let errorDiv = document.querySelector(".error-messages")
        if (!errorDiv) {
          errorDiv = document.createElement("div")
          errorDiv.className = "error-messages"
          const ul = document.createElement("ul")
          errorDiv.appendChild(ul)

          // Insert before the form
          paymentForm.parentNode.insertBefore(errorDiv, paymentForm)
        }

        // Add error message
        const ul = errorDiv.querySelector("ul")
        ul.innerHTML = "<li>Please select a payment method</li>"

        // Scroll to error message with smooth animation
        errorDiv.scrollIntoView({ behavior: "smooth", block: "start" })

        // Highlight payment methods to draw attention
        paymentMethodLabels.forEach((label) => {
          label.style.borderColor = "var(--payment-danger)"
          setTimeout(() => {
            label.style.borderColor = ""
          }, 2000)
        })
      } else {
        // Add loading state to submit button
        const submitBtn = paymentForm.querySelector('button[type="submit"]')
        if (submitBtn) {
          submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...'
          submitBtn.disabled = true
        }
      }
    })
  }

  // Add hover effect to payment methods
  paymentMethodLabels.forEach((label) => {
    label.addEventListener("mouseenter", function () {
      if (!this.classList.contains("active")) {
        this.style.transform = "translateY(-3px)"
      }
    })

    label.addEventListener("mouseleave", function () {
      if (!this.classList.contains("active")) {
        this.style.transform = ""
      }
    })
  })

  // Add focus styles for accessibility
  paymentMethodLabels.forEach((label) => {
    label.addEventListener("focus", function () {
      this.style.boxShadow = "0 0 0 3px rgba(74, 108, 247, 0.3)"
    })

    label.addEventListener("blur", function () {
      this.style.boxShadow = ""
    })
  })

  // Add animation to the payment notice
  const paymentNotice = document.querySelector(".payment-notice")
  if (paymentNotice) {
    paymentNotice.style.opacity = "0"
    paymentNotice.style.transform = "translateY(20px)"
    paymentNotice.style.transition = "opacity 0.3s ease, transform 0.3s ease"

    setTimeout(() => {
      paymentNotice.style.opacity = "1"
      paymentNotice.style.transform = "translateY(0)"
    }, 600)
  }

  // Add animation to the form actions
  const formActions = document.querySelector(".form-actions")
  if (formActions) {
    formActions.style.opacity = "0"
    formActions.style.transform = "translateY(20px)"
    formActions.style.transition = "opacity 0.3s ease, transform 0.3s ease"

    setTimeout(() => {
      formActions.style.opacity = "1"
      formActions.style.transform = "translateY(0)"
    }, 700)
  }

  // Automatically select PayPal if it exists (since it's free for testing)
  const paypalRadio = document.getElementById("paypal")
  if (paypalRadio && !document.querySelector('input[name="payment_method"]:checked')) {
    // Only auto-select if no method is already selected
    setTimeout(() => {
      paypalRadio.checked = true

      // Trigger the change event
      const event = new Event("change")
      paypalRadio.dispatchEvent(event)

      // Add a subtle highlight
      const paypalLabel = paypalRadio.nextElementSibling
      paypalLabel.style.boxShadow = "0 0 15px rgba(74, 108, 247, 0.3)"
      setTimeout(() => {
        paypalLabel.style.boxShadow = ""
      }, 1500)
    }, 1000)
  }
})

