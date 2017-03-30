<?php

namespace ExpressivePrismicTest\Middleware;

use ExpressivePrismic\Middleware\MetaDataAutomatorMiddleware;
use ExpressivePrismic\Service\MetaDataAutomator;
use Zend\Diactoros\ServerRequest;
use Prismic\Document;

class MetaDataAutomatorMiddlewareTest extends \PHPUnit_Framework_TestCase
{

    private $automator;

    private $middleware;

    private $document;

    public function setUp()
    {
        $this->automator = $this->createMock(MetaDataAutomator::class);
        $this->middleware = new MetaDataAutomatorMiddleware($this->automator);
        $this->document = Document::parse(json_decode(file_get_contents(__DIR__ . '/../../fixtures/document.json')));
    }

    public function testAutomatorNotCalledWithNoDocument()
    {
        $request = new ServerRequest;
        $delegate = new DelegateMock;

        $this->automator->expects($this->never())->method('apply');

        $this->middleware->process($request, $delegate);
    }

    public function testAutomatorCalledWhenDocumentPresent()
    {
        $request = (new ServerRequest)->withAttribute(Document::class, $this->document);
        $delegate = new DelegateMock;
        $this->automator->expects($this->once())
                        ->method('apply')
                        ->with($this->document);

        $this->middleware->process($request, $delegate);

    }
}
