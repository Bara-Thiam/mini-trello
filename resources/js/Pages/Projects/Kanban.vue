<script setup>
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import KanbanColumn from '@/Components/Kanban/KanbanColumn.vue'
import TaskForm from '@/Components/Kanban/TaskForm.vue'
import { useForm } from '@inertiajs/vue3'
import { reactive, computed, ref, watch } from 'vue'

const props = defineProps({
  projet: Object,
  colonnes: Array,
  taches: Array,
  users: Array,
})

const peutGererMembres = computed(() => {
  const role = page.props.auth?.user?.role
  return role === 'admin' || role === 'chef_projet'
})

const showMembresModal = ref(false)

function retirerMembre(userId) {
  if (confirm('Retirer cette personne du projet ? Ses tâches assignées seront désassignées.')) {
    router.delete(`/projects/${props.projet.id}/membres/${userId}`, { preserveScroll: true })
  }
}

const ORDRE_TO_STATUT = { 1: 'TODO', 2: 'DOING', 3: 'DONE' }

const colonnesTriees = computed(() => [...props.colonnes].sort((a, b) => a.ordre - b.ordre))

const tachesByColonne = reactive({})

function rebuildTachesByColonne() {
  colonnesTriees.value.forEach(colonne => {
    const statut = ORDRE_TO_STATUT[colonne.ordre]
    tachesByColonne[colonne.id] = props.taches.filter(t => t.statut === statut)
  })
}

watch(() => props.taches, rebuildTachesByColonne, { immediate: true, deep: true })
const filtreUserId = ref('')
const filtrePriorite = ref('')
function tachesAffichees(colonneId) {
  return tachesByColonne[colonneId].filter(t => {
    if (filtreUserId.value && t.userId !== Number(filtreUserId.value)) return false
    if (filtrePriorite.value && t.priorite !== filtrePriorite.value) return false
    return true
  })
}
function resetFiltres() { filtreUserId.value = ''; filtrePriorite.value = '' }
const filtresActifs = computed(() => filtreUserId.value !== '' || filtrePriorite.value !== '')

function syncTachePosition(tache) {
  colonnesTriees.value.forEach(colonne => {
    const liste = tachesByColonne[colonne.id]
    const index = liste.findIndex(t => t.id === tache.id)
    const doitEtreIci = ORDRE_TO_STATUT[colonne.ordre] === tache.statut

    if (index !== -1 && !doitEtreIci) {
      liste.splice(index, 1)
    } else if (index === -1 && doitEtreIci) {
      liste.push(tache)
    }
  })
}

async function onTaskMoved({ tache, nouvelleColonne }) {
  const ancienStatut = tache.statut
  const nouveauStatut = ORDRE_TO_STATUT[nouvelleColonne.ordre]

  tache.statut = nouveauStatut
  syncTachePosition(tache)

  try {
    await axios.patch(`/taches/${tache.id}/statut`, { statut: nouveauStatut })
  } catch (e) {
    tache.statut = ancienStatut
    syncTachePosition(tache)
    alert("Impossible de déplacer cette tâche — vous n'avez peut-être pas la permission.")
  }
}

const showModal = ref(false)
const editingTache = ref(null)
const targetColonneId = ref(null)

function openCreateModal(colonneId) { editingTache.value = null; targetColonneId.value = colonneId; showModal.value = true }
function openEditModal(tache) { editingTache.value = tache; targetColonneId.value = null; showModal.value = true }
function closeModal() { showModal.value = false; editingTache.value = null; targetColonneId.value = null }

function handleSubmit(formData) {
  const payload = {
    titre: formData.titre, description: formData.description,
    user_id: formData.userId, priorite: formData.priorite, echeance: formData.echeance,
  }
  if (editingTache.value) {
    router.put(`/taches/${editingTache.value.id}`, payload, { onSuccess: closeModal })
  } else {
    router.post(`/projects/${props.projet.id}/taches`, payload, { onSuccess: closeModal })
  }
}

const showInviteModal = ref(false)
const inviteForm = useForm({ email: '' })

function ouvrirInviteModal() { showInviteModal.value = true; inviteForm.reset(); inviteForm.clearErrors() }
function envoyerInvitation() {
  inviteForm.post(`/projects/${props.projet.id}/invitations`, { onSuccess: () => { showInviteModal.value = false } })
}

import { usePage } from '@inertiajs/vue3'
const page = usePage()
const peutInviter = computed(() => ['admin', 'chef_projet'].includes(page.props.auth?.user?.role))
</script>

