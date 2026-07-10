<script setup>
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import KanbanColumn from '@/Components/Kanban/KanbanColumn.vue'
import mockData from '@/mock-data.json'

const props = defineProps({
  projetId: {
    type: Number,
    default: 1
  }
})

// Ordre 1,2,3 des colonnes correspond aux statuts TODO/DOING/DONE
const ORDRE_TO_STATUT = { 1: 'TODO', 2: 'DOING', 3: 'DONE' }

const projet = computed(() =>
  mockData.projets.find(p => p.id === props.projetId)
)

const colonnesDuProjet = computed(() =>
  mockData.colonnes
    .filter(c => c.projetId === props.projetId)
    .sort((a, b) => a.ordre - b.ordre)
)

function tachesPourColonne(colonne) {
  const statut = ORDRE_TO_STATUT[colonne.ordre]
  return mockData.taches.filter(
    t => t.projetId === props.projetId && t.statut === statut
  )
}
</script>

<template>
  <AppLayout :title="projet?.nom ?? 'Projet'">
    <div class="flex gap-4 overflow-x-auto pb-4">
      <KanbanColumn
        v-for="colonne in colonnesDuProjet"
        :key="colonne.id"
        :colonne="colonne"
        :taches="tachesPourColonne(colonne)"
        :users="mockData.users"
      />
    </div>
  </AppLayout>
</template>