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
    ></div>
  </div>
</template>

<script setup>
import { NcButton, NcLoadingIcon } from '@nextcloud/vue'
import { ref } from 'vue'

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
    result[key] = value[key]
  }

  return result
}

const save = async () => {
  const data = filterConfig(config)
  const size = Object.keys(data).length
  const url = OC.generateUrl('/apps/side_menu/user/valueSet')

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

  for (let key in data) {
    let value = data[key]
    let formData = []

    if (Array.isArray(value) || typeof value === 'object') {
      value = JSON.stringify(value)
    } else if (typeof value === 'boolean') {
      value = value ? 1 : 0
    }

    formData.push('name=' + encodeURIComponent(key))
    formData.push('value=' + encodeURIComponent(value))

    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: formData.join('&'),
    })
      .then(update)
      .catch(() => {
        error.value = `Error while saving ${key}`
        update()
      })
  }
}
</script>

<style scoped>
#error {
  padding-top: 10px;
  color: red;
}
</style>
