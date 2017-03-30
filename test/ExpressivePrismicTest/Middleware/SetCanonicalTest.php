<?php

namespace ExpressivePrismicTest\Middleware;

// Under Test:
use ExpressivePrismic\Middleware\SetCanonical;

use Zend\Diactoros\ServerRequest;

use Prismic\Document;
use ExpressivePrismic\LinkResolver;

use Zend\View\HelperPluginManager;
use Zend\View\Helper\Doctype;
use Zend\View\Helper\HeadLink;
use Zend\View\Helper\HeadMeta;
use Zend\View\Renderer\PhpRenderer;

use Zend\ServiceManager\ServiceManager;
use Zend\Expressive\Delegate\NotFoundDelegate;
use Zend\Expressive\Helper\ServerUrlHelper;

class SetCanonicalTest extends \PHPUnit_Framework_TestCase
{

    private $urlHelper;

    private $linkResolver;

    private $helpers;

    private $middleware;

    private $delegate;

    private $document;

    public function setUp()
    {
        $this->urlHelper = $this->createMock(ServerUrlHelper::class);
        $this->linkResolver = $this->createMock(LinkResolver::class);
        $this->helpers = new HelperPluginManager(new ServiceManager);
        $this->helpers->setRenderer(new PhpRenderer);
        $this->delegate = $this->createMock(NotFoundDelegate::class);
        $this->document = Document::parse(json_decode(file_get_contents(__DIR__ . '/../../fixtures/document.json')));
        $this->middleware = new SetCanonical($this->linkResolver, $this->helpers, $this->urlHelper);
    }

    public function testRequestPassesThroughToDelegateWhenDocumentIsUnset()
    {
        $request = new ServerRequest;
        $this->delegate->expects($this->once())
                       ->method('process')
                       ->with($request);
        $this->middleware->process($request, $this->delegate);
    }

    public function testViewHelpersAreCalledWhenDocumentIsSet()
    {
        $request = (new ServerRequest)->withAttribute(Document::class, $this->document);

        $this->linkResolver->expects($this->once())
                           ->method('resolveDocument')
                           ->with($this->document)
                           ->willReturn('/document-path');

        $this->urlHelper->expects($this->once())
                        ->method('generate')
                        ->with('/document-path')
                        ->willReturn('URL');

        $this->delegate->expects($this->once())
                       ->method('process')
                       ->with($request);

        $headLink = $this->createMock(HeadLink::class);
        $this->helpers->setService(HeadLink::class, $headLink);
        $headLink->expects($this->once())
                 ->method('__invoke')
                 ->with(['rel' => 'canonical', 'href' => 'URL']);


        $doctype = $this->createMock(Doctype::class);
        $this->helpers->setService(Doctype::class, $doctype);
        $doctype->expects($this->once())
                ->method('__invoke')
                ->with($this->anything());

        /**
         * We can't mock specific calls to head meta
         * because they're all magic methods. Boo.
         */
        $headMeta = $this->createMock(HeadMeta::class);
        $this->helpers->setService(HeadMeta::class, $headMeta);
        $headMeta->expects($this->exactly(3))
                 ->method('__call')
                 ->with($this->anything());


        $this->middleware->process($request, $this->delegate);
    }



}
