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
  <NcContent
    v-if="config"
    app-name="side_menu"
  >
    <NcAppContent
      v-if="config['force']"
      class="cm-settings"
    >
      <SettingsSection>
        <SettingLabel label="You do not have permission to change the settings." />
      </SettingsSection>
    </NcAppContent>
    <NcAppContent
      v-else
      class="cm-settings"
    >
      <SettingsSection>
        <SectionTitle label="Menu" />

        <SettingItem>
          <SettingLabel
            label="Enable the custom menu"
            :middle="true"
          />
          <SettingValue>
            <FormYesNo v-model="config['enabled']" />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Applications kept in the top menu"
            help="If there is no selection then the global configuration is applied."
            :middle="true"
          />
          <SettingValue>
            <FormAppPicker v-model="config['top-menu-apps']" />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Applications kept in the top menu but also shown in side menu"
            help="These applications must be selected in the previous option."
            help2="If there is no selection then the global configuration is applied"
            :middle="true"
          />
          <SettingValue>
            <FormAppPicker v-model="config['top-side-menu-apps']" />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Open apps in new tab"
            :middle="true"
          />
          <SettingValue class="cm-settings-children-inline">
            <FormSelect
              v-model="config['target-blank-mode']"
              :options="[
                { id: 1, label: 'Use the global setting' },
                { id: 2, label: 'Use my selection' },
              ]"
            />
            <FormAppPicker
              v-if="config['target-blank-mode'] === 2"
              v-model="config['target-blank-apps']"
            />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Customize sorting"
            :top="true"
          />
          <SettingValue>
            <FormAppSort v-model="config['apps-order']" />
          </SettingValue>
        </SettingItem>
      </SettingsSection>

      <SettingsSection>
        <SectionTitle label="Support" />

        <SettingItem>
          <SettingLabel
            label="You like this app and you want to support me?"
            :middle="true"
          />
          <SettingValue>
            <ExternalLink href="https://www.buymeacoffee.com/deblan">
              <NcButton variant="secondary">{{ trans('Buy me a coffee ☕') }}</NcButton>
            </ExternalLink>
          </SettingValue>
        </SettingItem>

        <UserSaveButton :config="config" />
      </SettingsSection>
    </NcAppContent>
  </NcContent>
</template>

<script setup>
import { NcContent, NcAppContent, NcButton } from '@nextcloud/vue'
import { ref, onMounted } from 'vue'
import { useConfigStore } from '../store/config.js'
import { FormSelect, FormYesNo, FormAppPicker, FormAppSort } from '../components/settings/form'
import { SettingsSection, SettingItem, SettingLabel, SettingValue, SectionTitle, ExternalLink, UserSaveButton } from '../components/settings'

const config = ref(null)
const configStore = useConfigStore()

const trans = (value) => {
  return t('side_menu', value)
}

onMounted(async () => {
  config.value = await configStore.getUserConfig()
})
</script>

<style scoped>
.hidden {
  display: none;
}

.button-inline button {
  display: inline-block;
  margin-right: 5px;
}

.config {
  width: 100%;
  height: 30vh;
}

.modal__content {
  padding: 20px;
}

.modal__footer {
  margin-top: 20px;
  text-align: right;
}

.modal__footer button {
  display: inline-block;
  margin-right: 5px;
}

.save {
  display: block;
  float: right;
}
</style>
