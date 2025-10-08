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

use OC\User\User;
use OCA\SideMenu\AppInfo\Application;
use OCA\SideMenu\Service\Color;
use OCA\SideMenu\Service\ConfigProxy;
use OCA\Theming\ThemingDefaults;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IUserSession;

class CssController extends Controller
{
    protected ?User $user;

    public function __construct(
        string $appName,
        IRequest $request,
        protected ConfigProxy $config,
        protected ThemingDefaults $theming,
        protected Color $color,
    ) {
        parent::__construct($appName, $request);

        $this->user = \OC::$server[IUserSession::class]->getUser();
    }

    #[NoCSRFRequired]
    #[NoAdminRequired]
    #[PublicPage]
    #[FrontpageRoute(verb: 'GET', url: '/css/stylesheet')]
    public function stylesheet(): TemplateResponse
    {
        $response = new TemplateResponse(Application::APP_ID, 'css/stylesheet', $this->getConfig(), 'blank');
        $response->addHeader('Content-Type', 'text/css');

        return $response;
    }

    protected function getConfig(): array
    {
        $isForced = $this->config->getAppValueBool('force', '0');
        $topMenuApps = $this->config->getAppValueArray('top-menu-apps', '[]');
        $topSideMenuApps = $this->config->getAppValueArray('top-side-menu-apps', '[]');

        if ($this->user) {
            $userTopMenuApps = $this->config->getUserValueArray($this->user, 'top-menu-apps', '[]');
            $userTopSideMenuApps = $this->config->getUserValueArray($this->user, 'top-side-menu-apps', '[]');

            if (!empty($userTopMenuApps) && !$isForced) {
                $topMenuApps = $userTopMenuApps;
            }

            if (!empty($userTopSideMenuApps) && !$isForced) {
                $topSideMenuApps = $userTopSideMenuApps;
            }
        }

        $lightenPrimaryColor = $this->color->getLightenPrimaryColor();
        $darkenPrimaryColor = $this->color->getDarkenPrimaryColor();
        $darkenPrimaryColor2 = $this->color->getDarkenPrimaryColor2();
        $textColor = $this->color->getTextColorPrimary();

        $backgroundColor = $this->config->getAppValue('background-color', $darkenPrimaryColor);
        $backgroundColorTo = $this->config->getAppValue('background-color-to', $darkenPrimaryColor);
        $opacity = $this->config->getAppValueInt('background-color-opacity', '100');
        $backgroundOpacity = dechex($opacity * 255 / 100);

        $backgroundColor .= $backgroundOpacity;
        $backgroundColorTo .= $backgroundOpacity;

        $darkBackgroundColor = $this->config->getAppValue('dark-mode-background-color', $darkenPrimaryColor);
        $darkBackgroundColorTo = $this->config->getAppValue('dark-mode-background-color-to', $darkenPrimaryColor);
        $darkOpacity = $this->config->getAppValueInt('dark-mode-background-color-opacity', '100');
        $darkBackgroundOpacity = dechex($opacity * 255 / 100);

        $darkBackgroundColor .= $darkBackgroundOpacity;
        $darkBackgroundColorTo .= $darkBackgroundOpacity;

        return [
            'vars' => [
                'light' => [
                    'background-color' => $backgroundColor,
                    'background-color-to' => $backgroundColorTo,
                    'current-app-background-color' => $this->config->getAppValue(
                        'current-app-background-color',
                        $darkenPrimaryColor2
                    ),
                    'loader-color' => $this->config->getAppValue('loader-color', $lightenPrimaryColor),
                    'text-color' => $this->config->getAppValue('text-color', $textColor),
                    'opener' => $this->config->getAppValue('opener', 'side-menu-opener'),
                    'icon-invert-filter' => abs($this->config->getAppValueInt('icon-invert-filter', '0')).'%',
                    'icon-opacity' => abs($this->config->getAppValueInt('icon-opacity', '100') / 100),
                ],
                'dark' => [
                    'background-color' => $darkBackgroundColor,
                    'background-color-to' => $darkBackgroundColorTo,
                    'current-app-background-color' => $this->config->getAppValue(
                        'dark-mode-current-app-background-color',
                        $darkenPrimaryColor2
                    ),
                    'loader-color' => $this->config->getAppValue('dark-mode-loader-color', $lightenPrimaryColor),
                    'text-color' => $this->config->getAppValue('dark-mode-text-color', $textColor),
                    'opener' => $this->config->getAppValue('dark-mode-opener', 'side-menu-opener'),
                    'icon-invert-filter' => abs($this->config->getAppValueInt('dark-mode-icon-invert-filter', '0')).'%',
                    'icon-opacity' => abs($this->config->getAppValueInt('dark-mode-icon-opacity', '100') / 100),
                ]
            ],
            'opener-only' => $this->config->getAppValueBool('opener-only', '0'),
            'size-icon' => $this->config->getAppValue('size-icon', 'normal'),
            'size-text' => $this->config->getAppValue('size-text', 'normal'),
            'always-displayed' => $this->config->getAppValueBool('always-displayed', '0'),
        ];
    }
}
