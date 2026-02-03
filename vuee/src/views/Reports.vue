<template>
  <DashLayout>
    <div>
      <main>
        <div class="page-header">
          <div>
            <h2 class="page-title">{{ name }} Reports</h2>
            <p class="page-subtitle">
              {{ pageSubtitle }}
            </p>
          </div>
          <button v-if="store.state.user.role > 1" @click="openModal" class="add-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add New Report
          </button>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th class="col-name">Report</th>
                <th class="col-description">Description</th>
                <th class="col-link">Report_Link</th>
                <th class="col-link">Tableau_Link</th>
                <th v-if="store.state.user.role > 1" class="col-actions">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(report, index) in reports" :key="index">
                <td class="col-name">{{ report.name }}</td>
                <td class="col-description">
                  <span v-if="report.description">
                    {{ report.description }}
                  </span>
                  <span v-else class="empty-cell">No description</span>
                </td>
                <td class="col-link">
                  <a v-if="report.report_link" :href="report.report_link" target="_blank" class="link"
                    :title="report.report_link">
                    {{ report.report_link }}
                  </a>
                  <span v-else class="empty-cell">—</span>
                </td>
                <td class="col-link">
                  <a v-if="report.tableau_link" :href="report.tableau_link" target="_blank" class="link"
                    :title="report.tableau_link">
                    {{ report.tableau_link }}
                  </a>
                  <span v-else class="empty-cell">—</span>
                </td>
                <td v-if="store.state.user.role > 1" class="col-actions">
                  <div class="action-buttons">
                    <button @click="editReport(index)" class="action-btn edit-btn" title="Edit">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                      </svg>
                      <span class="btn-text">Edit</span>
                    </button>
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
          <TailwindPagination :data="laravelData" @pagination-change-page="getResults" />
        </div>
      </main>
    </div>

    <!-- Modal Overlay -->
    <div v-if="isModalOpen" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ isEditMode ? 'Edit Report' : 'Add New Report' }}</h3>
          <button @click="closeModal" class="close-btn">
            <i data-lucide="x" class="icon"></i>
          </button>
        </div>
        <form @submit.prevent="handleSubmit" class="modal-form">
          <div class="form-group">
            <label for="name">Report Name *</label>
            <input id="name" v-model="formData.name" type="text" placeholder="Enter report name" required />
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" v-model="formData.description" placeholder="Enter report description"
              rows="3"></textarea>
          </div>
          <div class="form-group">
            <label for="report_link">BI Report URL</label>
            <input id="report_link" v-model="formData.report_link" type="url"
              placeholder="https://example.com/bi-report" />
          </div>
          <div class="form-group">
            <label for="tableau_link">tableau_link URL</label>
            <input id="tableau_link" v-model="formData.tableau_link" type="url"
              placeholder="https://example.com/tableau_link-report" />
          </div>
          <div class="form-actions">
            <button type="button" @click="closeModal" class="btn-cancel">
              Cancel
            </button>
            <button type="submit" class="btn-submit">
              {{ isEditMode ? 'Update Report' : 'Add Report' }}
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
          <p>Are you sure you want to delete <strong>{{ reportToDelete?.name }}</strong>?</p>
          <p class="delete-warning">This action cannot be undone.</p>
          <div class="delete-modal-actions">
            <button type="button" @click="closeDeleteModal" class="btn-cancel">
              Cancel
            </button>
            <button type="button" @click="handleDelete" class="btn-delete">
              Delete Report
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashLayout>
</template>

<script setup>
import { useToast } from "vue-toastification";
import { computed, onMounted, ref, watch, nextTick } from "vue";
import DashLayout from "../components/DashLayout.vue";
import { useStore } from "vuex";
import { TailwindPagination } from 'laravel-vue-pagination';

// Pagination Stuff
const laravelData = ref({});

const getResults = async (page = 1) => {
  const response = await fetch(`https://example.com/results?page=${page}`);
  laravelData.value = await response.json();
}
// Pagination Stuff

const props = defineProps({
  name: String,
  id: String,
});

const store = useStore();
const toast = useToast();

