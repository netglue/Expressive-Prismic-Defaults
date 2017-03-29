<?php
namespace ExpressivePrismicTest\View;

use ExpressivePrismic\View\HeadTitleExtractor;
use Prismic;

class HeadTitleExtractorTest extends \PHPUnit_Framework_TestCase
{

    private $document;

    public function setUp()
    {
        $this->document = Prismic\Document::parse(json_decode(file_get_contents(__DIR__ . '/../../fixtures/document.json')));
    }

    public function testTitleIsReturnedFromTopLevelOfDocument()
    {
        $extractor = new HeadTitleExtractor(['plain_text_field']);
        $value = $extractor->extract($this->document);
        $expect = [
            'title' => 'Plain Text Value'
        ];
        $this->assertSame($expect, $value);

        $extractor = new HeadTitleExtractor(['article.plain_text_field']);
        $value = $extractor->extract($this->document);
        $this->assertSame($expect, $value);
    }

    public function testTitleIsReturnedFromGroup()
    {
        $group = $this->document->get('article.group_field');
        $extractor = new HeadTitleExtractor(['title']);
        $expect = [
            'title' => 'This is a title in a top level group'
        ];
        $value = $extractor->extract($group);
        $this->assertSame($expect, $value);

        $extractor = new HeadTitleExtractor(['unknown']);
        $this->assertSame([], $extractor->extract($group));
    }

    public function testTitleIsReturnedFromGroupDoc()
    {
        $group = $this->document->get('article.group_field')->getArray()[0];
        $extractor = new HeadTitleExtractor(['title']);
        $expect = [
            'title' => 'This is a title in a top level group'
        ];
        $value = $extractor->extract($group);
        $this->assertSame($expect, $value);

        $extractor = new HeadTitleExtractor(['unknown']);
        $this->assertSame([], $extractor->extract($group));
    }

    public function testTitleIsReturnedFromSlice()
    {
        $slice = $this->document->get('article.slice_field');
        $extractor = new HeadTitleExtractor(['title']);
        $expect = [
            'title' => 'This is a title in a group, in a slice'
        ];
        $value = $extractor->extract($slice);
        $this->assertSame($expect, $value);

        $extractor = new HeadTitleExtractor(['unknown']);
        $this->assertSame([], $extractor->extract($slice));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Properties to search for the head title must be string values
     */
    public function testExceptionThrownForInvalidSearches()
    {
        $extractor = new HeadTitleExtractor([
            'foo',
            100
        ]);
    }

    public function testGetTypeThrowsExceptionWithoutNameForGroup()
    {
        $group = $this->document->get('group_field');
        $this->assertNull($group);
        $group = $this->document->get('article.group_field');
        $this->assertInstanceOf(Prismic\Fragment\Group::class, $group);

        foreach ($group->getArray() as $groupDoc) {
            $this->assertInstanceOf(Prismic\Fragment\GroupDoc::class, $groupDoc);
            $this->assertInstanceOf(Prismic\Fragment\StructuredText::class, $groupDoc->get('title'));
        }

        $zone = $this->document->get('article.slice_field');
        $this->assertInstanceOf(Prismic\Fragment\SliceZone::class, $zone);
        foreach ($zone->getSlices() as $slice) {
            $this->assertInstanceOf(Prismic\Fragment\Slice::class, $slice);
            $fragment = $slice->getValue();
        }
    }


}
