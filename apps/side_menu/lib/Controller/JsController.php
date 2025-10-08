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
use OCA\SideMenu\Service\ConfigProxy;
use OCA\Theming\ThemingDefaults;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\L10N\IFactory;
use OCP\IAvatarManager;
use OCP\INavigationManager;
use OCP\IURLGenerator;

class JsController extends Controller
{
    protected ?User $user;

    public function __construct(
        string $appName,
        IRequest $request,
        protected ConfigProxy $config,
        protected ThemingDefaults $themingDefaults,
        protected IFactory $l10nFactory,
        protected IAvatarManager $avatarManager,
        protected IUserSession $userSession,
        protected INavigationManager $navigationManager,
        protected IURLGenerator $urlGenerator,
    ) {
        parent::__construct($appName, $request);
        $this->user = $this->userSession->getUser();
    }

    #[NoCSRFRequired]
    #[NoAdminRequired]
    #[PublicPage]
    #[FrontpageRoute(verb: 'GET', url: '/js/config')]
    public function config(): JSONResponse
    {
        return new JSONResponse($this->getConfig());
    }

    protected function getConfig(): array
    {
        $topMenuApps = $this->config->getAppValueArray('top-menu-apps', '[]');
        $topSideMenuApps = $this->config->getAppValueArray('top-side-menu-apps', '[]');
        $targetBlankApps = $this->config->getAppValueArray('target-blank-apps', '[]');
        $useAvatar = $this->config->getAppValueBool('use-avatar', '0');
        $isForced = $this->config->getAppValueBool('force', '0');
        $addLogoLink = $this->config->getAppValueBool('add-logo-link', '1');
        $appsOrder = $this->config->getAppValueArray('apps-order', '[]');
        $avatar = null;
        $settings = null;

        if ($this->user) {
            $userAppsOrder = $this->config->getUserValueArray($this->user, 'apps-order', '[]');
            $userTopMenuApps = $this->config->getUserValueArray($this->user, 'top-menu-apps', '[]');
            $userTopSideMenuApps = $this->config->getUserValueArray($this->user, 'top-side-menu-apps', '[]');

            if (!empty($userTopMenuApps) && !$isForced) {
                $topMenuApps = $userTopMenuApps;
            }

            if (!empty($userTopSideMenuApps) && !$isForced) {
                $topSideMenuApps = $userTopSideMenuApps;
            }

            if (!empty($userAppsOrder) && !$isForced) {
                $appsOrder = $userAppsOrder;
            }

            $userTargetBlankMode = $this->config->getUserValueInt($this->user, 'target-blank-mode', '1');
            $userTargetBlankApps = $this->config->getUserValueArray($this->user, 'target-blank-apps', '[]');

            if (2 === $userTargetBlankMode && !$isForced) {
                $targetBlankApps = $userTargetBlankApps;
            }

            $isAvatarSet = $this->avatarManager->getAvatar($this->user->getUID())->exists();

            if ($useAvatar && $isAvatarSet) {
                $avatar = $this->urlGenerator->linkToRoute('core.avatar.getAvatar', [
                    'userId' => $this->user->getUID(),
                    'size' => 128,
                    'v' => $this->config->getUserValueInt($this->user, 'avatar', 'version', 0),
                ]);
            }

            if ($this->config->getAppValueBool('show-settings', '0')) {
                $settingsNav = $this->navigationManager->getAll('settings');

                if (isset($settingsNav['settings'])) {
                    $settings = [
                        'href' => $settingsNav['settings']['href'],
                        'name' => $settingsNav['settings']['name'],
                        'avatar' => $this->urlGenerator->linkToRoute('core.avatar.getAvatar', [
                            'userId' => $this->user->getUID(),
                            'size' => 32,
                            'v' => $this->config->getUserValueInt($this->user, 'avatar', 'version', 0),
                        ]),
                    ];
                }
            }
        }

        $indexUrl = $this->urlGenerator->linkTo('', 'index.php');

        return [
            'opener-position' => $this->config->getAppValue('opener-position', 'before'),
            'opener-hover' => $this->config->getAppValueBool('opener-hover', '0'),
            'external-sites-in-top-menu' => $this->config->getAppValueBool('external-sites-in-top-menu', '0'),
            'force-light-icon' => $this->config->getAppValueBool('force-light-icon', '0'),
            'display-logo' => $this->config->getAppValueBool('display-logo', '1'),
            'use-avatar' => $this->config->getAppValueBool('use-avatar', '0'),
            'hide-when-no-apps' => $this->config->getAppValueBool('hide-when-no-apps', '0'),
            'loader-enabled' => $this->config->getAppValueBool('loader-enabled', '1'),
            'always-displayed' => $this->config->getAppValueBool('always-displayed', '0'),
            'side-with-categories' => $this->config->getAppValueBool('side-with-categories', '0'),
            'big-menu' => $this->config->getAppValueBool('big-menu', '0'),
            'big-menu-hidden-apps' => $this->config->getAppValueArray('big-menu-hidden-apps', '[]'),
            'apps-order' => $appsOrder,
            'avatar' => $avatar,
            'top-menu-apps' => $topMenuApps,
            'top-side-menu-apps' => $topSideMenuApps,
            'top-menu-mouse-over-hidden-label' => $this->config->getAppValueInt(
                'top-menu-mouse-over-hidden-label',
                '0'
            ),
            'target-blank-apps' => $targetBlankApps,
            'settings' => $settings,
            'logo' => $this->themingDefaults->getLogo(),
            'logo-link' => $addLogoLink ? $indexUrl : null,
        ];
    }
}
