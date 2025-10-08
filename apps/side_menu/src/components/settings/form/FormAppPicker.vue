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
  <div class="cm-settings-form-apppicker">
    <NcButton
      aria-label="t('side_menu', 'Select apps')"
      variant="primary"
      @click="openModal"
    >
      {{ t('side_menu', 'Select apps') }} ({{ model.length }})
    </NcButton>

    <NcModal
      v-if="modal"
      size="small"
      class="cm-settings-form-apppicker-modal"
      @close="closeModal"
    >
      <div class="modal__content">
        <NcCheckboxRadioSwitch
          v-for="(item, key) in apps"
          :key="key"
          v-model="model"
          name="value"
          :value="item.id"
        >
          <img
            :src="item.icon"
            :alt="item.name"
          />
          {{ item.name }}
        </NcCheckboxRadioSwitch>

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
import { NcButton, NcModal, NcCheckboxRadioSwitch } from '@nextcloud/vue'
import { useNavStore } from '../../../store/nav.js'
import { ref, onMounted } from 'vue'

const model = defineModel({ type: Array })
const navStore = useNavStore()
const modal = ref(false)
const apps = ref([])

const openModal = () => {
  modal.value = true
}

const closeModal = () => {
  modal.value = false
}

onMounted(async () => {
  apps.value = await navStore.getCoreApps()
})
</script>
