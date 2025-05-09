// Usuarios page specific JavaScript

document.addEventListener("DOMContentLoaded", () => {
  // Search and filter functionality
  const searchInput = document.getElementById("searchUsers");
  const filterRole = document.getElementById("filterRole");
  const tableRows = document.querySelectorAll("#usersTable tbody tr");

  const filterTable = () => {
    const searchTerm = searchInput.value.toLowerCase();
    const roleFilter = filterRole.value;

    tableRows.forEach((row) => {
      const name = row
        .querySelector("td:first-child")
        .textContent.toLowerCase();
      const email = row
        .querySelector("td:nth-child(2)")
        .textContent.toLowerCase();
      const role = row.getAttribute("data-role");

      const matchesSearch =
        name.includes(searchTerm) || email.includes(searchTerm);
      const matchesRole = roleFilter === "" || role === roleFilter;

      if (matchesSearch && matchesRole) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  };

  if (searchInput) searchInput.addEventListener("input", filterTable);
  if (filterRole) filterRole.addEventListener("change", filterTable);

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

  // Edit user functionality - Fixed to ensure it works properly
  const editButtons = document.querySelectorAll(".edit-user");
  const editUserModal = document.getElementById("editUserModal");

  if (editButtons.length > 0 && editUserModal) {
    editButtons.forEach((button) => {
      button.onclick = function (e) {
        e.preventDefault();
        e.stopPropagation();

        const userId = this.getAttribute("data-user-id");

        // Fallback to getting data from the table row
        const userRow = this.closest("tr");
        const fullName = userRow
          .querySelector("td:first-child")
          .textContent.trim();
        const nameParts = fullName.split(" ");
        const nombre = nameParts[0];
        const apellido = nameParts.slice(1).join(" ");
        const email = userRow
          .querySelector("td:nth-child(2)")
          .textContent.trim();
        const role_id = userRow.getAttribute("data-role");

        document.getElementById("edit_id").value = userId;
        document.getElementById("edit_nombre").value = nombre;
        document.getElementById("edit_apellido").value = apellido;
        document.getElementById("edit_email").value = email;
        document.getElementById("edit_rol_id").value = role_id;

        // Show the modal
        editUserModal.classList.add("show");
      };
    });
  }

  // Delete user functionality
  const deleteButtons = document.querySelectorAll(".delete-user");
  const deleteUserModal = document.getElementById("deleteUserModal");

  if (deleteButtons.length > 0 && deleteUserModal) {
    deleteButtons.forEach((button) => {
      button.onclick = function (e) {
        e.preventDefault();
        e.stopPropagation();

        const userId = this.getAttribute("data-user-id");
        document.getElementById("delete_id").value = userId;
        deleteUserModal.classList.add("show");
      };
    });
  }

  // Form validation
  const addUserForm = document.getElementById("addUserForm");
  const editUserForm = document.getElementById("editUserForm");

  if (addUserForm) {
    addUserForm.addEventListener("submit", (e) => {
      const password = document.getElementById("password").value;
      if (password.length < 6) {
        e.preventDefault();
        alert("La contraseña debe tener al menos 6 caracteres.");
      }
    });
  }

  if (editUserForm) {
    editUserForm.addEventListener("submit", (e) => {
      const password = document.getElementById("edit_password").value;
      if (password && password.length < 6) {
        e.preventDefault();
        alert("The password must be at least 6 characters long.");
      }
    });
  }

  // Add event listeners for form submission buttons
  const saveUserBtn = document.getElementById("saveUserBtn");
  const updateUserBtn = document.getElementById("updateUserBtn");
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

  if (saveUserBtn && addUserForm) {
    saveUserBtn.onclick = () => {
      addUserForm.submit();
    };
  }

  if (updateUserBtn && editUserForm) {
    updateUserBtn.onclick = () => {
      editUserForm.submit();
    };
  }

  if (confirmDeleteBtn && document.getElementById("deleteUserForm")) {
    confirmDeleteBtn.onclick = () => {
      document.getElementById("deleteUserForm").submit();
    };
  }
});
