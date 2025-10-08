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
  <template v-if="display && hasApps">
    <PageLoader v-if="hasPageLoader" />
    <TopWideMenu
      v-if="display === 'big-menu'"
      id="side-menu"
      :open="isOpen"
      class="cm"
      @close="closeMenu"
    />
    <SideMenuWithCategories
      v-else-if="display === 'side-with-categories'"
      id="side-menu"
      :open="isOpen"
      class="cm"
      @close="closeMenu"
    />
    <SimpleSideMenu
      v-else-if="display === 'simple-side-menu'"
      id="side-menu"
      :open="isOpen"
      class="cm"
      @close="closeMenu"
      @open="openMenu"
      @toggle="toggleMenu(!isOpen)"
    />
  </template>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useConfigStore } from '../store/config.js'
import { useNavStore } from '../store/nav.js'
import { createElement } from '../lib/dom.js'
import { translate as t } from '@nextcloud/l10n'

import SimpleSideMenu from './SimpleSideMenu'
import TopWideMenu from './TopWideMenu'
import SideMenuWithCategories from './SideMenuWithCategories'
import PageLoader from '../components/PageLoader'

const config = ref(null)
const configStore = useConfigStore()
const navStore = useNavStore()
const display = ref(null)
const hasPageLoader = ref(false)
const isOpen = ref(false)
const hasApps = ref(false)
const openerHover = ref(false)

const toggleMenu = (value) => {
  isOpen.value = value
}

const openMenu = () => {
  toggleMenu(true)
}

const closeMenu = () => {
  toggleMenu(false)
}

const createOpener = () => {
  const nextcloud = document.querySelector('#nextcloud')
  const logo = document.querySelector('.header-left .logo, .header-start .logo')

  if (!nextcloud || !logo) {
    return
  }

  if (logo.parentNode !== nextcloud) {
    nextcloud.appendChild(logo)
  }

  const opener = createElement('button', {
    class: 'cm-opener',
    'arial-label': t('side_menu', 'Toggle the menu'),
    html: `<span>${t('side_menu', 'Toggle the menu')}</span>`,
  })

  if (config.value['opener-position'] === 'before') {
    nextcloud.parentNode.insertBefore(opener, nextcloud)
  } else {
    nextcloud.parentNode.insertBefore(opener, nextcloud.nextSibling)
  }

  opener.addEventListener('click', () => toggleMenu(true), true)

  if (openerHover.value) {
    opener.addEventListener('mouseenter', () => toggleMenu(true), true)
  }
}

onMounted(async () => {
  hasApps.value = (await navStore.getApps()).length > 0

  config.value = await configStore.getConfig()

  if (config.value['big-menu']) {
    display.value = 'big-menu'
  } else if (config.value['side-with-categories']) {
    display.value = 'side-with-categories'
  } else {
    display.value = 'simple-side-menu'
  }

  hasPageLoader.value = config.value['loader-enabled']
  openerHover.value = config.value['opener-hover']

  if (hasApps.value) {
    createOpener()
  }

  window.document.addEventListener('keydown', (e) => {
    const key = e.key || e.keyCode

    if ((key === 'o' || key === 79) && e.ctrlKey === true) {
      e.preventDefault()
      toggleMenu(!isOpen.value)
    }
  })
})
</script>
