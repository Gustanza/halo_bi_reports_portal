<template>
  <div class="page">
    <main>
      <div class="auth-wrapper">
        <div class="auth-card">
          <div class="auth-header">
            <h2>Sign in</h2>
            <p class="auth-subtitle">
              Access your BI dashboards and reports.
            </p>
          </div>

          <form class="auth-form" @submit.prevent="handleSubmit">
            <div class="field">
              <label for="username">Username</label>
              <input id="username" v-model="user.email" type="text" autocomplete="username" required />
            </div>

            <div class="field">
              <label for="password">Password</label>
              <input id="password" v-model="user.password" type="password" autocomplete="current-password" required />
            </div>

            <div class="field field-inline">
              <label class="checkbox">
                <input v-model="remember" type="checkbox" />
                <span>Remember this device</span>
              </label>
              <!-- <router-link :to="{ name: 'ForgotPassword' }">
                <button type="button" class="link-button">
                  Forgot password?
                </button>
              </router-link> -->
            </div>

            <button class="primary-button" type="submit">
              <span>Continue</span>
              <i data-lucide="arrow-right" class="button-icon"></i>
            </button>

            <p v-if="error" class="error-text">
              {{ error }}
            </p>
          </form>
        </div>
      </div>
    </main>

    <footer>
      © 2025 VIETTEL TANZANIA PLC (HALOTEL). All rights reserved.
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import store from "../store";
import { useRouter } from "vue-router";

const remember = ref(false);
const error = ref("");
const router = useRouter();

const user = ref({
  email: "",
  password: ""
});

const handleSubmit = () => {
  error.value = "";
  store.dispatch("login", user.value).then((result) => {
    if (result && result.user) {
      router.push({ name: "Departments" })
    }
  });

};

const handleForgotPassword = () => {
  // Placeholder: navigate to password reset page or open support.
  alert("Please contact your system administrator to reset your password.");
};

onMounted(() => {
  if (window.lucide && typeof window.lucide.createIcons === "function") {
    window.lucide.createIcons();
  }
});
</script>

<style scoped>
:root {
  --halotel-orange: #f97316;
  --bg: #f5f5f7;
  --card: #ffffff;
  --border: #e5e7eb;
  --text: #111827;
  --muted: #6b7280;
}

* {
  box-sizing: border-box;
  font-family: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
}

.page {
  min-height: 100vh;
  background: linear-gradient(135deg, #fafafa, #f3f4f6);
  color: var(--text);
  display: flex;
  flex-direction: column;
}

header {
  position: sticky;
  top: 0;
  z-index: 10;
  backdrop-filter: blur(16px);
  background: rgba(255, 255, 255, 0.75);
  border-bottom: 1px solid var(--border);
}

.header-inner {
  max-width: 1200px;
  margin: auto;
  padding: 20px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

h1 {
  margin: 0;
  font-size: 22px;
  font-weight: 600;
  letter-spacing: -0.02em;
}

h1 span {
  color: var(--halotel-orange);
}

main {
  flex: 1;
  max-width: 1200px;
  margin: auto;
  padding: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.auth-wrapper {
  width: 100%;
  display: flex;
  justify-content: center;
}

.auth-card {
  width: 100%;
  max-width: 420px;
  background: var(--card);
  border-radius: 18px;
  border: 1px solid var(--border);
  padding: 28px 28px 32px;
  box-shadow: 0 18px 45px rgba(15, 23, 42, 0.15);
}

.auth-header h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  letter-spacing: -0.02em;
}

.auth-subtitle {
  margin-top: 6px;
  font-size: 13px;
  color: var(--muted);
}

.auth-form {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field-inline {
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
}

label {
  font-size: 13px;
  font-weight: 500;
}

input[type="text"],
input[type="password"] {
  border-radius: 12px;
  border: 1px solid var(--border);
  padding: 10px 12px;
  font-size: 14px;
  outline: none;
  background: #f9fafb;
  transition: border-color 0.15s ease, box-shadow 0.15s ease,
    background-color 0.15s ease;
}

input[type="text"]:focus,
input[type="password"]:focus {
  border-color: var(--halotel-orange);
  box-shadow: 0 0 0 1px rgba(249, 115, 22, 0.25);
  background: #ffffff;
}

.checkbox {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: var(--muted);
}

.checkbox input {
  width: 14px;
  height: 14px;
}

.link-button {
  border: none;
  background: none;
  padding: 0;
  font-size: 13px;
  color: var(--halotel-orange);
  cursor: pointer;
  text-decoration: none;
}

.primary-button {
  margin-top: 6px;
  width: 100%;
  border: none;
  border-radius: 999px;
  padding: 10px 14px;
  font-size: 14px;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: linear-gradient(135deg, #f97316, #ea580c);
  color: white;
  cursor: pointer;
  box-shadow: 0 18px 35px rgba(234, 88, 12, 0.4);
  transition: transform 0.15s ease, box-shadow 0.15s ease,
    filter 0.15s ease;
}

.primary-button:hover {
  transform: translateY(-1px);
  box-shadow: 0 22px 40px rgba(234, 88, 12, 0.5);
  filter: brightness(1.02);
}

.primary-button:active {
  transform: translateY(0);
  box-shadow: 0 12px 24px rgba(234, 88, 12, 0.35);
}

.button-icon {
  width: 16px;
  height: 16px;
  stroke-width: 1.8;
}

.error-text {
  margin-top: 4px;
  font-size: 12px;
  color: #b91c1c;
}

footer {
  border-top: 1px solid var(--border);
  padding: 20px 24px;
  text-align: center;
  font-size: 12px;
  color: var(--muted);
  background: rgba(255, 255, 255, 0.85);
}

@media (max-width: 600px) {
  main {
    padding: 20px 16px 32px;
  }

  .auth-card {
    padding: 24px 20px 28px;
  }
}
</style>
