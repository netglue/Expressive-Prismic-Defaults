<?php
declare(strict_types=1);

/**
 * If there are potentially multiple routes that can display your content,
 * this middleware will use the link resolver to set a canonical link
 * for the requested document.
 */

namespace ExpressivePrismic\Middleware;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\View\HelperPluginManager;
use Zend\View\Helper\HeadLink;
use Zend\View\Helper\HeadMeta;
use Zend\View\Helper\Doctype;
use Prismic;
use Zend\Expressive\Helper\ServerUrlHelper;

class SetCanonical implements MiddlewareInterface
{

    /**
     * @var Prismic\LinkResolver
     */
    private $linkResolver;

    /**
     * @var HelperPluginManager
     */
    private $helpers;

    /**
     * @var ServerUrlHelper
     */
    private $serverUrl;

    public function __construct(Prismic\LinkResolver $resolver, HelperPluginManager $helpers, ServerUrlHelper $serverUrl)
    {
        $this->linkResolver = $resolver;
        $this->helpers = $helpers;
        $this->serverUrl = $serverUrl;
    }

    /**
     * @param  Request           $request
     * @param  DelegateInterface $delegate
     * @return Response
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        if ($document = $request->getAttribute(Prismic\Document::class)) {
            $canonical = $this->serverUrl->generate($this->linkResolver->resolveDocument($document));

            $helper = $this->helpers->get(HeadLink::class);
            $helper([
                'rel' => 'canonical',
                'href' => $canonical,
            ]);
            $doctype = $this->helpers->get(Doctype::class);
            $doctype($doctype::HTML5);
            $meta = $this->helpers->get(HeadMeta::class);
            $meta->setProperty('og:url', $canonical);
            $meta->setName('twitter:url', $canonical);
            $meta->setItemprop('url', $canonical);
        }

        return $delegate->process($request);
    }

}
