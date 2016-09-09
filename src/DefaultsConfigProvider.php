<?php
declare(strict_types=1);
namespace ExpressivePrismic;

class DefaultsConfigProvider
{

    public function __invoke() : array
    {
        return [
            'prismic' => [
                'user_config_bookmark' => null,
            ],

            'dependencies' => [
                'factories' => [
                    Service\UserConfig::class => Service\Factory\UserConfigFactory::class,
                ],
            ],
        ];
    }

}
