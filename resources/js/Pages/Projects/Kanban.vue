<script setup>
import { reactive, computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import KanbanColumn from '@/Components/Kanban/KanbanColumn.vue'
import TaskForm from '@/Components/Kanban/TaskForm.vue'

const props = defineProps({
  projet: Object,
  colonnes: Array,
  taches: Array,
  users: Array,
})

const ORDRE_TO_STATUT = { 1: 'TODO', 2: 'DOING', 3: 'DONE' }

const colonnesTriees = computed(() => [...props.colonnes].sort((a, b) => a.ordre - b.ordre))

const tachesByColonne = reactive({})
colonnesTriees.value.forEach(colonne => {
  const statut = ORDRE_TO_STATUT[colonne.ordre]
  tachesByColonne[colonne.id] = props.taches.filter(t => t.statut === statut)
})

// --- Filtres (inchangé, fonctionne sur les vraies données) ---
const filtreUserId = ref('')
const filtrePriorite = ref('')
function tachesAffichees(colonneId) {
  return tachesByColonne[colonneId].filter(t => {
    if (filtreUserId.value && t.userId !== Number(filtreUserId.value)) return false
    if (filtrePriorite.value && t.priorite !== filtrePriorite.value) return false
    return true
  })
}
function resetFiltres() { filtreUserId.value = ''; filtrePriorite.value = '' }
const filtresActifs = computed(() => filtreUserId.value !== '' || filtrePriorite.value !== '')

// --- Drag & drop : appel JSON direct, avec rollback si refusé (403) ---
async function onTaskMoved({ tache, nouvelleColonne }) {
  const nouveauStatut = ORDRE_TO_STATUT[nouvelleColonne.ordre]
  const ancienStatut = tache.statut
  tache.statut = nouveauStatut
  try {
    await axios.patch(`/taches/${tache.id}/statut`, { statut: nouveauStatut })
  } catch (e) {
    tache.statut = ancienStatut
    alert("Impossible de déplacer cette tâche.")
  }
}

// --- Modale création / édition ---
const showModal = ref(false)
const editingTache = ref(null)
const targetColonneId = ref(null)

function openCreateModal(colonneId) {
  editingTache.value = null
  targetColonneId.value = colonneId
  showModal.value = true
}
function openEditModal(tache) {
  editingTache.value = tache
  targetColonneId.value = null
  showModal.value = true
}
function closeModal() {
  showModal.value = false
  editingTache.value = null
  targetColonneId.value = null
}

function handleSubmit(formData) {
  const payload = {
    titre: formData.titre,
    description: formData.description,
    user_id: formData.userId,
    priorite: formData.priorite,
    echeance: formData.echeance,
  }

  if (editingTache.value) {
    router.put(`/taches/${editingTache.value.id}`, payload, { onSuccess: closeModal })
  } else {
    router.post(`/projects/${props.projet.id}/taches`, payload, { onSuccess: closeModal })
  }
}
</script>

<template>
  <AppLayout :title="projet?.nom ?? 'Projet'">
    <div class="flex items-center gap-3 mb-4 flex-wrap">
      <select v-model="filtreUserId" class="border border-gray-300 rounded-md px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">Tous les membres</option>
        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
      </select>
      <select v-model="filtrePriorite" class="border border-gray-300 rounded-md px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">Toutes les priorités</option>
        <option value="HIGH">Haute</option>
        <option value="MEDIUM">Moyenne</option>
        <option value="LOW">Basse</option>
      </select>
      <button v-if="filtresActifs" @click="resetFiltres" class="text-sm text-indigo-600 hover:text-indigo-800 underline">
        Réinitialiser les filtres
      </button>
    </div>

    <div class="flex gap-4 overflow-x-auto pb-4">
      <div v-for="colonne in colonnesTriees" :key="colonne.id" class="flex flex-col">
        <KanbanColumn
          :colonne="colonne"
          :taches="tachesAffichees(colonne.id)"
          :users="users"
          @task-moved="onTaskMoved"
          @edit-task="openEditModal"
        />
        <button
          v-if="colonne.nom === 'ToDo'"
          @click="openCreateModal(colonne.id)"
          class="mt-2 text-sm text-gray-500 hover:text-indigo-600 hover:bg-white rounded-md px-3 py-2 text-left transition-colors"
        >
          + Ajouter une tâche
        </button>
      </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
          {{ editingTache ? 'Modifier la tâche' : 'Nouvelle tâche' }}
        </h2>
        <TaskForm :users="users" :tache="editingTache" @submit="handleSubmit" @cancel="closeModal" />
      </div>
    </div>
  </AppLayout>
</template>