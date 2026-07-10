<script setup>
defineProps({
  tache: {
    type: Object,
    required: true
  },
  assigne: {
    type: Object,
    default: null
  }
})

const PRIORITE_STYLES = {
  HIGH: 'bg-red-100 text-red-700',
  MEDIUM: 'bg-yellow-100 text-yellow-700',
  LOW: 'bg-green-100 text-green-700'
}

function prioriteClass(priorite) {
  return PRIORITE_STYLES[priorite] ?? 'bg-gray-100 text-gray-700'
}

function formatEcheance(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' })
}
</script>

<template>
  <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm hover:shadow-md transition-shadow cursor-pointer">
    <h3 class="font-medium text-sm text-gray-800 mb-1">{{ tache.titre }}</h3>
    <p v-if="tache.description" class="text-xs text-gray-500 mb-2 line-clamp-2">
      {{ tache.description }}
    </p>
    <div class="flex items-center justify-between text-xs">
      <span :class="['px-2 py-0.5 rounded-full font-medium', prioriteClass(tache.priorite)]">
        {{ tache.priorite }}
      </span>
      <span v-if="tache.echeance" class="text-gray-400">
        {{ formatEcheance(tache.echeance) }}
      </span>
    </div>
    <div v-if="assigne" class="mt-2 flex items-center gap-1.5">
      <div class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px] font-semibold">
        {{ assigne.name.charAt(0) }}
      </div>
      <span class="text-xs text-gray-500">{{ assigne.name }}</span>
    </div>
  </div>
</template>