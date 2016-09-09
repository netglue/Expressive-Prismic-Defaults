<?php
declare(strict_types=1);

namespace ExpressivePrismic\Service\Factory;
use Interop\Container\ContainerInterface;
use ExpressivePrismic\Service\UserConfig;
use Prismic;

class UserConfigFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : UserConfig
    {
        $config = $container->get('config');
        $options = isset($config['prismic']) ? $config['prismic'] : [];
        if (empty($options['user_config_bookmark'])) {
            throw new \RuntimeException(sprintf(
                'An instance of %s can not be created because no prismic bookmark has been provided in the key [prismic][user_config_bookmark]',
                UserConfig::class
            ));
        }

        $api          = $container->get(Prismic\Api::class);
        $linkResolver = $container->get(Prismic\LinkResolver::class);
        return new UserConfig($api, $bookmark, $linkResolver);
    }

}
