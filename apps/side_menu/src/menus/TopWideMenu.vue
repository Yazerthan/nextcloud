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
  <div
    ref="menu"
    class="cm--topwidemenu"
    :class="{ open: open }"
  >
    <div class="cm-header">
      <CloserButton
        v-if="!openerHover"
        @click="$emit('close')"
      />
      <SettingsButton
        v-if="settings"
        :href="settings.href"
        :label="settings.name"
        :avatar="settings.avatar"
      />
      <AppSearch v-model="search" />
      <OpenerButton
        v-if="!openerHover"
        @click="$emit('close')"
      />
    </div>

    <div class="cm-categories-wrapper">
      <div class="cm-categories">
        <template
          v-for="(category, key) in items"
          :key="key"
        >
          <div
            v-if="containsAppsMatchingSearch(category.apps, search)"
            class="cm-category"
          >
            <h2
              v-if="category.name != ''"
              class="cm-category-title"
            >
              {{ category.name }}
            </h2>

            <ul class="cm-apps">
              <template
                v-for="(app, appId) in category.apps"
                :key="appId"
              >
                <SideMenuBigApp
                  v-if="isAppMatchingSearch(app, search)"
                  class="cm-app"
                  :classes="{ active: activeApp === appId }"
                  :icon="app.icon"
                  :label="app.name"
                  :href="app.href"
                  :target="targetBlankApps.indexOf(appId) !== -1 ? '_blank' : undefined"
                />
              </template>
            </ul>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, useTemplateRef, onMounted, watch } from 'vue'
import { useNavStore } from '../store/nav.js'
import { useConfigStore } from '../store/config.js'
import { getActiveAppId } from '../lib/app.js'
import { focusActiveApp } from '../lib/menu.js'
import { containsAppsMatchingSearch, isAppMatchingSearch } from '../lib/search.js'

import OpenerButton from '../components/OpenerButton'
import CloserButton from '../components/CloserButton'
import SettingsButton from '../components/SettingsButton'
import AppSearch from '../components/AppSearch'
import SideMenuBigApp from '../components/SideMenuBigApp'

const emit = defineEmits(['close'])
const { open } = defineProps({
  open: {
    type: Boolean,
    required: true,
  },
})

const configStore = useConfigStore()
const navStore = useNavStore()
const items = ref([])
const activeApp = ref(null)
const targetBlankApps = ref([])
const settings = ref(null)
const search = ref('')
const openerHover = ref(false)
const menu = useTemplateRef('menu')
const isTouchDevice = window.matchMedia('(pointer: coarse)').matches

watch(
  () => open,
  (val) => {
    if (val) {
      focusActiveApp(menu.value)
    }
  },
)

onMounted(async () => {
  const config = await configStore.getConfig()

  targetBlankApps.value = config['target-blank-apps']
  settings.value = config['settings']
  openerHover.value = config['opener-hover'] && !isTouchDevice

  items.value = await navStore.getCategories()
  activeApp.value = getActiveAppId()

  if (openerHover.value) {
    menu.value.addEventListener('mouseleave', () => emit('close'))
  }
})
</script>
