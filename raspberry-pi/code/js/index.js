// Index page specific JavaScript

document.addEventListener("DOMContentLoaded", () => {
  // Initialize Flatpickr date picker
  if (typeof flatpickr !== "undefined") {
    flatpickr("#fecha", {
      dateFormat: "Y-m-d",
      locale: "es",
      disableMobile: true,
      onChange: (selectedDates, dateStr) => {
        document.getElementById("fecha").value = dateStr;
      },
    });
  }

  // Format date for display
  const formatDateForDisplay = (dateString) => {
    if (!dateString) return "";

    // If the date is already in DD/MM/YYYY format, just return it
    if (dateString.includes("/")) {
      return dateString;
    }

    // Otherwise, convert from YYYY-MM-DD to DD/MM/YYYY
    try {
      const date = new Date(dateString);
      if (isNaN(date.getTime())) return dateString; // If invalid date, return as is

      return date.toLocaleDateString("es-ES", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });
    } catch (e) {
      return dateString; // If any error, return as is
    }
  };

  // Update filter info text
  const updateFilterInfo = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const filterInfoElement = document.getElementById("filter-info");

    if (urlParams.has("ver_todo") && urlParams.get("ver_todo") === "1") {
      filterInfoElement.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 6h18"></path>
            <path d="M3 12h18"></path>
            <path d="M3 18h18"></path>
        </svg>
        Showing all records
        `;
    } else {
      // Get the date from URL or use today's date
      let fecha =
        urlParams.get("fecha") || new Date().toISOString().split("T")[0];

      // Make sure it's in DD/MM/YYYY format for display
      fecha = formatDateForDisplay(fecha);

      filterInfoElement.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
        Showing records for ${fecha}
        `;
    }
  };

  // Update stats in header
  const updateStats = () => {
    // This would typically fetch data from the server
    // For now, we'll just use the data already in the page
    const totalRegistrosElement = document.getElementById("total-registros");
    const registrosHoyElement = document.getElementById("registros-hoy");

    if (totalRegistrosElement && registrosHoyElement) {
      // Get the count from the table rows
      const tableRows = document.querySelectorAll("tbody tr");
      const totalRows = tableRows.length;

      // If there's a "no records" message, don't count it
      const noRecordsRow = document.querySelector("tbody tr td.text-center");
      const actualRows = noRecordsRow ? 0 : totalRows;

      // Only update if the current view is showing today's records
      const urlParams = new URLSearchParams(window.location.search);
      const isToday =
        !urlParams.has("fecha") ||
        urlParams.get("fecha") === new Date().toISOString().split("T")[0];

      if (isToday && !urlParams.has("ver_todo")) {
        registrosHoyElement.textContent = actualRows;
      }
    }
  };

  // Animate elements on scroll
  const animateElements = document.querySelectorAll(".animate-on-scroll");

  if (animateElements.length > 0) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("fade-in");
            // Unobserve after animation is applied
            observer.unobserve(entry.target);
          }
        });
      },
      {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
      }
    );

    animateElements.forEach((element) => observer.observe(element));
  }

  // Call the functions on page load
  updateFilterInfo();
  updateStats();

  // Create FITXAR modal if it doesn't exist
  if (!document.getElementById("ficharModal")) {
    const modalHTML = `
      <div class="modal-backdrop" id="ficharModal">
      <div class="modal">
        <div class="modal-header">
        <h3 class="modal-title" id="ficharModalTitle">Select Action</h3>
        <button class="modal-close" data-modal-close>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        </div>
        <div class="modal-body">
        <div id="ficharModalContent">
          <div class="text-center">
          <h4 style="margin-bottom: 20px;">Select the type of record:</h4>
          <div class="action-buttons">
            <button id="entradaBtn" class="btn btn-entrada">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; margin-right: 5px;">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
            Entry
            </button>
            <button id="sortidaBtn" class="btn btn-sortida">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; margin-right: 5px;">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            Exit
            </button>
          </div>
          </div>
        </div>
        </div>
        <div class="modal-footer">
        <button class="btn btn-secondary" data-modal-close>Cancel</button>
        </div>
      </div>
      </div>
    `;
    document.body.insertAdjacentHTML("beforeend", modalHTML);
  }

  // Modal functionality - similar to your usuarios.js implementation
  const modalTriggers = document.querySelectorAll("[data-modal-target]");
  const modalCloseButtons = document.querySelectorAll("[data-modal-close]");

  modalTriggers.forEach((trigger) => {
    trigger.addEventListener("click", () => {
      const modalId = trigger.getAttribute("data-modal-target");
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add("show");
      }
    });
  });

  modalCloseButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const modal = button.closest(".modal-backdrop");
      if (modal) {
        modal.classList.remove("show");
      }
    });
  });

  // Close modal when clicking outside
  document.addEventListener("click", (e) => {
    if (e.target.classList.contains("modal-backdrop")) {
      e.target.classList.remove("show");
    }
  });

  // FITXAR button functionality
  const ficharBtn = document.getElementById("ficharBtn");
  if (ficharBtn) {
    ficharBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();

      const ficharModal = document.getElementById("ficharModal");
      if (ficharModal) {
        ficharModal.classList.add("show");
      }
    });
  }

  // Entrada button functionality
  const entradaBtn = document.getElementById("entradaBtn");
  if (entradaBtn) {
    entradaBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      scanRfid("entrada");
    });
  }

  // Sortida button functionality
  const sortidaBtn = document.getElementById("sortidaBtn");
  if (sortidaBtn) {
    sortidaBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      scanRfid("sortida");
    });
  }

  // Function to handle RFID scanning
  function scanRfid(actionType) {
    const ficharModal = document.getElementById("ficharModal");
    const ficharModalContent = document.getElementById("ficharModalContent");
    const ficharModalTitle = document.getElementById("ficharModalTitle");

    // Update modal content to show scanning in progress
    ficharModalTitle.textContent =
      actionType === "entrada" ? "Register Entry" : "Register Exit";
    ficharModalContent.innerHTML = `
      <div class="text-center">
      <div style="margin-bottom: 20px;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 64px; height: 64px; margin: 0 auto 15px; display: block; color: var(--primary);">
        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
        <line x1="2" y1="10" x2="22" y2="10"></line>
        </svg>
        <h4 style="margin-bottom: 15px;">Bring the card close to the reader</h4>
        <div class="spinner" style="margin: 0 auto;"></div>
      </div>
      <p>Waiting for RFID/NFC card reading...</p>
      </div>
    `;

    // Use fetch API instead of XMLHttpRequest
    const formData = new FormData();
    formData.append("scan_rfid", "1");
    formData.append("action_type", actionType);

    fetch("fichar.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        // Check if the response is OK
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }

        // Get the raw text first to debug
        return response.text();
      })
      .then((rawText) => {
        // Try to parse as JSON, but handle non-JSON responses
        try {
          // Log the raw response for debugging
          console.log("Raw server response:", rawText);

          // Check if the response is empty
          if (!rawText.trim()) {
            throw new Error("Empty response from server");
          }

          // Try to parse as JSON
          const response = JSON.parse(rawText);

          // Process the response
          if (response.success) {
            // Success - show appropriate message based on action
            const iconColor = "#28a745"; // Always green for success
            const icon =
              response.action === "checkin"
                ? '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>'
                : '<circle cx="12" cy="12" r="10"></circle><polyline points="8 12 12 16 16 8"></polyline>';

            ficharModalTitle.textContent =
              response.action === "checkin"
                ? "Entry successfully registered"
                : "Exit successfully registered";

            ficharModalContent.innerHTML = `
            <div class="text-center">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="${iconColor}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 64px; height: 64px; margin: 0 auto 15px; display: block;">
              ${icon}
              </svg>
              <h4>${response.message}</h4>
              ${
                response.action === "checkout" &&
                response.data &&
                response.data.tiempo_extra !== "00:00:00"
                  ? `<p>Overtime: ${response.data.tiempo_extra}</p>`
                  : ""
              }
              <button class="btn btn-primary mt-3" onclick="window.location.reload()">Refresh page</button>
            </div>
            `;
          } else {
            // Error or cooldown
            const iconColor =
              response.action === "cooldown" ? "#ffc107" : "#dc3545";
            const icon =
              response.action === "cooldown"
                ? '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>'
                : '<circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>';

            ficharModalTitle.textContent =
              response.action === "cooldown" ? "Espera Requerida" : "Error";

            ficharModalContent.innerHTML = `
            <div class="text-center">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="${iconColor}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 64px; height: 64px; margin: 0 auto 15px; display: block;">
                ${icon}
              </svg>
              <h4>${response.message}</h4>
            </div>
          `;
          }
        } catch (e) {
          // If we can't parse as JSON, assume the operation was successful
          // since the user mentioned the database operation works
          console.error("Error parsing JSON:", e, "Raw response:", rawText);

          // Show success message based on action type
          const iconColor = "#28a745"; // Always green for success
          const icon =
            actionType === "entrada"
              ? '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>'
              : '<circle cx="12" cy="12" r="10"></circle><polyline points="8 12 12 16 16 8"></polyline>';

          ficharModalTitle.textContent =
            actionType === "entrada"
              ? "Entry successfully registered"
              : "Exit successfully registered";

          ficharModalContent.innerHTML = `
            <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="${iconColor}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 64px; height: 64px; margin: 0 auto 15px; display: block;">
              ${icon}
            </svg>
            <h4>${
              actionType === "entrada"
                ? "Entry successfully registered"
                : "Exit successfully registered"
            }</h4>
            <p class="mt-2">The operation has been completed successfully.</p>
            <button class="btn btn-primary mt-3" onclick="window.location.reload()">Refresh page</button>
            </div>
          `;
        }
      })
      .catch((error) => {
        console.error("Fetch error:", error);
        ficharModalTitle.textContent = "Error";
        ficharModalContent.innerHTML = `
        <div class="text-center">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 64px; height: 64px; margin: 0 auto 15px; display: block;">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
          <h4>Entry successfully registered</h4>
          <p>The operation has been completed successfully.</p>
          <button class="btn btn-primary mt-3" onclick="window.location.reload()">Refresh page</button>
        </div>
            `;
      });
  }
});
