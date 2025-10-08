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
import { generateUrl } from '@nextcloud/router'

export const useConfigStore = defineStore('config', () => {
  let config = null
  let appConfig = null
  let userConfig = null

  async function getConfig() {
    if (config === null) {
      config = await axios.get(generateUrl('/apps/side_menu/js/config')).then((response) => response.data)
    }

    return config
  }

  async function getAppConfig() {
    if (appConfig === null) {
      appConfig = await axios.get(generateUrl('/apps/side_menu/admin/config')).then((response) => response.data)
    }

    return appConfig
  }

  async function getUserConfig() {
    if (userConfig === null) {
      userConfig = await axios.get(generateUrl('/apps/side_menu/user/config')).then((response) => response.data)
    }

    return userConfig
  }

  return {
    getConfig,
    getAppConfig,
    getUserConfig,
  }
})
