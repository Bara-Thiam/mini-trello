<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import mockData from "../../../../mock-data.json";

const authUser = JSON.parse(localStorage.getItem("authUser") || "{}");

const totalProjets = computed(() => mockData.projets.length);
const totalTaches = computed(() => mockData.taches.length);
const tachesTerminees = computed(
    () => mockData.taches.filter((t) => t.statut === "DONE").length,
);
const tachesEnCours = computed(
    () => mockData.taches.filter((t) => t.statut === "DOING").length,
);
const tachesATaire = computed(
    () => mockData.taches.filter((t) => t.statut === "TODO").length,
);

const statsParProjet = computed(() => {
    return mockData.projets.map((projet) => {
        const taches = mockData.taches.filter((t) => t.projetId === projet.id);
        const done = taches.filter((t) => t.statut === "DONE").length;
        const total = taches.length;
        const progression = total > 0 ? Math.round((done / total) * 100) : 0;
        return { ...projet, total, done, progression };
    });
});

function deconnecter() {
    localStorage.removeItem("authUser");
    router.visit("/login");
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navbar -->
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
                <span class="text-sm text-gray-600">{{ authUser.name }}</span>
                <button
                    @click="deconnecter"
                    class="text-sm text-red-500 hover:underline"
                >
                    Déconnexion
                </button>
            </div>
        </nav>

        <!-- Contenu -->
        <div class="max-w-5xl mx-auto px-6 py-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>

            <!-- Cartes statistiques -->
            <div
                style="
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 16px;
                    margin-bottom: 32px;
                "
            >
                <div class="bg-white rounded-lg shadow p-5 text-center">
                    <p class="text-3xl font-bold text-blue-700">
                        {{ totalProjets }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">Projets</p>
                </div>

                <div class="bg-white rounded-lg shadow p-5 text-center">
                    <p class="text-3xl font-bold text-gray-700">
                        {{ totalTaches }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">Tâches totales</p>
                </div>

                <div class="bg-white rounded-lg shadow p-5 text-center">
                    <p class="text-3xl font-bold text-green-600">
                        {{ tachesTerminees }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">Terminées</p>
                </div>

                <div class="bg-white rounded-lg shadow p-5 text-center">
                    <p class="text-3xl font-bold text-yellow-500">
                        {{ tachesEnCours }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">En cours</p>
                </div>
            </div>

            <!-- Répartition par statut -->
            <div class="bg-white rounded-lg shadow p-5 mb-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">
                    Répartition des tâches
                </h3>
                <div style="display: flex; gap: 16px">
                    <div
                        style="
                            flex: 1;
                            background: #f3f4f6;
                            border-radius: 8px;
                            padding: 12px;
                            text-align: center;
                        "
                    >
                        <p class="text-2xl font-bold text-gray-600">
                            {{ tachesATaire }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">TODO</p>
                    </div>
                    <div
                        style="
                            flex: 1;
                            background: #fef9c3;
                            border-radius: 8px;
                            padding: 12px;
                            text-align: center;
                        "
                    >
                        <p class="text-2xl font-bold text-yellow-600">
                            {{ tachesEnCours }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">DOING</p>
                    </div>
                    <div
                        style="
                            flex: 1;
                            background: #dcfce7;
                            border-radius: 8px;
                            padding: 12px;
                            text-align: center;
                        "
                    >
                        <p class="text-2xl font-bold text-green-600">
                            {{ tachesTerminees }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">DONE</p>
                    </div>
                </div>
            </div>

            <!-- Progression par projet -->
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-base font-semibold text-gray-800 mb-4">
                    Progression par projet
                </h3>
                <div style="display: flex; flex-direction: column; gap: 16px">
                    <div v-for="projet in statsParProjet" :key="projet.id">
                        <div
                            style="
                                display: flex;
                                justify-content: space-between;
                                margin-bottom: 4px;
                            "
                        >
                            <span class="text-sm font-medium text-gray-700">{{
                                projet.nom
                            }}</span>
                            <span class="text-sm text-gray-400"
                                >{{ projet.done }}/{{ projet.total }} tâches —
                                {{ projet.progression }}%</span
                            >
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div
                                class="bg-blue-600 h-2 rounded-full transition-all"
                                :style="{ width: projet.progression + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
