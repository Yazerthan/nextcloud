<template>
  <div class="cm-settings-form-displaypicker">
    <div class="cm-settings-button-inline">
      <NcButton
        :variant="is(false, false, false) ? 'primary' : 'seconday'"
        @click="update(false, false, false)"
      >
        {{ t('side_menu', 'Default') }}
      </NcButton>
      <NcButton
        :variant="is(true, false, false) ? 'primary' : 'seconday'"
        @click="update(true, false, false)"
      >
        {{ t('side_menu', 'Always displayed') }}
      </NcButton>
      <NcButton
        :variant="is(false, true, false) ? 'primary' : 'seconday'"
        @click="update(false, true, false)"
      >
        {{ t('side_menu', 'Big menu') }}
      </NcButton>
      <NcButton
        :variant="is(false, false, true) ? 'primary' : 'seconday'"
        @click="update(false, false, true)"
      >
        {{ t('side_menu', 'With categories') }}
      </NcButton>
    </div>

    <p>
      <img
        v-if="is(false, false, false)"
        :src="DefaultImg"
      />
      <img
        v-if="is(true, false, false)"
        :src="AlwaysDisplayedImg"
      />
      <img
        v-if="is(false, true, false)"
        class="side-menu-display"
        :src="TopWideImg"
      />
      <img
        v-if="is(false, false, true)"
        :src="SideMenuWithCategoriesImg"
      />
    </p>
  </div>
</template>
<script setup>
import { NcButton } from '@nextcloud/vue'

import AlwaysDisplayedImg from '../../../../img/admin/layout-always-displayed.svg'
import TopWideImg from '../../../../img/admin/layout-big-menu.svg'
import SideMenuWithCategoriesImg from '../../../../img/admin/layout-side-menu-with-categories.svg'
import DefaultImg from '../../../../img/admin/layout-default.svg'

const emit = defineEmits(['update:alwaysDisplayed', 'update:topWideMenu', 'update:sideMenuWithCategories'])

const { alwaysDisplayed, topWideMenu, sideMenuWithCategories } = defineProps({
  alwaysDisplayed: {
    type: Boolean,
    required: true,
  },
  topWideMenu: {
    type: Boolean,
    required: true,
  },
  sideMenuWithCategories: {
    type: Boolean,
    required: true,
  },
})

const update = (isAlwayDisplayed, isTopWideMenu, isSideMenuWithCategories) => {
  emit('update:alwaysDisplayed', isAlwayDisplayed)
  emit('update:topWideMenu', isTopWideMenu)
  emit('update:sideMenuWithCategories', isSideMenuWithCategories)
}

const is = (isAlwayDisplayed, isTopWideMenu, isSideMenuWithCategories) => {
  return isAlwayDisplayed === alwaysDisplayed && isTopWideMenu === topWideMenu && isSideMenuWithCategories === sideMenuWithCategories
}
</script>
