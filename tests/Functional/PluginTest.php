<?php

namespace sdLanguageShopSessionSyncShopware\Tests;

use sdLanguageShopSessionSyncShopware\sdLanguageShopSessionSyncShopware as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'sdLanguageShopSessionSyncShopware' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['sdLanguageShopSessionSyncShopware'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
