<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import mockData from '../../../../mock-data.json'

const email = ref('')
const password = ref('')
const erreur = ref('')

function handleLogin() {
  erreur.value = ''

  const user = mockData.users.find(
    u => u.email === email.value && u.password === password.value
  )

  if (user) {
    localStorage.setItem('authUser', JSON.stringify(user))
    router.visit('/projects')
  } else {
    erreur.value = 'Email ou mot de passe incorrect.'
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">

      <h1 class="text-2xl font-bold text-center text-blue-700 mb-6">
        Connexion
      </h1>

      <div v-if="erreur" class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm">
        {{ erreur }}
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
          v-model="email"
          type="email"
          placeholder="awa@mail.com"
          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
        <input
          v-model="password"
          type="password"
          placeholder="••••••••"
          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <button
        @click="handleLogin"
        class="w-full bg-blue-700 text-white py-2 rounded font-semibold hover:bg-blue-800 transition"
      >
        Se connecter
      </button>

      <p class="text-center text-sm text-gray-500 mt-4">
        Pas encore de compte ?
        <a href="/register" class="text-blue-600 hover:underline">S'inscrire</a>
      </p>

    </div>
  </div>
</template>