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
    <NcAppNavigation class="cm-settings-nav">
      <NcAppNavigationItem
        v-for="item in menu"
        :key="item.label"
        :name="trans(item.label)"
        :active="item.section === section"
        @click="setSection(item.section)"
      />
    </NcAppNavigation>
    <NcAppContent class="cm-settings cm-settings--nav">
      <SettingsSection :hidden="section !== 'panel'">
        <SectionTitle label="Panel" />

        <SettingItem>
          <SettingValue>
            <FormDisplayPicker
              :always-displayed="config['always-displayed']"
              :top-wide-menu="config['big-menu']"
              :side-menu-with-categories="config['side-with-categories']"
              @update:always-displayed="(value) => (config['always-displayed'] = value)"
              @update:top-wide-menu="(value) => (config['big-menu'] = value)"
              @update:side-menu-with-categories="(value) => (config['side-with-categories'] = value)"
            />
          </SettingValue>
        </SettingItem>
        <SettingItem :disabled="config['big-menu'] || config['always-displayed'] || config['side-with-categories']">
          <SettingLabel
            label="Display the logo"
            :middle="true"
          />
          <SettingValue>
            <FormYesNo v-model="config['display-logo']" />
          </SettingValue>
        </SettingItem>
        <SettingItem :disabled="config['big-menu'] || config['always-displayed'] || config['side-with-categories']">
          <SettingLabel
            label="Use the avatar instead of the logo"
            :middle="true"
          />
          <SettingValue>
            <FormYesNo v-model="config['use-avatar']" />
          </SettingValue>
        </SettingItem>
        <SettingItem :disabled="config['big-menu'] || config['always-displayed'] || config['side-with-categories']">
          <SettingLabel
            label="The logo is a link to the default app"
            :middle="true"
          />
          <SettingValue>
            <FormYesNo v-model="config['add-logo-link']" />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Show the link to settings"
            :middle="true"
          />
          <SettingValue>
            <FormYesNo v-model="config['show-settings']" />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Icons"
            :middle="true"
          />
          <SettingValue>
            <FormSize v-model="config['size-icon']" />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Texts"
            :middle="true"
          />
          <SettingValue>
            <FormSize v-model="config['size-text']" />
          </SettingValue>
        </SettingItem>
        <AdminSaveButton :config="config" />
      </SettingsSection>

      <SettingsSection :hidden="section !== 'topMenu'">
        <SectionTitle label="Top menu" />

        <SettingItem>
          <SettingLabel
            label="Applications kept in the top menu"
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
            :middle="true"
          />
          <SettingValue>
            <FormAppPicker v-model="config['top-side-menu-apps']" />
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="Hide labels on mouse over"
            :top="true"
          />
          <SettingValue>
            <FormSelect
              v-model="config['top-menu-mouse-over-hidden-label']"
              :expanded="true"
              :options="[
                { id: 1, label: 'Yes' },
                { id: 0, label: 'No' },
                { id: 2, label: 'Except the hovered app' },
              ]"
            />
          </SettingValue>
        </SettingItem>
        <AdminSaveButton :config="config" />
      </SettingsSection>

      <SettingsSection :hidden="section !== 'apps'">
        <SectionTitle label="Applications" />

        <SettingItem>
          <SettingLabel
            label="Apps that should not be displayed in the menu"
            :middle="true"
          />
          <SettingValue>
            <FormAppPicker v-model="config['big-menu-hidden-apps']" />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Open apps in new tab"
            :middle="true"
          />
          <SettingValue>
            <FormAppPicker v-model="config['target-blank-apps']" />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Customize sorting"
            :middle="true"
          />
          <SettingValue>
            <FormAppSort v-model="config['apps-order']" />
          </SettingValue>
        </SettingItem>
        <AdminSaveButton :config="config" />
      </SettingsSection>

      <SettingsSection :hidden="section !== 'cats'">
        <SectionTitle label="Categories" />

        <SettingItem :disabled="!(config['big-menu'] || config['always-displayed'] || config['side-with-categories'])">
          <SettingLabel
            label="Order by"
            :middle="true"
          />
          <SettingValue>
            <FormSelect
              v-model="config['categories-order-type']"
              :options="[
                { id: 'default', label: 'Name' },
                { id: 'custom', label: 'Custom' },
              ]"
            />
          </SettingValue>
        </SettingItem>
        <SettingItem :disabled="!(config['big-menu'] || config['always-displayed'] || config['side-with-categories'])">
          <SettingLabel
            label="Customize sorting"
            :middle="true"
          />
          <SettingValue>
            <FormCatSort v-model="config['categories-order']" />
          </SettingValue>
        </SettingItem>
        <SettingItem :disabled="!(config['big-menu'] || config['always-displayed'] || config['side-with-categories'])">
          <SettingLabel
            label="Customize application categories"
            :top="true"
          >
          </SettingLabel>
          <SettingValue>
            <FormAppCategory
              :langs="config['langs']"
              :categories-custom="config['categories-custom']"
              :apps-categories-custom="config['apps-categories-custom']"
              @update:categories-custom="(value) => (config['categories-custom'] = value)"
              @update:apps-categories-custom="(value) => (config['apps-categories-custom'] = value)"
            />
          </SettingValue>
        </SettingItem>

        <AdminSaveButton :config="config" />
      </SettingsSection>

      <SettingsSection :hidden="section !== 'opener'">
        <SectionTitle label="Opener" />

        <SettingItem>
          <SettingLabel
            label="Opener"
            :middle="true"
          />
          <SettingValue>
            <FormOpener v-model="config['opener']" />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Dark mode opener"
            :middle="true"
          />
          <SettingValue>
            <FormOpener v-model="config['dark-mode-opener']" />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Position"
            :middle="true"
          />
          <SettingValue>
            <FormSelect
              v-model="config['opener-position']"
              :options="[
                { id: 'before', label: 'Before the logo' },
                { id: 'after', label: 'After the logo' },
              ]"
            />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Show only the opener (hidden logo)"
            :middle="true"
          />
          <SettingValue>
            <FormYesNo v-model="config['opener-only']" />
          </SettingValue>
        </SettingItem>
        <SettingItem>
          <SettingLabel
            label="Open the menu when the mouse is hover the opener (automatically disabled on touch screens)"
            :middle="true"
          />
          <SettingValue>
            <FormYesNo v-model="config['opener-hover']" />
          </SettingValue>
        </SettingItem>
        <AdminSaveButton :config="config" />
      </SettingsSection>

      <SettingsSection :hidden="section !== 'colors'">
        <SectionTitle label="Colors" />

        <SettingItem>
          <SettingLabel
            label="Background color"
            :middle="true"
          />
          <SettingValue>
            <FormColorPicker v-model="config['background-color']" />
            <FormColorPicker v-model="config['background-color-to']" />
            <FormRange
              v-model="config['background-color-opacity']"
              prepend="Transparent"
              append="Opaque"
            />
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="Background color of current app"
            :middle="true"
          />
          <SettingValue>
            <FormColorPicker v-model="config['current-app-background-color']" />
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="Text color"
            :middle="true"
          />
          <SettingValue>
            <FormColorPicker v-model="config['text-color']" />
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="Loader"
            :middle="true"
          />
          <SettingValue>
            <FormColorPicker v-model="config['loader-color']" />
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="Icon"
            :middle="true"
          />
          <SettingValue>
            <FormRange
              v-model="config['icon-invert-filter']"
              prepend="Same color"
              append="Opposite color"
            />
            <FormRange
              v-model="config['icon-opacity']"
              prepend="Transparent"
              append="Opaque"
            />
          </SettingValue>
        </SettingItem>

        <SectionTitle label="Dark mode colors" />

        <SettingItem>
          <SettingLabel
            label="Background color"
            :middle="true"
          />
          <SettingValue>
            <FormColorPicker v-model="config['dark-mode-background-color']" />
            <FormColorPicker v-model="config['dark-mode-background-color-to']" />
            <FormRange
              v-model="config['dark-mode-background-color-opacity']"
              prepend="Transparent"
              append="Opaque"
            />
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="Background color of current app"
            :middle="true"
          />
          <SettingValue>
            <FormColorPicker v-model="config['dark-mode-current-app-background-color']" />
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="Text color"
            :middle="true"
          />
          <SettingValue>
            <FormColorPicker v-model="config['dark-mode-text-color']" />
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="Loader"
            :middle="true"
          />
          <SettingValue>
            <FormColorPicker v-model="config['dark-mode-loader-color']" />
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="Icon"
            :middle="true"
          />
          <SettingValue>
            <FormRange
              v-model="config['dark-mode-icon-invert-filter']"
              prepend="Same color"
              append="Opposite color"
            />
            <FormRange
              v-model="config['dark-mode-icon-opacity']"
              prepend="Transparent"
              append="Opaque"
            />
          </SettingValue>
        </SettingItem>
        <AdminSaveButton :config="config" />
      </SettingsSection>

      <SettingsSection :hidden="section !== 'global'">
        <p class="cm-settings-tips">
          <em>{{ t('side_menu', 'Use the shortcut Ctrl+o to open and to hide the side menu. Use tab key to navigate.') }}</em>
        </p>

        <SectionTitle label="Global" />

        <SettingItem>
          <SettingLabel
            label="The menu is enabled by default for users"
            help="Except when the configuration is forced."
            :middle="true"
          />
          <SettingValue>
            <FormYesNo v-model="config['default-enabled']" />
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="Force this configuration to users"
            :middle="true"
          />
          <SettingValue>
            <FormYesNo v-model="config['force']" />
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="Loader enabled"
            :middle="true"
          />
          <SettingValue>
            <FormYesNo v-model="config['loader-enabled']" />
          </SettingValue>
        </SettingItem>
        <AdminSaveButton :config="config" />
      </SettingsSection>

      <SettingsSection :hidden="section !== 'support'">
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

        <SettingItem>
          <SettingLabel label="Need help" />
          <SettingValue class="cm-settings-button-inline">
            <ExternalLink href="https://deblan.gitnet.page/side_menu_doc/">
              <NcButton variant="secondary">{{ trans('Open the documentation') }}</NcButton>
            </ExternalLink>
            <ExternalLink href="https://gitnet.fr/deblan/side_menu/issues/new?template=.gitea%2fissue_template%2fQUESTION_TEMPLATE.yml">
              <NcButton variant="secondary">{{ trans('Ask the developer') }}</NcButton>
            </ExternalLink>
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="I would like a new feature"
            :middle="true"
          />
          <SettingValue>
            <ExternalLink href="https://gitnet.fr/deblan/side_menu/issues/new?template=.gitea%2fissue_template%2fFEATURE_TEMPLATE.yml">
              <NcButton variant="secondary">{{ trans('New request') }}</NcButton>
            </ExternalLink>
          </SettingValue>
        </SettingItem>

        <SettingItem>
          <SettingLabel
            label="Something went wrong"
            :top="true"
          />
          <SettingValue class="cm-settings-button-inline">
            <ExternalLink href="https://gitnet.fr/deblan/side_menu/issues/new?template=.gitea%2fissue_template%2fISSUE_TEMPLATE.yml">
              <NcButton variant="secondary">{{ trans('Report a bug') }}</NcButton>
            </ExternalLink>
            <NcButton
              variant="secondary"
              @click="showConfig = true"
              >{{ trans('Show the configuration') }}</NcButton
            >

            <NcModal
              v-if="showConfig"
              class="cm-settings-config-modal"
              @close="showConfig = false"
            >
              <div class="modal__content">
                <p style="margin-bottom: 5px">{{ trans('Configuration:') }}</p>
                <textarea
                  readonly
                  v-text="filterConfig(config)"
                ></textarea>

                <div class="modal__footer">
                  <NcButton
                    variant="secondary"
                    @click="copyConfig"
                  >
                    <span v-if="configCopied">{{ trans('Done!') }}</span>
                    <span v-else>{{ trans('Copy') }}</span>
                  </NcButton>
                  <NcButton
                    variant="primary"
                    @click="showConfig = false"
                  >
                    {{ t('side_menu', 'Close') }}
                  </NcButton>
                </div>
              </div>
            </NcModal>
          </SettingValue>
        </SettingItem>
        <AdminSaveButton :config="config" />
      </SettingsSection>
    </NcAppContent>
  </NcContent>
