<?php

namespace nlxLanguageShopSessionSyncShopware\Tests;

use nlxLanguageShopSessionSyncShopware\nlxLanguageShopSessionSyncShopware as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'nlxLanguageShopSessionSyncShopware' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['nlxLanguageShopSessionSyncShopware'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
