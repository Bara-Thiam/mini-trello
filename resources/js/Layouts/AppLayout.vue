<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import { onMounted, onUnmounted} from 'vue'

defineProps({
  title: { type: String, default: '' }
})

const page = usePage()
const currentPath = computed(() => page.url)
function isActive(path) { return currentPath.value.startsWith(path) }
const userInitial = computed(() => (page.props.auth?.user?.name ?? '?').charAt(0).toUpperCase())

const navItems = [
  { href: '/projects', label: 'Projets', match: '/projects', icon: 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-19.5 0v6a2.25 2.25 0 002.25 2.25h15a2.25 2.25 0 002.25-2.25v-6m-19.5 0h19.5M4.5 9.75V6a2.25 2.25 0 012.25-2.25h3.879a1.5 1.5 0 011.06.44l1.72 1.72a1.5 1.5 0 001.06.44H19.5A2.25 2.25 0 0121.75 9.75' },
  { href: '/tasks/mine', label: 'Mes tâches', match: '/tasks', icon: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
  { href: '/dashboard', label: 'Dashboard', match: '/dashboard', icon: 'M3 13.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z' },
]

const ROLE_LABELS = { admin: 'Admin', chef_projet: 'Chef de projet', membre: 'Membre' }
const roleLabel = computed(() => ROLE_LABELS[page.props.auth?.user?.role] ?? '')

const isSidebarOpen = ref(false)
function closeSidebar() { isSidebarOpen.value = false }
// Ferme automatiquement la sidebar mobile après une navigation
watch(currentPath, () => { isSidebarOpen.value = false })

const localUnread = ref(page.props.unreadNotificationsCount ?? 0)

onMounted(() => {
  const userId = page.props.auth?.user?.id
  if (userId && window.Echo) {
    window.Echo.private(`App.Models.User.${userId}`).notification(() => {
      localUnread.value++
    })
  }
})

onUnmounted(() => {
  const userId = page.props.auth?.user?.id
  if (userId && window.Echo) {
    window.Echo.leave(`App.Models.User.${userId}`)
  }
})
</script>

<template>
  <div class="min-h-screen bg-brand-50"
    style="background-image: radial-gradient(circle, var(--color-brand-100) 1.5px, transparent 1.5px); background-size: 22px 22px;">
    <!-- Barre mobile : logo + hamburger, visible uniquement < lg -->
    <div class="lg:hidden h-16 bg-ink flex items-center justify-between px-4 sticky top-0 z-30 shadow-md">
      <Link href="/projects" class="flex items-center gap-2">
        <span
          class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-coral flex items-center justify-center font-display font-bold text-white text-sm">mT</span>
        <span class="font-display font-bold text-white">mini-Trello</span>
      </Link>
      <button @click="isSidebarOpen = true"
        class="text-white p-2 -mr-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400 rounded-lg"
        aria-label="Ouvrir le menu">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
      </button>
    </div>

    <!-- Fond sombre derrière la sidebar mobile ouverte -->
    <div v-if="isSidebarOpen" @click="closeSidebar" class="fixed inset-0 bg-ink/60 z-40 lg:hidden"></div>

    <!-- Sidebar : hors champ sur mobile par défaut, toujours visible dès lg -->
    <aside :class="[
      'w-60 bg-ink flex flex-col fixed inset-y-0 left-0 z-50 shadow-[12px_0_40px_-8px_rgba(0,0,0,0.5)] transition-transform duration-200',
      isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
      'lg:translate-x-0'
    ]">
      <div class="h-16 flex items-center justify-between gap-2 px-5 border-b border-white/10">
        <Link href="/projects" class="flex items-center gap-2">
          <span
            class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-coral flex items-center justify-center font-display font-bold text-white text-sm flex-shrink-0 shadow-md shadow-brand-500/40">mT</span>
          <span class="font-display font-bold text-white tracking-tight">mini-Trello</span>
        </Link>
        <button @click="closeSidebar"
          class="lg:hidden text-white/60 hover:text-white p-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400 rounded"
          aria-label="Fermer le menu">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <nav class="flex-1 px-3 py-4 flex flex-col gap-1">
        <Link v-for="item in navItems" :key="item.href" :href="item.href"
          :class="['relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400', isActive(item.match) ? 'bg-gradient-to-r from-brand-500/25 to-transparent text-white shadow-inner' : 'text-white/60 hover:text-white hover:bg-white/5']">
          <span v-if="isActive(item.match)"
            class="absolute left-0 top-1.5 bottom-1.5 w-1 rounded-full bg-brand-400"></span>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-5 h-5 flex-shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
          </svg>
          {{ item.label }}
        </Link>
      </nav>

      <div class="p-3 border-t border-white/10">
        <Link href="/notifications"
          :class="['relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors mb-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400', isActive('/notifications') ? 'bg-white/10 text-white' : 'text-white/60 hover:text-white hover:bg-white/5']">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-5 h-5 flex-shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
          </svg>
          Notifications
          <span v-if="localUnread  > 0"
            class="ml-auto min-w-[18px] h-[18px] px-1 rounded-full bg-coral text-white text-[10px] font-bold flex items-center justify-center">
            {{ localUnread > 9 ? '9+' : localUnread }}
          </span>
        </Link>

        <div class="flex items-center gap-2.5 px-3 py-2">
          <span
            class="w-8 h-8 rounded-full bg-gradient-to-br from-mint to-brand-500 flex items-center justify-center text-xs font-bold text-white flex-shrink-0 ring-2 ring-white/10 shadow-sm">{{
              userInitial }}</span>
          <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-white truncate">{{ $page.props.auth?.user?.name ?? 'Invité' }}</p>
            <p class="text-[11px] text-white/40">{{ roleLabel }}</p>
          </div>
        </div>

        <Link href="/logout" method="post" as="button"
          class="w-full flex items-center gap-3 px-3 py-2 mt-1 rounded-lg text-sm font-medium text-coral/70 hover:text-coral hover:bg-coral/10 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coral">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-5 h-5 flex-shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
          </svg>
          Déconnexion
        </Link>
      </div>
    </aside>

    <!-- Contenu : pas de marge sur mobile (sidebar hors champ), lg:ml-60 dès que la sidebar est fixe -->
    <div class="lg:ml-60 min-w-0">
      <header v-if="title" class="px-4 lg:px-8 pt-8">
        <h1 class="font-display text-2xl font-bold text-ink">{{ title }}</h1>
      </header>
      <main class="px-4 lg:px-8 py-8">
        <slot />
      </main>
    </div>
  </div>
</template>