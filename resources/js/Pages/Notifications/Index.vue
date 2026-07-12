<script setup>
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ notifications: Array })

const nonLues = props.notifications.filter((n) => !n.lu).length

function marquerLue(id) { router.post(`/notifications/${id}/lue`, {}, { preserveScroll: true }) }
function marquerToutesLues() { router.post('/notifications/tout-lu', {}, { preserveScroll: true }) }
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
                @click="marquerLue(notif.id)"
                :class="['flex items-start gap-3 rounded-xl p-4 cursor-pointer transition-colors', notif.lu ? 'bg-white border border-brand-100' : 'bg-brand-50 border border-brand-100']"
            >
                <span :class="['w-2 h-2 rounded-full mt-1.5 flex-shrink-0', notif.lu ? 'bg-transparent' : 'bg-brand-500']"></span>
                <div class="flex-1 min-w-0">
                    <p :class="['text-sm', notif.lu ? 'text-ink-soft' : 'font-semibold text-ink']">{{ notif.message }}</p>
                    <p class="text-xs text-ink-soft mt-1">{{ notif.creeLe }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>