</template>

<script setup>
import { NcContent, NcAppContent, NcButton, NcModal, NcAppNavigation, NcAppNavigationItem } from '@nextcloud/vue'
import { ref, onMounted } from 'vue'
import { useConfigStore } from '../store/config.js'
import { SettingsSection, SettingItem, SettingLabel, SettingValue, SectionTitle, ExternalLink, AdminSaveButton } from '../components/settings'
import {
  FormRange,
  FormColorPicker,
  FormOpener,
  FormSelect,
  FormYesNo,
  FormSize,
  FormAppPicker,
  FormAppSort,
  FormCatSort,
  FormDisplayPicker,
  FormAppCategory,
} from '../components/settings/form'


const menu = [
  { label: 'Global', section: 'global', icon: '' },
  { label: 'Panel', section: 'panel', icon: '' },
  { label: 'Colors', section: 'colors', icon: '' },
  { label: 'Opener', section: 'opener', icon: '' },
  { label: 'Applications', section: 'apps', icon: '' },
  { label: 'Categories', section: 'cats', icon: '' },
  { label: 'Top menu', section: 'topMenu', icon: '' },
  { label: 'Support', section: 'support', icon: '' },
]

const config = ref(null)
const showConfig = ref(false)
const configCopied = ref(false)
const configStore = useConfigStore()
const section = ref(null)

const setSection = (value) => {
  sessionStorage.setItem('side_menu_section', value)

  section.value = value
}

const trans = (value) => {
  return t('side_menu', value)
}

const copyConfig = () => {
  navigator.clipboard.writeText(JSON.stringify(filterConfig(config.value), null, 2))

  configCopied.value = true

  window.setTimeout(() => {
    configCopied.value = false
  }, 2000)
}

const filterConfig = (value) => {
  const result = {}

  for (let key in value) {
    if (['cache-categories', 'cache', 'langs', 'enabled'].includes(key) === false) {
      result[key] = value[key]
    }
  }

  return result
}

onMounted(async () => {
  config.value = await configStore.getAppConfig()

  setSection(sessionStorage.getItem('side_menu_section') ?? menu[0].section)
})
</script>
