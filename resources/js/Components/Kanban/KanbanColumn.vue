<script setup>
import TaskCard from './TaskCard.vue'

defineProps({
  colonne: {
    type: Object,
    required: true
  },
  taches: {
    type: Array,
    default: () => []
  },
  users: {
    type: Array,
    default: () => []
  }
})

function findUser(userId, users) {
  return users.find(u => u.id === userId) ?? null
}
</script>

<template>
  <div class="bg-gray-100 rounded-lg p-3 w-72 flex-shrink-0">
    <div class="flex items-center justify-between mb-3 px-1">
      <h2 class="font-semibold text-sm text-gray-700">{{ colonne.nom }}</h2>
      <span class="text-xs text-gray-400 bg-white px-2 py-0.5 rounded-full">
        {{ taches.length }}
      </span>
    </div>
    <div class="flex flex-col gap-2">
      <TaskCard
        v-for="tache in taches"
        :key="tache.id"
        :tache="tache"
        :assigne="findUser(tache.userId, users)"
      />
      <p v-if="taches.length === 0" class="text-xs text-gray-400 text-center py-4">
        Aucune tâche
      </p>
    </div>
  </div>
</template>