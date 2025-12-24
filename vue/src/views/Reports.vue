<template>
  <div>
    <main>
      <h2 class="page-title">{{ title ?? 'Title goes here' }}</h2>
      <p class="page-subtitle">
        {{ pageSubtitle }}
      </p>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Department</th>
              <th>Report</th>
              <th>Description of the report</th>
              <th>BI report</th>
              <th>Tableau</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(report, index) in deptInfo.reports" :key="index">
              <td>{{ deptInfo.name }}</td>
              <td>{{ report.name }}</td>
              <td>
                <span v-if="report.description">
                  {{ report.description }}
                </span>
                <span v-else class="empty-cell">No description</span>
              </td>
              <td>
                <a
                  v-if="report.biReport"
                  :href="report.biReport"
                  target="_blank"
                  class="link"
                >
                  Link
                </a>
                <span v-else class="empty-cell">—</span>
              </td>
              <td>
                <a
                  v-if="report.tableau"
                  :href="report.tableau"
                  target="_blank"
                  class="link"
                >
                  Link
                </a>
                <span v-else class="empty-cell">—</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, onMounted } from "vue";

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

const pageTitle = computed(() => `${deptInfo.value.name} Reports`);
const pageSubtitle = computed(
  () => `View and access all reports for ${deptInfo.value.name}`,
);

onMounted(() => {
  if (window.lucide && typeof window.lucide.createIcons === "function") {
    window.lucide.createIcons();
  }
});
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

.page-subtitle {
  color: var(--muted);
  font-size: 14px;
  margin-bottom: 32px;
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

footer {
  border-top: 1px solid var(--border);
  margin-top: 64px;
  padding: 24px;
  text-align: center;
  font-size: 12px;
  color: var(--muted);
}

@media (max-width: 768px) {
  main {
    padding: 24px 16px;
  }

  .table-container {
    overflow-x: auto;
  }

  table {
    min-width: 800px;
  }

  th,
  td {
    padding: 12px 16px;
    font-size: 13px;
  }
}
</style>

