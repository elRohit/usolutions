// Tarjetas page specific JavaScript

document.addEventListener("DOMContentLoaded", () => {
  // Search and filter functionality
  const searchInput = document.getElementById("searchRfid");
  const filterStatus = document.getElementById("filterStatus");
  const tableRows = document.querySelectorAll("#rfidTable tbody tr");

  const filterTable = () => {
    const searchTerm = searchInput.value.toLowerCase();
    const statusFilter = filterStatus.value;

    tableRows.forEach((row) => {
      const rfidCode = row
        .querySelector("td:first-child")
        .textContent.toLowerCase();
      const userName = row
        .querySelector("td:nth-child(2)")
        .textContent.toLowerCase();
      const status = row.getAttribute("data-status");

      const matchesSearch =
        rfidCode.includes(searchTerm) || userName.includes(searchTerm);
      const matchesStatus = statusFilter === "" || status === statusFilter;

      if (matchesSearch && matchesStatus) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  };

  if (searchInput) searchInput.addEventListener("input", filterTable);
  if (filterStatus) filterStatus.addEventListener("change", filterTable);

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

  // Assign card functionality
  const assignButtons = document.querySelectorAll(".assign-card");
  const assignCardModal = document.getElementById("assignCardModal");

  assignButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const cardId = button.getAttribute("data-card-id");
      document.getElementById("card_id").value = cardId;
      assignCardModal.classList.add("show");
    });
  });

  // Revoke card functionality
  const revokeButtons = document.querySelectorAll(".revoke-card");
  const revokeCardModal = document.getElementById("revokeCardModal");

  revokeButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const cardId = button.getAttribute("data-card-id");
      document.getElementById("revoke_card_id").value = cardId;
      revokeCardModal.classList.add("show");
    });
  });

  // Delete card functionality
  const deleteButtons = document.querySelectorAll(".delete-card");
  const deleteCardModal = document.getElementById("deleteCardModal");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const cardId = button.getAttribute("data-card-id");
      document.getElementById("delete_card_id").value = cardId;
      deleteCardModal.classList.add("show");
    });
  });

  // Assign to user functionality
  const assignToUserButtons = document.querySelectorAll(".assign-to-user");
  const addRfidModal = document.getElementById("addRfidModal");

  assignToUserButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const userId = button.getAttribute("data-user-id");
      const userName = button.getAttribute("data-user-name");

      // Open the add RFID modal and select the user
      document.getElementById("usuario_id").value = userId;

      // Generate a random RFID code
      document.getElementById("rfid_code").value = generateRandomRfid();

      addRfidModal.classList.add("show");
    });
  });

  // Register RFID button functionality
  const confirmRegisterBtn = document.getElementById("confirmRegisterBtn");
  if (confirmRegisterBtn) {
    confirmRegisterBtn.addEventListener("click", function () {
      const selectedUserId = document.getElementById("selected_user_id").value;
      if (!selectedUserId) {
        alert("Please select a user to assign the card.");
        return false;
      }

      // Show loading state
      this.disabled = true;
      this.innerHTML = '<span class="spinner"></span> Procesando...';

      // Submit the form
      document.getElementById("registerRfidForm").submit();
    });
  }

  // Helper function to generate random RFID code
  function generateRandomRfid() {
    return Math.floor(Math.random() * 1000000000)
      .toString()
      .padStart(9, "0");
  }

  // Show success message for a limited time
  const alertMessages = document.querySelectorAll(".alert");
  if (alertMessages.length > 0) {
    setTimeout(() => {
      alertMessages.forEach((alert) => {
        alert.style.opacity = "0";
        setTimeout(() => {
          alert.style.display = "none";
        }, 500);
      });
    }, 5000);
  }

  // Add spinner CSS
  const style = document.createElement("style");
  style.textContent = `
    .spinner {
      display: inline-block;
      width: 1rem;
      height: 1rem;
      border: 2px solid rgba(255,255,255,0.3);
      border-radius: 50%;
      border-top-color: #fff;
      animation: spin 1s ease-in-out infinite;
      margin-right: 0.5rem;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  `;
  document.head.appendChild(style);
});
