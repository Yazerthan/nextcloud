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

use OCA\SideMenu\Service\AppRepository;
use OCA\SideMenu\Service\ConfigProxy;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUserSession;

class AppController extends Controller
{
    public function __construct(
        string $appName,
        IRequest $request,
        protected AppRepository $appRepository,
        protected IURLGenerator $urlGenerator,
        protected ConfigProxy $config,
    ) {
        parent::__construct($appName, $request);
    }

    #[NoCSRFRequired]
    #[NoAdminRequired]
    #[FrontpageRoute(verb: 'GET', url: '/')]
    public function index(): RedirectResponse
    {
        $user = \OC::$server[IUserSession::class]->getUser();
        $topMenuApps = $this->config->getAppValueArray('top-menu-apps', '[]');
        $hiddenApps = $this->config->getAppValueArray('big-menu-hidden-apps', '[]');
        $isForced = $this->config->getAppValueBool('force', '0');
        $userTopMenuApps = $this->config->getUserValueArray($user, 'top-menu-apps', '[]');
        $apps = $this->appRepository->getOrderedApps($user);

        if (!$isForced && !empty($userTopMenuApps)) {
            $topMenuApps = $userTopMenuApps;
        }

        foreach ($apps as $app) {
            $inTopMenuApps = in_array($app['id'], $topMenuApps);
            $inHiddenApps = in_array($app['id'], $hiddenApps);

            if (!$inTopMenuApps && $inHiddenApps) {
                continue;
            }

            return $this->redirectToApp($app, true);
        }

        return $this->redirectToApp('files');
    }

    protected function redirectToApp($app, bool $isHref = false): RedirectResponse
    {
        if (!$isHref) {
            $isIgnoreFrontController = true === \OC::$server->getConfig()->getSystemValue(
                'htaccess.IgnoreFrontController',
                false
            );

            $isFrontControllerActive = 'true' === getenv('front_controller_active');

            if ($isIgnoreFrontController || $isFrontControllerActive) {
                $path = '/apps/%s/';
            } else {
                $path = '/index.php/apps/%s/';
            }

            $url = $this->urlGenerator->getAbsoluteURL(sprintf($path, $app));
        } else {
            $url = $app['href'];
        }

        return new RedirectResponse($url);
    }
}
