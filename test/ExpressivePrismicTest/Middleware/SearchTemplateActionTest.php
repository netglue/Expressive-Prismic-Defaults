<?php

namespace ExpressivePrismicTest\Middleware;

use ExpressivePrismic\Middleware\SearchTemplateAction;
use ExpressivePrismic\Middleware\SearchMiddleware;
use Zend\Diactoros\ServerRequest;
use Prismic\Document;
use ExpressivePrismic\LinkResolver;
use Zend\Expressive\ZendView\ZendViewRenderer;
use Zend\Expressive\Delegate\NotFoundDelegate;
use Zend\Diactoros\Response\HtmlResponse;

class SearchTemplateActionTest extends \PHPUnit_Framework_TestCase
{


    private $renderer;

    private $action;

    private $document;

    private $delegate;

    private $linkResolver;

    public function setUp()
    {
        $this->linkResolver = $this->createMock(LinkResolver::class);
        $this->renderer = $this->createMock(ZendViewRenderer::class);

        $this->action = new SearchTemplateAction($this->renderer, $this->linkResolver);
        $this->document = Document::parse(json_decode(file_get_contents(__DIR__ . '/../../fixtures/document.json')));
        $this->delegate = $this->createMock(NotFoundDelegate::class);
    }

    public function testDelegateProcessesWhenNoDocumentIsPresent()
    {
        $request = new ServerRequest;
        $this->delegate->expects($this->once())
                       ->method('process')
                       ->with($request);

        $this->action->process($request, $this->delegate);
    }

    public function testRenderIsCalledWithExpectedViewVariables()
    {
        $request = new ServerRequest;
        $request = $request->withAttribute(Document::class, $this->document);
        $request = $request->withAttribute('template', 'templateName');
        $request = $request->withAttribute(SearchMiddleware::class, ['some' => 'data']);

        $expectedViewVariables = function($subject) {
            if (!is_array($subject)) {
                return false;
            }
            if (!array_key_exists('some', $subject)) {
                return false;
            }
            if ($subject['document'] !== $this->document) {
                return false;
            }
            if ($subject['linkResolver'] !== $this->linkResolver) {
                return false;
            }
            return true;
        };

        $this->renderer->expects($this->once())
                       ->method('render')
                       ->with(
                            $this->equalTo('templateName'),
                            $this->callback($expectedViewVariables)
                        )
                       ->willReturn('Some Text');

        $response = $this->action->process($request, $this->delegate);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }
}
