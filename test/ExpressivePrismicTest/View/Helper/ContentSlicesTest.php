<?php
namespace ExpressivePrismicTest\View\Helper;

use ExpressivePrismic\View\Helper\ContentSlices as Helper;
use ExpressivePrismic\LinkResolver as Resolver;
use ExpressivePrismic\Service\CurrentDocument;
use Prismic;
use Zend\View\Helper\Partial;

class ContentSlicesTest extends \PHPUnit_Framework_TestCase
{

    private $document;

    public function setUp()
    {
        $this->document = Prismic\Document::parse(json_decode(file_get_contents(__DIR__ . '/../../../fixtures/document.json')));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage A document cannot be found with which to render slice content
     */
    public function testExceptionThrownWithAbsenceOfDocument()
    {
        $service = new CurrentDocument;
        $partial = new MockPartial;
        $helper = new Helper([], $service, $partial);
        $helper('fragName');
    }

    public function testEmptyStringReturnedForUnknownSlice()
    {
        $service = new CurrentDocument;
        $service->setDocument($this->document);
        $partial = new MockPartial;
        $helper = new Helper([], $service, $partial);
        $result = $helper('fragName');
        $this->assertSame('', $result);
    }

    public function testUnmappedZoneReturnsEmptyString()
    {
        $service = new CurrentDocument;
        $service->setDocument($this->document);
        $partial = new MockPartial;
        $helper = new Helper([], $service, $partial);
        $result = $helper('slice_field');
        $this->assertSame('', $result);
    }

    public function testPartialHelperIsCalled()
    {
        $service = new CurrentDocument;
        $service->setDocument($this->document);
        $partial = new MockPartial;
        $map = [
            // zone type => template name
            'features' => 'some::template',
        ];
        $this->assertNull($partial->template);
        $this->assertNull($partial->model);
        $helper = new Helper($map, $service, $partial);
        $result = $helper('slice_field');
        $this->assertSame('Partial', $result);
        $this->assertSame('some::template', $partial->template);
        $this->assertSame($this->document, $partial->model['document']);
        $this->assertInstanceOf(Prismic\Fragment\Slice::class, $partial->model['slice']);
    }



}

class MockPartial extends Partial
{

    public $template;

    public $model;

    public function __construct()
    {

    }

    public function __invoke($template = null, $model = null)
    {
        $this->template = $template;
        $this->model = $model;

        return 'Partial';
    }
}
