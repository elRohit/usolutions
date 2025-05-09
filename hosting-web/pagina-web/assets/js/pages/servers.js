/**
 * USOLUTIONS - Servers Page JavaScript
 */

document.addEventListener("DOMContentLoaded", () => {
    // Server tabs functionality
    initServerTabs()
  
    // FAQ accordion
    initFaqAccordion()
  
    // Server card animations
    initServerCardAnimations()
  })
  
  /**
   * Initialize server tabs functionality
   */
  function initServerTabs() {
    const tabs = document.querySelectorAll(".server-tabs .tab")
  
    tabs.forEach((tab) => {
      tab.addEventListener("click", function (e) {
        // We don't prevent default here because we want the page to navigate
  
        // Add active class to clicked tab (for when JS is enabled but page doesn't reload)
        tabs.forEach((t) => t.classList.remove("active"))
        this.classList.add("active")
      })
    })
  }
  
  /**
   * Initialize FAQ accordion
   */
  function initFaqAccordion() {
    const faqItems = document.querySelectorAll(".faq-item")
  
    faqItems.forEach((item) => {
      const heading = item.querySelector("h3")
  
      if (heading) {
        heading.addEventListener("click", () => {
          // Toggle active class on the FAQ item
          item.classList.toggle("active")
  
          // Close other FAQ items
          faqItems.forEach((otherItem) => {
            if (otherItem !== item && otherItem.classList.contains("active")) {
              otherItem.classList.remove("active")
            }
          })
        })
      }
    })
  }
  
  /**
   * Initialize server card animations
   */
  function initServerCardAnimations() {
    const serverCards = document.querySelectorAll(".server-card")
  
    if (serverCards.length > 0) {
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add("animate")
            }
          })
        },
        { threshold: 0.1 },
      )
  
      serverCards.forEach((card) => {
        observer.observe(card)
      })
    }
  }
  
  