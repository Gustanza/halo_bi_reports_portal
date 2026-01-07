<template>
  <DashLayout>
    <div>
      <main>
        <div class="grid">
          <div v-for="card in departments" :key="card.dept" class="card" @click="openDept(card)">
            <div class="card-header">

              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
              </svg>

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
  // store.dispatch("getDepartments")
  //   .then((response) => {
  //     if (response && response.data) {
  //       departments.value = response.data;
  //     }
  //   }).catch((error) => {
  //     console.log("This is an error", error);
  //   });

  store.dispatch("getUser", { id: store.state.user.id })
    .then((response) => {
      if (response) {
        departments.value = response;
      }
    }).catch((error) => {
      console.log("This is an error", error);
    })

}
</script>
