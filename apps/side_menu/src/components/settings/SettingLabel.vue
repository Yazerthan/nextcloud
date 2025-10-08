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
    class="cm-settings-item-label"
    :class="{
      'cm-settings-item-label--short': short,
      'cm-settings-item-label--top': top,
      'cm-settings-item-label--middle': middle,
    }"
  >
    {{ t('side_menu', label) }}

    <template v-if="help">
      <br />
      <em>{{ t('side_menu', help) }}</em>
    </template>
    <template v-if="help2">
      <br />
      <em>{{ t('side_menu', help2) }}</em>
    </template>
  </div>
</template>

<script setup>
const { short, label } = defineProps({
  short: {
    type: Boolean,
    required: false,
    default: false,
  },
  label: {
    type: String,
    required: true,
  },
  middle: {
    type: Boolean,
    required: false,
    default: false,
  },
  top: {
    type: Boolean,
    required: false,
    default: true,
  },
  help: {
    type: [String, null],
    required: false,
    default: null,
  },
  help2: {
    type: [String, null],
    required: false,
    default: null,
  },
})
</script>
