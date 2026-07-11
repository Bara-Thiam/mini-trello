<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function handleRegister() {
    form.post('/register')
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold text-center text-blue-700 mb-6">Créer un compte</h1>

            <div v-if="Object.keys(form.errors).length" class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                <p v-for="(message, field) in form.errors" :key="field">{{ message }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                <input v-model="form.name" type="text" placeholder="Awa Ndiaye"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input v-model="form.email" type="email" placeholder="awa@mail.com"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                <input v-model="form.password" type="password" placeholder="••••••••"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                <input v-model="form.password_confirmation" type="password" placeholder="••••••••"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <button @click="handleRegister" :disabled="form.processing"
                class="w-full bg-blue-700 text-white py-2 rounded font-semibold hover:bg-blue-800 transition">
                S'inscrire
            </button>

            <p class="text-center text-sm text-gray-500 mt-4">
                Déjà un compte ? <a href="/login" class="text-blue-600 hover:underline">Se connecter</a>
            </p>
        </div>
    </div>
</template>