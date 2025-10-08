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

import { defineStore } from 'pinia'
import axios from '@nextcloud/axios'
import { generateUrl, generateOcsUrl } from '@nextcloud/router'

export const useNavStore = defineStore('nav', () => {
  let categories = null
  let apps = null
  let coreApps = null

  async function getApps() {
    if (apps === null) {
      apps = []
      const cats = await getCategories()

      cats.forEach((category) => {
        Object.values(category.apps).forEach((app) => {
          apps.push(app)
        })
      })
    }

    return apps
  }

  async function getCoreApps() {
    if (coreApps == null) {
      coreApps = await await axios.get(generateUrl('/apps/side_menu/core/apps')).then((response) => response.data.items)
    }

    return coreApps
  }

  async function getCategories() {
    if (categories === null) {
      categories = await axios.get(generateUrl('/apps/side_menu/nav/items')).then((response) => response.data.items)
    }

    return categories
  }

  return {
    getApps,
    getCoreApps,
    getCategories,
  }
})
