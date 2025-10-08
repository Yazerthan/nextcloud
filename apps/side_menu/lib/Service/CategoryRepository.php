<?php

namespace OCA\SideMenu\Service;

use OC\App\AppStore\Fetcher\CategoryFetcher;
use OCA\SideMenu\AppInfo\Application;
use OCP\IConfig;
use OCP\IUserSession;
use OCP\L10N\IFactory;

/**
 * class CategoryRepository.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class CategoryRepository
{
    public function __construct(
        protected CategoryFetcher $categoryFetcher,
        protected ConfigProxy $config,
        protected IConfig $iConfig,
        protected IFactory $l10nFactory,
        protected IUserSession $userSession,
    ) {
    }

    /**
     * Retrieves categories.
     */
    public function getOrderedCategories(): array
    {
        $currentLanguage = substr($this->l10nFactory->findLanguage(), 0, 2);
        $type = $this->config->getAppValue('categories-order-type', 'default');
        $order = $this->config->getAppValueArray('categories-order', '[]');
        $categoriesLabels = $this->config->getAppValueArray('cache-categories', '[]');
        $customCategories = $this->config->getAppValueArray('categories-custom', '[]');

        if (empty($categoriesLabels)) {
            $categoriesLabels = $this->categoryFetcher->get();

            $this->iConfig->setAppValue(Application::APP_ID, 'cache-categories', json_encode($categoriesLabels));
        }

        foreach ($categoriesLabels as $k => $category) {
            $categoriesLabels[$category['id']] = $category['translations'][$currentLanguage]['name'] ??
                $category['translations']['en']['name'];

            unset($categoriesLabels[$k]);
        }

        $categoriesLabels['external_links'] = $this->l10nFactory->get('external')->t('External sites');
        $categoriesLabels['other'] = '';

        $user = $this->userSession->getUser();

        if ($user) {
            $lang = $this->iConfig->getUserValue($user->getUid(), 'core', 'lang');
        } else {
            $lang = 'en';
        }

        foreach ($customCategories as $category) {
            $categoriesLabels[$category['id']] = $category[$lang] ?? $category['en'];
        }

        asort($categoriesLabels);

        if ('custom' === $type) {
            $ordered = [];

            foreach ($order as $id) {
                if (isset($categoriesLabels[$id])) {
                    $ordered[$id] = $categoriesLabels[$id];
                }
            }

            foreach ($categoriesLabels as $id => $value) {
                if (!isset($ordered[$id])) {
                    $ordered[$id] = $value;
                }
            }

            $categoriesLabels = $ordered;
        }

        return $categoriesLabels;
    }
}
