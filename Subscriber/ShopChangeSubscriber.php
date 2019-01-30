<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdLanguageShopSessionSyncShopware\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;

class ShopChangeSubscriber implements SubscriberInterface
{
    /** @var ContextServiceInterface */
    private $contextService;

    public function __construct(ContextServiceInterface $contextService)
    {
        $this->contextService = $contextService;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return ['Enlight_Controller_Front_RouteShutdown' => 'onRouteShutdown'];
    }

    public function onRouteShutdown(\Enlight_Controller_EventArgs $args)
    {
        $request = $args->getRequest();
        $response = $args->getResponse();

        if (null !== $request->getPost('__shop')) {
            $currentShop = $this->contextService->getShopContext()->getShop();
            // get session_id from any previous shop but current
            foreach ($request->getCookie() as $cookieKey => $cookieValue) {
                if (preg_match('/^session-((?!' . $currentShop->getId() . ').)/', $cookieKey)) {
                    $cookiePath = rtrim((string) $currentShop->getPath(), '/') . '/';
                    $response->removeCookie($cookieKey, $currentShop->getPath());
                    $response->setCookie('session-' . $request->getPost('__shop'), $cookieValue, 0, $cookiePath);
                }
            }
        }
    }
}
