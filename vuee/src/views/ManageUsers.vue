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
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add New User
          </button>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Department</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(user, index) in users" :key="index">
                <td>{{ user.name }}</td>
                <td>
                  <a :href="`mailto:${user.email}`" class="link">{{ user.email }}</a>
                </td>
                <!-- <td>
                  {{ user.role === 3 ? "SUPER ADMIN" : user.role === 2 ? "ADMIN" : "USER" }}
                </td> -->
                <td>
                  <div class="form-group">
                    <select id="role" v-model="user.role" @change="onRoleChange" required>
                      <option :value="0">--- Select Role ---</option>
                      <option :value="1">User</option>
                      <option :value="2">Admin </option>
                      <option v-if="currentRole > 2" :value="3">Super Admin</option>
                    </select>
                  </div>
                </td>
                <td>
                  <div v-for="dpt in user.departments" :key="dpt" @click="editUser(index)" style="cursor: pointer;">
                    <span style="background-color: grey; 
                    cursor: pointer;
                    padding: 1px 8px; border-radius: 8px; color: white">{{
                      dpt.name.toLowerCase() }}
                    </span>
                  </div>
                  <button v-if="!user.departments || user.departments.length === 0" @click="editUser(index)"
                    class="action-btn" title="Delete">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="btn-text">Assign</span>
                  </button>
                </td>
                <td>
                  <div class="action-buttons">
                    <!-- Save -->
                    <button @click="handleSaving(user)" class="action-btn save-btn" title="Delete">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6" color="green">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9" />
                      </svg>
                      <span class="btn-text">Save</span>
                    </button>
                    <!-- Reset -->
                    <button @click="resetPassword(user.email)" class="action-btn save-btn" title="Delete">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                      </svg>
                      <span class="btn-text">Reset</span>
                    </button>
                    <!-- Delete -->
                    <button @click="confirmDelete(index)" class="action-btn delete-btn" title="Delete">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                      </svg>
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
          <h3>{{ isEditMode ? "Assign Department(s)" : "Add New User" }}</h3>
          <button @click="closeModal" class="close-btn">
            <i data-lucide="x" class="icon"></i>
          </button>
        </div>
        <form @submit.prevent="handleSubmit" class="modal-form">
          <div v-if="!isEditMode" class="form-group">
            <label for="name">Full Name *</label>
            <input id="name" v-model="formData.name" type="text" placeholder="Enter full name" required />
          </div>
          <div v-if="!isEditMode" class="form-group">
            <label for="email">Email *</label>
            <input id="email" v-model="formData.email" type="email" placeholder="Enter email address" required />
            <small class="form-hint">Enter a valid email address</small>
          </div>
          <div v-if="!isEditMode" class="form-group">
            <label for="role">User Role *</label>
            <select id="role" v-model="formData.role" required>
              <option :value="0">--- Select Role ---</option>
              <option :value="1">User</option>
              <option :value="2">Admin</option>
              <option v-if="currentRole > 2" :value="3">Super Admin</option>
            </select>
            <small class="form-hint">Choose an appropriate role for the user</small>
          </div>

          <div class="form-group">
            <label>Departments</label>
            <div class="checkbox-group">
              <label v-for="dept in departments" :key="dept.id" class="checkbox-item">
                <input type="checkbox" :id="`dept-${dept.id}`" :value="Number(dept.id)" v-model="formData.departments">
                <span class="checkbox-label"> {{ dept.name }} </span>
              </label>
            </div>
            <small class="form-hint">You can select multiple departments (check all that apply)</small>
          </div>
          <div v-if="!isEditMode" class="form-group">
            <label for="password">Password *</label>
            <input id="created_at" v-model="formData.password" type="password" placeholder="Enter password" required />
            <small class="form-hint">Enter password with atleast 8 mixed characters</small>
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
            Are you sure you want to delete <strong>{{ userToDelete?.name }}</strong>?
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
import { computed, onMounted, ref } from "vue";
// import { toast } from 'vue3-toastify';
import { useToast } from "vue-toastification";
import DashLayout from "../components/DashLayout.vue";
import { useStore } from "vuex";

import axiosClient from "../axiosClient";

const loading = ref(false);

const store = useStore();
const toast = useToast();

const users = ref([]);
const currentRole = computed(() => {
  return store.state.user.role;
});
const departments = ref([]);

// Modal state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const isRoleChanged = ref(false);
const editingIndex = ref(null);
const formData = ref({
  name: "",
  email: "",
  created_at: "",
  role: 0,
  departments: [],
  password: "",
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
    created_at: "",
    role: 0,
    departments: [],
    password: "",
  };
};

// const editUser = (index) => {
//   isEditMode.value = true;
//   editingIndex.value = index;
//   const user = users.value[index];

//   // // Normalize departments to array of IDs (handle both object arrays and ID arrays)
//   // let departmentIds = [];
//   // if (user.departments && Array.isArray(user.departments)) {
//   //   departmentIds = user.departments.map(dept => {
//   //     // If department is an object, extract the id; otherwise use the value directly
//   //     return typeof dept === 'object' && dept !== null ? dept.id : dept;
//   //   }).map(id => Number(id)); // Ensure all IDs are numbers to match checkbox values
//   // }

//   formData.value = {
//     id: user.id,
//     name: user.name || "",
//     email: user.email || "",
//     departments: user.departments,
//     role: user.role || 0,
//   };
//   isModalOpen.value = true;
// };

