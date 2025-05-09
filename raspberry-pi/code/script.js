// script.js

document.addEventListener("DOMContentLoaded", () => {
  // Initialize Flatpickr date picker
  if (typeof flatpickr !== "undefined") {
    flatpickr("#fecha", {
      dateFormat: "d/m/Y",
      locale: "es",
      disableMobile: true,
      onChange: (selectedDates, dateStr) => {
        document.getElementById("fecha").value = dateStr;
      },
    });
  }

  // Initialize tsParticles with a more subtle configuration
  if (typeof tsParticles !== "undefined") {
    tsParticles.load("tsparticles", {
      background: {
        color: {
          value: "transparent",
        },
      },
      fpsLimit: 60,
      interactivity: {
        events: {
          onClick: { enable: true, mode: "push" },
          onHover: { enable: true, mode: "repulse" },
          resize: true,
        },
        modes: {
          push: { quantity: 2 },
          repulse: { distance: 100, duration: 0.4 },
        },
      },
      particles: {
        color: { value: "#2563eb" },
        links: {
          color: "#2563eb",
          distance: 150,
          enable: true,
          opacity: 0.2,
          width: 1,
        },
        collisions: { enable: true },
        move: {
          direction: "none",
          enable: true,
          outModes: { default: "bounce" },
          random: false,
          speed: 0.8,
          straight: false,
        },
        number: {
          density: { enable: true, area: 1200 },
          value: 30,
        },
        opacity: { value: 0.3 },
        shape: { type: "circle" },
        size: { value: { min: 1, max: 3 } },
      },
      detectRetina: true,
    });
  }

  // Animate table rows on scroll
  const animateElements = document.querySelectorAll(".animate-on-scroll");

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
        Mostrando todos los registros
      `;
    } else {
      // Get the date from URL or use today's date
      let fecha =
        urlParams.get("fecha") ||
        new Date().toLocaleDateString("es-ES", {
          day: "2-digit",
          month: "2-digit",
          year: "numeric",
        });

      // Make sure it's in DD/MM/YYYY format
      fecha = formatDateForDisplay(fecha);

      filterInfoElement.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
          <line x1="16" y1="2" x2="16" y2="6"></line>
          <line x1="8" y1="2" x2="8" y2="6"></line>
          <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
        Mostrando registros del ${fecha}
      `;
    }
  };

  // Update current time in header
  const updateCurrentTime = () => {
    const currentTimeElement = document.getElementById("current-time");
    if (currentTimeElement) {
      const now = new Date();
      const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
      };
      currentTimeElement.textContent = now.toLocaleDateString("es-ES", options);
    }
  };

  // Update stats in header
  const updateStats = () => {
    // This would typically fetch data from the server
    // For now, we'll just use placeholder data
    const totalRegistrosElement = document.getElementById("total-registros");
    const registrosHoyElement = document.getElementById("registros-hoy");

    if (totalRegistrosElement && registrosHoyElement) {
      // Get the count from the table rows
      const tableRows = document.querySelectorAll("tbody tr");
      const totalRows = tableRows.length;

      // For demo purposes, we'll set "registros hoy" to be a portion of total
      const registrosHoy = Math.min(totalRows, 5);

      totalRegistrosElement.textContent = totalRows;
      registrosHoyElement.textContent = registrosHoy;
    }
  };

  // Call the functions on page load
  updateFilterInfo();
  updateCurrentTime();
  updateStats();

  // Update time every second
  setInterval(updateCurrentTime, 1000);
});
