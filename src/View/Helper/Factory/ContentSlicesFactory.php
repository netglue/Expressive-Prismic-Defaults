<?php
declare(strict_types=1);

namespace ExpressivePrismic\View\Helper\Factory;

use Interop\Container\ContainerInterface;

use Zend\Expressive\Template\TemplateRendererInterface;
use ExpressivePrismic\View\Helper\ContentSlices;
use ExpressivePrismic\Service\CurrentDocument;
use Zend\View\HelperPluginManager;

class ContentSlicesFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : ContentSlices
    {
        $config           = $container->get('config');
        $templates        = isset($config['prismic']['slice_templates'])
                            ? $config['prismic']['slice_templates']
                            : [];
        $renderer         = $container->get(TemplateRendererInterface::class);
        $documentRegistry = $container->get(CurrentDocument::class);
        $helpers = $container->get(HelperPluginManager::class);

        return new ContentSlices($templates, $renderer, $documentRegistry, $helpers);
    }

}
