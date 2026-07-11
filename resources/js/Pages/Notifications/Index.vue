<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import mockData from "../../../../mock-data.json";

const authUser = JSON.parse(localStorage.getItem("authUser") || "{}");

const notifications = ref(
    mockData.notifications.filter((n) => n.userId === authUser.id),
);

const nonLues = computed(() => notifications.value.filter((n) => !n.lu).length);

function marquerLue(id) {
    const notif = notifications.value.find((n) => n.id === id);
    if (notif) notif.lu = true;
}

function marquerToutesLues() {
    notifications.value.forEach((n) => (n.lu = true));
}

function deconnecter() {
    localStorage.removeItem("authUser");
    router.visit("/login");
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <nav
            class="bg-white shadow px-6 py-4 flex justify-between items-center"
        >
            <h1 class="text-xl font-bold text-blue-700">Mini-Trello</h1>
            <div style="display: flex; gap: 16px; align-items: center">
                <a
                    href="/projects"
                    class="text-sm text-gray-600 hover:underline"
                    >Projets</a
                >
                <a
                    href="/tasks/mine"
                    class="text-sm text-gray-600 hover:underline"
                    >Mes tâches</a
                >
                <a
                    href="/dashboard"
                    class="text-sm text-gray-600 hover:underline"
                    >Dashboard</a
                >
                <a
                    href="/notifications"
                    style="
                        position: relative;
                        display: inline-flex;
                        align-items: center;
                    "
                    class="text-sm text-gray-600 hover:underline"
                >
                    Notifications
                    <span
                        v-if="nonLues > 0"
                        style="
                            position: absolute;
                            top: -8px;
                            right: -12px;
                            background: #ef4444;
                            color: white;
                            border-radius: 9999px;
                            font-size: 10px;
                            padding: 1px 5px;
                            font-weight: 600;
                        "
                    >
                        {{ nonLues }}
                    </span>
                </a>
                <span class="text-sm text-gray-600">{{ authUser.name }}</span>
                <button
                    @click="deconnecter"
                    class="text-sm text-red-500 hover:underline"
                >
                    Déconnexion
                </button>
            </div>
        </nav>

        <div class="max-w-3xl mx-auto px-6 py-8">
            <div
                style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 24px;
                "
            >
                <h2 class="text-2xl font-bold text-gray-800">
                    Notifications
                    <span
                        v-if="nonLues > 0"
                        class="text-sm font-normal text-red-500 ml-2"
                        >{{ nonLues }} non lue(s)</span
                    >
                </h2>
                <button
                    v-if="nonLues > 0"
                    @click="marquerToutesLues"
                    class="text-sm text-blue-600 hover:underline"
                >
                    Tout marquer comme lu
                </button>
            </div>

            <div
                v-if="notifications.length === 0"
                class="text-center text-gray-400 mt-12"
            >
                Aucune notification.
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px">
                <div
                    v-for="notif in notifications"
                    :key="notif.id"
                    @click="marquerLue(notif.id)"
                    :style="{
                        background: notif.lu ? '#f9fafb' : '#eff6ff',
                        borderLeft: notif.lu
                            ? '4px solid #e5e7eb'
                            : '4px solid #3b82f6',
                        cursor: 'pointer',
                    }"
                    class="rounded-lg shadow p-4"
                >
                    <div
                        style="
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                        "
                    >
                        <p
                            :class="
                                notif.lu
                                    ? 'text-sm text-gray-500'
                                    : 'text-sm font-semibold text-gray-800'
                            "
                        >
                            {{ notif.message }}
                        </p>
                        <span
                            v-if="!notif.lu"
                            style="
                                width: 8px;
                                height: 8px;
                                background: #3b82f6;
                                border-radius: 9999px;
                                flex-shrink: 0;
                                margin-left: 12px;
                            "
                        ></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
