<?php
declare(strict_types=1);

namespace ExpressivePrismic\View\Helper\Factory;

use Interop\Container\ContainerInterface;

use Zend\Expressive\Template\TemplateRendererInterface;
use ExpressivePrismic\View\Helper\ContentSlices;
use ExpressivePrismic\Service\CurrentDocument;

class ContentSlicesFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : ContentSlices
    {
        $config = $container->get('config');

        $renderer = $container->get(TemplateRendererInterface::class);
        $documentRegistry = $container->get(CurrentDocument::class);
        return new ContentSlices($templates, $renderer, $documentRegistry);
    }

}
