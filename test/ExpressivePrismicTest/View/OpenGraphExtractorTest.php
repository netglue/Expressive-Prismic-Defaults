<?php
namespace ExpressivePrismicTest\View;

use ExpressivePrismic\View\OpenGraphExtractor;
use Prismic;

class OpenGraphExtractorTest extends \PHPUnit_Framework_TestCase
{

    private $document;

    public function setUp()
    {
        $this->document = Prismic\Document::parse(json_decode(file_get_contents(__DIR__ . '/../../fixtures/document.json')));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage is not a known opengraph meta tag
     */
    public function testExceptionThrownForUnknownMetaProperties()
    {
        $e = new OpenGraphExtractor(['foo' => 'bar']);
    }

    public function testExpectedValues()
    {
        $e = new OpenGraphExtractor([
            'og:title'       => 'plain_text_field',
            'og:description' => 'article.plain_text_field',
            'og:whatever'    => 'unknown',
        ]);

        $expect = [
            'og:title'       => 'Plain Text Value',
            'og:description' => 'Plain Text Value',
        ];

        $data = $e->extract($this->document);
        $this->assertSame($expect, $data);
    }

}
