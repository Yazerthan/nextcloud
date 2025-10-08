<!--
@license GNU AGPL version 3 or any later version

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
-->
<template>
  <div class="cm-settings-form-appsort">
    <NcButton
      aria-label="t('side_menu', 'Sort')"
      variant="primary"
      @click="openModal"
    >
      {{ t('side_menu', 'Sort') }}
    </NcButton>

    <NcModal
      v-if="modal"
      size="small"
      class="cm-settings-form-appsort-modal"
      @close="closeModal"
    >
      <div class="modal__content">
        <draggable
          v-model="apps"
          item-key="id"
          @end="update"
        >
          <template #item="{ element }">
            <div class="cm-settings-form-draggable">
              <span class="cm-settings-form-arrow">⇅</span>
              {{ element.name }}
            </div>
          </template>
        </draggable>

        <div class="modal__footer">
          <NcButton
            variant="primary"
            @click="closeModal"
          >
            {{ t('side_menu', 'Close') }}
          </NcButton>
        </div>
      </div>
    </NcModal>
  </div>
</template>

<script setup>
import { NcButton, NcModal } from '@nextcloud/vue'
import { useNavStore } from '../../../store/nav.js'
import { ref, onMounted } from 'vue'
import draggable from 'vuedraggable'

const model = defineModel({ type: Array })

const emit = defineEmits(['update:modelValue'])
const navStore = useNavStore()
const modal = ref(false)
const apps = ref([])

const openModal = () => {
  modal.value = true
}

const closeModal = () => {
  modal.value = false
}

const setApps = (items) => {
  apps.value = []

  model.value.forEach((id) => {
    items.forEach((app) => {
      if (app.id === id) {
        apps.value.push(app)
      }
    })
  })

  items.forEach((app) => {
    if (!apps.value.find((element) => element.id === app.id)) {
      apps.value.push(app)
    }
  })
}

const update = () => {
  const value = []

  apps.value.forEach((app) => {
    value.push(app.id)
  })

  emit('update:modelValue', value)
}

onMounted(async () => {
  const items = await navStore.getCoreApps()

  window.setTimeout(() => {
    setApps(items)
  }, 500)
})
</script>
