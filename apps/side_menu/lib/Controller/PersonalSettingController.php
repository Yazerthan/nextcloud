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
use OCA\SideMenu\Service\ConfigProxy;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IUserSession;

class PersonalSettingController extends Controller
{
    public function __construct(
        $appName,
        IRequest $request,
        protected IConfig $config,
        protected ConfigProxy $configProxy,
        protected IUserSession $userSession,
    ) {
        parent::__construct($appName, $request);
    }

    #[NoCSRFRequired]
    #[NoAdminRequired]
    #[FrontpageRoute(verb: 'POST', url: '/user/valueSet')]
    public function valueSet($name, $value): array
    {
        $doSave = false;
        $user = $this->userSession->getUser();

        if ('enabled' === $name) {
            $doSave = true;

            if (!in_array($value, ['0', '1'])) {
                $value = '1';
            }
        }

        if ('target-blank-mode' === $name) {
            $doSave = true;

            if (!in_array($value, ['1', '2'])) {
                $value = '1';
            }
        }

        if (in_array($name, ['target-blank-apps', 'top-menu-apps', 'top-side-menu-apps', 'apps-order'])) {
            $doSave = true;
            $data = json_decode($value, true);

            if (!is_array($data)) {
                $doSave = false;
            } else {
                foreach ($data as $v) {
                    if (!is_string($v)) {
                        $doSave = false;
                    }
                }
            }
        }

        if ($this->configProxy->getAppValueBool('force', '0')) {
            $doSave = false;
        }

        if ($doSave) {
            $this->config->setUserValue($user->getUid(), Application::APP_ID, $name, $value);

            return [
                'name' => $name,
                'value' => $value,
            ];
        }

        return [];
    }

    #[NoCSRFRequired]
    #[NoAdminRequired]
    #[FrontpageRoute(verb: 'GET', url: '/user/config')]
    public function configuration(): JSONResponse
    {
        $user = $this->userSession->getUser();
        $keys = $this->config->getUserKeys($user->getUid(), Application::APP_ID);

        $booleans = [
            'enabled',
        ];

        $arrays = [
            'apps-order',
            'target-blank-apps',
            'top-menu-apps',
            'top-side-menu-apps',
        ];

        $integers = [
            'target-blank-mode',
        ];

        $defaults = [
            'enabled' => '1',
            'target-blank-mode' => '1',
            'apps-order' => '[]',
            'target-blank-apps' => '[]',
            'top-menu-apps' => '[]',
            'top-side-menu-apps' => '[]',
        ];

        $config = [];

        foreach ($keys as $key) {
            if (!isset($defaults[$key])) {
                continue;
            }

            if (in_array($key, $booleans)) {
                $config[$key] = $this->configProxy->getUserValueBool($user, $key, $defaults[$key]);
            } elseif (in_array($key, $arrays)) {
                $config[$key] = $this->configProxy->getUserValueArray($user, $key, $defaults[$key]);
            } elseif (in_array($key, $integers)) {
                $config[$key] = $this->configProxy->getUserValueInt($user, $key, $defaults[$key]);
            } else {
                $config[$key] = $this->configProxy->getUserValue($user, $key, $defaults[$key]);
            }
        }

        foreach ($defaults as $key => $default) {
            if (!array_key_exists($key, $config)) {
                $config[$key] = $default;
            }
        }

        return new JSONResponse($config);
    }
}
