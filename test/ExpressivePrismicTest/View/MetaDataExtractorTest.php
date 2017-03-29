<?php
namespace ExpressivePrismicTest\View;

use ExpressivePrismic\View\MetaDataExtractor;
use Prismic;

class MetaDataExtractorTest extends \PHPUnit_Framework_TestCase
{

    private $document;

    public function setUp()
    {
        $this->document = Prismic\Document::parse(json_decode(file_get_contents(__DIR__ . '/../../fixtures/document.json')));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage not a known HTML meta tag
     */
    public function testExceptionThrownForUnknownMetaProperties()
    {
        $e = new MetaDataExtractor(['foo' => 'bar']);
    }

    public function testExpectedValuesReturned()
    {
        $e = new MetaDataExtractor([
            'description' => 'plain_text_field',
            'subtitle'    => 'article.plain_text_field',
            'keywords'    => 'rich_text_field',
            'copyright'   => 'article.rich_text_field',
            'topic'       => 'unknown',
        ]);

        $expect = [
            'description' => 'Plain Text Value',
            'subtitle'    => 'Plain Text Value',
            'keywords'    => 'Some rich text',
            'copyright'   => 'Some rich text',
        ];

        $data = $e->extract($this->document);
        $this->assertSame($expect, $data);
    }
}
