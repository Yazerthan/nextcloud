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

use OC\URLGenerator;
use OCA\SideMenu\Service\AppRepository;
use OCA\SideMenu\Service\CategoryRepository;
use OCA\SideMenu\Service\ConfigProxy;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\L10N\IFactory;

class NavController extends Controller
{
    public function __construct(
        string $appName,
        IRequest $request,
        protected ConfigProxy $config,
        protected AppRepository $appRepository,
        protected CategoryRepository $categoryRepository,
        protected URLGenerator $router,
        protected IFactory $l10nFactory,
    ) {
        parent::__construct($appName, $request);
    }

    #[NoCSRFRequired]
    #[NoAdminRequired]
    #[PublicPage]
    #[FrontpageRoute(verb: 'GET', url: '/nav/items')]
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
        $categoriesLabels = $this->categoryRepository->getOrderedCategories();
        $hiddenApps = $this->config->getAppValueArray('big-menu-hidden-apps', '[]');
        $isForced = $this->config->getAppValueBool('force', '0');
        $topMenuApps = $this->config->getAppValueArray('top-menu-apps', '[]');
        $topSideMenuApps = $this->config->getAppValueArray('top-side-menu-apps', '[]');
        $userTopSideMenuApps = $this->config->getUserValueArray($user, 'top-side-menu-apps', '[]');
        $userTopMenuApps = $this->config->getUserValueArray($user, 'top-menu-apps', '[]');
        $appsCategories = [];
        $categoriesAppsCount = [];

        if (!$isForced && !empty($userTopMenuApps)) {
            $topMenuApps = $userTopMenuApps;
        }

        if (!$isForced && !empty($userTopSideMenuApps)) {
            $topSideMenuApps = $userTopSideMenuApps;
        }

        foreach ($apps as $app) {
            $inTopMenuApps = in_array($app['id'], $topMenuApps);
            $inTopSideMenuApps = in_array($app['id'], $topSideMenuApps);
            $inHiddenApps = in_array($app['id'], $hiddenApps);

            if (($inTopMenuApps && !$inTopSideMenuApps) || $inHiddenApps) {
                continue;
            }

            $categories = (array) $app['category'];
            $appsCategories[$app['id']] = [];

            if (empty($categories)) {
                $categories = ['other'];
            }

            foreach ($categories as $category) {
                if (!isset($items[$category])) {
                    $items[$category] = [
                        'name' => $categoriesLabels[$category] ?? $category,
                        'categoryId' => $category,
                        'apps' => [],
                    ];
                }

                if (!isset($categoriesAppsCount[$category])) {
                    $categoriesAppsCount[$category] = 0;
                }

                ++$categoriesAppsCount[$category];

                $appsCategories[$app['id']][] = $category;

                $items[$category]['apps'][$app['id']] = [
                    'id' => $app['id'],
                    'name' => $app['name'],
                    'href' => $app['href'],
                    'icon' => $app['icon'],
                ];
            }
        }

        arsort($categoriesAppsCount);

        $keys = array_keys($categoriesAppsCount);

        foreach ($appsCategories as $app => $appCategories) {
            $smallerIndex = count($categoriesAppsCount) - 1;

            foreach ($appCategories as $appCategory) {
                $appKey = array_keys($keys, $appCategory)[0];

                if ($appKey < $smallerIndex) {
                    $smallerIndex = $appKey;
                }
            }

            $category = $keys[$smallerIndex];

            foreach ($items as $itemCategory => $value) {
                if ($itemCategory !== $category && isset($value['apps'][$app])) {
                    unset($items[$itemCategory]['apps'][$app]);

                    if (empty($items[$itemCategory]['apps'])) {
                        unset($items[$itemCategory]);
                    }
                }
            }
        }

        foreach ($items as $category => $value) {
            if (empty($items[$category]['apps'])) {
                unset($items[$category]);
            }
        }

        usort($items, function ($a, $b) use ($categoriesLabels) {
            foreach ($categoriesLabels as $key => $value) {
                if ('other' === $a['categoryId']) {
                    return -1;
                }

                if ('other' === $b['categoryId']) {
                    return 1;
                }

                if ($a['categoryId'] === $key) {
                    return -1;
                }

                if ($b['categoryId'] === $key) {
                    return 1;
                }
            }

            return 0;
        });

        return new JSONResponse([
            'items' => $items,
        ]);
    }
}
