<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPacketaPlugin\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Sylius Behat SecurityService stores auth tokens only in the HTTP session.
 * AddItemToCartHandler checks TokenStorage (in-memory) via UserContextInterface.
 * This hook populates TokenStorage before each scenario so that command bus
 * handlers see the logged-in user.
 */
final class TokenStorageResetContext implements Context
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly SharedStorageInterface $sharedStorage,
    ) {
    }

    /** @AfterStep */
    public function populateTokenStorageAfterLogin(): void
    {
        if ($this->tokenStorage->getToken() !== null) {
            return;
        }

        if (!$this->sharedStorage->has('user')) {
            return;
        }

        $user = $this->sharedStorage->get('user');
        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $this->tokenStorage->setToken(
            new UsernamePasswordToken($user, 'shop', $user->getRoles()),
        );
    }

    /** @BeforeScenario */
    public function resetTokenStorage(BeforeScenarioScope $scope): void
    {
        $this->tokenStorage->setToken(null);
    }
}
