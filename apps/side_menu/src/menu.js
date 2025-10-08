/**
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

import './scss/menu.scss'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createElement, waitContainer } from './lib/dom.js'

import StandardMenu from './menus/StandardMenu'
import MenuContainer from './menus/MenuContainer'

const pinia = createPinia()
const body = document.querySelector('body')
const container = createElement('div', {
  id: 'side-menu-container',
})

body.appendChild(container)

const app = createApp(MenuContainer)
app.use(pinia)
app.mixin({ methods: { t, n } })
app.mount(container)

waitContainer('#header .app-menu').then((container) => {
  const menu = createElement('div', {
    id: 'app-menu-container',
  })

  container.parentNode.insertBefore(menu, container.nextSibling)
  container.remove()

  const app = createApp(StandardMenu)
  app.use(pinia)
  app.mixin({ methods: { t, n } })
  app.mount(menu)
})
