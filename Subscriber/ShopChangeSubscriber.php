<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdLanguageShopSessionSyncShopware\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Request_Request;
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

        if (true === $this->isShopChangeRequest($request)) {
            $currentShop = $this->contextService->getShopContext()->getShop();
            // get session_id from any previous shop but current
            foreach ($request->getCookie() as $cookieKey => $cookieValue) {
                if (preg_match('/^session-((?!' . $currentShop->getId() . ').)/', $cookieKey)) {
                    $cookiePath = rtrim((string) $currentShop->getPath(), '/') . '/';
                    // reset the cookie so only one valid cookie will be set IE11 fix
                    $response->setCookie($cookieKey, '', 1);
                    $response->setCookie('session-' . $request->getPost('__shop'), $cookieValue, 0, $cookiePath);
                }
            }
        }
    }

    private function isShopChangeRequest(
        Enlight_Controller_Request_Request $request
    ) {
        if (true === $request->isPost() && null !== $request->getPost('__shop')) {
            return true;
        }

        return false;
    }
}
