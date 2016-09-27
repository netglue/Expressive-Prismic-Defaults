<?php
declare(strict_types=1);

namespace ExpressivePrismic\View\Helper\Factory;

use Interop\Container\ContainerInterface;

use Prismic;
use ExpressivePrismic\View\Helper\LinkResolver;

class LinkResolverFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : LinkResolver
    {
        $resolver = $container->get(Prismic\LinkResolver::class);
        return new LinkResolver($resolver);
    }

}
