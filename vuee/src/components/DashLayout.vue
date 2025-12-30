<template>
  <div class="layout">
    <header>
      <div class="header-inner">
        <router-link :to="{ name: 'Departments' }">
          <h1><span>BI</span> Reports</h1>
        </router-link>
        <div class="user-menu-wrapper">
          <button @click="toggleDropdown" class="user-menu-trigger">
            <span class="username">{{ username }}</span>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 6l4 4 4-4" />
            </svg>
          </button>
          <div v-if="isDropdownOpen" class="dropdown-menu">
            <router-link v-if="role > 2" :to="{ name: 'Manage Departments' }" @click="closeDropdown"
              class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="2" width="12" height="12" rx="2" />
                <path d="M6 8h4M8 6v4" />
              </svg>
              Manage Departments
            </router-link>
            <router-link v-if="role > 2" :to="{ name: 'Manage Users' }" @click="closeDropdown" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M8 8a3 3 0 100-6 3 3 0 000 6z" />
                <path d="M2 14c0-2 2-4 6-4s6 2 6 4" />
              </svg>
              Manage Users
            </router-link>
            <div class="dropdown-divider" v-if="role > 2"></div>
            <button @click="handleLogout" class="dropdown-item logout-item">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 14H3a1 1 0 01-1-1V3a1 1 0 011-1h3M10 12l4-4-4-4M14 8H6" />
              </svg>
              Logout
            </button>
          </div>
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
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";

const store = useStore();
const router = useRouter();

const isDropdownOpen = ref(false);

// Use computed properties to reactively watch store changes
const username = computed(() => store.state.user.name || '');
const role = computed(() => {
  const roleValue = store.state.user.role;
  // Convert to number if it's a string (sessionStorage returns strings)
  return roleValue ? Number(roleValue) : 0;
});

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

const closeDropdown = () => {
  isDropdownOpen.value = false;
};

const handleLogout = () => {
  // Clear session storage
  sessionStorage.removeItem('token');
  sessionStorage.removeItem('name');
  sessionStorage.removeItem('role');

  // Clear store state
  store.state.user.token = null;
  store.state.user.name = null;
  store.state.user.role = null;

  // Close dropdown and redirect to login
  closeDropdown();
  router.push({ name: 'Login' });
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (isDropdownOpen.value && !event.target.closest('.user-menu-wrapper')) {
    closeDropdown();
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

</script>

<style scoped>
.user-menu-wrapper {
  position: relative;
}

.user-menu-trigger {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border: 1px solid var(--border);
  border-radius: 10px;
  background: #fff;
  color: var(--text);
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.user-menu-trigger:hover {
  background: var(--bg);
  border-color: var(--halotel-orange);
}

.username {
  font-weight: 500;
  color: var(--text);
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  min-width: 200px;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  overflow: hidden;
  z-index: 1000;
  animation: slideDown 0.2s ease;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  width: 100%;
  text-align: left;
  background: none;
  border: none;
  color: var(--text);
  font-size: 14px;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.2s ease;
}

.dropdown-item:hover {
  background: var(--bg);
}

.dropdown-item svg {
  flex-shrink: 0;
  color: var(--muted);
}

.dropdown-item:hover svg {
  color: var(--halotel-orange);
}

.logout-item {
  color: #dc2626;
}

.logout-item:hover {
  background: #fef2f2;
  color: #dc2626;
}

.logout-item svg {
  color: #dc2626;
}

.dropdown-divider {
  height: 1px;
  background: var(--border);
  margin: 4px 0;
}
</style>