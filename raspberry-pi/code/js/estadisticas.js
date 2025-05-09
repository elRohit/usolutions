// Estadisticas page specific JavaScript

document.addEventListener("DOMContentLoaded", () => {
  // Date range picker functionality
  const dateRangeButtons = document.querySelectorAll(".date-range-picker .btn");
  const dateRangeModal = document.getElementById("dateRangeModal");
  const applyDateRangeBtn = document.getElementById("applyDateRange");

  dateRangeButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const range = button.getAttribute("data-range");

      if (range === "custom") {
        // Show date range modal
        dateRangeModal.classList.add("show");
      } else {
        // Calculate date range based on selection
        let startDate, endDate;
        const today = new Date();

        switch (range) {
          case "month":
            // Current month
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
          case "quarter":
            // Current quarter
            const quarter = Math.floor(today.getMonth() / 3);
            startDate = new Date(today.getFullYear(), quarter * 3, 1);
            endDate = new Date(today.getFullYear(), (quarter + 1) * 3, 0);
            break;
          case "year":
            // Current year
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today.getFullYear(), 11, 31);
            break;
        }

        // Format dates for URL
        const formatDate = (date) => {
          return date.toISOString().split("T")[0];
        };

        // Redirect to the page with the new date range
        window.location.href = `estadisticas.php?start_date=${formatDate(
          startDate
        )}&end_date=${formatDate(endDate)}&range=${range}`;
      }
    });
  });

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
});
