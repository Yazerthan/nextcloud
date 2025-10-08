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
  <li :class="classes">
    <a
      :href="href"
      :target="target"
      :title="label"
    >
      <img
        class="cm-app-icon"
        :src="icon"
        :alt="label"
      />
      <span class="cm-app-text">{{ label }}</span>
    </a>
  </li>
</template>

<script setup>
const { label, icon, href, classes, target } = defineProps({
  label: {
    type: String,
    required: true,
  },
  icon: {
    type: String,
    required: true,
  },
  href: {
    type: String,
    required: true,
  },
  classes: {
    type: Object,
    required: true,
  },
  target: {
    type: String,
    required: false,
    default: null,
  },
})
</script>
