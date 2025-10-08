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
  <div class="cm-settings-form-select">
    <template v-if="!expanded">
      <select
        v-if="!expanded"
        v-model="model"
        :multiple="multiple"
      >
        <option
          v-if="!required"
          :value="null"
        ></option>
        <option
          v-for="option in options"
          :key="option.id"
          :value="option.id"
        >
          {{ t('side_menu', option.label) }}
        </option>
      </select>
    </template>
    <template v-else>
      <NcCheckboxRadioSwitch
        v-for="option in options"
        :key="option.id"
        v-model="model"
        :value="option.id"
        :type="multiple ? 'checkbox' : 'radio'"
        name="value"
      >
        {{ t('side_menu', option.label) }}
      </NcCheckboxRadioSwitch>
    </template>
  </div>
</template>

<script setup>
import { NcCheckboxRadioSwitch } from '@nextcloud/vue'

const model = defineModel({ type: [Number, String, Array, null] })
const { options, expanded } = defineProps({
  options: {
    type: Array,
    required: true,
  },
  required: {
    type: Boolean,
    required: false,
    default: true,
  },
  expanded: {
    type: Boolean,
    required: false,
    default: false,
  },
  multiple: {
    type: Boolean,
    required: false,
    default: false,
  },
})
</script>
