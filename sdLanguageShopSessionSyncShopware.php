<?php

namespace sdLanguageShopSessionSyncShopware;

use Shopware\Components\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

# This vendor/autoload.php is only needed if there are own requirements defined
# in the composer.json and this plugin is installed via zip distribution
/*if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}*/

/**
 * Shopware-Plugin sdLanguageShopSessionSyncShopware.
 */
class sdLanguageShopSessionSyncShopware extends Plugin
{

    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('sdlanguageshopsessionsyncshopware.plugin_dir', $this->getPath());
        parent::build($container);
    }
}
