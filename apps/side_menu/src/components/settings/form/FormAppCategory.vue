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
  <div class="cm-settings-form-appcategory">
    <NcButton
      aria-label="t('side_menu', 'Customize')"
      variant="primary"
      @click="openModal"
    >
      {{ t('side_menu', 'Customize') }}
    </NcButton>

    <NcModal
      v-if="modal"
      class="cm-settings-form-appcategory-modal"
      @close="closeModal"
    >
      <div class="modal__content">
        <div class="menu">
          <NcButton
            aria-label="t('side_menu', 'Categories')"
            :variant="section === 'cats' ? 'primary' : 'secondary'"
            @click="setSection('cats')"
          >
            {{ t('side_menu', 'Categories') }}
          </NcButton>
          <NcButton
            aria-label="t('side_menu', 'Applications')"
            :variant="section === 'apps' ? 'primary' : 'secondary'"
            @click="setSection('apps')"
          >
            {{ t('side_menu', 'Applications') }}
          </NcButton>
        </div>

        <div v-if="section === 'cats'">
          <table
            v-if="!newCustomCategory && editCustomCategoryKey === null"
            width="100%"
          >
            <tbody>
              <tr
                v-for="(item, key) in categoriesCustom"
                :key="key"
              >
                <td>{{ item[langs[0]] }}</td>
                <td width="50px">
                  <NcActions>
                    <NcActionButton
                      icon="icon-edit"
                      @click="editCustomCategory(key)"
                    ></NcActionButton>
                    <NcActionButton
                      icon="icon-delete"
                      @click="removeCustomCategory(key)"
                    ></NcActionButton>
                  </NcActions>
                </td>
              </tr>
            </tbody>
          </table>
          <div
            v-else
            class="form"
          >
            <template v-if="newCustomCategory">
              <NcTextField
                v-for="lang in langs"
                :key="lang"
                v-model="newCustomCategory[lang]"
                :label="lang"
              />
            </template>
            <template v-if="editCustomCategoryKey !== null">
              <NcTextField
                v-for="lang in langs"
                :key="lang"
                v-model="categoriesCustom[editCustomCategoryKey][lang]"
                :label="lang"
              />
            </template>
          </div>
        </div>

        <div v-if="section === 'apps'">
          <table width="100%">
            <tbody>
              <tr
                v-for="item in apps"
                :key="item.id"
              >
                <td>
                  <img
                    :src="item.icon"
                    :alt="item.name"
                  />
                  {{ item.name }}
                </td>
                <td width="50%">
                  <FormSelect
                    v-model="appsCategoriesCustom[item.id]"
                    :options="getOptions(categoriesCustom)"
                    :required="false"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="modal__footer">
          <template v-if="section === 'cats'">
            <template v-if="newCustomCategory">
              <NcActions>
                <NcActionButton
                  icon="icon-close"
                  @click="cancelCustomCategory"
                ></NcActionButton>
              </NcActions>
              <NcActions>
                <NcActionButton
                  icon="icon-checkmark"
                  @click="saveCustomCategory"
                ></NcActionButton>
              </NcActions>
            </template>
            <template v-if="editCustomCategoryKey !== null">
              <NcActions>
                <NcActionButton
                  icon="icon-close"
                  @click="cancelCustomCategory"
                ></NcActionButton>
              </NcActions>
            </template>
            <template v-else>
              <NcActions>
                <NcActionButton
                  v-if="!newCustomCategory && editCustomCategoryKey === null"
                  icon="icon-add"
                  @click="addCustomCategory"
                ></NcActionButton>
                <NcActionButton
                  v-if="editCustomCategoryKey !== null"
                  icon="icon-checkmark"
                  @click="saveCustomCategory"
                ></NcActionButton>
              </NcActions>
            </template>
          </template>
          <NcButton
            variant="primary"
            class="btn-close"
            @click="closeModal"
          >
            {{ t('side_menu', 'Close') }}
          </NcButton>
        </div>
      </div>
    </NcModal>
  </div>
</template>

<script setup>
import { NcButton, NcModal, NcActions, NcActionButton, NcTextField } from '@nextcloud/vue'
import { useNavStore } from '../../../store/nav.js'
import { ref, onMounted, watch } from 'vue'
import FormSelect from './FormSelect'

const emit = defineEmits(['update:categoriesCustom', 'update:appsCategoriesCustom'])
const { categoriesCustom, appsCategoriesCustom, langs } = defineProps({
  categoriesCustom: {
    type: Array,
    required: true,
  },
  appsCategoriesCustom: {
    type: [Object, Array],
    required: true,
  },
  langs: {
    type: Array,
    required: true,
  },
})

const navStore = useNavStore()
const modal = ref(false)
const apps = ref([])
const categories = ref([])
const section = ref('apps')
const newCustomCategory = ref(null)
const editCustomCategoryKey = ref(null)

const openModal = () => {
  modal.value = true
}

const closeModal = () => {
  modal.value = false
}

const setSection = (value) => {
  section.value = value
}

const addCustomCategory = () => {
  let data = {
    id: 'cat' + Math.random().toString().replace('0.', ''),
  }

  langs.forEach((lang) => {
    data[lang] = ''
  })

  newCustomCategory.value = data
}

const cancelCustomCategory = () => {
  newCustomCategory.value = null
  editCustomCategoryKey.value = null
}

const saveCustomCategory = () => {
  const data = categoriesCustom

  if (editCustomCategoryKey.value === null) {
    data.push({ ...newCustomCategory.value })
  }

  emit('update:categoriesCustom', data)

  newCustomCategory.value = null
  editCustomCategoryKey.value = null
}

const removeCustomCategory = (key) => {
  const data = categoriesCustom
  delete data[key]

  emit('update:categoriesCustom', Object.values(data))
}

const editCustomCategory = (key) => {
  editCustomCategoryKey.value = key
}

const getOptions = (custom) => {
  const data = []

  custom.forEach((item) => {
    data.push({ id: item.id, label: item[langs[0]] })
  })

  categories.value.forEach((item) => {
    data.push({ id: item.categoryId, label: item.name !== '' ? item.name : t('side_menu', 'Other') })
  })

  data.sort((a, b) => (a.label < b.label ? -1 : 1))

  return data
}

onMounted(async () => {
  apps.value = await navStore.getApps()
  categories.value = await navStore.getCategories()

  let value = {}

  apps.value.forEach((app) => {
    if (!appsCategoriesCustom[app.id]) {
      value[app.id] = null
    } else {
      value[app.id] = appsCategoriesCustom[app.id]
    }
  })

  emit('update:appsCategoriesCustom', value)
})
</script>