<template>
  <AppLayout>
    <!-- Fil d'ariane + titre -->
    <div class="flex items-center gap-2 mb-1">
      <button @click="router.visit('/projects')" class="text-ink-soft hover:text-brand-600 transition-colors"
        aria-label="Retour aux projets">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
          class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
      </button>
      <span class="text-sm text-ink-soft">Projets</span>
      <span class="text-sm text-ink-soft">/</span>
      <span class="text-sm font-medium text-ink">{{ projet?.nom }}</span>
    </div>
    <h1 class="font-display text-2xl font-bold text-ink mb-5">{{ projet?.nom ?? 'Projet' }}</h1>

    <!-- Filtres -->
    <div class="flex items-center gap-3 mb-5 flex-wrap">
      <select v-model="filtreUserId"
        class="border border-brand-100 rounded-lg px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
        <option value="">Tous les membres</option>
        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
      </select>
      <select v-model="filtrePriorite"
        class="border border-brand-100 rounded-lg px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
        <option value="">Toutes les priorités</option>
        <option value="HIGH">Haute</option>
        <option value="MEDIUM">Moyenne</option>
        <option value="LOW">Basse</option>
      </select>
      <button v-if="filtresActifs" @click="resetFiltres"
        class="text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
        Réinitialiser
      </button>
      <button v-if="peutInviter" @click="ouvrirInviteModal"
        class="ml-auto flex items-center gap-1.5 text-sm font-semibold bg-brand-500 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-brand-600 hover:shadow-md transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
        </svg>
        Inviter
      </button>
      <button @click="showMembresModal = true"
        class="flex items-center gap-1.5 text-sm font-medium text-ink-soft hover:text-brand-600 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
        </svg>
        Membres ({{ users.length }})
      </button>
      <div v-if="showInviteModal" class="fixed inset-0 bg-ink/50 flex items-center justify-center z-50 p-4"
        @click.self="showInviteModal = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6">
          <h3 class="font-display text-lg font-bold text-ink mb-4">Inviter un membre</h3>
          <div v-if="inviteForm.errors.email"
            class="bg-coral/10 text-coral px-4 py-2 rounded-lg mb-4 text-sm font-medium">{{ inviteForm.errors.email }}
          </div>
          <input v-model="inviteForm.email" type="email" placeholder="email@exemple.com"
            class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 mb-4" />
          <div class="flex justify-end gap-3">
            <button @click="showInviteModal = false"
              class="px-4 py-2 text-sm text-ink-soft hover:text-ink transition-colors">Annuler</button>
            <button @click="envoyerInvitation" :disabled="inviteForm.processing"
              class="font-display font-semibold bg-brand-500 text-white px-4 py-2 rounded-lg hover:bg-brand-600 transition-colors">Envoyer</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Colonnes -->
    <div class="flex gap-4 overflow-x-auto pb-4 -mx-4 px-4 lg:mx-0 lg:px-0">
      <div v-for="colonne in colonnesTriees" :key="colonne.id" class="flex flex-col">
        <KanbanColumn :colonne="colonne" :taches="tachesAffichees(colonne.id)" :users="users" @task-moved="onTaskMoved"
          @edit-task="openEditModal" />
        <button v-if="colonne.nom === 'ToDo'" @click="openCreateModal(colonne.id)"
          class="mt-2 flex items-center gap-1.5 text-sm font-medium text-ink-soft hover:text-brand-600 rounded-lg px-3 py-2 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
            class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
          Ajouter une tâche
        </button>
      </div>
    </div>

    <!-- Modale -->
    <div v-if="showModal" class="fixed inset-0 bg-ink/50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h2 class="font-display text-lg font-bold text-ink mb-4">
          {{ editingTache ? 'Modifier la tâche' : 'Nouvelle tâche' }}
        </h2>
        <TaskForm :users="users" :tache="editingTache" @submit="handleSubmit" @cancel="closeModal" />
      </div>
    </div>
  </AppLayout>
  <div v-if="showMembresModal" class="fixed inset-0 bg-ink/50 flex items-center justify-center z-50 p-4"
    @click.self="showMembresModal = false">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6">
      <h3 class="font-display text-lg font-bold text-ink mb-4">Membres du projet</h3>
      <div class="flex flex-col gap-2">
        <div v-for="user in users" :key="user.id" class="flex items-center justify-between gap-3 px-1 py-2">
          <div class="flex items-center gap-2.5 min-w-0">
            <span
              class="w-7 h-7 rounded-full bg-gradient-to-br from-brand-500 to-mint flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
              {{ user.name.charAt(0) }}
            </span>
            <span class="text-sm text-ink truncate">{{ user.name }}</span>
          </div>
          <button v-if="peutGererMembres && users.length > 1" @click="retirerMembre(user.id)"
            class="text-ink-soft/40 hover:text-coral transition-colors flex-shrink-0" aria-label="Retirer du projet">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <button @click="showMembresModal = false"
        class="w-full mt-4 text-sm font-medium text-ink-soft hover:text-ink transition-colors">
        Fermer
      </button>
    </div>
  </div>
</template>