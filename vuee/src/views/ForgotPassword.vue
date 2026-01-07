<template>
    <div class="page">
        <main>
            <div class="auth-wrapper">
                <div class="auth-card">
                    <div class="auth-header">
                        <h2>Forgot password</h2>
                        <p class="auth-subtitle">
                            Enter your account email to receive a reset link.
                        </p>
                    </div>

                    <form class="auth-form" @submit.prevent="handleSubmit">
                        <div class="field">
                            <label for="email">Email</label>
                            <input id="email" v-model="email" type="email" autocomplete="email" required
                                placeholder="example@halotel.co.tz" />
                        </div>

                        <button class="primary-button" type="submit" :disabled="loading">
                            <span v-if="!loading">Send reset link</span>
                            <span v-else>Sending...</span>
                            <i data-lucide="arrow-right" class="button-icon"></i>
                        </button>

                        <p v-if="status" class="status-text success">
                            {{ status }}
                        </p>
                        <p v-if="error" class="status-text error">
                            {{ error }}
                        </p>
                    </form>

                    <div class="footer-link">
                        <router-link :to="{ name: 'Login' }">Back to login</router-link>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            © 2025 VIETTEL TANZANIA PLC (HALOTEL). All rights reserved.
        </footer>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axiosClient from "../axiosClient";

const router = useRouter();
const email = ref("");
const status = ref("");
const error = ref("");
const loading = ref(false);

const handleSubmit = () => {
    status.value = "";
    error.value = "";
    loading.value = true;

    axiosClient
        .post("/forgot-password", { email: email.value })
        .then(() => {
            status.value = "If that email exists, a reset link has been sent.";
        })
        .catch(() => {
            error.value = "Could not send reset link. Please try again.";
        })
        .finally(() => {
            loading.value = false;
        });
};
</script>

<style scoped>
::root {
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

label {
    font-size: 13px;
    font-weight: 500;
}

input[type="email"] {
    border-radius: 12px;
    border: 1px solid var(--border);
    padding: 10px 12px;
    font-size: 14px;
    outline: none;
    background: #f9fafb;
    transition: border-color 0.15s ease, box-shadow 0.15s ease,
        background-color 0.15s ease;
}

input[type="email"]:focus {
    border-color: var(--halotel-orange);
    box-shadow: 0 0 0 1px rgba(249, 115, 22, 0.25);
    background: #ffffff;
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
        filter 0.15s ease, opacity 0.15s ease;
}

.primary-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.primary-button:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 22px 40px rgba(234, 88, 12, 0.5);
    filter: brightness(1.02);
}

.primary-button:active:not(:disabled) {
    transform: translateY(0);
    box-shadow: 0 12px 24px rgba(234, 88, 12, 0.35);
}

.button-icon {
    width: 16px;
    height: 16px;
    stroke-width: 1.8;
}

.status-text {
    margin: 4px 0 0 0;
    font-size: 13px;
}

.status-text.success {
    color: #16a34a;
}

.status-text.error {
    color: #b91c1c;
}

.footer-link {
    margin-top: 14px;
    font-size: 13px;
    color: var(--muted);
    text-align: center;
}

.footer-link a {
    color: var(--halotel-orange);
    text-decoration: none;
    font-weight: 500;
}

.footer-link a:hover {
    text-decoration: underline;
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