// Common JavaScript for all pages

document.addEventListener("DOMContentLoaded", () => {
  // Initialize tsParticles with a subtle configuration
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
      currentTimeElement.textContent = now.toLocaleDateString("en-ES", options);
    }
  };

  // Call the function on page load
  updateCurrentTime();

  // Update time every second
  setInterval(updateCurrentTime, 1000);

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

  // Modal functionality
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
});
