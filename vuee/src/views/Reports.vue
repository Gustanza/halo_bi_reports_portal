<template>
  <DashLayout>
    <div>
      <main>
        <div class="page-header">
          <div>
            <h2 class="page-title">{{ title ?? 'Title goes here' }}</h2>
            <p class="page-subtitle">
              {{ pageSubtitle }}
            </p>
          </div>
          <button @click="openModal" class="add-btn">
            <i data-lucide="plus" class="icon"></i>
            Add New Report
          </button>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Report</th>
                <th>Description of the report</th>
                <th>BI report</th>
                <th>Tableau</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(report, index) in reports" :key="index">
                <td>{{ report.name }}</td>
                <td>
                  <span v-if="report.description">
                    {{ report.description }}
                  </span>
                  <span v-else class="empty-cell">No description</span>
                </td>
                <td>
                  <a v-if="report.biReport" :href="report.biReport" target="_blank" class="link">
                    Link
                  </a>
                  <span v-else class="empty-cell">—</span>
                </td>
                <td>
                  <a v-if="report.tableau" :href="report.tableau" target="_blank" class="link">
                    Link
                  </a>
                  <span v-else class="empty-cell">—</span>
                </td>
                <td>
                  <div class="action-buttons">
                    <button @click="editReport(index)" class="action-btn edit-btn" title="Edit">
                      <i data-lucide="pencil" class="icon"></i>
                      <span class="btn-text">Edit</span>
                    </button>
                    <button @click="confirmDelete(index)" class="action-btn delete-btn" title="Delete">
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
          <h3>{{ isEditMode ? 'Edit Report' : 'Add New Report' }}</h3>
          <button @click="closeModal" class="close-btn">
            <i data-lucide="x" class="icon"></i>
          </button>
        </div>
        <form @submit.prevent="handleSubmit" class="modal-form">
          <div class="form-group">
            <label for="name">Report Name *</label>
            <input
              id="name"
              v-model="formData.name"
              type="text"
              placeholder="Enter report name"
              required
            />
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <textarea
              id="description"
              v-model="formData.description"
              placeholder="Enter report description"
              rows="3"
            ></textarea>
          </div>
          <div class="form-group">
            <label for="biReport">BI Report URL</label>
            <input
              id="biReport"
              v-model="formData.biReport"
              type="url"
              placeholder="https://example.com/bi-report"
            />
          </div>
          <div class="form-group">
            <label for="tableau">Tableau URL</label>
            <input
              id="tableau"
              v-model="formData.tableau"
              type="url"
              placeholder="https://example.com/tableau-report"
            />
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
import { computed, onMounted, ref, watch, nextTick } from "vue";
import DashLayout from "../components/DashLayout.vue";

const props = defineProps({
  title: String,
  dept: {
    type: String,
    default: "marketing",
  },
});

// Placeholder data structure: replace with real data or API calls
const departmentData = {
  marketing: {
    name: "Marketing Department",
    reports: [
      {
        name: "Campaign Performance Overview",
        description: "High-level view of active and past campaigns.",
        biReport: "https://placeholder.local/bi/marketing-campaign",
        tableau: "https://placeholder.local/tableau/marketing-campaign",
      },
      {
        name: "Customer Acquisition Trends",
        description: "Trends of new customers over time.",
        biReport: "",
        tableau: "",
      },
    ],
  },
  sales: {
    name: "Sales Department",
    reports: [
      {
        name: "Sales Performance Dashboard",
        description: "Core metrics and KPIs for sales teams.",
        biReport: "https://placeholder.local/bi/sales-performance",
        tableau: "",
      },
      {
        name: "Revenue Breakdown",
        description: "Revenue by product, region, and segment.",
        biReport: "",
        tableau: "https://placeholder.local/tableau/revenue-breakdown",
      },
    ],
  },
  default: {
    name: "Department",
    reports: [
      {
        name: "Sample Report A",
        description: "Short description of what this report covers.",
        biReport: "",
        tableau: "",
      },
      {
        name: "Sample Report B",
        description: "",
        biReport: "",
        tableau: "",
      },
    ],
  },
};

const deptKey = computed(() => props.dept.toLowerCase());

const deptInfo = computed(() => {
  return departmentData[deptKey.value] || departmentData.default;
});

// Make reports reactive
const reports = ref([...deptInfo.value.reports]);

// Update reports when department changes
watch(deptInfo, (newDeptInfo) => {
  reports.value = [...newDeptInfo.reports];
  // Re-initialize icons after reports update
  nextTick(() => {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  });
}, { immediate: true });

const pageTitle = computed(() => `${deptInfo.value.name} Reports`);
const pageSubtitle = computed(
  () => `View and access all reports for ${deptInfo.value.name}`,
);

// Modal state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const editingIndex = ref(null);
const formData = ref({
  name: "",
  description: "",
  biReport: "",
  tableau: "",
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
    biReport: "",
    tableau: "",
  };
  // Update icons after modal opens
  setTimeout(() => {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  }, 0);
};

const editReport = (index) => {
  isEditMode.value = true;
  editingIndex.value = index;
  const report = reports.value[index];
  formData.value = {
    name: report.name || "",
    description: report.description || "",
    biReport: report.biReport || "",
    tableau: report.tableau || "",
  };
  isModalOpen.value = true;
  // Update icons after modal opens
  setTimeout(() => {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  }, 0);
};

const closeModal = () => {
  isModalOpen.value = false;
  isEditMode.value = false;
  editingIndex.value = null;
  formData.value = {
    name: "",
    description: "",
    biReport: "",
    tableau: "",
  };
};

const handleSubmit = () => {
  if (isEditMode.value && editingIndex.value !== null) {
    // Update existing report
    reports.value[editingIndex.value] = {
      name: formData.value.name,
      description: formData.value.description || "",
      biReport: formData.value.biReport || "",
      tableau: formData.value.tableau || "",
    };
  } else {
    // Add new report to the list
    reports.value.push({
      name: formData.value.name,
      description: formData.value.description || "",
      biReport: formData.value.biReport || "",
      tableau: formData.value.tableau || "",
    });
  }
  
  // Close modal and reset form
  closeModal();
};

const confirmDelete = (index) => {
  deletingIndex.value = index;
  reportToDelete.value = reports.value[index];
  isDeleteModalOpen.value = true;
  // Update icons after modal opens
  setTimeout(() => {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  }, 0);
};

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false;
  reportToDelete.value = null;
  deletingIndex.value = null;
};

const handleDelete = () => {
  if (deletingIndex.value !== null) {
    reports.value.splice(deletingIndex.value, 1);
  }
  closeDeleteModal();
};

onMounted(() => {
  nextTick(() => {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  });
});

// Watch for icon updates when reports change
watch(reports, () => {
  nextTick(() => {
    if (window.lucide && typeof window.lucide.createIcons === "function") {
      window.lucide.createIcons();
    }
  });
}, { deep: true });
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
