<?php
declare(strict_types=1);

namespace ExpressivePrismic\View\Helper\Factory;

use Interop\Container\ContainerInterface;

use Prismic;
use ExpressivePrismic\View\Helper\Finder;

class FinderFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : Finder
    {
        $api = $container->get(Prismic\Api::class);

        return new Finder($api);
    }

}
