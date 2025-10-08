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
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IUserSession;

class CoreController extends Controller
{
    public function __construct(
        string $appName,
        IRequest $request,
        protected ConfigProxy $config,
        protected AppRepository $appRepository,
    ) {
        parent::__construct($appName, $request);
    }

    #[NoCSRFRequired]
    #[NoAdminRequired]
    #[PublicPage]
    #[FrontpageRoute(verb: 'GET', url: '/core/apps')]
    public function items(): JSONResponse
    {
        $user = \OC::$server[IUserSession::class]->getUser();
        $items = [];

        if (!$user) {
            return new JSONResponse([
                'items' => $items,
            ]);
        }

        $apps = $this->appRepository->getOrderedApps($user);
        $keys = ['id', 'name', 'category', 'href', 'icon'];

        foreach ($apps as &$app) {
            foreach ($app as $key => $value) {
                if (!in_array($key, $keys)) {
                    unset($app[$key]);
                }
            }
        }

        return new JSONResponse([
            'items' => $apps,
        ]);
    }
}
