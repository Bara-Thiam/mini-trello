<script setup>
defineProps({
    taches: Array,
})

function getStatutClass(statut) {
    if (statut === "DONE") return "bg-green-100 text-green-700";
    if (statut === "DOING") return "bg-yellow-100 text-yellow-700";
    return "bg-gray-100 text-gray-600";
}

function getPrioriteClass(priorite) {
    if (priorite === "HIGH") return "bg-red-100 text-red-600";
    return "bg-blue-100 text-blue-600";
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <div class="max-w-4xl mx-auto px-6 py-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Mes Tâches</h2>

            <div v-if="taches.length === 0" class="text-center text-gray-400 mt-12">
                Aucune tâche assignée pour l'instant.
            </div>

            <div style="display: flex; flex-direction: column; gap: 16px">
                <div v-for="tache in taches" :key="tache.id" class="bg-white rounded-lg shadow p-5">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px">
                        <h3 class="text-base font-semibold text-gray-800">{{ tache.titre }}</h3>
                        <div style="display: flex; gap: 8px; margin-left: 16px">
                            <span :class="['text-xs font-medium px-2 py-1 rounded-full', getStatutClass(tache.statut)]">
                                {{ tache.statut }}
                            </span>
                            <span :class="['text-xs font-medium px-2 py-1 rounded-full', getPrioriteClass(tache.priorite)]">
                                {{ tache.priorite }}
                            </span>
                        </div>
                    </div>

                    <p class="text-sm text-gray-500 mb-3">{{ tache.description }}</p>

                    <div style="display: flex; justify-content: space-between" class="text-xs text-gray-400">
                        <span>📁 {{ tache.projet?.nom ?? 'Projet inconnu' }}</span>
                        <span>📅 {{ tache.echeance }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>