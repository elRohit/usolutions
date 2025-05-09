/**
 * USOLUTIONS - Dashboard JavaScript
 * Modern, simplified approach
 */

// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", () => {
  // Initialize dashboard if we're on the dashboard page
  if (document.querySelector(".dashboard")) {
    initializeDashboard()
  }
})

/**
 * Main initialization function
 */
function initializeDashboard() {
  // Set current date in dashboard header
  setCurrentDate()

  // Initialize navigation
  initNavigation()

  // Initialize server cards
  initServerCards()

  // Initialize tables
  initTables()

  // Initialize responsive menu
  initResponsiveMenu()

  // Initialize stats counters
  initStatsCounters()
}

/**
 * Set current date in dashboard header
 */
function setCurrentDate() {
  const dateElement = document.querySelector(".dashboard-date")
  if (dateElement) {
    const now = new Date()
    const options = { weekday: "long", year: "numeric", month: "long", day: "numeric" }
    dateElement.innerHTML = `<i class="far fa-calendar-alt"></i> ${now.toLocaleDateString("en-US", options)}`
  }
}

/**
 * Initialize dashboard navigation
 */
function initNavigation() {
  const navLinks = document.querySelectorAll(".menu-link")
  const sections = document.querySelectorAll(".dashboard-section")

  // Hide all sections except the first one
  sections.forEach((section, index) => {
    if (index !== 0) {
      section.style.display = "none"
    }
  })

  // Set first link as active
  if (navLinks.length > 0) {
    navLinks[0].classList.add("active")
  }

  // Add click event to each nav link
  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
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
          section.style.display = "none"
        })

        // Show target section
        targetSection.style.display = "block"

        // Update URL hash
        history.pushState(null, null, `#${targetId}`)

        // Close mobile menu if open
        const mobileMenu = document.querySelector(".dashboard-menu")
        if (mobileMenu && mobileMenu.classList.contains("mobile-open")) {
          mobileMenu.classList.remove("mobile-open")
        }
      }
    })
  })

  // Check for hash in URL
  if (window.location.hash) {
    const hash = window.location.hash.substring(1)
    const hashLink = document.querySelector(`.menu-link[href="#${hash}"]`)

    if (hashLink) {
      hashLink.click()
    }
  }
}

/**
 * Initialize server cards with hover effects and status indicators
 */
function initServerCards() {
  const serverCards = document.querySelectorAll(".server-card")

  serverCards.forEach((card) => {
    // Add hover effect
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-5px)"
    })

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0)"
    })

    // Add status indicator pulse effect
    const status = card.querySelector(".server-status")
    if (status && status.classList.contains("active")) {
      const icon = status.querySelector("i")
      if (icon) {
        setInterval(() => {
          icon.style.opacity = "0.5"
          setTimeout(() => {
            icon.style.opacity = "1"
          }, 500)
        }, 1500)
      }
    }

    // Add server management link handling
    const manageLinks = card.querySelectorAll(".server-btn-primary")
    manageLinks.forEach((link) => {
      if (link.getAttribute("href").startsWith("http")) {
        link.addEventListener("click", (e) => {
          // Add a confirmation if needed
          if (!confirm("You will be redirected to your server management interface. Continue?")) {
            e.preventDefault()
          }
        })
      }
    })
  })
}

/**
 * Initialize tables with sorting and filtering
 */
function initTables() {
  const tables = document.querySelectorAll(".data-table")

  tables.forEach((table) => {
    // Add table wrapper if not already wrapped
    if (!table.parentElement.classList.contains("table-wrapper")) {
      const wrapper = document.createElement("div")
      wrapper.className = "table-wrapper"
      table.parentNode.insertBefore(wrapper, table)
      wrapper.appendChild(table)
    }

    // Add search input
    const tableContainer = table.closest(".panel-content")
    if (tableContainer) {
      const searchContainer = document.createElement("div")
      searchContainer.className = "table-search"
      searchContainer.innerHTML = `
        <input type="text" placeholder="Search..." class="search-input">
        <i class="fas fa-search search-icon"></i>
      `

      tableContainer.insertBefore(searchContainer, tableContainer.firstChild)

      // Add search functionality
      const searchInput = searchContainer.querySelector(".search-input")
      searchInput.addEventListener("input", function () {
        const searchTerm = this.value.toLowerCase()
        const rows = table.querySelectorAll("tbody tr")

        rows.forEach((row) => {
          const text = row.textContent.toLowerCase()
          row.style.display = text.includes(searchTerm) ? "" : "none"
        })
      })
    }

    // Add sorting functionality
    const headers = table.querySelectorAll("th")
    headers.forEach((header, index) => {
      if (header.classList.contains("no-sort")) {
        return
      }

      header.style.cursor = "pointer"
      header.innerHTML += ' <i class="fas fa-sort sort-icon"></i>'

      header.addEventListener("click", function () {
        sortTable(table, index, this)
      })
    })
  })
}

/**
 * Sort table by column
 */
