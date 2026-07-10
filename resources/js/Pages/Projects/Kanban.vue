<script setup>
import { reactive, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import KanbanColumn from '@/Components/Kanban/KanbanColumn.vue'
import mockData from '@/mock-data.json'

const props = defineProps({
  projetId: {
    type: Number,
    default: 1
  }
})

const ORDRE_TO_STATUT = { 1: 'TODO', 2: 'DOING', 3: 'DONE' }

const projet = computed(() =>
  mockData.projets.find(p => p.id === props.projetId)
)

const colonnesDuProjet = computed(() =>
  mockData.colonnes
    .filter(c => c.projetId === props.projetId)
    .sort((a, b) => a.ordre - b.ordre)
)

// Un tableau réactif STABLE par colonne (indispensable pour vuedraggable)
const tachesByColonne = reactive({})
colonnesDuProjet.value.forEach(colonne => {
  const statut = ORDRE_TO_STATUT[colonne.ordre]
  tachesByColonne[colonne.id] = mockData.taches.filter(
    t => t.projetId === props.projetId && t.statut === statut
  )
})

function onTaskMoved({ tache, nouvelleColonne }) {
  const nouveauStatut = ORDRE_TO_STATUT[nouvelleColonne.ordre]
  tache.statut = nouveauStatut
  // TODO: PATCH /tasks/{id}/status une fois l'API prête (voir accord technique point 3)
}
</script>

<template>
  <AppLayout :title="projet?.nom ?? 'Projet'">
    <div class="flex gap-4 overflow-x-auto pb-4">
      <KanbanColumn
        v-for="colonne in colonnesDuProjet"
        :key="colonne.id"
        :colonne="colonne"
        v-model:taches="tachesByColonne[colonne.id]"
        :users="mockData.users"
        @task-moved="onTaskMoved"
      />
    </div>
  </AppLayout>
</template>