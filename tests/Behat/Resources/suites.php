<?php

declare(strict_types=1);

use Behat\Config\Config;

return (new Config())
    ->import([
        'suites/set_apikey_to_shipping_method.php',
        'suites/admin_sees_packeta_branch_in_order.php',
        'suites/select_packeta_branch_on_checkout.php',
    ]);
