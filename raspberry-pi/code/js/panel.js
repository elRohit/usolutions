// Panel page specific JavaScript

document.addEventListener("DOMContentLoaded", () => {
  // Animation for stat cards
  const statCards = document.querySelectorAll(".stat-card");

  statCards.forEach((card) => {
    card.addEventListener("mouseenter", () => {
      card.style.backgroundColor = "rgba(96, 165, 250, 0.1)";
    });

    card.addEventListener("mouseleave", () => {
      card.style.backgroundColor = "";
    });
  });

  // Update stats periodically (simulated for demo)
  const updateRandomStats = () => {
    const statValues = document.querySelectorAll(".widget-stat-value");

    statValues.forEach((stat) => {
      // Only update some stats randomly
      if (Math.random() > 0.7) {
        const currentText = stat.textContent;
        // Check if the text contains 'h' (for hours)
        if (currentText.includes("h")) {
          const currentValue = Number.parseInt(currentText);
          if (!isNaN(currentValue)) {
            // Add or subtract a small random amount
            const change = Math.floor(Math.random() * 3) - 1;
            stat.textContent = currentValue + change + "h";
          }
        } else {
          const currentValue = Number.parseInt(currentText);
          if (!isNaN(currentValue)) {
            // Add or subtract a small random amount
            const change = Math.floor(Math.random() * 3) - 1;
            stat.textContent = currentValue + change;
          }
        }

        // Add a subtle animation
        stat.style.transform = "scale(1.05)";
        setTimeout(() => {
          stat.style.transform = "scale(1)";
        }, 300);
      }
    });
  };

  // Update stats every 10 seconds
  setInterval(updateRandomStats, 10000);

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
