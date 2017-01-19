<?php
declare(strict_types=1);
namespace ExpressivePrismic\Middleware\Factory;

use Interop\Container\ContainerInterface;
use ExpressivePrismic\Middleware\SearchMiddleware;
use ExpressivePrismic\Service\SearchService;

class SearchMiddlewareFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : SearchMiddleware
    {
        if (!$container->has(SearchService::class)) {
            throw new \RuntimeException('SearchService cannot be located in the container');
        }

        $config = $container->get('config');
        $config = isset($config['prismic']['search']['config'])
                ? $config['prismic']['search']['config']
                : [];

        $service = $container->get(SearchService::class);

        return new SearchMiddleware($service, $config);
    }

}
