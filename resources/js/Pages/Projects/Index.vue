<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ projets: Array })

const ACCENTS = ['bg-brand-500', 'bg-mint', 'bg-coral', 'bg-amber']
function accentFor(id) { return ACCENTS[id % ACCENTS.length] }

const progressionMoyenne = computed(() => {
  if (props.projets.length === 0) return 0
  return Math.round(props.projets.reduce((s, p) => s + p.progression, 0) / props.projets.length)
})
const totalTaches = computed(() => props.projets.reduce((s, p) => s + p.repartition.todo + p.repartition.doing + p.repartition.done, 0))

const showModal = ref(false)
const nouveauProjet = ref({ nom: '', description: '' })
const erreur = ref('')
function ouvrirModal() { showModal.value = true; erreur.value = ''; nouveauProjet.value = { nom: '', description: '' } }
function fermerModal() { showModal.value = false }
function creerProjet() {
  if (!nouveauProjet.value.nom.trim()) { erreur.value = 'Le nom du projet est obligatoire.'; return }
  router.post('/projects', nouveauProjet.value, { onSuccess: fermerModal, onError: () => { erreur.value = 'Impossible de créer le projet.' } })
}
function ouvrirProjet(id) { router.visit(`/projects/${id}`) }
function segmentWidth(count, total) { return total > 0 ? (count / total) * 100 : 0 }
</script>

<template>
  <AppLayout>
    <!-- Bannière avec lueur colorée -->
    <div
      class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-brand-600 to-brand-500 p-8 mb-8 shadow-[0_20px_45px_-12px_rgba(108,92,231,0.6)]">
      <div class="absolute inset-0 opacity-[0.06]"
        style="background-image: radial-gradient(circle, white 1.5px, transparent 1.5px); background-size: 18px 18px;">
      </div>
      <div class="relative flex items-start justify-between flex-wrap gap-6">
        <div>
          <p class="font-mono text-[11px] font-medium uppercase tracking-[0.15em] text-white/60 mb-2">Tableau de bord
          </p>
          <h1 class="font-display text-4xl font-bold text-white mb-5 tracking-tight">Vos projets, en un coup d'œil</h1>
          <div class="flex items-center gap-6">
            <div>
              <p class="font-mono text-3xl font-bold text-white leading-none">{{ projets.length }}</p>
              <p class="text-[11px] uppercase tracking-wide text-white/60 mt-1.5">projets</p>
            </div>
            <div class="w-px h-9 bg-white/20"></div>
            <div>
              <p class="font-mono text-3xl font-bold text-white leading-none">{{ totalTaches }}</p>
              <p class="text-[11px] uppercase tracking-wide text-white/60 mt-1.5">tâches</p>
            </div>
            <div class="w-px h-9 bg-white/20"></div>
            <div>
              <p class="font-mono text-3xl font-bold text-white leading-none">{{ progressionMoyenne }}%</p>
              <p class="text-[11px] uppercase tracking-wide text-white/60 mt-1.5">complété</p>
            </div>
          </div>
        </div>
        <button @click="ouvrirModal"
          class="flex items-center gap-1.5 font-display font-semibold bg-white text-ink px-5 py-2.5 rounded-lg shadow-lg hover:shadow-xl transition-shadow focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-600">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
            class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
          Nouveau projet
        </button>
      </div>
    </div>

    <!-- Grille de projets -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
      <div v-for="projet in projets" :key="projet.id" @click="ouvrirProjet(projet.id)"
        @keydown.enter="ouvrirProjet(projet.id)" tabindex="0" role="button"
        class="bg-white rounded-xl border border-brand-100 overflow-hidden cursor-pointer shadow-[0_4px_16px_rgba(30,27,46,0.1)] hover:shadow-[0_16px_36px_rgba(30,27,46,0.2)] hover:-translate-y-1.5 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
        <div class="p-5">
          <div class="flex items-center gap-3 mb-3">
            <div
              :class="['w-9 h-9 rounded-lg flex items-center justify-center font-display font-bold text-sm text-white flex-shrink-0 shadow-sm', accentFor(projet.id)]">
              {{ projet.nom.charAt(0).toUpperCase() }}
            </div>
            <h3 class="font-display text-base font-bold text-ink truncate">{{ projet.nom }}</h3>
          </div>
          <p class="text-sm font-normal text-ink-soft mb-4 line-clamp-2">{{ projet.description }}</p>

          <div class="flex h-1.5 rounded-full overflow-hidden bg-brand-50 mb-2">
            <div class="bg-ink-soft/40"
              :style="{ width: segmentWidth(projet.repartition.todo, projet.repartition.todo + projet.repartition.doing + projet.repartition.done) + '%' }">
            </div>
            <div class="bg-amber"
              :style="{ width: segmentWidth(projet.repartition.doing, projet.repartition.todo + projet.repartition.doing + projet.repartition.done) + '%' }">
            </div>
            <div class="bg-mint"
              :style="{ width: segmentWidth(projet.repartition.done, projet.repartition.todo + projet.repartition.doing + projet.repartition.done) + '%' }">
            </div>
          </div>
          <div class="flex items-center justify-between">
            <span class="font-mono text-[11px] text-ink-soft">{{ projet.repartition.todo }} ToDo · {{
              projet.repartition.doing }} Doing · {{ projet.repartition.done }} Done</span>
            <span class="font-mono text-xs font-bold text-ink">{{ projet.progression }}%</span>
          </div>
        </div>
      </div>

      <button @click="ouvrirModal"
        class="border-2 border-dashed border-brand-200 rounded-xl flex flex-col items-center justify-center gap-2 py-8 text-ink-soft hover:border-brand-400 hover:text-brand-600 hover:bg-brand-50/50 hover:shadow-md transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span class="text-sm font-medium">Nouveau projet</span>
      </button>
    </div>

    <div v-if="projets.length === 0" class="text-center py-20">
      <p class="font-display text-lg font-semibold text-ink mb-1">Aucun projet pour l'instant</p>
      <p class="text-ink-soft">Créez votre premier projet pour commencer à organiser vos tâches.</p>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-ink/50 flex items-center justify-center z-50 p-4"
      @click.self="fermerModal">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="font-display text-lg font-bold text-ink mb-5">Nouveau projet</h3>
        <div v-if="erreur" class="bg-coral/10 text-coral px-4 py-2 rounded-lg mb-4 text-sm font-medium">{{ erreur }}
        </div>
        <div class="mb-4">
          <label class="block text-sm font-semibold text-ink mb-1.5">Nom du projet</label>
          <input v-model="nouveauProjet.nom" type="text" placeholder="Ex: Refonte site vitrine"
            class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-colors" />
        </div>
        <div class="mb-6">
          <label class="block text-sm font-semibold text-ink mb-1.5">Description</label>
          <textarea v-model="nouveauProjet.description" rows="3" placeholder="Description du projet..."
            class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-colors resize-none"></textarea>
        </div>
        <div class="flex gap-3 justify-end">
          <button @click="fermerModal"
            class="px-4 py-2 text-sm font-medium text-ink-soft hover:text-ink transition-colors">Annuler</button>
          <button @click="creerProjet"
            class="font-display font-semibold bg-brand-500 text-white px-5 py-2 rounded-lg shadow-md hover:shadow-lg hover:bg-brand-600 transition-all">Créer</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>