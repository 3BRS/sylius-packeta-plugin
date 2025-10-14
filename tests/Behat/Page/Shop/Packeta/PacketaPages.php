<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPacketaPlugin\Behat\Page\Shop\Packeta;

use Sylius\Behat\Page\Admin\Channel\UpdatePage as BaseUpdatePage;

final class PacketaPages extends BaseUpdatePage implements PacketaPagesInterface
{
    public function selectPacketaBranch(array $packeta): void
    {
        $packetaSelect = $this->getElement('packeta_hidden_input');
        $packetaSelect->setValue(json_encode($packeta));
    }

    public function iSeePacketaBranchInsteadOfShippingAddress(): bool
    {
        $shippingAddress = $this->getElement('shippingAddress')->getText();

        return str_contains($shippingAddress, 'Packeta branch');
    }

    public function getGenericValidationMessageForShipment(): string
    {
        $validationElement = $this->getDocument()->find('css', '[data-test-validation-error]');

        return $validationElement?->getText() ?? '';
    }

    public function getPacketaValidationMessageForShipment(): string
    {
        $validationElement = $this->getDocument()->find('css', '[data-test-packeta-validation-error]');

        return $validationElement?->getText() ?? '';
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_checkout_select_shipping';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'packeta_hidden_input' => 'input[type="hidden"][name^="sylius_shop_checkout_select_shipping[shipments][0][packeta_"]',
            'shippingAddress'      => '[data-test-shipping-address]',
        ]);
    }
}