const editUser = (index) => {
  isEditMode.value = true;
  editingIndex.value = index;
  const user = users.value[index];
  // Normalize departments to array of IDs (handle both object arrays and ID arrays)
  let departmentIds = [];
  if (user.departments && Array.isArray(user.departments)) {
    departmentIds = user.departments.map(dept => {
      // If department is an object, extract the id; otherwise use the value directly
      return typeof dept === 'object' && dept !== null ? dept.id : dept;
    }).map(id => Number(id)); // Ensure all IDs are numbers to match checkbox values
  }
  formData.value = {
    id: user.id,
    name: user.name || "",
    email: user.email || "",
    departments: departmentIds,
    role: user.role || 0,
  };
  isModalOpen.value = true;
};

function onRoleChange(event) {
  isRoleChanged.value = true;
}

const closeModal = () => {
  isModalOpen.value = false;
  isEditMode.value = false;
  editingIndex.value = null;
  formData.value = {
    name: "",
    email: "",
    created_at: "",
    role: 0,
    departments: [],
    password: "",
  };
};

const handleSubmit = () => {
  // return console.log("Come look at this:", formData.value);
  if (isEditMode.value) {
    store.dispatch('updateUser', formData.value).then((response) => {
      if (response && response.data) {
        closeModal();
        fetchy();
      }
    })
  } else {
    store.dispatch("register", formData.value).then((response) => {
      if (response && response.user) {
        closeModal();
        fetchy();
      } else {
        toast.success("Success", {
          timeout: 2000
        });
      }
    });
  }

};

function handleSaving(user) {
  // console.log("Loooook: ", user.value)
  let departmentIds = [];
  if (user.departments && Array.isArray(user.departments)) {
    departmentIds = user.departments.map(dept => {
      // If department is an object, extract the id; otherwise use the value directly
      return typeof dept === 'object' && dept !== null ? dept.id : dept;
    }).map(id => Number(id)); // Ensure all IDs are numbers to match checkbox values
  }
  user.departments = departmentIds;

  store.dispatch('updateUser', user).then((response) => {
    if (response && response.data) {
      toast.success("Success", {
        timeout: 2000
      });
      fetchy();
    }
  });
}

const confirmDelete = (index) => {
  deletingIndex.value = index;
  userToDelete.value = users.value[index];
  isDeleteModalOpen.value = true;
};

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false;
  userToDelete.value = null;
  deletingIndex.value = null;
};

const handleDelete = () => {
  if (deletingIndex.value !== null) {
    const usr = users.value;
    store.dispatch("dropUser", users.value[deletingIndex.value])
      .then((response) => {
        if (response) {
          closeDeleteModal();
          fetchy();
        } else {
          toast.success("Success", {
            timeout: 2000
          });
        }
      });
  }

};

function resetPassword(email) {
  loading.value = true;
  axiosClient
    .post("/forgot-password", { email: email })
    .then((response) => {
      console.log("Response:", response.data);
      toast.success("Success", {
        timeout: 2000
      });
    })
    .catch(() => {
      toast.success("Failed", {
        timeout: 2000
      });
    })
    .finally(() => {
      loading.value = false;
    });
}

onMounted(() => {
  fetchy();
});

async function fetchy() {
  // store.dispatch('getDepartments').then((response) => {
  //   if (response && response.data) {
  //     departments.value = response.data;
  //   }
  // });
  store.dispatch("getUser", { id: store.state.user.id })
    .then((response) => {
      if (response) {
        departments.value = response;
      }
    }).catch((error) => {
      console.log("This is an error", error);
    })
  store.dispatch('getUsers').then((response) => {
    if (response && response.data) {
      users.value = response.data;
    }
  });
}

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

.save-btn {
  color: green;
  border-color: rgba(0, 128, 0, 0.309);
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
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid var(--border);
  border-radius: 8px;
  font-size: 14px;
  font-family: inherit;
  color: var(--text);
  background: var(--card);
  transition: border-color 0.2s ease;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
}

.form-group select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  padding-right: 40px;
  cursor: pointer;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: var(--halotel-orange);
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.form-group select:hover {
  border-color: var(--halotel-orange);
}

.form-hint {
  display: block;
  margin-top: 6px;
  font-size: 12px;
  color: var(--muted);
}

.checkbox-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 12px;
  border: 1px solid var(--border);
  border-radius: 8px;
  background: var(--bg);
  max-height: 500px;
  overflow-y: auto;
}

.checkbox-item {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  padding: 10px 12px;
  border-radius: 6px;
  border-left: 3px solid transparent;
  transition: all 0.2s ease;
}

.checkbox-item:hover {
  background-color: rgba(249, 115, 22, 0.05);
}

.checkbox-item input[type="checkbox"] {
  width: 18px;
  height: 18px;
  margin: 0;
  padding: 0;
  cursor: pointer;
  accent-color: var(--halotel-orange);
  flex-shrink: 0;
  appearance: checkbox;
  -webkit-appearance: checkbox;
  -moz-appearance: checkbox;
}

.checkbox-item input[type="checkbox"]:checked {
  accent-color: var(--halotel-orange);
  background-color: var(--halotel-orange);
  border-color: var(--halotel-orange);
}

.checkbox-item input[type="checkbox"]:focus {
  outline: 2px solid var(--halotel-orange);
  outline-offset: 2px;
  box-shadow: none;
}

.checkbox-item:has(input[type="checkbox"]:checked) {
  background-color: rgba(249, 115, 22, 0.08);
  border-left: 3px solid var(--halotel-orange);
}

.checkbox-item:has(input[type="checkbox"]:checked) .checkbox-label {
  color: var(--halotel-orange);
  font-weight: 500;
}

.checkbox-label {
  font-size: 14px;
  margin-left: 20px;
  color: var(--text);
  font-weight: 400;
  user-select: none;
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

.btn-save {
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  background: green;
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
