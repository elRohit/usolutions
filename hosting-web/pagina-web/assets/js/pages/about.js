/**
 * USOLUTIONS - About Page JavaScript
 */

document.addEventListener("DOMContentLoaded", () => {
    // Team member hover effect
    initTeamMemberHover()
  
    // Values cards animation
    initValuesAnimation()
  })
  
  /**
   * Initialize team member hover effect
   */
  function initTeamMemberHover() {
    const teamMembers = document.querySelectorAll(".team-member")
  
    teamMembers.forEach((member) => {
      member.addEventListener("mouseenter", function () {
        this.classList.add("hovered")
      })
  
      member.addEventListener("mouseleave", function () {
        this.classList.remove("hovered")
      })
    })
  }
  
  /**
   * Initialize values cards animation
   */
  function initValuesAnimation() {
    const valueCards = document.querySelectorAll(".value-card")
  
    if (valueCards.length > 0) {
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
  
      valueCards.forEach((card) => {
        observer.observe(card)
      })
    }
  }
  
  