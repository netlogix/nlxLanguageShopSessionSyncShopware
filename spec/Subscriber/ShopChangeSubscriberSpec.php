<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdLanguageShopSessionSyncShopware\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_EventArgs;
use Enlight_Controller_Request_Request;
use Enlight_Controller_Response_ResponseHttp;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use sdLanguageShopSessionSyncShopware\Subscriber\ShopChangeSubscriber;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class ShopChangeSubscriberSpec extends ObjectBehavior
{
    public function let(
        ContextServiceInterface $contextService,
        LoggerInterface $logger,
        ShopContextInterface $shopContext,
        Shop $shop
    ) {
        $contextService
            ->getShopContext()
            ->willReturn($shopContext);

        $shopContext
            ->getShop()
            ->willReturn($shop);

        $this->beConstructedWith(
            $contextService,
            $logger
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ShopChangeSubscriber::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(SubscriberInterface::class);
    }

    public function it_can_set_previous_session_on_language_change_post_request(
        Enlight_Controller_EventArgs $args,
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_ResponseHttp $response,
        Shop $shop
    ) {
        $this->prepareArguments($args, $request, $response);

        $request->isPost()
            ->willReturn(true);
        $request->getPost('__shop')
            ->shouldBeCalled()
            ->willReturn(2);
        $request->getCookie()
            ->willReturn([
                'session-1' => 'swordfish',
                'session-2' => 'session-two',
            ]);
        $request->getRequestUri()
            ->shouldBeCalled()
            ->willReturn('/FRAMES/');

        $shop->getId()
            ->shouldBeCalled()
            ->willReturn(2);
        $shop->getPath()
            ->shouldBeCalled()
            ->willReturn('/');

        $response->setCookie('session-2', 'swordfish', 0, '/')
            ->shouldBeCalled();
        $response->setCookie('session-1', '', 1)
            ->shouldBeCalled();
        $response->setRedirect('/FRAMES/')
            ->shouldBeCalled();

        $this->onRouteShutdown($args);
    }

    public function it_wont_set_previous_session_on_language_change_post_request_if_new_language_is_same(
        Enlight_Controller_EventArgs $args,
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_ResponseHttp $response,
        Shop $shop
    ) {
        $this->prepareArguments($args, $request, $response);

        $request->isPost()
            ->willReturn(true);
        $request->getPost('__shop')
            ->shouldBeCalled()
            ->willReturn(2);
        $request->getCookie()
            ->willReturn([
                'session-2' => 'session-two',
            ]);

        $shop->getId()
            ->shouldBeCalled()
            ->willReturn(2);

        $response->setCookie(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $this->onRouteShutdown($args);
    }

    public function it_can_set_previous_session_on_language_change_through_get_request(
        Enlight_Controller_EventArgs $args,
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_ResponseHttp $response,
        Shop $shop
    ) {
        $this->prepareArguments($args, $request, $response);

        $request->isPost()
            ->shouldBeCalled()
            ->willReturn(false);
        $request->isGet()
            ->shouldBeCalled()
            ->willReturn(true);
        $request->getQuery('__shop')
            ->shouldBeCalled()
            ->willReturn(2);
        $request->getCookie()
            ->willReturn([
                'session-1' => 'swordfish',
                'session-2' => 'session-two',
            ]);
        $request->getRequestUri()
            ->shouldBeCalled()
            ->willReturn('/FRAMES/');

        $shop->getId()
            ->shouldBeCalled()
            ->willReturn(2);
        $shop->getPath()
            ->shouldBeCalled()
            ->willReturn('/');

        $response->setCookie('session-2', 'swordfish', 0, '/')
            ->shouldBeCalled();
        $response->setCookie('session-1', '', 1)
            ->shouldBeCalled();
        $response->setRedirect('/FRAMES/')
            ->shouldBeCalled();

        $this->onRouteShutdown($args);
    }

    public function it_wont_set_previous_session_on_language_change_get_request_if_new_language_is_same(
        Enlight_Controller_EventArgs $args,
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_ResponseHttp $response,
        Shop $shop
    ) {
        $this->prepareArguments($args, $request, $response);

        $request->isPost()
            ->willReturn(false);
        $request->isGet()
            ->shouldBeCalled()
            ->willReturn(true);
        $request->getQuery('__shop')
            ->shouldBeCalled()
            ->willReturn(2);
        $request->getCookie()
            ->willReturn([
                'session-2' => 'session-two',
            ]);

        $shop->getId()
            ->shouldBeCalled()
            ->willReturn(2);

        $response->setCookie(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $this->onRouteShutdown($args);
    }

    public function it_wont_set_session_on_missing_arguments(
        Enlight_Controller_EventArgs $args,
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_ResponseHttp $response
    ) {
        $this->prepareArguments($args, $request, $response);

        $response->setCookie(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $this->onRouteShutdown($args);
    }

    public function it_wont_set_session_if_it_is_not_a_get_request_nor_a_post_request(
        Enlight_Controller_EventArgs $args,
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_ResponseHttp $response
    ) {
        $this->prepareArguments($args, $request, $response);

        $request->isPost()
            ->willReturn(false);
        $request->isGet()
            ->willReturn(false);

        $response->setCookie(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $this->onRouteShutdown($args);
    }

    public function it_wont_set_session_on_empty_post_argument(
        Enlight_Controller_EventArgs $args,
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_ResponseHttp $response
    ) {
        $this->prepareArguments($args, $request, $response);

        $request->isPost()
            ->willReturn(true);
        $request->isGet()
            ->willReturn(false);
        $request->getPost('__shop')
            ->shouldBeCalled()
            ->willReturn(null);
        $request->getCookie()
            ->shouldNotBeCalled();

        $response->setCookie(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $this->onRouteShutdown($args);
    }

    public function it_wont_set_session_on_empty_get_argument(
        Enlight_Controller_EventArgs $args,
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_ResponseHttp $response
    ) {
        $this->prepareArguments($args, $request, $response);

        $request->isPost()
            ->willReturn(false);
        $request->isGet()
            ->willReturn(true);
        $request->getQuery('__shop')
            ->shouldBeCalled()
            ->willReturn(null);
        $request->getCookie()
            ->shouldNotBeCalled();

        $response->setCookie(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $this->onRouteShutdown($args);
    }

    private function prepareArguments(
        Enlight_Controller_EventArgs $args,
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_ResponseHttp $response
    ) {
        $args->getRequest()
            ->shouldBeCalled()
            ->willReturn($request);
        $args->getResponse()
            ->shouldBeCalled()
            ->willReturn($response);
    }
}