// Placeholder data structure: replace with real data or API calls

// Make reports reactive
const reports = ref([]);

const pageTitle = computed(() => `${props.name} Reports`);
const pageSubtitle = computed(
  () => `View and access all reports for ${props.name}`,
);

// Modal state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const editingIndex = ref(null);
const formData = ref({
  name: "",
  description: "",
  report_link: "",
  tableau_link: "",
});

// Delete modal state
const isDeleteModalOpen = ref(false);
const reportToDelete = ref(null);
const deletingIndex = ref(null);

const openModal = () => {
  isEditMode.value = false;
  editingIndex.value = null;
  isModalOpen.value = true;
  // Reset form
  formData.value = {
    name: "",
    description: "",
    report_link: "",
    tableau_link: "",
  };
};

const editReport = (index) => {
  isEditMode.value = true;
  editingIndex.value = index;
  const report = reports.value[index];
  formData.value = {
    id: report.id,
    name: report.name || "",
    description: report.description || "",
    report_link: report.report_link || "",
    tableau_link: report.tableau_link || "",
  };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  isEditMode.value = false;
  editingIndex.value = null;
  formData.value = {
    name: "",
    description: "",
    report_link: "",
    tableau_link: "",
  };
};

const handleSubmit = () => {
  formData.value.department_id = props.id;
  if (!isEditMode.value) {
    store.dispatch("createReport", formData.value)
      .then((response) => {
        if (response && response.data) {
          toast.success(`${formData.value.name} report added successfully`, { timeout: 2000 });
          fetchy();
          closeModal();
        }
      }).catch((error) => {
        toast.error(`Failed to add ${formData.value.name} report`, { timeout: 2000 });
      });
  } else {
    store.dispatch("editReport", formData.value)
      .then((response) => {
        if (response && response.data) {
          toast.success(`${formData.value.name} report updated successfully`, { timeout: 2000 });
          fetchy();
          closeModal();
        }
      }).catch((error) => {
        console.log("This is an error", error);
      });
  }

};

const confirmDelete = (index) => {
  deletingIndex.value = index;
  reportToDelete.value = reports.value[index];
  isDeleteModalOpen.value = true;
  // Update icons after modal opens
};

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false;
  reportToDelete.value = null;
  deletingIndex.value = null;
};

const handleDelete = () => {
  if (deletingIndex.value !== null && reportToDelete.value) {
    store.dispatch("dropReport", reports.value[deletingIndex.value])
      .then((response) => {
        if (response) {
          const name = reportToDelete.value?.name || "Report";
          toast.success(`${name} report deleted successfully`, { timeout: 2000 });
          closeDeleteModal();
          fetchy(); // refresh table
        }
      })
      .catch((error) => {
        toast.error("Failed to delete department");
      });
  }
};

onMounted(() => {
  fetchy();
});

function fetchy() {
  store.dispatch("getDepartmentReports", props.id)
    .then((response) => {
      if (response && response.data) {
        reports.value = response.data;
      }
    }).catch((error) => {
      console.log("This is an error", error);
    })
}

</script>




<style scoped>
.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text);
  text-decoration: none;
  font-size: 14px;
  transition: all 0.2s ease;
}

.back-btn:hover {
  background: var(--halotel-orange);
  color: white;
  border-color: var(--halotel-orange);
}


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

/* Column width optimizations */
.col-name {
  width: 25%;
  min-width: 200px;
}

.col-description {
  width: 25%;
  min-width: 150px;
  max-width: 300px;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.col-link {
  width: 20%;
  min-width: 150px;
  max-width: 250px;
}

.col-actions {
  width: 10%;
  min-width: 140px;
  white-space: nowrap;
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
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 100%;
}

.link:hover {
  opacity: 0.7;
  text-decoration: underline;
}

.empty-cell {
  color: var(--muted);
  font-style: italic;
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

footer {
  border-top: 1px solid var(--border);
  margin-top: 64px;
  padding: 24px;
  text-align: center;
  font-size: 12px;
  color: var(--muted);
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

.form-group textarea {
  resize: vertical;
  min-height: 80px;
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
    min-width: 900px;
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
