<?php

/**
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\SideMenu\Controller;

use OCA\SideMenu\AppInfo\Application;
use OCA\SideMenu\Service\Color;
use OCA\SideMenu\Service\ConfigProxy;
use OCA\SideMenu\Service\LangRepository;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\DataDownloadResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IURLGenerator;

class AdminSettingController extends Controller
{
    public function __construct(
        $appName,
        IRequest $request,
        protected IConfig $config,
        protected ConfigProxy $configProxy,
        protected IURLGenerator $urlGenerator,
        protected Color $color,
        protected LangRepository $langRepository,
    ) {
        parent::__construct($appName, $request);
    }

    #[NoCSRFRequired]
    #[FrontpageRoute(verb: 'GET', url: '/admin/cache/remove')]
    public function removeCache(): RedirectResponse
    {
        $this->config->setAppValue(Application::APP_ID, 'cache-categories', '[]');

        return new RedirectResponse($this->urlGenerator->linkToRoute('settings.AdminSettings.index', [
            'section' => Application::APP_ID,
        ]).'#more');
    }

    #[NoCSRFRequired]
    #[FrontpageRoute(verb: 'GET', url: '/admin/config/export')]
    public function exportConfiguration(): DataDownloadResponse
    {
        $keys = $this->config->getAppKeys(Application::APP_ID);
        $appConfig = [];
        $excludedKeys = [
            'cache',
            'cache-categories',
            'langs',
        ];

        foreach ($keys as $key) {
            if (in_array($key, $excludedKeys)) {
                continue;
            }

            $appConfig[$key] = $this->config->getAppValue(Application::APP_ID, $key);
        }

        return new DataDownloadResponse(
            json_encode($appConfig, JSON_PRETTY_PRINT),
            'config.json',
            'text/json'
        );
    }

    #[NoCSRFRequired]
    #[FrontpageRoute(verb: 'GET', url: '/admin/config')]
    public function configuration(): JSONResponse
    {
        $keys = $this->config->getAppKeys(Application::APP_ID);
        $booleans = [
            'opener-only',
            'opener-hover',
            'display-logo',
            'use-avatar',
            'add-logo-link',
            'show-settings',
            'loader-enabled',
            'always-displayed',
            'enabled',
            'force',
            'big-menu',
            'external-sites-in-top-menu',
            'force-light-icon',
            'side-with-categories',
            'default-enabled',
        ];

        $arrays = [
            'apps-categories-custom',
            'big-menu-hidden-apps',
            'apps-order',
            'categories-custom',
            'categories-order',
            'target-blank-apps',
            'top-menu-apps',
            'top-side-menu-apps',
        ];

        $integers = [
            'background-color-opacity',
            'dark-mode-background-color-opacity',
            'dark-mode-icon-invert-filter',
            'dark-mode-icon-opacity',
            'icon-invert-filter',
            'icon-opacity',
            'target-blank-mode',
            'top-menu-mouse-over-hidden-label',
        ];

        $defaults = [
            'opener-only' => '0',
            'opener-hover' => '0',
            'display-logo' => '1',
            'use-avatar' => '0',
            'add-logo-link' => '1',
            'show-settings' => '0',
            'loader-enabled' => '1',
            'always-displayed' => '0',
            'enabled' => '1',
            'force' => '0',
            'big-menu' => '0',
            'external-sites-in-top-menu' => '0',
            'force-light-icon' => '0',
            'side-with-categories' => '0',
            'cache' => '1',
            'default-enabled' => '1',

            'apps-categories-custom' => '[]',
            'big-menu-hidden-apps' => '[]',
            'apps-order' => '[]',
            'categories-custom' => '[]',
            'categories-order' => '[]',
            'target-blank-apps' => '[]',
            'top-menu-apps' => '[]',
            'top-side-menu-apps' => '[]',
            'cache-categories' => '[]',

            'background-color-opacity' => '100',
            'dark-mode-background-color-opacity' => '100',
            'dark-mode-icon-invert-filter' => '0',
            'dark-mode-icon-opacity' => '100',
            'icon-invert-filter' => '0',
            'icon-opacity' => '100',
            'top-menu-mouse-over-hidden-label' => '0',

            'opener' => 'side-menu-opener',
            'dark-mode-opener' => 'side-menu-opener',
            'size-icon' => 'normal',
            'size-text' => 'normal',
            'opener-position' => 'before',

            'background-color' => $this->color->getPrimaryColor(),
            'background-color-to' => $this->color->getLightenPrimaryColor(),
            'current-app-background-color' => $this->color->getDarkenPrimaryColor(),
            'text-color' => $this->color->getTextColorPrimary(),
            'loader-color' => $this->color->getLightenPrimaryColor(),

            'dark-mode-background-color' => $this->color->getDarkenPrimaryColor(),
            'dark-mode-background-color-to' => $this->color->getDarkenPrimaryColor(),
            'dark-mode-current-app-background-color' => $this->color->getDarkenPrimaryColor2(),
            'dark-mode-text-color' => $this->color->getTextColorPrimary(),
            'dark-mode-loader-color' => $this->color->getLightenPrimaryColor(),

            'categories-order-type' => 'default',
        ];

        $config = [];

        foreach ($keys as $key) {
            if (!isset($defaults[$key])) {
                continue;
            }

            if (in_array($key, $booleans)) {
                $config[$key] = $this->configProxy->getAppValueBool($key, $defaults[$key]);
            } elseif (in_array($key, $arrays)) {
                $config[$key] = $this->configProxy->getAppValueArray($key, $defaults[$key]);
            } elseif (in_array($key, $integers)) {
                $config[$key] = $this->configProxy->getAppValueInt($key, $defaults[$key]);
            } else {
                $config[$key] = $this->configProxy->getAppValue($key, $defaults[$key]);
            }
        }

        foreach ($defaults as $key => $default) {
            if (!array_key_exists($key, $config)) {
                $config[$key] = $default;
            }
        }

        $config['langs'] = $this->langRepository->getUsedLangs();

        return new JSONResponse($config);
    }
}
