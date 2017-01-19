<?php

/**
 * Middleware that renders a template when a search has been performed
 */

namespace ExpressivePrismic\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Prismic;

class SearchTemplateAction
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

    public function __invoke(Request $request, Response $response, callable $next = null) : Response
    {
        $template = $request->getAttribute('template');
        $document = $request->getAttribute(Prismic\Document::class);

        if (!$document && $next) {
            return $next($request, $response);
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
