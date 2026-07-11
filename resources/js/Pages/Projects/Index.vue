<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import mockData from '../../../../mock-data.json'

const projets = ref([...mockData.projets])
const authUser = JSON.parse(localStorage.getItem('authUser') || '{}')

const showModal = ref(false)
const nouveauProjet = ref({ nom: '', description: '' })
const erreur = ref('')

function ouvrirModal() {
  showModal.value = true
  erreur.value = ''
  nouveauProjet.value = { nom: '', description: '' }
}

function fermerModal() {
  showModal.value = false
}

function creerProjet() {
  if (!nouveauProjet.value.nom.trim()) {
    erreur.value = 'Le nom du projet est obligatoire.'
    return
  }

  const projet = {
    id: Date.now(),
    nom: nouveauProjet.value.nom,
    description: nouveauProjet.value.description,
    progression: 0
  }

  projets.value.push(projet)
  fermerModal()
}

function deconnecter() {
  localStorage.removeItem('authUser')
  router.visit('/login')
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold text-blue-700">Mini-Trello</h1>
      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">{{ authUser.name }}</span>
        <button
          @click="deconnecter"
          class="text-sm text-red-500 hover:underline"
        >
          Déconnexion
        </button>
      </div>
    </nav>

    <!-- Contenu -->
    <div class="max-w-5xl mx-auto px-6 py-8">

      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Mes Projets</h2>
        <button
          @click="ouvrirModal"
          class="bg-blue-700 text-white px-4 py-2 rounded font-semibold hover:bg-blue-800 transition"
        >
          + Nouveau projet
        </button>
      </div>

      <!-- Liste des projets -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="projet in projets"
          :key="projet.id"
          class="bg-white rounded-lg shadow p-5"
        >
          <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ projet.nom }}</h3>
          <p class="text-sm text-gray-500 mb-4">{{ projet.description }}</p>

          <!-- Barre de progression -->
          <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
            <div
              class="bg-blue-600 h-2 rounded-full transition-all"
              :style="{ width: projet.progression + '%' }"
            ></div>
          </div>
          <p class="text-xs text-gray-400 text-right">{{ projet.progression }}% complété</p>
        </div>
      </div>

      <!-- Message si aucun projet -->
      <div v-if="projets.length === 0" class="text-center text-gray-400 mt-12">
        Aucun projet pour l'instant. Créez-en un !
      </div>

    </div>

    <!-- Modal création projet -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Nouveau projet</h3>

        <div v-if="erreur" class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm">
          {{ erreur }}
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Nom du projet</label>
          <input
            v-model="nouveauProjet.nom"
            type="text"
            placeholder="Ex: Refonte site vitrine"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea
            v-model="nouveauProjet.description"
            rows="3"
            placeholder="Description du projet..."
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          ></textarea>
        </div>

        <div class="flex gap-3 justify-end">
          <button
            @click="fermerModal"
            class="px-4 py-2 text-sm text-gray-600 hover:underline"
          >
            Annuler
          </button>
          <button
            @click="creerProjet"
            class="bg-blue-700 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-blue-800 transition"
          >
            Créer
          </button>
        </div>
      </div>
    </div>

  </div>
</template>