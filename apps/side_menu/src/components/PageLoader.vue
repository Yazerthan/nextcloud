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
  <div class="cm-loader">
    <div
      class="cm-loader-bar"
      :style="createStyle(width)"
    ></div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const width = ref(0)
const createStyle = (size) => {
  return {
    width: `${size}%`,
  }
}
let interval = null

onMounted(() => {
  window.addEventListener('beforeunload', () => {
    interval = setInterval(() => {
      width.value = Math.min(width.value + 0.2, 100)

      if (width.value === 100) {
        clearInterval(interval)
        window.setTimeout(() => (width.value = 0), 2000)
      }
    }, 25)
  })
})
</script>
