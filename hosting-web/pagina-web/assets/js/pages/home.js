/**
 * USOLUTIONS - Home Page JavaScript
 */

document.addEventListener("DOMContentLoaded", () => {
    // Testimonial slider
    initTestimonialSlider()
  
    // Feature cards animation
    initFeatureCardsAnimation()
  })
  
  /**
   * Initialize testimonial slider
   */
  function initTestimonialSlider() {
    const testimonials = document.querySelectorAll(".testimonial")
    let currentTestimonial = 0
  
    function showTestimonial(index) {
      testimonials.forEach((testimonial) => {
        testimonial.style.display = "none"
        testimonial.classList.remove("active")
      })
  
      testimonials[index].style.display = "block"
  
      // Add a slight delay before adding the active class for animation
      setTimeout(() => {
        testimonials[index].classList.add("active")
      }, 50)
    }
  
    function nextTestimonial() {
      currentTestimonial = (currentTestimonial + 1) % testimonials.length
      showTestimonial(currentTestimonial)
    }
  
    if (testimonials.length > 0) {
      showTestimonial(currentTestimonial)
  
      // Create navigation dots
      const sliderContainer = document.querySelector(".testimonial-slider")
      if (sliderContainer) {
        const dotsContainer = document.createElement("div")
        dotsContainer.className = "slider-dots"
  
        testimonials.forEach((_, index) => {
          const dot = document.createElement("span")
          dot.className = "slider-dot"
          if (index === 0) dot.classList.add("active")
  
          dot.addEventListener("click", () => {
            currentTestimonial = index
            showTestimonial(currentTestimonial)
  
            // Update active dot
            document.querySelectorAll(".slider-dot").forEach((d, i) => {
              if (i === index) {
                d.classList.add("active")
              } else {
                d.classList.remove("active")
              }
            })
          })
  
          dotsContainer.appendChild(dot)
        })
  
        sliderContainer.appendChild(dotsContainer)
      }
  
      // Auto-advance the slider
      setInterval(nextTestimonial, 5000)
    }
  }
  
  /**
   * Initialize feature cards animation
   */
  function initFeatureCardsAnimation() {
    const featureCards = document.querySelectorAll(".feature-card")
  
    if (featureCards.length > 0) {
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add("animate")
            }
          })
        },
        { threshold: 0.2 },
      )
  
      featureCards.forEach((card) => {
        observer.observe(card)
      })
    }
  }
  
  