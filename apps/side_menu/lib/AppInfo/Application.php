<?php

namespace OCA\SideMenu\AppInfo;

use OC\AllConfig;
use OC\App\AppStore\Fetcher\CategoryFetcher;
use OC\AppFramework\Http\Request;
use OC\Security\CSP\ContentSecurityPolicyNonceManager;
use OC\User\User;
use OCA\SideMenu\Service\AppRepository;
use OCA\SideMenu\Service\CategoryRepository;
use OCA\SideMenu\Service\Color;
use OCA\SideMenu\Service\ConfigProxy;
use OCA\Theming\ThemingDefaults;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IConfig;
use OCP\INavigationManager;
use OCP\IUserSession;
use OCP\L10N\IFactory;
use OCP\Util;
use Psr\Container\ContainerInterface;

/**
 * class Application.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class Application extends App implements IBootstrap
{
    public const APP_ID = 'side_menu';
    public const APP_NAME = 'Custom menu';

    protected AllConfig $config;
    protected ContentSecurityPolicyNonceManager $cspnm;
    protected Request $request;
    protected ?User $user = null;

    public function __construct(array $urlParams = [])
    {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void
    {
        $context->registerService(CategoryRepository::class, function (ContainerInterface $c) {
            return new CategoryRepository(
                $c->get(CategoryFetcher::class),
                $c->get(ConfigProxy::class),
                $c->get(IConfig::class),
                $c->get(IFactory::class),
                $c->get(IUserSession::class)
            );
        });

        $context->registerService(AppRepository::class, function (ContainerInterface $c) {
            return new AppRepository(
                $c->get(\OC_App::class),
                $c->get(INavigationManager::class),
                $c->get(IFactory::class),
                $c->get(ConfigProxy::class),
                $c->get(CategoryRepository::class),
                $c->get(IEventDispatcher::class),
                $c->get(IUserSession::class)
            );
        });

        $context->registerService(ConfigProxy::class, function (ContainerInterface $c) {
            return new ConfigProxy(
                $c->get(IConfig::class),
            );
        });

        $context->registerService(Color::class, function (ContainerInterface $c) {
            return new Color(
                $c->get(ThemingDefaults::class),
            );
        });
    }

    public function boot(IBootContext $context): void
    {
        $this->config = \OC::$server->getConfig();
        $this->cspnm = \OC::$server->getContentSecurityPolicyNonceManager();
        $this->user = \OC::$server[IUserSession::class]->getUser();
        $this->request = \OC::$server->getRequest();

        if (!$this->isEnabled()) {
            return;
        }

        $this->addAssets();
    }

    protected function isEnabled(): bool
    {
        if (isset($this->request->server['HTTP_USER_AGENT']) && preg_match('/MemoriesNative/', $this->request->server['HTTP_USER_AGENT'])) {
            return false;
        }

        $enabled = true;
        $isForced = (bool) $this->config->getAppValue(self::APP_ID, 'force', '0');

        if (null !== $this->user && !$isForced) {
            $enabled = (bool) $this->config->getUserValue(
                $this->user->getUid(),
                self::APP_ID,
                'enabled',
                $this->config->getAppValue(
                    self::APP_ID,
                    'default-enabled',
                    '1'
                )
            );
        }

        return $enabled;
    }

    protected function addAssets()
    {
        Util::addScript(self::APP_ID, 'side_menu-menu');

        $assets = [
            'stylesheet' => [
                'route' => 'side_menu.Css.stylesheet',
                'type' => 'link',
                'route_attr' => 'href',
                'attr' => [
                    'rel' => 'stylesheet',
                ],
            ],
        ];

        $cache = $this->config->getAppValue(self::APP_ID, 'cache', '0');

        foreach ($assets as $value) {
            $route = \OC::$server->getURLGenerator()->linkToRoute(
                $value['route'],
                ['v' => $cache]
            );

            $value['attr'][$value['route_attr']] = $route;

            Util::addHeader($value['type'], $value['attr'], '');
        }
    }
}
