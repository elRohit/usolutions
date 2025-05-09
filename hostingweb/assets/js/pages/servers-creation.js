document.addEventListener("DOMContentLoaded", () => {
    // Initialize the loading animation
    initializeLoading()
  
    // Start the countdown for redirect (if applicable)
    const redirectCountdown = document.querySelector("#redirect-countdown")
    if (redirectCountdown) {
      startRedirectCountdown(redirectCountdown)
    }
  })
  
  /**
   * Initialize the loading animation and step progression
   */
  function initializeLoading() {
    const steps = document.querySelectorAll(".creation-steps li")
    if (!steps.length) return
  
    // Add the 'step' class to all steps for initial styling
    steps.forEach((step) => step.classList.add("step"))
  
    // Set the first step as current
    steps[0].classList.add("current")
  
    // Simulate step progression
    simulateStepProgression(steps)
  
    // Initialize the elapsed time counter
    initializeTimeCounter()
  }
  
  /**
   * Simulate the progression through the creation steps
   * @param {NodeList} steps - The list of step elements
   */
  function simulateStepProgression(steps) {
    // Define the time for each step (in milliseconds)
    const stepTimes = [
      3000, // Step 1: Allocating resources
      5000, // Step 2: Setting up OS
      4000, // Step 3: Configuring network
      3000, // Step 4: Installing software
      2000, // Step 5: Finalizing security
    ]
  
    let currentStep = 0
  
    // Function to progress to the next step
    function progressToNextStep() {
      if (currentStep >= steps.length) return
  
      // Mark the current step as completed
      steps[currentStep].classList.remove("current")
      steps[currentStep].classList.add("completed")
  
      currentStep++
  
      // If there are more steps, mark the next one as current
      if (currentStep < steps.length) {
        steps[currentStep].classList.add("current")
  
        // Schedule the next step progression
        setTimeout(progressToNextStep, stepTimes[currentStep] || 3000)
      } else {
        // All steps completed, show the redirect message if it exists
        const redirectMessage = document.querySelector(".redirect-message")
        if (redirectMessage) {
          redirectMessage.style.display = "block"
  
          // Add a subtle animation to the redirect message
          redirectMessage.style.animation = "fadeInUp 0.5s ease-out"
        }
      }
    }
  
    // Start the progression after the first step time
    setTimeout(progressToNextStep, stepTimes[0])
  }
  
  /**
   * Initialize the elapsed time counter
   */
  function initializeTimeCounter() {
    const timeCounter = document.querySelector("#elapsed-time")
    if (!timeCounter) return
  
    let seconds = 0
  
    // Update the counter every second
    setInterval(() => {
      seconds++
      const minutes = Math.floor(seconds / 60)
      const remainingSeconds = seconds % 60
  
      // Format the time as MM:SS
      timeCounter.textContent = `${minutes.toString().padStart(2, "0")}:${remainingSeconds.toString().padStart(2, "0")}`
  
      // Add a subtle pulse animation on each update
      timeCounter.style.animation = "none"
      timeCounter.offsetHeight // Trigger reflow
      timeCounter.style.animation = "pulse 1s ease-out"
    }, 1000)
  }
  
  /**
   * Start the countdown for redirect
   * @param {HTMLElement} countdownElement - The element to display the countdown
   */
  function startRedirectCountdown(countdownElement) {
    let seconds = Number.parseInt(countdownElement.textContent) || 5
  
    // Update the countdown every second
    const countdownInterval = setInterval(() => {
      seconds--
      countdownElement.textContent = seconds
  
      // Add a scale animation on each count
      countdownElement.style.animation = "none"
      countdownElement.offsetHeight // Trigger reflow
      countdownElement.style.animation = "scale 0.5s ease-out"
  
      if (seconds <= 0) {
        clearInterval(countdownInterval)
      }
    }, 1000)
  }
  
  // Additional animation for the scale effect
  document.head.insertAdjacentHTML(
    "beforeend",
    `
      <style>
          @keyframes scale {
              0% { transform: scale(1.2); }
              100% { transform: scale(1); }
          }
      </style>
  `,
  )
  