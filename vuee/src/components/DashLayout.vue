<template>
  <div class="layout">
    <header>
      <div class="header-inner">
        <router-link :to="{ name: 'Departments' }">
          <h1><span>BI</span> Reports</h1>
        </router-link>
        <div style="
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #6b7280;
          ">
          <span>Welcome, {{ username }} </span>
          <router-link v-if="clearanceLevel > 5" :to="{ name: 'Manage Departments' }" style="
              display: inline-flex;
              align-items: center;
              gap: 6px;
              padding: 8px 16px;
              margin-left: 12px;
              border: 1px solid var(--border);
              border-radius: 10px;
              text-decoration: none;
              color: var(--text);
              background: #fff;
            ">
            Manage Departments
          </router-link>
        </div>
      </div>
    </header>

    <main>
      <slot></slot>
    </main>

    <footer>
      © 2025 VIETTEL TANZANIA PLC (HALOTEL). All rights reserved.
    </footer>
  </div>
</template>


<script setup>

import { useStore } from "vuex";
import { computed } from "vue";
import { ref } from "vue";

const store = useStore();

let username = ref('');
let clearanceLevel = 0;

username.value = computed(() => store.state.user.data.fullName);
clearanceLevel = computed(() => store.state.user.clearanceLevel);

</script>