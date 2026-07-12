<script setup>
defineProps({
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
</script>

<template>
  <div
    tabindex="0"
    role="button"
    class="bg-white rounded-lg border border-brand-100 p-3 shadow-[0_2px_8px_rgba(30,27,46,0.06)] hover:shadow-[0_8px_20px_rgba(30,27,46,0.14)] hover:-translate-y-0.5 transition-all cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
  >
    <h3 class="font-display font-semibold text-sm text-ink mb-1">{{ tache.titre }}</h3>
    <p v-if="tache.description" class="text-xs text-ink-soft mb-2.5 line-clamp-2">
      {{ tache.description }}
    </p>
    <div class="flex items-center justify-between text-xs mb-2">
      <span :class="['px-2 py-0.5 rounded-full font-medium text-[11px]', prioriteClass(tache.priorite)]">
        {{ tache.priorite }}
      </span>
      <span v-if="tache.echeance" class="font-mono text-[11px] text-ink-soft">
        {{ formatEcheance(tache.echeance) }}
      </span>
    </div>
    <div v-if="assigne" class="flex items-center gap-1.5 pt-2 border-t border-brand-50">
      <div :class="['w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0', avatarColor(assigne.id)]">
        {{ assigne.name.charAt(0) }}
      </div>
      <span class="text-xs text-ink-soft truncate">{{ assigne.name }}</span>
    </div>
  </div>
</template>