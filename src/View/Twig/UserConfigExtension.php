<?php

namespace ExpressivePrismic\View\Twig;

use ExpressivePrismic\Service\UserConfig;
use Twig_Extension;
use Twig_SimpleFunction as TwigFunction;

class UserConfigExtension extends Twig_Extension
{

    /**
     * @var UserConfig
     */
    private $config;

    /**
     * Constructor
     *
     * @param UserConfig $config
     */
    public function __construct(UserConfig $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'prismicConfig';
    }


    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('prismicConfigGet', [$this->config, 'get']),
        ];
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->config, $method], $args);
    }


}
