<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' })
function handleRegister() { form.post('/register') }
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-brand-50 px-4 py-8" style="background-image: radial-gradient(circle, var(--color-brand-200) 1px, transparent 2px); background-size: 24px 24px;">
    <div class="w-full max-w-sm">
      <div class="flex items-center justify-center gap-2 mb-8">
        <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-coral flex items-center justify-center font-display font-bold text-white shadow-lg shadow-brand-500/30">mT</span>
        <span class="font-display font-bold text-xl text-ink">mini-Trello</span>
      </div>

      <div class="bg-white rounded-2xl border border-brand-100 shadow-[0_20px_45px_-12px_rgba(30,27,46,0.15)] p-8">
        <h1 class="font-display text-xl font-bold text-ink mb-1">Créer un compte</h1>
        <p class="text-sm text-ink-soft mb-6">Rejoignez votre équipe sur mini-Trello.</p>

        <div v-if="Object.keys(form.errors).length" class="bg-coral/10 text-coral px-4 py-2.5 rounded-lg mb-4 text-sm font-medium">
          <p v-for="(message, field) in form.errors" :key="field">{{ message }}</p>
        </div>

        <form @submit.prevent="handleRegister" class="flex flex-col gap-4">
          <div>
            <label class="block text-sm font-semibold text-ink mb-1.5">Nom complet</label>
            <input v-model="form.name" type="text" placeholder="Awa Ndiaye"
              class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-colors" />
          </div>

          <div>
            <label class="block text-sm font-semibold text-ink mb-1.5">Email</label>
            <input v-model="form.email" type="email" placeholder="awa@gmail.com"
              class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-colors" />
          </div>

          <div>
            <label class="block text-sm font-semibold text-ink mb-1.5">Mot de passe</label>
            <input v-model="form.password" type="password" placeholder="••••••••"
              class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-colors" />
          </div>

          <div>
            <label class="block text-sm font-semibold text-ink mb-1.5">Confirmer le mot de passe</label>
            <input v-model="form.password_confirmation" type="password" placeholder="••••••••"
              class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-colors" />
          </div>

          <button type="submit" :disabled="form.processing"
            class="font-display font-semibold bg-brand-500 text-white py-2.5 rounded-lg shadow-md hover:shadow-lg hover:bg-brand-600 transition-all mt-2 disabled:opacity-60">
            S'inscrire
          </button>
        </form>

        <p class="text-center text-sm text-ink-soft mt-6">
          Déjà un compte ?
          <a href="/login" class="text-brand-600 font-medium hover:text-brand-700 transition-colors">Se connecter</a>
        </p>
      </div>
    </div>
  </div>
</template>