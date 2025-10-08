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
  <div class="cm-settings-form-range">
    <em
      v-if="prepend"
      class="cm-settings-form-range-prepend"
      >{{ t('side_menu', prepend) }}</em
    >

    <input
      v-model="model"
      type="range"
      :min="min"
      :max="max"
    />

    <em
      v-if="append"
      class="cm-settings-form-range-append"
      >{{ t('side_menu', append) }}</em
    >
  </div>
</template>

<script setup>
const model = defineModel({ type: Number })

defineProps({
  prepend: {
    type: [String, null],
    required: false,
    default: null,
  },
  append: {
    type: [String, null],
    required: false,
    default: null,
  },
  min: {
    type: Number,
    required: false,
    default: 0,
  },
  max: {
    type: Number,
    required: false,
    default: 100,
  },
})
</script>
