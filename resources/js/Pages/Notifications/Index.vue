<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ notifications: Array })

const nonLues = computed(() => props.notifications.filter((n) => !n.lu).length)

function marquerLue(notif) {
  if (notif.kind === 'invitation') return
  router.post(`/notifications/${notif.id}/lue`, {}, { preserveScroll: true })
}
function marquerToutesLues() { router.post('/notifications/tout-lu', {}, { preserveScroll: true }) }
function accepter(id) { router.post(`/notifications/${id}/accepter`, {}, { preserveScroll: true }) }
function refuser(id) { router.post(`/notifications/${id}/refuser`, {}, { preserveScroll: true }) }
</script>

<template>
  <AppLayout title="Notifications">
    <div class="flex items-center justify-between mb-6">
      <p v-if="nonLues > 0" class="text-sm font-medium text-coral">{{ nonLues }} non lue{{ nonLues > 1 ? 's' : '' }}</p>
      <p v-else class="text-sm text-ink-soft">Tout est à jour</p>
      <button v-if="nonLues > 0" @click="marquerToutesLues" class="text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
        Tout marquer comme lu
      </button>
    </div>

    <div v-if="notifications.length === 0" class="text-center py-20">
      <p class="font-display text-lg font-semibold text-ink mb-1">Aucune notification</p>
      <p class="text-ink-soft">Vous serez averti ici quand une tâche vous sera assignée.</p>
    </div>

    <div class="flex flex-col gap-2">
      <div
        v-for="notif in notifications"
        :key="notif.id"
        @click="marquerLue(notif)"
        :class="['rounded-xl p-4 transition-colors', notif.lu ? 'bg-white border border-brand-100' : 'bg-brand-50 border border-brand-100', notif.kind !== 'invitation' ? 'cursor-pointer' : '']"
      >
        <div class="flex items-start gap-3">
          <span :class="['w-2 h-2 rounded-full mt-1.5 flex-shrink-0', notif.lu ? 'bg-transparent' : 'bg-brand-500']"></span>
          <div class="flex-1 min-w-0">
            <p :class="['text-sm', notif.lu ? 'text-ink-soft' : 'font-semibold text-ink']">{{ notif.message }}</p>
            <p class="text-xs text-ink-soft mt-1">{{ notif.creeLe }}</p>

            <div v-if="notif.kind === 'invitation' && notif.statut === 'en_attente'" class="flex gap-2 mt-3">
              <button @click.stop="accepter(notif.id)" class="font-display font-semibold text-xs bg-mint text-white px-3 py-1.5 rounded-lg hover:bg-mint/90 transition-colors">
                Accepter
              </button>
              <button @click.stop="refuser(notif.id)" class="font-display font-semibold text-xs bg-white border border-brand-100 text-ink-soft px-3 py-1.5 rounded-lg hover:border-coral hover:text-coral transition-colors">
                Refuser
              </button>
            </div>
            <p v-else-if="notif.kind === 'invitation'" class="text-xs font-medium mt-2" :class="notif.statut === 'acceptee' ? 'text-mint' : 'text-coral'">
              {{ notif.statut === 'acceptee' ? 'Acceptée' : 'Refusée' }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>