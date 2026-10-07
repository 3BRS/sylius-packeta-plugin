<?php

declare(strict_types=1);

use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Tests\ThreeBRS\SyliusPacketaPlugin\Behat\Context\Setup\ShippingMethodContext;
use Tests\ThreeBRS\SyliusPacketaPlugin\Behat\Context\Ui\Admin\ManagingShippingMethodContext;
use Tests\ThreeBRS\SyliusPacketaPlugin\Behat\Context\Ui\Shop\ManagingPacketaContext;
use Tests\ThreeBRS\SyliusPacketaPlugin\Behat\Page\Admin\ShippingMethod\UpdatePage;
use Tests\ThreeBRS\SyliusPacketaPlugin\Behat\Page\Admin\ShippingMethod\UpdatePageInterface;
use Tests\ThreeBRS\SyliusPacketaPlugin\Behat\Page\Shop\Packeta\PacketaPages;
use Tests\ThreeBRS\SyliusPacketaPlugin\Behat\Page\Shop\Packeta\PacketaPagesInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->public();

    $services->set(UpdatePageInterface::class, UpdatePage::class)
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args([
            'sylius_admin_channel_update',
            service(AutocompleteHelperInterface::class),
        ]);

    $services->set('sylius_packeta_plugin.context.ui.admin.shipping_method', ManagingShippingMethodContext::class)
        ->args([
            service(UpdatePageInterface::class),
            service('sylius.behat.notification_checker.admin'),
        ]);

    $services->set('sylius_packeta_plugin.context.setup.shipping_method', ShippingMethodContext::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.behat.shared_storage'),
            service('sylius.behat.context.setup.zone'),
        ]);

    $services->set('sylius_packeta_plugin.context.ui.shop.managing_packeta_context', ManagingPacketaContext::class)
        ->args([
            service(PacketaPagesInterface::class),
            service('sylius.behat.context.ui.shop.checkout.shipping'),
            service('behat.mink.default_session'),
            service('router'),
        ]);

    $services->set(PacketaPagesInterface::class, PacketaPages::class)
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args([
            'sylius_admin_channel_update',
            service(AutocompleteHelperInterface::class),
        ]);
};
