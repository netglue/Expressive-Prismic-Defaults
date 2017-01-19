<?php
/**
 * This file is part of the Expressive Prismic Defaults Package
 * Copyright 2016 Net Glue Ltd (https://netglue.uk).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace ExpressivePrismic;

/**
 * Module config provider
 *
 * @package ExpressivePrismic
 */
class DefaultsConfigProvider
{

    /**
     * @return array
     */
    public function __invoke() : array
    {
        return [

            'dependencies' => $this->getDependencyConfig(),

            'view_helpers' => $this->getViewHelperConfig(),

            'prismic' => [

                /**
                 * A bookmark that points to a document used for site-wide configuration
                 */
                'user_config_bookmark' => null,

                /**
                 * Search Service Settings
                 */
                'search' => [
                    /**
                     * Provide document types to restrict the search service to
                     * You should only list the document types that
                     * a) you want to be found in search
                     * b) are routable
                     */
                    'types' => [
                        // 'article',
                        // 'page',
                        // 'case-study',
                        // etc…
                    ],
                    /**
                     * Configuration for the search middleware
                     */
                    'config' => [
                        // The number of results per page
                        'default_per_page' => 10,
                        // The query parameter to look for
                        'query_param' => 'q',
                        // The parameter to look for the page number
                        'page_param' => 'page',
                        // The parameter to look for the results per page
                        'per_page_param' => 'per_page',
                    ],

                ],

                /**
                 * Template map for the ContentSlices view helper
                 */
                'slice_templates' => [
                    // 'sliceLabel' => 'templateName'
                ],

                /**
                 * Automatic but naive retrieval of various head meta tags and elements
                 */
                'head' => [
                    /**
                     * A map where <meta> name -> document property, without the type prefix
                     * ie. to achieve <meta name="description" content="foo">, you would set
                     * 'description' => 'my_property', not 'my_type.my_property'
                     *
                     * Acceptable meta tags can be found in ExpressivePrismic\View\MetaDataExtractor
                     */
                    'meta_data_map' => [
                        // For example…
                        //'description' => 'meta_description',
                        //'keywords' => 'meta_keywords',
                        //'robots' => 'meta_robots',
                    ],
                    /**
                     * Setting the head title is a little more flexible,
                     * You can provide an array of document properties to search in order of preference
                     */
                    'title_search' => [
                        // For example…
                        // 'head_title',
                        // 'meta_title',
                        // 'title'
                        // etc…
                    ],
                    /**
                     * As with normal meta tags, but specific to Twitter Cards
                     */
                    'twitter_map' => [
                        // 'twitter:card' => 'my_card_type_property',
                        // 'twitter:title' => 'twitter_title',
                    ],
                    /**
                     * Open Graph
                     */
                    'og_map' => [
                        // 'og:title' => 'my_title_property',
                        // etc…
                    ],

                ],
            ],
        ];
    }

    /**
     * Return config specific to Zend View Helpers
     * @return array
     */
    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                Service\UserConfig::class        => Service\Factory\UserConfigFactory::class,
                View\Helper\ContentSlices::class => View\Helper\Factory\ContentSlicesFactory::class,
                View\Helper\Finder::class        => View\Helper\Factory\FinderFactory::class,
                View\Helper\LinkResolver::class  => View\Helper\Factory\LinkResolverFactory::class,
            ],
            'aliases' => [
                'prismicConfig' => Service\UserConfig::class,
                'contentSlices' => View\Helper\ContentSlices::class,
                'prismicFinder' => View\Helper\Finder::class,
                'linkResolver'  => View\Helper\LinkResolver::class,
            ],
        ];
    }

    /**
     * Return Dependency Config
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'factories' => [

                /**
                 * Service that returns values stored in a single configuration document
                 */
                Service\UserConfig::class => Service\Factory\UserConfigFactory::class,

                /**
                 * A Service for performing keyword searches of repository documents
                 */
                Service\SearchService::class => Service\Factory\SearchServiceFactory::class,

                /**
                 * Service to aid automation of setting regular web page meta data retrieved from
                 * Prismic document. Only much use with Zend View
                 */
                Service\MetaDataAutomator::class                => Service\Factory\MetaDataAutomatorFactory::class,


                /**
                 * Middleware that consumes the meta data automator service and uses it for the current document
                 */
                Middleware\MetaDataAutomatorMiddleware::class   => Middleware\Factory\MetaDataAutomatorMiddlewareFactory::class,

                /**
                 * Middleware that sets the canonical URL along with og:url, twitter:url etc
                 * using the link resolver and server url helpers
                 */
                Middleware\SetCanonical::class                  => Middleware\Factory\SetCanonicalFactory::class,

                /**
                 * Middleware that processes search queries and adds the results as an attribute of the request
                 */
                Middleware\SearchMiddleware::class              => Middleware\Factory\SearchMiddlewareFactory::class,

                /**
                 * Middleware that renders a template for search results pages
                 */
                Middleware\SearchTemplateAction::class          => Middleware\Factory\SearchTemplateActionFactory::class,
            ],
        ];
    }

}
