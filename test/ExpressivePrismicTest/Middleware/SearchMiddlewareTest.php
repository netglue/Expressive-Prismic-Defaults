<?php

namespace ExpressivePrismicTest\Middleware;

use ExpressivePrismic\Middleware\SearchMiddleware;
use ExpressivePrismic\Service\SearchService;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Diactoros\ServerRequest;

class SearchMiddlewareTest extends \PHPUnit_Framework_TestCase
{

    private $service;

    private $pager;

    public function setUp()
    {
        $pager = $this->pager = new Paginator(new ArrayAdapter);
        $this->service = $this->createMock(SearchService::class);
        $this->service->method('search')
            ->willReturn($pager);
    }

    public function factory($config = [])
    {
        return new SearchMiddleware($this->service, $config);
    }

    public function testEmptyTermWillPassThroughToDelegate()
    {
        $request = new ServerRequest;
        $middleware = $this->factory();
        $delegate = new DelegateMock;
        $middleware->process($request, $delegate);
        $this->assertSame($request, $delegate->request);
    }

    public function testDefaultValuesAreAvailableInRequest()
    {
        $request = new ServerRequest;
        $request = $request->withAttribute('q', 'search term');
        $middleware = $this->factory();
        $delegate = new DelegateMock;
        $middleware->process($request, $delegate);

        $data = $delegate->request->getAttribute(SearchMiddleware::class);
        $this->assertSame('search term', $data['term']);
        $this->assertSame(1, $data['page']);
        $this->assertSame(10, $data['per_page']);
        $this->assertInstanceOf(Paginator::class, $data['results']);
    }

    public function testQueryParamsCanBeOverridenByConfig()
    {
        $request = new ServerRequest(
            [], // _SERVER
            [], // _FILES
            null, // URI
            'GET', // METHOD
            'php://memory', // Stream
            [], // HEADERS
            [], // _COOKIE
            [ // Query
                'searchTerm' => 'my search term',
                'numberPage' => 2,
                'perPage' => 50,
            ],
            null, // Parsed Body
            null // Protocol version
        );


        $middleware = $this->factory([
            'query_param' => 'searchTerm',
            'page_param'  => 'numberPage',
            'per_page_param'  => 'perPage',
            'default_per_page' => 100,
        ]);

        $delegate = new DelegateMock;
        $middleware->process($request, $delegate);

        $data = $delegate->request->getAttribute(SearchMiddleware::class);
        $this->assertSame('my search term', $data['term']);
        $this->assertSame(2, $data['page']);
        $this->assertSame(50, $data['per_page']);
        $this->assertInstanceOf(Paginator::class, $data['results']);

    }

}
