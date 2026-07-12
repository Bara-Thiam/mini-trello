<script setup>
import draggable from 'vuedraggable'
import TaskCard from './TaskCard.vue'

const props = defineProps({
  colonne: { type: Object, required: true },
  taches: { type: Array, required: true },
  users: { type: Array, default: () => [] }
})

const emit = defineEmits(['task-moved', 'edit-task'])

const STATUS_CONFIG = {
  ToDo: {
    dot: 'bg-ink-soft/50',
    badgeBg: 'bg-brand-50',
    badgeText: 'text-ink-soft',
    icon: 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08M6.75 21h9M6.75 3v18M18 3v18'
  },
  Doing: {
    dot: 'bg-amber',
    badgeBg: 'bg-amber/10',
    badgeText: 'text-amber',
    icon: 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  Done: {
    dot: 'bg-mint',
    badgeBg: 'bg-mint/10',
    badgeText: 'text-mint',
    icon: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
  },
}

function config(nom) { return STATUS_CONFIG[nom] ?? STATUS_CONFIG.ToDo }

function findUser(userId, users) { return users.find(u => u.id === userId) ?? null }

function onChange(event) {
  if (event.added) {
    emit('task-moved', { tache: event.added.element, nouvelleColonne: props.colonne })
  }
}
</script>

<template>
  <div class="bg-white/60 backdrop-blur-sm rounded-xl border border-brand-100 p-3 w-72 flex-shrink-0 flex flex-col max-h-full">
    <div class="flex items-center gap-2 mb-3 px-1">
      <span :class="['w-6 h-6 rounded-md flex items-center justify-center flex-shrink-0', config(colonne.nom).badgeBg, config(colonne.nom).badgeText]">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
          <path stroke-linecap="round" stroke-linejoin="round" :d="config(colonne.nom).icon" />
        </svg>
      </span>
      <h2 class="font-display font-semibold text-sm text-ink">{{ colonne.nom }}</h2>
      <span class="font-mono text-[11px] text-ink-soft bg-brand-50 px-1.5 py-0.5 rounded-full ml-auto">
        {{ taches.length }}
      </span>
    </div>

    <draggable
      :list="taches"
      group="taches"
      item-key="id"
      class="flex flex-col gap-2 min-h-[60px] overflow-y-auto pb-1"
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

    <p v-if="taches.length === 0" class="text-xs text-ink-soft/70 text-center py-6">
      Aucune tâche
    </p>
  </div>
</template>