function sortTable(table, columnIndex, header) {
  const sortDirection = header.getAttribute("data-sort") === "asc" ? "desc" : "asc"

  // Update sort icons
  table.querySelectorAll("th").forEach((th) => {
    th.setAttribute("data-sort", "")
    const icon = th.querySelector(".sort-icon")
    if (icon) {
      icon.className = "fas fa-sort sort-icon"
    }
  })

  // Update clicked header
  header.setAttribute("data-sort", sortDirection)
  const icon = header.querySelector(".sort-icon")
  if (icon) {
    icon.className = `fas fa-sort-${sortDirection === "asc" ? "up" : "down"} sort-icon`
  }

  // Get rows and sort
  const tbody = table.querySelector("tbody")
  const rows = Array.from(tbody.querySelectorAll("tr"))

  rows.sort((a, b) => {
    let aValue = a.cells[columnIndex].textContent.trim()
    let bValue = b.cells[columnIndex].textContent.trim()

    // Check for status badges
    if (a.cells[columnIndex].querySelector(".table-status")) {
      aValue = a.cells[columnIndex].querySelector(".table-status").textContent.trim()
      bValue = b.cells[columnIndex].querySelector(".table-status").textContent.trim()
    }

    // Check for numbers
    if (!isNaN(Number.parseFloat(aValue)) && !isNaN(Number.parseFloat(bValue))) {
      return sortDirection === "asc"
        ? Number.parseFloat(aValue) - Number.parseFloat(bValue)
        : Number.parseFloat(bValue) - Number.parseFloat(aValue)
    }

    // Check for dates
    const aDate = new Date(aValue)
    const bDate = new Date(bValue)
    if (!isNaN(aDate) && !isNaN(bDate)) {
      return sortDirection === "asc" ? aDate - bDate : bDate - aDate
    }

    // Default string comparison
    return sortDirection === "asc" ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue)
  })

  // Reappend sorted rows
  rows.forEach((row) => {
    tbody.appendChild(row)
  })
}

/**
 * Initialize responsive menu for mobile devices
 */
function initResponsiveMenu() {
  // Create toggle button
  const dashboardContainer = document.querySelector(".dashboard-container")
  const menu = document.querySelector(".dashboard-menu")

  if (dashboardContainer && menu) {
    const toggleBtn = document.createElement("button")
    toggleBtn.className = "menu-toggle"
    toggleBtn.innerHTML = '<i class="fas fa-bars"></i>'
    toggleBtn.setAttribute("aria-label", "Toggle menu")

    dashboardContainer.insertBefore(toggleBtn, dashboardContainer.firstChild)

    // Add toggle functionality
    toggleBtn.addEventListener("click", function () {
      menu.classList.toggle("mobile-open")
      this.innerHTML = menu.classList.contains("mobile-open")
        ? '<i class="fas fa-times"></i>'
        : '<i class="fas fa-bars"></i>'
    })

    // Add CSS for mobile menu
    const style = document.createElement("style")
    style.textContent = `
      .menu-toggle {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--dash-secondary);
        color: white;
        border: none;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        z-index: 100;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        font-size: 18px;
      }
      
      @media (max-width: 992px) {
        .menu-toggle {
          display: flex;
        }
        
        .dashboard-menu {
          position: fixed;
          left: -300px;
          top: 0;
          height: 100vh;
          z-index: 99;
          transition: left 0.3s ease;
          width: 280px;
        }
        
        .dashboard-menu.mobile-open {
          left: 0;
          box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
      }
    `

    document.head.appendChild(style)
  }
}

/**
 * Initialize stats counters with animation
 */
function initStatsCounters() {
  const statNumbers = document.querySelectorAll(".stat-number")

  statNumbers.forEach((stat) => {
    const finalValue = Number.parseInt(stat.textContent, 10)

    if (!isNaN(finalValue)) {
      // Start from zero
      let currentValue = 0
      stat.textContent = "0"

      // Animate to final value
      const duration = 1000 // ms
      const interval = 20 // ms
      const increment = finalValue / (duration / interval)

      const counter = setInterval(() => {
        currentValue += increment

        if (currentValue >= finalValue) {
          stat.textContent = finalValue.toString()
          clearInterval(counter)
        } else {
          stat.textContent = Math.floor(currentValue).toString()
        }
      }, interval)
    }
  })
}

/**
 * Add panel toggle functionality
 */
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("panel-btn") && e.target.dataset.action === "toggle") {
    const panel = e.target.closest(".content-panel")
    const content = panel.querySelector(".panel-content")

    if (content) {
      if (content.style.display === "none") {
        content.style.display = "block"
        e.target.innerHTML = '<i class="fas fa-chevron-up"></i>'
      } else {
        content.style.display = "none"
        e.target.innerHTML = '<i class="fas fa-chevron-down"></i>'
      }
    }
  }
})

/**
 * Add refresh functionality
 */
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("panel-btn") && e.target.dataset.action === "refresh") {
    const panel = e.target.closest(".content-panel")

    // Add loading spinner
    e.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'

    // Simulate refresh
    setTimeout(() => {
      e.target.innerHTML = '<i class="fas fa-sync-alt"></i>'
    }, 1000)
  }
})

/**
 * Handle empty search results
 */
function handleEmptySearchResults() {
  const searchInputs = document.querySelectorAll(".search-input")

  searchInputs.forEach((input) => {
    input.addEventListener("input", function () {
      const tableWrapper = this.closest(".panel-content").querySelector(".table-wrapper")
      const table = tableWrapper.querySelector(".data-table")
      const rows = table.querySelectorAll("tbody tr")

      // Check if all rows are hidden
      const allHidden = Array.from(rows).every((row) => row.style.display === "none")

      // Remove existing no-results message if it exists
      const existingMessage = tableWrapper.querySelector(".no-results")
      if (existingMessage) {
        existingMessage.remove()
      }

      // Add no-results message if all rows are hidden
      if (allHidden && this.value.trim() !== "") {
        const noResults = document.createElement("div")
        noResults.className = "no-results"
        noResults.textContent = `No results found for "${this.value}"`
        tableWrapper.appendChild(noResults)
      }
    })
  })
}

// Call the function after tables are initialized
document.addEventListener("DOMContentLoaded", () => {
  if (document.querySelector(".dashboard")) {
    setTimeout(handleEmptySearchResults, 500)
  }
})
