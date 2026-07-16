<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({ taches: Array })

const PRIORITE_STYLES = { HIGH: 'bg-coral/10 text-coral', MEDIUM: 'bg-amber/10 text-amber', LOW: 'bg-mint/10 text-mint' }
const STATUT_STYLES = { DONE: 'bg-mint/10 text-mint', DOING: 'bg-amber/10 text-amber', TODO: 'bg-brand-50 text-ink-soft' }
function prioriteClass(p) { return PRIORITE_STYLES[p] ?? 'bg-brand-50 text-ink-soft' }
function statutClass(s) { return STATUT_STYLES[s] ?? 'bg-brand-50 text-ink-soft' }
function estEnRetard(tache) {
  if (!tache.echeance || tache.statut === 'DONE') return false
  const today = new Date(); today.setHours(0, 0, 0, 0)
  return new Date(tache.echeance) < today
}
</script>

<template>
    <AppLayout title="Mes tâches">
        <div v-if="taches.length === 0" class="text-center py-20">
            <p class="font-display text-lg font-semibold text-ink mb-1">Aucune tâche assignée</p>
            <p class="text-ink-soft">Les tâches qu'on vous confie apparaîtront ici.</p>
        </div>

        <div class="flex flex-col gap-3">
            <div v-for="tache in taches" :key="tache.id" class="bg-white rounded-xl border border-brand-100 p-5 shadow-[0_2px_10px_rgba(30,27,46,0.06)]">
                <div class="flex items-start justify-between gap-4 mb-1.5">
                    <h3 class="font-display text-base font-semibold text-ink">{{ tache.titre }}</h3>
                    <div class="flex gap-2 flex-shrink-0">
                        <span :class="['text-[11px] font-medium px-2 py-1 rounded-full', statutClass(tache.statut)]">{{ tache.statut }}</span>
                        <span :class="['text-[11px] font-medium px-2 py-1 rounded-full', prioriteClass(tache.priorite)]">{{ tache.priorite }}</span>
                    </div>
                </div>
                <p class="text-sm text-ink-soft mb-3">{{ tache.description }}</p>
                <div class="flex items-center justify-between text-xs text-ink-soft pt-3 border-t border-brand-50">
                    <span class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-19.5 0v6a2.25 2.25 0 002.25 2.25h15a2.25 2.25 0 002.25-2.25v-6m-19.5 0h19.5M4.5 9.75V6a2.25 2.25 0 012.25-2.25h3.879a1.5 1.5 0 011.06.44l1.72 1.72a1.5 1.5 0 001.06.44H19.5A2.25 2.25 0 0121.75 9.75" />
                        </svg>
                        {{ tache.projet?.nom ?? 'Projet inconnu' }}
                    </span>
                    <span :class="['font-mono', estEnRetard(tache) ? 'text-coral font-semibold' : '']">{{ tache.echeance }}</span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>