<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({ email: '', password: '' })
function handleLogin() { form.post('/login') }
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-brand-50 px-4" style="background-image: radial-gradient(circle, var(--color-brand-100) 1.5px, transparent 1.5px); background-size: 22px 22px;">
    <div class="w-full max-w-sm">
      <div class="flex items-center justify-center gap-2 mb-8">
        <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-coral flex items-center justify-center font-display font-bold text-white shadow-lg shadow-brand-500/30">mT</span>
        <span class="font-display font-bold text-xl text-ink">mini-Trello</span>
      </div>

      <div class="bg-white rounded-2xl border border-brand-100 shadow-[0_20px_45px_-12px_rgba(30,27,46,0.15)] p-8">
        <h1 class="font-display text-xl font-bold text-ink mb-1">Connexion</h1>
        <p class="text-sm text-ink-soft mb-6">Content de vous revoir.</p>

        <div v-if="form.errors.email" class="bg-coral/10 text-coral px-4 py-2.5 rounded-lg mb-4 text-sm font-medium">
          {{ form.errors.email }}
        </div>

        <form @submit.prevent="handleLogin" class="flex flex-col gap-4">
          <div>
            <label class="block text-sm font-semibold text-ink mb-1.5">Email</label>
            <input v-model="form.email" type="email" placeholder="bara@mail.com"
              class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-colors" />
          </div>

          <div>
            <label class="block text-sm font-semibold text-ink mb-1.5">Mot de passe</label>
            <input v-model="form.password" type="password" placeholder="••••••••"
              class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-colors" />
          </div>

          <button type="submit" :disabled="form.processing"
            class="font-display font-semibold bg-brand-500 text-white py-2.5 rounded-lg shadow-md hover:shadow-lg hover:bg-brand-600 transition-all mt-2 disabled:opacity-60">
            Se connecter
          </button>
        </form>

        <p class="text-center text-sm text-ink-soft mt-6">
          Pas encore de compte ?
          <a href="/register" class="text-brand-600 font-medium hover:text-brand-700 transition-colors">S'inscrire</a>
        </p>
      </div>
    </div>
  </div>
</template>