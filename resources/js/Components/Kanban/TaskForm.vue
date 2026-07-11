<script setup>
import { reactive, computed } from 'vue'

const props = defineProps({
  users: {
    type: Array,
    required: true
  },
  tache: {
    type: Object,
    default: null // null = mode création, objet = mode édition
  }
})

const emit = defineEmits(['submit', 'cancel'])

const form = reactive({
  titre: props.tache?.titre ?? '',
  description: props.tache?.description ?? '',
  userId: props.tache?.userId ?? '',
  priorite: props.tache?.priorite ?? '',
  echeance: props.tache?.echeance ?? ''
})

const errors = reactive({
  titre: '',
  userId: '',
  priorite: '',
  echeance: ''
})

function validate() {
  errors.titre = ''
  errors.userId = ''
  errors.priorite = ''
  errors.echeance = ''

  if (!form.titre || form.titre.trim().length < 3) {
    errors.titre = 'Le titre doit contenir au moins 3 caractères.'
  }
  if (!form.userId) {
    errors.userId = 'Veuillez assigner la tâche à quelqu\'un.'
  }
  if (!form.priorite) {
    errors.priorite = 'Veuillez choisir une priorité.'
  }
  if (!form.echeance) {
    errors.echeance = 'Veuillez choisir une échéance.'
  } else {
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    const echeanceDate = new Date(form.echeance)
    if (echeanceDate < today) {
      errors.echeance = 'L\'échéance ne peut pas être dans le passé.'
    }
  }

  return !errors.titre && !errors.userId && !errors.priorite && !errors.echeance
}

function handleSubmit() {
  if (!validate()) return
  emit('submit', {
    ...form,
    userId: Number(form.userId)
  })
}

const isEdition = computed(() => props.tache !== null)
</script>

<template>
  <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
      <input
        v-model="form.titre"
        type="text"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
        placeholder="Titre de la tâche"
      />
      <p v-if="errors.titre" class="text-xs text-red-600 mt-1">{{ errors.titre }}</p>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
      <textarea
        v-model="form.description"
        rows="3"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
        placeholder="Description (optionnelle)"
      ></textarea>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Assigné à</label>
      <select
        v-model="form.userId"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
      >
        <option value="" disabled>Sélectionner une personne</option>
        <option v-for="user in users" :key="user.id" :value="user.id">
          {{ user.name }}
        </option>
      </select>
      <p v-if="errors.userId" class="text-xs text-red-600 mt-1">{{ errors.userId }}</p>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
      <select
        v-model="form.priorite"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
      >
        <option value="" disabled>Sélectionner une priorité</option>
        <option value="HIGH">Haute</option>
        <option value="MEDIUM">Moyenne</option>
        <option value="LOW">Basse</option>
      </select>
      <p v-if="errors.priorite" class="text-xs text-red-600 mt-1">{{ errors.priorite }}</p>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Échéance</label>
      <input
        v-model="form.echeance"
        type="date"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
      />
      <p v-if="errors.echeance" class="text-xs text-red-600 mt-1">{{ errors.echeance }}</p>
    </div>

    <div class="flex justify-end gap-2 pt-2">
      <button
        type="button"
        @click="emit('cancel')"
        class="px-4 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50"
      >
        Annuler
      </button>
      <button
        type="submit"
        class="px-4 py-2 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-700"
      >
        {{ isEdition ? 'Enregistrer' : 'Créer la tâche' }}
      </button>
    </div>
  </form>
</template>