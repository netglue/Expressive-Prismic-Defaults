<?php
declare(strict_types=1);

namespace ExpressivePrismic\View\Twig\Factory;

use Interop\Container\ContainerInterface;

use ExpressivePrismic\Service\UserConfig;
use ExpressivePrismic\View\Twig\UserConfigExtension;

class UserConfigExtensionFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : UserConfigExtension
    {
        $config           = $container->get(UserConfig::class);

        return new UserConfigExtension($config);
    }

}
