<DashLayout>

  <div>
    <main>
      <div class="grid">
        <div
          v-for="card in cards"
          :key="card.dept"
          class="card"
          @click="openDept(card)"
        >
          <div class="card-header">
            <i :data-lucide="card.icon" class="icon"></i>
            <div class="dot"></div>
          </div>
          <h2>{{ card.title }}</h2>
          <div class="divider"></div>
          <div class="cta">Open dashboard →</div>
        </div>
      </div>
    </main>
  </div>

</DashLayout>

<script setup>
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import DashLayout from "./components/DashLayout.vue";

const router = useRouter();

const cards = [
  {
    dept: "marketing",
    icon: "bar-chart-3",
    title: "Marketing department",
  },
  {
    dept: "sales",
    icon: "coins",
    title: "Sales department",
  },
  {
    dept: "corporate",
    icon: "briefcase",
    title: "Corporate departments",
  },
  {
    dept: "customer-care",
    icon: "headphones",
    title: "Customer Care department",
  },
  {
    dept: "hr",
    icon: "users",
    title: "HR department",
  },
  {
    dept: "fixed-broadband",
    icon: "network",
    title: "Fixed-broadband department",
  },
  {
    dept: "legal",
    icon: "scale",
    title: "Legal departments",
  },
  {
    dept: "investment",
    icon: "dollar-sign",
    title: "Investment department",
  },
];

const openDept = (card) => {
  return router.push({
    name: "Reports",
    params: {
      title: card.title,
    }
  });
};

onMounted(() => {
  if (window.lucide && typeof window.lucide.createIcons === "function") {
    window.lucide.createIcons();
  }
});
</script>
