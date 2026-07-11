<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
    notifications: Array,
})

const nonLues = props.notifications.filter((n) => !n.lu).length

function marquerLue(id) {
    router.post(`/notifications/${id}/lue`, {}, { preserveScroll: true })
}

function marquerToutesLues() {
    router.post('/notifications/tout-lu', {}, { preserveScroll: true })
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <div class="max-w-3xl mx-auto px-6 py-8">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px">
                <h2 class="text-2xl font-bold text-gray-800">
                    Notifications
                    <span v-if="nonLues > 0" class="text-sm font-normal text-red-500 ml-2">{{ nonLues }} non lue(s)</span>
                </h2>
                <button v-if="nonLues > 0" @click="marquerToutesLues" class="text-sm text-blue-600 hover:underline">
                    Tout marquer comme lu
                </button>
            </div>

            <div v-if="notifications.length === 0" class="text-center text-gray-400 mt-12">
                Aucune notification.
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px">
                <div v-for="notif in notifications" :key="notif.id" @click="marquerLue(notif.id)"
                    :style="{
                        background: notif.lu ? '#f9fafb' : '#eff6ff',
                        borderLeft: notif.lu ? '4px solid #e5e7eb' : '4px solid #3b82f6',
                        cursor: 'pointer',
                    }"
                    class="rounded-lg shadow p-4">
                    <div style="display: flex; justify-content: space-between; align-items: center">
                        <div>
                            <p :class="notif.lu ? 'text-sm text-gray-500' : 'text-sm font-semibold text-gray-800'">
                                {{ notif.message }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">{{ notif.creeLe }}</p>
                        </div>
                        <span v-if="!notif.lu" style="width: 8px; height: 8px; background: #3b82f6; border-radius: 9999px; flex-shrink: 0; margin-left: 12px"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>