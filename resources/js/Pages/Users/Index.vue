<script setup>
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ utilisateurs: Array })

const ROLES = [
  { value: 'membre', label: 'Membre' },
  { value: 'chef_projet', label: 'Chef de projet' },
  { value: 'admin', label: 'Admin' },
]

function changerRole(user, event) {
  const nouveauRole = event.target.value
  if (!confirm(`Changer le rôle de ${user.name} en "${ROLES.find(r => r.value === nouveauRole).label}" ?`)) {
    event.target.value = user.role
    return
  }
  router.patch(`/users/${user.id}/role`, { role: nouveauRole }, { preserveScroll: true })
}
</script>

<template>
  <AppLayout title="Utilisateurs">
    <div class="bg-white rounded-xl border border-brand-100 shadow-[0_2px_10px_rgba(30,27,46,0.06)] overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-brand-50 text-ink-soft text-xs uppercase tracking-wide">
          <tr>
            <th class="text-left px-5 py-3 font-semibold">Nom</th>
            <th class="text-left px-5 py-3 font-semibold">Email</th>
            <th class="text-left px-5 py-3 font-semibold">Rôle</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-brand-50">
          <tr v-for="user in utilisateurs" :key="user.id">
            <td class="px-5 py-3 font-medium text-ink">{{ user.name }}</td>
            <td class="px-5 py-3 text-ink-soft">{{ user.email }}</td>
            <td class="px-5 py-3">
              <select
                :value="user.role"
                @change="changerRole(user, $event)"
                class="border border-brand-100 rounded-lg px-2.5 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500"
              >
                <option v-for="r in ROLES" :key="r.value" :value="r.value">{{ r.label }}</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>