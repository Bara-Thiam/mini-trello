<script setup>
import { reactive, computed } from 'vue'

const props = defineProps({
  users: { type: Array, required: true },
  tache: { type: Object, default: null }
})

const emit = defineEmits(['submit', 'cancel'])

const form = reactive({
  titre: props.tache?.titre ?? '',
  description: props.tache?.description ?? '',
  userId: props.tache?.userId ?? '',
  priorite: props.tache?.priorite ?? '',
  echeance: props.tache?.echeance ?? ''
})

const errors = reactive({ titre: '', userId: '', priorite: '', echeance: '' })

function validate() {
  errors.titre = ''; errors.userId = ''; errors.priorite = ''; errors.echeance = ''

  if (!form.titre || form.titre.trim().length < 3) errors.titre = 'Le titre doit contenir au moins 3 caractères.'
  if (!form.userId) errors.userId = 'Veuillez assigner la tâche à quelqu\'un.'
  if (!form.priorite) errors.priorite = 'Veuillez choisir une priorité.'
  if (!form.echeance) {
    errors.echeance = 'Veuillez choisir une échéance.'
  } else {
    const today = new Date(); today.setHours(0, 0, 0, 0)
    if (new Date(form.echeance) < today) errors.echeance = 'L\'échéance ne peut pas être dans le passé.'
  }
  return !errors.titre && !errors.userId && !errors.priorite && !errors.echeance
}

function handleSubmit() {
  if (!validate()) return
  emit('submit', { ...form, userId: Number(form.userId) })
}

const isEdition = computed(() => props.tache !== null)

const PRIORITE_OPTIONS = [
  { value: 'HIGH', label: 'Haute', dot: 'bg-coral' },
  { value: 'MEDIUM', label: 'Moyenne', dot: 'bg-amber' },
  { value: 'LOW', label: 'Basse', dot: 'bg-mint' },
]
</script>

<template>
  <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
    <div>
      <label class="block text-sm font-semibold text-ink mb-1.5">Titre</label>
      <input v-model="form.titre" type="text" placeholder="Titre de la tâche"
        class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-colors" />
      <p v-if="errors.titre" class="text-xs text-coral mt-1">{{ errors.titre }}</p>
    </div>

    <div>
      <label class="block text-sm font-semibold text-ink mb-1.5">Description</label>
      <textarea v-model="form.description" rows="3" placeholder="Description (optionnelle)"
        class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-colors resize-none"></textarea>
    </div>

    <div>
      <label class="block text-sm font-semibold text-ink mb-1.5">Assigné à</label>
      <select v-model="form.userId"
        class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-colors bg-white">
        <option value="" disabled>Sélectionner une personne</option>
        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
      </select>
      <p v-if="errors.userId" class="text-xs text-coral mt-1">{{ errors.userId }}</p>
    </div>

    <div>
      <label class="block text-sm font-semibold text-ink mb-1.5">Priorité</label>
      <div class="flex gap-2">
        <button
          v-for="opt in PRIORITE_OPTIONS"
          :key="opt.value"
          type="button"
          @click="form.priorite = opt.value"
          :class="['flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium border transition-colors', form.priorite === opt.value ? 'border-brand-500 bg-brand-50 text-ink' : 'border-brand-100 text-ink-soft hover:border-brand-200']"
        >
          <span :class="['w-2 h-2 rounded-full', opt.dot]"></span>
          {{ opt.label }}
        </button>
      </div>
      <p v-if="errors.priorite" class="text-xs text-coral mt-1">{{ errors.priorite }}</p>
    </div>

    <div>
      <label class="block text-sm font-semibold text-ink mb-1.5">Échéance</label>
      <input v-model="form.echeance" type="date"
        class="w-full border border-brand-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-colors" />
      <p v-if="errors.echeance" class="text-xs text-coral mt-1">{{ errors.echeance }}</p>
    </div>

    <div class="flex justify-end gap-2 pt-2">
      <button type="button" @click="emit('cancel')" class="px-4 py-2 text-sm font-medium text-ink-soft hover:text-ink transition-colors">
        Annuler
      </button>
      <button type="submit" class="font-display font-semibold px-4 py-2 text-sm rounded-lg bg-brand-500 text-white shadow-sm hover:shadow-md hover:bg-brand-600 transition-all">
        {{ isEdition ? 'Enregistrer' : 'Créer la tâche' }}
      </button>
    </div>
  </form>
</template>