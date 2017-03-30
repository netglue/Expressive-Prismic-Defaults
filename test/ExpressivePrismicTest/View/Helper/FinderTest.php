<?php
namespace ExpressivePrismicTest\View\Helper;

use ExpressivePrismic\View\Helper\Finder as Helper;
use Prismic;

class FinderTest extends \PHPUnit_Framework_TestCase
{

    private $document;

    private $api;

    private $helper;

    public function setUp()
    {
        $this->document = Prismic\Document::parse(json_decode(file_get_contents(__DIR__ . '/../../../fixtures/document.json')));
        $this->api = $this->createMock(Prismic\Api::class);
        $this->api->method('getByID')
                  ->willReturn($this->document);
        $this->helper = new Helper($this->api);
    }

    public function testInvoke()
    {
        $result = ($this->helper)();
        $this->assertSame($this->helper, $result);
    }

    public function testFindById()
    {
        $this->assertSame($this->document, $this->helper->findById('foo'));
    }
}
