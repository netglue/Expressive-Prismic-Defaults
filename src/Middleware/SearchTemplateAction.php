<?php

/**
 * Middleware that renders a template when a search has been performed
 */

namespace ExpressivePrismic\Middleware;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Prismic;

class SearchTemplateAction implements MiddlewareInterface
{

    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    /**
     * @var Prismic\LinkResolver
     */
    private $linkResolver;

    public function __construct(TemplateRendererInterface $renderer, Prismic\LinkResolver $linkResolver)
    {
        $this->renderer = $renderer;
        $this->linkResolver = $linkResolver;
    }

    /**
     * @param  Request           $request
     * @param  DelegateInterface $delegate
     * @return Response
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        $template = $request->getAttribute('template');
        $document = $request->getAttribute(Prismic\Document::class);

        if (!$document) {
            return $delegate->process($request);
        }

        $view = [
            'document' => $document,
            'linkResolver' => $this->linkResolver,
        ];

        $search = $request->getAttribute(SearchMiddleware::class, null);

        if ($search) {
            $view = array_merge($view, $search);
        }

        return new HtmlResponse($this->renderer->render($template, $view));
    }

}
