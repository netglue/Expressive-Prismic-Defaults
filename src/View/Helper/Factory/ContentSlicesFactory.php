<?php
declare(strict_types=1);

namespace ExpressivePrismic\View\Helper\Factory;

use Interop\Container\ContainerInterface;

use ExpressivePrismic\View\Helper\ContentSlices;
use ExpressivePrismic\Service\CurrentDocument;
use Zend\View\HelperPluginManager;
use Zend\View\Helper\Partial;

/**
 * Class ContentSlicesFactory
 *
 * @package ExpressivePrismic\View\Helper\Factory
 */
class ContentSlicesFactory
{

    /**
     * {@inheritDoc}
     * @return ContentSlices
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : ContentSlices
    {
        $config           = $container->get('config');
        $templates        = isset($config['prismic']['slice_templates'])
                            ? $config['prismic']['slice_templates']
                            : [];
        $documentRegistry = $container->get(CurrentDocument::class);
        $helpers = $container->get(HelperPluginManager::class);
        $partialHelper = $helpers->get(Partial::class);

        return new ContentSlices($templates, $documentRegistry, $partialHelper);
    }

}
