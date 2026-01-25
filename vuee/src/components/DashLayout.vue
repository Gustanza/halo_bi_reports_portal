<template>
  <div class="layout">
    <header>
      <div class="header-inner">
        <router-link :to="{ name: 'Departments' }" class="brand">
          <img :src="logo" alt="Halotel Logo" class="app-logo" />
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
            <router-link v-if="role > 1" :to="{ name: 'Manage Users' }" @click="closeDropdown" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M8 8a3 3 0 100-6 3 3 0 000 6z" />
                <path d="M2 14c0-2 2-4 6-4s6 2 6 4" />
              </svg>
              Manage Users
            </router-link>
            <!-- <div class="dropdown-divider" v-if="role > 2"> </div> -->
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
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useStore } from "vuex";
import { useToast } from "vue-toastification";

// Import Logo from assets
import logo from '../assets/halotel-logo.png';
// import logo from '@/assets/halotel-logo.png';

const store = useStore();
const router = useRouter();
const toast = useToast();

const isDropdownOpen = ref(false);
const toggleDropdown = () => { isDropdownOpen.value = !isDropdownOpen.value; };
const closeDropdown = () => { isDropdownOpen.value = false; };

const username = computed(() => store.state.user.name || "User");
const role = computed(() => Number(store.state.user.role || 0));

// -------------------
// Logout Function
// -------------------
const handleLogout = () => {
  // Clear session storage
  sessionStorage.clear();
  // Clear session storage
  sessionStorage.removeItem('token');
  sessionStorage.removeItem('name');
  sessionStorage.removeItem('role');

  // Clear store state
  store.state.user.token = null;
  store.state.user.name = null;
  store.state.user.role = null;

  // Clear Vuex store
  store.commit("logout");

  // Close dropdown and show toast
  closeDropdown();
  toast.success(`${username.value} logged out successfully`, { timeout: 2000 });

  // Redirect after a short delay
  setTimeout(() => {
    router.push({ name: "Login" });
  }, 500);
};

// -------------------
// Dropdown outside click
// -------------------
const handleClickOutside = (event) => {
  if (isDropdownOpen.value && !event.target.closest(".user-menu-wrapper")) {
    closeDropdown();
  }
};

// -------------------
// Inactivity Auto Logout
// -------------------
let inactivityTimer;
const LOGOUT_TIME = 1 * 60 * 1000; // 5 minutes

const autoLogout = () => {
  sessionStorage.clear();
  store.state.user.token = null;
  store.state.user.name = null;
  store.state.user.role = null;

  localStorage.setItem("logout", Date.now());

  toast.info("Logged out due to inactivity");

  // Immediately redirect
  router.replace({ name: "Login" });
};
// const autoLogout = () => {
//   // Clear session and store
//   sessionStorage.clear();
//   store.commit("logout");

//   // Notify other tabs
//   localStorage.setItem("logout", Date.now());

//   toast.info("Logged out due to inactivity");
//   router.push({ name: "Login" });
// };

// Reset timer on activity
const resetTimer = () => {
  clearTimeout(inactivityTimer);
  inactivityTimer = setTimeout(autoLogout, LOGOUT_TIME);
};

// Activity events to listen for
const activityEvents = ["mousemove", "keydown", "click", "scroll"];

// Listen for logout from other tabs
const storageListener = (event) => {
  if (event.key === "logout") {
    sessionStorage.clear();
    store.commit("logout");
    toast.info("Logged out from another tab");
    router.push({ name: "Login" });
  }
};

// -------------------
// Lifecycle hooks
// -------------------
onMounted(() => {
  // Dropdown outside click
  document.addEventListener("click", handleClickOutside);

  // Activity detection
  activityEvents.forEach((e) => window.addEventListener(e, resetTimer));
  window.addEventListener("storage", storageListener);

  // Start inactivity timer
  resetTimer();
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
  activityEvents.forEach((e) => window.removeEventListener(e, resetTimer));
  window.removeEventListener("storage", storageListener);
  clearTimeout(inactivityTimer);
});
</script>

<!-- <script setup>
import { useToast } from "vue-toastification";
import { useStore } from "vuex";
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";

const store = useStore();
const router = useRouter();
const toast = useToast();

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
  const name = username.value || 'User';

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
  toast.success(` ${name} logged out successfully`, {
            timeout: 2000
          });
  setTimeout(() =>{
    router.push({name: 'Login'});
  }, 3000);
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

let inactivityTimer;
const LOGOUT_TIME = 2 * 60 * 1000;

// Auto Logout function
const autoLogout = () => {
  store.commit("logout");
  sessionStorage.clear();

  // Notifying other tab
  localStorage.setItem("logout", Date.now());

  toast.info("Logged out due to inactivity");
  router.push({ name: "Login"});
};

// Reset timer on activity
const resetTimer = () => {
  clearTimeout(inactivityTimer);
  inactivityTimer = setTimeout(autoLogout, LOGOUT_TIME);
};

// Listen to activity event 

const activityEvents = ["mousemove", "keydown", "click", "scroll"];

// Listen for logout from other tabs
const storageListener = (event) => {
  if (event.key === "logout") {
    store.commit("logout");
    sessionStorage.clear();
    toast.info("Logged out from another tab");
    router.push ({ name: "Login"});
  }
};

onMounted(() => {
  activityEvents.forEach((event) => window.addEventListener(event,resetTimer));
  window.addEventListener("storage", storageListener);

  resetTimer();
});

onUnmounted(() => {
  activityEvents.forEach((event) => window.removeEventListener(event, resetTimer));
  window.removeEventListener("storage", storageListener);
  clearTimeout(inactivityTimer);
});

</script> -->

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
.brand {
  display: flex;
  align-items: center;
  gap: 12px;               /* space between logo and text */
  text-decoration: none;
  color: inherit;
}

/* Logo styling */
.app-logo {
  height: 100px;            /* control size here */
  width: auto;
  object-fit: contain;
  background: white;       /* since image is not transparent */
  padding: 4px;            /* optional: clean look */
  border-radius: 6px;      /* optional */
}

/* Title */
.brand h1 {
  font-size: 22px;
  font-weight: 700;
  margin: 0;
  line-height: 1;
}

.brand h1 span {
  color: var(--halotel-orange);
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