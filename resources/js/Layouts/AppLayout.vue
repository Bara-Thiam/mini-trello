<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  title: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-white border-b border-gray-200">
      <div class="max-w-6xl mx-auto px-4 flex items-center justify-between h-14">
        <div class="flex items-center gap-6">
          <Link href="/projects" class="font-semibold text-gray-800">
            mini-Trello
          </Link>
          <Link href="/projects" class="text-sm text-gray-600 hover:text-gray-900">
            Projets
          </Link>
          <Link href="/tasks/mine" class="text-sm text-gray-600 hover:text-gray-900">
            Mes tâches
          </Link>
          <Link href="/dashboard" class="text-sm text-gray-600 hover:text-gray-900">
            Dashboard
          </Link>
        </div>

        <div class="flex items-center gap-4">
          <!-- Placeholder notifications, à brancher par Membre 3 -->
          <button class="text-sm text-gray-500 hover:text-gray-800" aria-label="Notifications">
            🔔
          </button>
          <span class="text-sm text-gray-700">
            {{ $page.props.auth?.user?.name ?? 'Invité' }}
          </span>
        </div>
      </div>
    </nav>

    <header v-if="title" class="max-w-6xl mx-auto px-4 pt-6">
      <h1 class="text-xl font-semibold text-gray-800">{{ title }}</h1>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-6">
      <slot />
    </main>
  </div>
</template>