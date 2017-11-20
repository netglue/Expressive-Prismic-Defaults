<?php
namespace ExpressivePrismicTest\View\Helper;

use ExpressivePrismic\View\Helper\Finder as Helper;
use Prismic;

class FinderTest extends \PHPUnit_Framework_TestCase
{

    private $document;

    private $api;

    public function setUp()
    {
        $this->document = $this->prophesize(Prismic\Document::class);
        $this->api = $this->prophesize(Prismic\Api::class);
    }

    protected function getHelper()
    {
        return new Helper($this->api->reveal());
    }

    public function testInvoke()
    {
        $helper = $this->getHelper();
        $this->assertSame($helper, $helper->__invoke());
    }

    public function testFindByIdIsSuccessful()
    {
        $doc = $this->document->reveal();
        $this->api->getByID('foo')->willReturn($doc);
        $this->assertSame($doc, $this->getHelper()->findById('foo'));
    }

    public function testFindByIdReturnsNull()
    {
        $this->api->getByID('foo')->willReturn(null);
        $this->assertNull($this->getHelper()->findById('foo'));
    }

    public function testFindByBookmarkIsSuccessful()
    {
        $doc = $this->document->reveal();
        $this->api->bookmark('name')->willReturn('SomeId');
        $this->api->getByID('SomeId')->willReturn($doc);

        $this->assertSame($doc, $this->getHelper()->findByBookmark('name'));
    }

    public function testFindByBookmarkReturnsNull()
    {
        $this->api->bookmark('name')->willReturn(null);
        $this->api->getByID()->shouldNotBeCalled();

        $this->assertNull($this->getHelper()->findByBookmark('name'));
    }

}
