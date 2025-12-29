<template>
  <DashLayout>
    <div>
      <main>
        <div class="page-header">
          <div>
            <h2 class="page-title">Manage Users</h2>
            <p class="page-subtitle">View and manage all users in the system</p>
          </div>
          <button @click="openModal" class="add-btn">
            <i data-lucide="plus" class="icon"></i>
            Add New User
          </button>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(user, index) in users" :key="index">
                <td>{{ user.name }}</td>
                <td>
                  <a :href="`mailto:${user.email}`" class="link">{{ user.email }}</a>
                </td>
                <td>
                  <a :href="`tel:${user.password}`" class="link">{{ user.password }}</a>
                </td>
                <td>
                  <div class="action-buttons">
                    <button
                      @click="editUser(index)"
                      class="action-btn edit-btn"
                      title="Edit"
                    >
                      <i data-lucide="pencil" class="icon"></i>
                      <span class="btn-text">Edit</span>
                    </button>
                    <button
                      @click="confirmDelete(index)"
                      class="action-btn delete-btn"
                      title="Delete"
                    >
                      <i data-lucide="trash-2" class="icon"></i>
                      <span class="btn-text">Delete</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </main>
    </div>

    <!-- Modal Overlay -->
    <div v-if="isModalOpen" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ isEditMode ? "Edit User" : "Add New User" }}</h3>
          <button @click="closeModal" class="close-btn">
            <i data-lucide="x" class="icon"></i>
          </button>
        </div>
        <form @submit.prevent="handleSubmit" class="modal-form">
          <div class="form-group">
            <label for="name">Full Name *</label>
            <input
              id="name"
              v-model="formData.name"
              type="text"
              placeholder="Enter full name"
              required
            />
          </div>
          <div class="form-group">
            <label for="email">Email *</label>
            <input
              id="email"
              v-model="formData.email"
              type="email"
              placeholder="Enter email address"
              required
            />
            <small class="form-hint">Enter a valid email address</small>
          </div>
          <div class="form-group">
            <label for="role">User Role *</label>
            <select id="role" v-model="formData.role" required>
              <option :value="0">--- Select Role ---</option>
              <option :value="1">User</option>
              <option :value="2">Admin</option>
              <option :value="3">Super Admin</option>
            </select>
            <small class="form-hint">Choose an appropriate role for the user</small>
          </div>
          <div class="form-group">
            <label for="password">Password *</label>
            <input
              id="password"
              v-model="formData.password"
              type="text"
              placeholder="Enter password"
              required
            />
            <small class="form-hint"
              >Enter password with atleast 8 mixed characters</small
            >
          </div>

          <div class="form-actions">
            <button type="button" @click="closeModal" class="btn-cancel">Cancel</button>
            <button type="submit" class="btn-submit">
              {{ isEditMode ? "Update User" : "Add User" }}
            </button>
          </div>
        </form>
      </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <div v-if="isDeleteModalOpen" class="modal-overlay" @click="closeDeleteModal">
      <div class="modal-content delete-modal" @click.stop>
        <div class="modal-header">
          <h3>Confirm Delete</h3>
          <button @click="closeDeleteModal" class="close-btn">
            <i data-lucide="x" class="icon"></i>
          </button>
        </div>
        <div class="delete-modal-body">
          <p>
            Are you sure you want to delete <strong>{{ userToDelete?.name }}</strong
            >?
          </p>
          <p class="delete-warning">This action cannot be undone.</p>
          <div class="delete-modal-actions">
            <button type="button" @click="closeDeleteModal" class="btn-cancel">
              Cancel
            </button>
            <button type="button" @click="handleDelete" class="btn-delete">
              Delete User
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashLayout>
</template>

<script setup>
import { computed, onMounted, ref, watch, nextTick } from "vue";
import { toast } from 'vue3-toastify';
import DashLayout from "../components/DashLayout.vue";
import { useStore } from "vuex";

const store = useStore();

// Initial users data
const initialUsers = [
  {
    name: "Gustanza Sam",
    email: "gustanza.sam@halotel.co.tz",
    password: "+255 123 456 789",
  },
  {
    name: "John Doe",
    email: "john.doe@halotel.co.tz",
    password: "+255 987 654 321",
  },
  {
    name: "Jane Smith",
    email: "jane.smith@halotel.co.tz",
    password: "+255 111 222 333",
  },
];

// Make users reactive
const users = ref([...initialUsers]);

const pageSubtitle = computed(() => `View and manage all users in the system`);

// Modal state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const editingIndex = ref(null);
const formData = ref({
  name: "",
  email: "",
  password: "",
  role: 0,
});

// Delete modal state
const isDeleteModalOpen = ref(false);
const userToDelete = ref(null);
const deletingIndex = ref(null);

const openModal = () => {
  isEditMode.value = false;
  editingIndex.value = null;
  isModalOpen.value = true;
  // Reset form
  formData.value = {
    name: "",
    email: "",
    password: "",
    role: 0,
  };
  // Update icons after modal opens
  nextTick(() => {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  });
};

const editUser = (index) => {
  isEditMode.value = true;
  editingIndex.value = index;
  const user = users.value[index];
  formData.value = {
    name: user.name || "",
    email: user.email || "",
    password: user.password || "",
    role: user.role || 0,
  };
  isModalOpen.value = true;
  // Update icons after modal opens
  nextTick(() => {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  });
};

const closeModal = () => {
  isModalOpen.value = false;
  isEditMode.value = false;
  editingIndex.value = null;
  formData.value = {
    name: "",
    email: "",
    password: "",
    role: 0,
  };
};

