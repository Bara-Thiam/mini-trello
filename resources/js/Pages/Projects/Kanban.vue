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

const showModal = ref(false)
const editingTache = ref(null)
const targetColonneId = ref(null)

function openCreateModal(colonneId) { editingTache.value = null; targetColonneId.value = colonneId; showModal.value = true }
function openEditModal(tache) { editingTache.value = tache; targetColonneId.value = null; showModal.value = true }
function closeModal() { showModal.value = false; editingTache.value = null; targetColonneId.value = null }

function handleSubmit(formData) {
  const payload = {
    titre: formData.titre, description: formData.description,
    user_id: formData.userId, priorite: formData.priorite, echeance: formData.echeance,
  }
  if (editingTache.value) {
    router.put(`/taches/${editingTache.value.id}`, payload, { onSuccess: closeModal })
  } else {
    router.post(`/projects/${props.projet.id}/taches`, payload, { onSuccess: closeModal })
  }
}
</script>

<template>
  <AppLayout>
    <!-- Fil d'ariane + titre -->
    <div class="flex items-center gap-2 mb-1">
      <button @click="router.visit('/projects')" class="text-ink-soft hover:text-brand-600 transition-colors" aria-label="Retour aux projets">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
      </button>
      <span class="text-sm text-ink-soft">Projets</span>
      <span class="text-sm text-ink-soft">/</span>
      <span class="text-sm font-medium text-ink">{{ projet?.nom }}</span>
    </div>
    <h1 class="font-display text-2xl font-bold text-ink mb-5">{{ projet?.nom ?? 'Projet' }}</h1>

    <!-- Filtres -->
    <div class="flex items-center gap-3 mb-5 flex-wrap">
      <select v-model="filtreUserId" class="border border-brand-100 rounded-lg px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
        <option value="">Tous les membres</option>
        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
      </select>
      <select v-model="filtrePriorite" class="border border-brand-100 rounded-lg px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
        <option value="">Toutes les priorités</option>
        <option value="HIGH">Haute</option>
        <option value="MEDIUM">Moyenne</option>
        <option value="LOW">Basse</option>
      </select>
      <button v-if="filtresActifs" @click="resetFiltres" class="text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
        Réinitialiser
      </button>
    </div>

    <!-- Colonnes -->
    <div class="flex gap-4 overflow-x-auto pb-4 -mx-4 px-4 lg:mx-0 lg:px-0">
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
          class="mt-2 flex items-center gap-1.5 text-sm font-medium text-ink-soft hover:text-brand-600 rounded-lg px-3 py-2 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
          Ajouter une tâche
        </button>
      </div>
    </div>

    <!-- Modale -->
    <div v-if="showModal" class="fixed inset-0 bg-ink/50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h2 class="font-display text-lg font-bold text-ink mb-4">
          {{ editingTache ? 'Modifier la tâche' : 'Nouvelle tâche' }}
        </h2>
        <TaskForm :users="users" :tache="editingTache" @submit="handleSubmit" @cancel="closeModal" />
      </div>
    </div>
  </AppLayout>
</template>