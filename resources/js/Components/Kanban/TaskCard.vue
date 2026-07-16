<script setup>

import { computed } from 'vue'

const props = defineProps({
  tache: { type: Object, required: true },
  assigne: { type: Object, default: null }
})

const PRIORITE_STYLES = {
  HIGH: 'bg-coral/10 text-coral',
  MEDIUM: 'bg-amber/10 text-amber',
  LOW: 'bg-mint/10 text-mint',
}
function prioriteClass(priorite) { return PRIORITE_STYLES[priorite] ?? 'bg-brand-50 text-ink-soft' }

const ACCENTS = ['bg-brand-500', 'bg-mint', 'bg-coral', 'bg-amber']
function avatarColor(id) { return ACCENTS[id % ACCENTS.length] }

function formatEcheance(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' })
}

const isEnRetard = computed(() => {
  if (!props.tache.echeance || props.tache.statut === 'DONE') return false
  const today = new Date(); today.setHours(0, 0, 0, 0)
  return new Date(props.tache.echeance) < today
})
</script>

<template>
  <div tabindex="0" role="button"
    :class="['bg-white rounded-lg border p-3 shadow-[0_2px_8px_rgba(30,27,46,0.06)] hover:shadow-[0_8px_20px_rgba(30,27,46,0.14)] hover:-translate-y-0.5 transition-all cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500', isEnRetard ? 'border-coral/40 ring-1 ring-coral/20' : 'border-brand-100']">
    <h3 class="font-display font-semibold text-sm text-ink mb-1">{{ tache.titre }}</h3>
    <p v-if="tache.description" class="text-xs text-ink-soft mb-2.5 line-clamp-2">
      {{ tache.description }}
    </p>
    <div class="flex items-center justify-between text-xs mb-2">
      <span :class="['px-2 py-0.5 rounded-full font-medium text-[11px]', prioriteClass(tache.priorite)]">
        {{ tache.priorite }}
      </span>
      <span v-if="tache.echeance"
        :class="['font-mono text-[11px] flex items-center gap-1', isEnRetard ? 'text-coral font-semibold' : 'text-ink-soft']">
        <svg v-if="isEnRetard" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
          stroke="currentColor" class="w-3 h-3">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
        {{ formatEcheance(tache.echeance) }}
      </span>
    </div>
    <div v-if="assigne" class="flex items-center gap-1.5 pt-2 border-t border-brand-50">
      <div
        :class="['w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0', avatarColor(assigne.id)]">
        {{ assigne.name.charAt(0) }}
      </div>
      <span class="text-xs text-ink-soft truncate">{{ assigne.name }}</span>
    </div>
  </div>
</template>