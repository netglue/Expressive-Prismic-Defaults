<?php
declare(strict_types=1);

namespace ExpressivePrismic\Middleware\Factory;

use Interop\Container\ContainerInterface;

use Zend\Expressive\Template\TemplateRendererInterface;
use ExpressivePrismic\Middleware\SearchTemplateAction;
use ExpressivePrismic\LinkResolver;

class SearchTemplateActionFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : SearchTemplateAction
    {
        $renderer = $container->get(TemplateRendererInterface::class);
        $linkResolver = $container->get(LinkResolver::class);
        return new SearchTemplateAction($renderer, $linkResolver);
    }
}
