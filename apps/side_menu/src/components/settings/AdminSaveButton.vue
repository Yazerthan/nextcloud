<template>
  <div class="cm-settings-btn cm-settings-btn--save">
    <NcButton
      variant="success"
      @click="save"
    >
      <template v-if="!loading">
        {{ t('side_menu', 'Save') }}
      </template>
      <NcLoadingIcon v-else />
    </NcButton>

    <div
      v-if="error"
      id="error"
    >
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { NcButton, NcLoadingIcon } from '@nextcloud/vue'
import { ref } from 'vue'
import { waitPasswordConfirmation } from '../../lib/setting.js'

const loading = ref(false)
const error = ref(null)
const { config } = defineProps({
  config: {
    type: Object,
    required: true,
  },
})

const filterConfig = (value) => {
  const result = {}

  for (let key in value) {
    if (['cache-categories', 'cache', 'langs', 'enabled'].includes(key) === false) {
      result[key] = value[key]
    }
  }

  return result
}

const save = async () => {
  const data = filterConfig(config)
  const size = Object.keys(data).length
  let counter = 0

  loading.value = true
  error.value = null

  const update = () => {
    ++counter

    if (counter === size) {
      loading.value = false

      if (!error.value) {
        location.reload()
      }
    }
  }

  waitPasswordConfirmation()
    .then(() => {
      for (let key in data) {
        let value = data[key]

        if (Array.isArray(value) || typeof value === 'object') {
          value = JSON.stringify(value)
        } else if (typeof value === 'boolean') {
          value = value ? 1 : 0
        }

        OCP.AppConfig.setValue('side_menu', key, value, {
          success() {
            update()
          },
          error() {
            error.value = `Error while saving ${key}`
            update()
          },
        })
      }
    })
    .catch(() => {
      counter = 0
      loading.value = false
      error.value = null
    })
}
</script>

<style scoped>
#error {
  padding-top: 10px;
  color: red;
}
</style>
