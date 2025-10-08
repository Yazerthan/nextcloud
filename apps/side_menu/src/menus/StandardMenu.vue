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
  <nav
    class="cm-standardmenu app-menu show"
    :aria-label="t('core', 'Applications menu')"
  >
    <ul
      v-if="ready"
      class="app-menu-main"
      :class="{
        'app-menu-main__hidden-label': hiddenLabels === 1,
        'app-menu-main__show-hovered': hiddenLabels === 2,
      }"
    >
      <li
        v-for="app in mainAppList"
        :key="app.id"
        :data-app-id="app.id"
        class="app-menu-entry"
        :class="{
          'app-menu-entry__active': app.id === activeApp,
          'app-menu-entry__hidden-label': hiddenLabels === 1,
          'app-menu-main__show-hovered': hiddenLabels === 2,
        }"
        :style="makeStyle(app)"
      >
        <a
          :href="app.href"
          :class="{ 'has-unread': app.unread > 0 }"
          :aria-label="app.name"
          :target="targetBlankApps.indexOf(app.id) !== -1 ? '_blank' : undefined"
          :aria-current="app.id === activeApp ? 'page' : false"
        >
          <img
            :src="app.icon"
            :alt="app.name"
          />
          <div class="app-menu-entry--label">
            {{ app.name }}
            <span
              v-if="app.unread > 0"
              class="hidden-visually unread-counter"
              >{{ app.unread }}</span
            >
          </div>
        </a>
      </li>
    </ul>
    <NcActions
      class="cm-standardmenu-app-menu-more app-menu-more"
      :aria-label="t('core', 'More apps')"
    >
      <NcActionLink
        v-for="app in popoverAppList"
        :key="app.id"
        :aria-label="app.name"
        :aria-current="app.id === activeApp ? 'page' : false"
        :href="app.href"
        :style="makeStyle(app)"
        class="cm-standardmenu-app-menu-popover-entry app-menu-popover-entry"
      >
        <template #icon>
          <div
            class="app-icon"
            :class="{ 'has-unread': app.unread > 0 }"
          >
            <img
              :src="app.icon"
              :alt="app.name"
            />
          </div>
        </template>
        {{ app.name }}
        <span
          v-if="app.unread > 0"
          class="hidden-visually unread-counter"
          >{{ app.unread }}</span
        >
      </NcActionLink>
    </NcActions>
  </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useConfigStore } from '../store/config.js'
import { useNavStore } from '../store/nav.js'
import { NcActions, NcActionLink } from '@nextcloud/vue'
import { getActiveAppId } from '../lib/app.js'

const navStore = useNavStore()
const configStore = useConfigStore()
const ready = ref(false)
const appList = ref([])
const targetBlankApps = ref(null)
const hiddenLabels = ref(false)
const topMenuApps = ref([])
const appsOrder = ref([])
const mainAppList = ref([])
const popoverAppList = ref([])
const activeApp = ref(null)
let resizeTimeout = null

const setApps = (value) => {
  value.forEach((app) => {
    Array.from(topMenuApps.value).forEach((id) => {
      if (app.id === id) {
        app.order = appsOrder.value.findIndex((element) => element === app.id) || null
        appList.value.push(app)
      }
    })
  })

  computeLists()
}

const appLimit = () => {
  const headerStart = document.querySelector('#header .header-start')
  const headerEnd = document.querySelector('#header .header-end')
  const body = document.querySelector('body')

  let size = (headerEnd ? headerEnd.offsetWidth : 0) + 70

  if (headerStart) {
    Array.from(headerStart.children).forEach((child) => {
      if (child.id !== 'app-menu-container') {
        size += child.offsetWidth
      }
    })
  }

  return Math.max(0, Math.floor((body.offsetWidth - size) / 70))
}

const makeStyle = (app) => {
  if (app.order !== null) {
    return { order: app.order }
  }

  return {}
}

const computeLists = () => {
  mainAppList.value = appList.value.slice(0, appLimit())
  popoverAppList.value = appList.value.slice(appLimit()).sort((a, b) => a.order - b.order)
}

const reComputeLists = (delay) => {
    window.clearTimeout(resizeTimeout)
    resizeTimeout = window.setTimeout(computeLists, delay || 100)
}

onMounted(async () => {
  const config = await configStore.getConfig()

  targetBlankApps.value = config['target-blank-apps']
  hiddenLabels.value = config['top-menu-mouse-over-hidden-label']
  topMenuApps.value = config['top-menu-apps']
  appsOrder.value = config['apps-order']
  activeApp.value = getActiveAppId()
  ready.value = true

  setApps(await navStore.getCoreApps())

  window.addEventListener('resize', reComputeLists)
})
</script>

<script>
export default {
  compatConfig: {
    GLOBAL_MOUNT_CONTAINER: false,
  },
}
</script>