const handleSubmit = () => {
  store.dispatch("register", formData.value).then((result) => {
    if (result && result.user) {
      toast("Success");
      closeModal();
    }else{
      toast(`${result}`);
    }
    
  });
};

const confirmDelete = (index) => {
  deletingIndex.value = index;
  userToDelete.value = users.value[index];
  isDeleteModalOpen.value = true;
  // Update icons after modal opens
  nextTick(() => {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  });
};

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false;
  userToDelete.value = null;
  deletingIndex.value = null;
};

const handleDelete = () => {
  if (deletingIndex.value !== null) {
    users.value.splice(deletingIndex.value, 1);
  }
  closeDeleteModal();

  // Re-initialize icons after delete
  nextTick(() => {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  });
};

onMounted(() => {
  nextTick(() => {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  });
});

// Watch for icon updates when users change
watch(
  users,
  () => {
    nextTick(() => {
      if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
      }
    });
  },
  { deep: true }
);
</script>

<style scoped>
.page-title {
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 8px;
  letter-spacing: -0.02em;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 32px;
  gap: 24px;
}

.page-subtitle {
  color: var(--muted);
  font-size: 14px;
  margin-bottom: 0;
}

.add-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: var(--halotel-orange);
  border: none;
  border-radius: 8px;
  color: white;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.add-btn:hover {
  background: #ea580c;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
}

.add-btn .icon {
  width: 16px;
  height: 16px;
}

.table-container {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: linear-gradient(135deg, #fafafa, #f5f5f7);
  border-bottom: 2px solid var(--border);
}

th {
  padding: 16px 20px;
  text-align: left;
  font-size: 13px;
  font-weight: 600;
  color: var(--text);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

td {
  padding: 16px 20px;
  border-bottom: 1px solid var(--border);
  font-size: 14px;
  color: var(--text);
}

tbody tr {
  transition: background-color 0.2s ease;
}

tbody tr:hover {
  background-color: rgba(249, 115, 22, 0.04);
}

tbody tr:last-child td {
  border-bottom: none;
}

.link {
  color: var(--halotel-orange);
  text-decoration: none;
  font-weight: 500;
  transition: opacity 0.2s ease;
}

.link:hover {
  opacity: 0.7;
  text-decoration: underline;
}

.action-buttons {
  display: flex;
  gap: 8px;
  align-items: center;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 12px;
  border: 1px solid var(--border);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  background: var(--card);
  font-size: 13px;
  font-weight: 500;
  min-width: auto;
}

.action-btn .icon {
  width: 16px;
  height: 16px;
  display: block;
  flex-shrink: 0;
}

.action-btn .btn-text {
  display: inline-block;
}

.edit-btn {
  color: var(--halotel-orange);
  border-color: rgba(249, 115, 22, 0.3);
}

.edit-btn:hover {
  background: rgba(249, 115, 22, 0.1);
  border-color: var(--halotel-orange);
  transform: translateY(-1px);
}

.delete-btn {
  color: #ef4444;
  border-color: rgba(239, 68, 68, 0.3);
}

.delete-btn:hover {
  background: rgba(239, 68, 68, 0.1);
  border-color: #ef4444;
  transform: translateY(-1px);
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: var(--card);
  border-radius: 18px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-bottom: 1px solid var(--border);
}

.modal-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: var(--text);
}

.close-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  background: transparent;
  border-radius: 6px;
  cursor: pointer;
  color: var(--muted);
  transition: all 0.2s ease;
}

.close-btn:hover {
  background: var(--border);
  color: var(--text);
}

.close-btn .icon {
  width: 20px;
  height: 20px;
}

.modal-form {
  padding: 24px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-size: 14px;
  font-weight: 500;
  color: var(--text);
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid var(--border);
  border-radius: 8px;
  font-size: 14px;
  font-family: inherit;
  color: var(--text);
  background: var(--card);
  transition: border-color 0.2s ease;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--halotel-orange);
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.form-hint {
  display: block;
  margin-top: 6px;
  font-size: 12px;
  color: var(--muted);
}

.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid var(--border);
}

.btn-cancel,
.btn-submit {
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.btn-cancel {
  background: var(--card);
  color: var(--text);
  border: 1px solid var(--border);
}

.btn-cancel:hover {
  background: var(--border);
}

.btn-submit {
  background: var(--halotel-orange);
  color: white;
}

.btn-submit:hover {
  background: #ea580c;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
}

.btn-delete {
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  background: #ef4444;
  color: white;
}

.btn-delete:hover {
  background: #dc2626;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.delete-modal {
  max-width: 400px;
}

.delete-modal-body {
  padding: 24px;
}

.delete-modal-body p {
  margin: 0 0 12px 0;
  font-size: 14px;
  color: var(--text);
}

.delete-modal-body p:last-of-type {
  margin-bottom: 24px;
}

.delete-modal-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid var(--border);
}

.delete-warning {
  color: var(--muted);
  font-size: 13px;
}

@media (max-width: 768px) {
  main {
    padding: 24px 16px;
  }

  .page-header {
    flex-direction: column;
    align-items: stretch;
  }

  .add-btn {
    width: 100%;
    justify-content: center;
  }

  .table-container {
    overflow-x: auto;
  }

  table {
    min-width: 700px;
  }

  th,
  td {
    padding: 12px 16px;
    font-size: 13px;
  }

  .modal-content {
    max-width: 100%;
    margin: 0;
  }

  .modal-header,
  .modal-form {
    padding: 20px;
  }
}
</style>
