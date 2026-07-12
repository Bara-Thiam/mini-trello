<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    totalProjets: Number,
    totalTaches: Number,
    tachesTerminees: Number,
    tachesEnCours: Number,
    tachesATaire: Number,
    parProjet: Array,
    parMembre: Array,
})

const STAT_ICONS = {
  projets: 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-19.5 0v6a2.25 2.25 0 002.25 2.25h15a2.25 2.25 0 002.25-2.25v-6m-19.5 0h19.5M4.5 9.75V6a2.25 2.25 0 012.25-2.25h3.879a1.5 1.5 0 011.06.44l1.72 1.72a1.5 1.5 0 001.06.44H19.5A2.25 2.25 0 0121.75 9.75',
  taches: 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08M6.75 21h9M6.75 3v18M18 3v18',
  done: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
  doing: 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
}
</script>

<template>
    <AppLayout title="Dashboard">
        <!-- Cartes principales -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-brand-100 p-5 shadow-[0_2px_10px_rgba(30,27,46,0.06)]">
                <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="STAT_ICONS.projets" />
                    </svg>
                </div>
                <p class="font-mono text-2xl font-bold text-ink">{{ totalProjets }}</p>
                <p class="text-xs text-ink-soft mt-1">Projets</p>
            </div>
            <div class="bg-white rounded-xl border border-brand-100 p-5 shadow-[0_2px_10px_rgba(30,27,46,0.06)]">
                <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="STAT_ICONS.taches" />
                    </svg>
                </div>
                <p class="font-mono text-2xl font-bold text-ink">{{ totalTaches }}</p>
                <p class="text-xs text-ink-soft mt-1">Tâches totales</p>
            </div>
            <div class="bg-white rounded-xl border border-brand-100 p-5 shadow-[0_2px_10px_rgba(30,27,46,0.06)]">
                <div class="w-9 h-9 rounded-lg bg-mint/10 flex items-center justify-center text-mint mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="STAT_ICONS.done" />
                    </svg>
                </div>
                <p class="font-mono text-2xl font-bold text-mint">{{ tachesTerminees }}</p>
                <p class="text-xs text-ink-soft mt-1">Terminées</p>
            </div>
            <div class="bg-white rounded-xl border border-brand-100 p-5 shadow-[0_2px_10px_rgba(30,27,46,0.06)]">
                <div class="w-9 h-9 rounded-lg bg-amber/10 flex items-center justify-center text-amber mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="STAT_ICONS.doing" />
                    </svg>
                </div>
                <p class="font-mono text-2xl font-bold text-amber">{{ tachesEnCours }}</p>
                <p class="text-xs text-ink-soft mt-1">En cours</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Répartition -->
            <div class="bg-white rounded-xl border border-brand-100 p-5 shadow-[0_2px_10px_rgba(30,27,46,0.06)]">
                <h3 class="font-display text-base font-semibold text-ink mb-4">Répartition des tâches</h3>
                <div class="flex h-2.5 rounded-full overflow-hidden bg-brand-50 mb-3">
                    <div class="bg-ink-soft/40" :style="{ width: (totalTaches > 0 ? tachesATaire / totalTaches * 100 : 0) + '%' }"></div>
                    <div class="bg-amber" :style="{ width: (totalTaches > 0 ? tachesEnCours / totalTaches * 100 : 0) + '%' }"></div>
                    <div class="bg-mint" :style="{ width: (totalTaches > 0 ? tachesTerminees / totalTaches * 100 : 0) + '%' }"></div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-ink-soft/40"></span>
                        <span class="text-ink-soft">{{ tachesATaire }} ToDo</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber"></span>
                        <span class="text-ink-soft">{{ tachesEnCours }} Doing</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-mint"></span>
                        <span class="text-ink-soft">{{ tachesTerminees }} Done</span>
                    </div>
                </div>
            </div>

            <!-- Tâches par membre -->
            <div class="bg-white rounded-xl border border-brand-100 p-5 shadow-[0_2px_10px_rgba(30,27,46,0.06)]">
                <h3 class="font-display text-base font-semibold text-ink mb-4">Tâches par membre</h3>
                <div class="flex flex-col gap-3">
                    <div v-for="membre in parMembre" :key="membre.userId" class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-brand-500 to-mint flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                            {{ membre.name.charAt(0) }}
                        </div>
                        <span class="text-sm text-ink flex-1 truncate">{{ membre.name }}</span>
                        <span class="font-mono text-sm font-semibold text-ink-soft">{{ membre.tasksCount }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progression par projet -->
        <div class="bg-white rounded-xl border border-brand-100 p-5 shadow-[0_2px_10px_rgba(30,27,46,0.06)] mt-6">
            <h3 class="font-display text-base font-semibold text-ink mb-4">Progression par projet</h3>
            <div class="flex flex-col gap-5">
                <div v-for="projet in parProjet" :key="projet.id">
                    <div class="flex justify-between mb-1.5">
                        <span class="text-sm font-medium text-ink">{{ projet.nom }}</span>
                        <span class="font-mono text-xs text-ink-soft">{{ projet.done }}/{{ projet.total }} — {{ projet.progression }}%</span>
                    </div>
                    <div class="w-full bg-brand-50 rounded-full h-2">
                        <div class="bg-gradient-to-r from-brand-500 to-mint h-full rounded-full transition-all" :style="{ width: projet.progression + '%' }"></div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>