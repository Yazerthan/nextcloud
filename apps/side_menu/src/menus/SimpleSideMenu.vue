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
    :class="{ open: open }"
  >
    <div
      v-if="settings || displayLogo || open || !openerHover"
      class="cm-header"
    >
      <SettingsButton
        v-if="settings && open"
        :href="settings.href"
        :label="settings.name"
        :avatar="settings.avatar"
      />
      <AppSearch
        v-if="open"
        v-model="search"
      />
      <OpenerButton
        v-if="!openerHover || isTouchDevice"
        @click="$emit('toggle')"
      />
      <MenuLogo
        v-if="displayLogo"
        class="cm-logo"
        :classes="{ avatardiv: false }"
        :image="useAvatarAsLogo ? avatar : logo"
        :link="logoLink"
      />
    </div>

    <ul
      class="cm-apps"
      :class="{ 'side-menu-apps-list--with-logo': displayLogo }"
    >
      <template
        v-for="(app, key) in apps"
        :key="key"
      >
        <SideMenuApp
          v-if="isAppMatchingSearch(app, search)"
          class="cm-app"
          :classes="{ active: app.id === activeApp }"
          :icon="app.icon"
          :label="app.name"
          :href="app.href"
          :target="targetBlankApps.indexOf(app.id) !== -1 ? '_blank' : undefined"
        />
      </template>
    </ul>
  </div>
</template>

<script setup>
import { ref, useTemplateRef, onMounted, watch } from 'vue'
import { useConfigStore } from '../store/config.js'
import { useNavStore } from '../store/nav.js'
import { focusActiveApp } from '../lib/menu.js'
import { isAppMatchingSearch } from '../lib/search.js'
import { getActiveAppId } from '../lib/app.js'

import OpenerButton from '../components/OpenerButton'
import SettingsButton from '../components/SettingsButton'
import SideMenuApp from '../components/SideMenuApp'
import AppSearch from '../components/AppSearch'
import MenuLogo from '../components/MenuLogo'

const { open } = defineProps({
  open: {
    type: Boolean,
    required: true,
  },
})
const emit = defineEmits(['close', 'open', 'toggle'])

const navStore = useNavStore()
const configStore = useConfigStore()
const targetBlankApps = ref(null)
const forceLightIcon = ref(null)
const activeApp = ref(null)
const avatar = ref(null)
const logo = ref(null)
const logoLink = ref(null)
const settings = ref(null)
const openerHover = ref(false)
const alwaysDisplayed = ref(false)
const displayLogo = ref(false)
const useAvatarAsLogo = ref(false)
const search = ref('')
const apps = ref([])
const menu = useTemplateRef('menu')
const isTouchDevice = window.matchMedia('(pointer: coarse)').matches

watch(apps, (val) => {
  document.querySelector('html').classList.toggle('cm-always-displayed', alwaysDisplayed.value && val.length)
})

watch(
  () => open,
  (val) => {
    if (val) {
      focusActiveApp(menu.value)
    }
  },
)

function getFiltredAndSortedApps(items, order, topMenuApps, topSideMenuApps) {
  const data = []

  items.forEach((item) => {
    if (topMenuApps.includes(item.id) && !topSideMenuApps.includes(item.id)) {
      return
    }

    item.order = items.length + 1

    order.forEach((id, key) => {
      if (id === item.id) {
        item.order = key + 1
      }
    })

    data.push(item)
  })

  return data.sort((a, b) => {
    return a.order < b.order ? -1 : 1
  })
}

onMounted(async () => {
  const config = await configStore.getConfig()

  alwaysDisplayed.value = config['always-displayed']
  targetBlankApps.value = config['target-blank-apps']
  forceLightIcon.value = config['force-light-icon']

  avatar.value = config['avatar']
  logo.value = config['logo']
  useAvatarAsLogo.value = config['use-avatar']
  displayLogo.value = config['display-logo'] && !alwaysDisplayed.value && ((!useAvatarAsLogo.value && logo.value) || (useAvatarAsLogo.value && avatar.value))

  logoLink.value = config['logo-link']
  settings.value = config['settings']
  openerHover.value = config['opener-hover'] && !isTouchDevice
  activeApp.value = getActiveAppId()

  apps.value = getFiltredAndSortedApps(await navStore.getApps(), config['apps-order'], config['top-menu-apps'], config['top-side-menu-apps'])

  if (openerHover.value) {
    menu.value.addEventListener('mouseleave', () => emit('close'))
  }

  if (alwaysDisplayed.value && openerHover.value) {
    menu.value.addEventListener('mouseenter', () => emit('open'))
    menu.value.addEventListener('mouseleave', () => emit('close'))
  }
})
</script>
