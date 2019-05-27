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
use Psr\Log\LoggerInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;

class ShopChangeSubscriber implements SubscriberInterface
{
    /** @var ContextServiceInterface */
    private $contextService;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ContextServiceInterface $contextService,
        LoggerInterface $logger
    ) {
        $this->contextService = $contextService;
        $this->logger = $logger;
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
            $this->logger->debug('[LANGUAGESHOPSESSIONSYNC] It is a shop change request');
            $currentShop = $this->contextService->getShopContext()->getShop();
            $newShopId = $this->getNewShopId($request);
            // get session_id from any previous shop but current
            foreach ($request->getCookie() as $cookieKey => $cookieValue) {
                if (\preg_match('/^session-((?!' . $currentShop->getId() . ').)/', $cookieKey)) {
                    $currentUri = $this->getUri($request);
                    $this->logger->debug(\sprintf('[LANGUAGESHOPSESSIONSYNC] Found cookie (%s) to check and use it for the new shop (%s)', $cookieKey, $newShopId));
                    $cookiePath = \rtrim((string) $currentShop->getPath(), '/') . '/';
                    // reset the cookie so only one valid cookie will be set IE11 fix
                    $response->setCookie($cookieKey, '', 1);
                    $response->setCookie('session-' . $newShopId, $cookieValue, 0, $cookiePath);
                    // perform redirect to enforce a complete session (re)load
                    $response->setRedirect($currentUri);
                }
            }
        }
    }

    private function getUri(Enlight_Controller_Request_Request $request): string
    {
        $currentUri = $request->getRequestUri();

        if ($this->isHomepage($currentUri)) {
            $currentUri = '/';
        }

        return $currentUri;
    }

    private function isHomepage(string $currentUri)
    {
        return \preg_match('/__shop=/', $currentUri);
    }

    private function isShopChangeRequest(
        Enlight_Controller_Request_Request $request
    ) {
        if (true === $request->isPost() && null !== $request->getPost('__shop')) {
            return true;
        }

        if (true === $request->isGet() && null !== $request->getQuery('__shop')) {
            return true;
        }

        return false;
    }

    private function getNewShopId(
        Enlight_Controller_Request_Request $request
    ) {
        if (true === $request->isPost()) {
            return $request->getPost('__shop');
        }

        if (true === $request->isGet()) {
            return $request->getQuery('__shop');
        }

        return null;
    }
}
