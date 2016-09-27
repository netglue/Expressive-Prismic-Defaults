<?php
namespace ExpressivePrismicTest\View\Helper;

use ExpressivePrismic\View\Helper\LinkResolver as Helper;
use ExpressivePrismic\LinkResolver as Resolver;

class LinkResolverTest extends \PHPUnit_Framework_TestCase
{

    private $resolver;

    public function setUp()
    {
        $this->resolver = $this->createMock(Resolver::class);
        $this->resolver->method('resolve')
                  ->willReturn('url');
    }

    public function testInvokeReturnsLinkResolverInstance()
    {
        $helper = new Helper($this->resolver);
        $this->assertInstanceOf(Resolver::class, $helper());
    }

    public function testInvokeWithArgsInvokesResolve()
    {
        $helper = new Helper($this->resolver);
        $this->assertSame('url', $helper('foo'));
    }

}
