<?php
declare(strict_types=1);

namespace ExpressivePrismic\Service\Factory;
use Interop\Container\ContainerInterface;
use ExpressivePrismic\Service\SearchService;
use Prismic;
use ExpressivePrismic\Paginator\PaginatorFactoryInterface;

class SearchServiceFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : SearchService
    {
        $config = $container->get('config');
        $options = isset($config['prismic']) ? $config['prismic'] : [];

        $types = $options['search']['types'] ? $options['search']['types'] : [];

        $api = $container->get(Prismic\Api::class);
        $factory = null;

        if ($container->has(PaginatorFactoryInterface::class)) {
            $factory = $container->get(PaginatorFactoryInterface::class);
        }

        return new SearchService($api, $types, $factory);
    }

}
