<template>
  <DashLayout>
    <div>
      <main>
        <div class="grid">
          <div v-for="card in departments" :key="card.dept" class="card" @click="openDept(card)">
            <div class="card-header">
              <i :data-lucide="card.icon" class="icon"></i>
              <div class="dot"></div>
            </div>
            <h2>{{ card.name }}</h2>
            <div class="divider"></div>
            <div class="cta">Open dashboard →</div>
          </div>
        </div>
      </main>
    </div>
  </DashLayout>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { useStore } from "vuex";
import DashLayout from "../components/DashLayout.vue";

const router = useRouter();
const store = useStore();

const departments = ref([]);

const openDept = (card) => {
  return router.push({
    name: "Reports",
    params: {
      name: card.name,
      id: card.id
    }
  });
};

onMounted(() => {
  fetchy();
});

function fetchy() {
  store.dispatch("getDepartments")
    .then((response) => {
      if (response && response.data) {
        departments.value = response.data;
      }
    }).catch((error) => {
      console.log("This is an error", error);
    })
}
</script>
