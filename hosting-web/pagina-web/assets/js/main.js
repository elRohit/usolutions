/**
 * USOLUTIONS - Main JavaScript
 * Contains common functionality used across all pages
 */

document.addEventListener('DOMContentLoaded', function() {
  // Mobile menu toggle
  initMobileMenu();
  
  // Header scroll effect
  initHeaderScroll();
  
  // Initialize tooltips
  initTooltips();
  
  // Initialize modals
  initModals();
});

/**
* Initialize mobile menu functionality
*/
function initMobileMenu() {
  const menuToggle = document.querySelector('.menu-toggle');
  const mainNav = document.querySelector('.main-nav');
  
  if (menuToggle && mainNav) {
      menuToggle.addEventListener('click', function() {
          mainNav.classList.toggle('active');
          menuToggle.classList.toggle('active');
      });
  }
}

/**
* Initialize header scroll effect
*/
function initHeaderScroll() {
  const header = document.querySelector('.main-header');
  
  if (header) {
      window.addEventListener('scroll', function() {
          if (window.scrollY > 50) {
              header.classList.add('scrolled');
          } else {
              header.classList.remove('scrolled');
          }
      });
  }
}

/**
* Initialize tooltips
*/
function initTooltips() {
  const tooltips = document.querySelectorAll('[data-tooltip]');
  
  tooltips.forEach(tooltip => {
      tooltip.addEventListener('mouseenter', function() {
          const tooltipText = this.getAttribute('data-tooltip');
          const tooltipEl = document.createElement('div');
          tooltipEl.className = 'tooltip';
          tooltipEl.textContent = tooltipText;
          
          document.body.appendChild(tooltipEl);
          
          const rect = this.getBoundingClientRect();
          const tooltipRect = tooltipEl.getBoundingClientRect();
          
          tooltipEl.style.top = `${rect.top - tooltipRect.height - 10 + window.scrollY}px`;
          tooltipEl.style.left = `${rect.left + (rect.width / 2) - (tooltipRect.width / 2)}px`;
          tooltipEl.style.opacity = '1';
      });
      
      tooltip.addEventListener('mouseleave', function() {
          const tooltipEl = document.querySelector('.tooltip');
          if (tooltipEl) {
              tooltipEl.remove();
          }
      });
  });
}

/**
* Initialize modals
*/
function initModals() {
  const modalTriggers = document.querySelectorAll('[data-toggle="modal"]');
  const closeModalButtons = document.querySelectorAll('.close-modal');
  
  modalTriggers.forEach(trigger => {
      trigger.addEventListener('click', function() {
          const modalId = this.dataset.target;
          const modal = document.querySelector(modalId);
          
          if (modal) {
              modal.style.display = 'flex';
              document.body.classList.add('modal-open');
          }
      });
  });
  
  closeModalButtons.forEach(button => {
      button.addEventListener('click', function() {
          const modal = this.closest('.modal');
          
          if (modal) {
              modal.style.display = 'none';
              document.body.classList.remove('modal-open');
          }
      });
  });
  
  window.addEventListener('click', function(event) {
      if (event.target.classList.contains('modal')) {
          event.target.style.display = 'none';
          document.body.classList.remove('modal-open');
      }
  });
}

/**
* Copy text to clipboard
* @param {string} text - The text to copy
* @param {HTMLElement} button - The button element that triggered the copy
*/
function copyToClipboard(text, button) {
  navigator.clipboard.writeText(text).then(() => {
      const originalText = button.textContent;
      button.textContent = 'Copied!';
      
      setTimeout(() => {
          button.textContent = originalText;
      }, 2000);
  });
}

/**
* Show/hide password
* @param {HTMLElement} passwordField - The password input field
* @param {HTMLElement} button - The button element that triggered the toggle
*/
function togglePasswordVisibility(passwordField, button) {
  if (passwordField.type === 'password') {
      passwordField.type = 'text';
      button.innerHTML = '<i class="fas fa-eye-slash"></i>';
  } else {
      passwordField.type = 'password';
      button.innerHTML = '<i class="fas fa-eye"></i>';
  }
}

/**
* Smooth scroll to element
* @param {string} elementId - The ID of the element to scroll to
*/
function smoothScrollTo(elementId) {
  const element = document.getElementById(elementId);
  
  if (element) {
      window.scrollTo({
          top: element.offsetTop - 100,
          behavior: 'smooth'
      });
  }
}
