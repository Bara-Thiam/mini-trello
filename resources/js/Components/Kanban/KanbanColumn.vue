<script setup>
import draggable from 'vuedraggable'
import TaskCard from './TaskCard.vue'

const props = defineProps({
  colonne: {
    type: Object,
    required: true
  },
  taches: {
    type: Array,
    required: true
  },
  users: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['task-moved', 'edit-task'])

function findUser(userId, users) {
  return users.find(u => u.id === userId) ?? null
}

function onChange(event) {
  if (event.added) {
    emit('task-moved', {
      tache: event.added.element,
      nouvelleColonne: props.colonne
    })
  }
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
    <draggable
      :list="taches"
      group="taches"
      item-key="id"
      class="flex flex-col gap-2 min-h-[40px]"
      ghost-class="opacity-40"
      @change="onChange"
    >
      <template #item="{ element }">
        <TaskCard
          :tache="element"
          :assigne="findUser(element.userId, users)"
          @click="emit('edit-task', element)"
        />
      </template>
    </draggable>
    <p v-if="taches.length === 0" class="text-xs text-gray-400 text-center py-4">
      Aucune tâche
    </p>
  </div>
</template>