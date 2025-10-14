<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPacketaPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutShippingContext;
use Symfony\Component\Routing\RouterInterface;
use Tests\ThreeBRS\SyliusPacketaPlugin\Behat\Page\Shop\Packeta\PacketaPagesInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingPacketaContext implements Context
{
    public function __construct(
        private PacketaPagesInterface   $packetaPages,
        private CheckoutShippingContext $checkoutShippingContext,
        private Session                 $session,
        private RouterInterface         $router,
    ) {
    }

    /**
     * @Then I should not be able to go to the payment step again
     */
    public function iShouldNotBeAbleToGoToThePaymentStepAgain()
    {
        Assert::throws(function () {
            $this->checkoutShippingContext->iShouldBeAbleToGoToThePaymentStepAgain();
        }, UnexpectedPageException::class);
    }

    /**
     * @Then I select :packetaName Packeta branch
     */
    public function iSelectPacketaBranch(string $packetaName)
    {
        $this->packetaPages->selectPacketaBranch(['id' => 1, 'place' => $packetaName]);
    }

    /**
     * @Given I see Packeta branch instead of shipping address
     */
    public function iSeePacketaBranchInsteadOfShippingAddress()
    {
        Assert::true($this->packetaPages->iSeePacketaBranchInsteadOfShippingAddress());
    }

    /**
     * @When I try to complete the shipping step without selected Packeta branch
     */
    public function iTryToCompleteTheShippingStepWithoutSelectedPacketaBranch()
    {
        $this->checkoutShippingContext->iCompleteTheShippingStep();
    }

    /**
     * @Then I should still be on the checkout shipping step
     */
    public function iShouldStillBeOnTheCheckoutShippingStep()
    {
        $currentUrl = $this->session->getCurrentUrl();
        $currentPath = parse_url($currentUrl, PHP_URL_PATH);
        Assert::string($currentPath, sprintf('Current URL "%s" is not valid.', $currentUrl));

        $expectedPath = $this->router->generate(
            'sylius_shop_checkout_select_shipping',
            ['_locale' => 'en_US'],
            RouterInterface::ABSOLUTE_PATH,
        );

        Assert::same(
            $currentPath,
            $expectedPath,
            sprintf('Expected to be on path %s, but current URL is: %s', $expectedPath, $currentUrl),
        );
    }

    /**
     * @Then I should be notified that Packeta branch is required
     */
    public function iShouldBeNotifiedThatPacketaBranchIsRequired()
    {
        $genericErrorMessage = $this->packetaPages->getGenericValidationMessageForShipment();
        Assert::notEmpty(
            $genericErrorMessage,
            'Expected to see a validation error message, but got none.',
        );
        Assert::contains(
            strtolower($genericErrorMessage),
            'packeta',
            sprintf('Expected generic error message to mention Packeta, but got: "%s"', $genericErrorMessage),
        );

        $packetaErrorMessage = $this->packetaPages->getPacketaValidationMessageForShipment();
        Assert::notEmpty(
            $packetaErrorMessage,
            'Expected to see a validation error message, but got none.',
        );
        Assert::contains(
            strtolower($packetaErrorMessage),
            'packeta',
            sprintf('Expected packeta error message to mention Packeta, but got: "%s"', $packetaErrorMessage),
        );
    }
}
