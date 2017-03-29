<?php
namespace ExpressivePrismicTest\Service;

use ExpressivePrismic\Service\UserConfig;
use ExpressivePrismic\LinkResolver;
use Prismic;

class UserConfigTest extends \PHPUnit_Framework_TestCase
{

    private $resolver;

    private $config;

    private $document;

    public function setUp()
    {
        $this->resolver = $this->createMock(LinkResolver::class);
        $this->resolver->method('resolve')
                  ->willReturn('resolved-url');

        $this->document = Prismic\Document::parse(json_decode(file_get_contents(__DIR__ . '/../../fixtures/document.json')));
        $this->config = new UserConfig($this->document, $this->resolver);
    }

    public function testInvokeReturnsSelf()
    {
        $this->assertSame($this->config, ($this->config)());
    }

    public function testDocumentIsRetrievable()
    {
        $this->assertSame($this->document, $this->config->getDocument());
    }

    public function testGetFragmentReturnsFragmentWithUnqualifiedName()
    {
        $frag = $this->config->getFragment('plain_text_field');
        $this->assertInstanceOf(Prismic\Fragment\FragmentInterface::class, $frag);
    }

    public function testGetFragmentReturnsFragmentWithQualifiedName()
    {
        $frag = $this->config->getFragment('article.plain_text_field');
        $this->assertInstanceOf(Prismic\Fragment\FragmentInterface::class, $frag);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Found a dot in the fragment name
     */
    public function testExceptionThrownAccessingIncorrectQualifiedFragmentName()
    {
        $frag = $this->config->getFragment('wrong.plain_text_field');
    }

    public function testGetReturnsExpectedValue()
    {
        $text = $this->config->get('plain_text_field');
        $this->assertSame('Plain Text Value', $text);
    }

    public function testGetReturnsNullForUnknownField()
    {
        $text = $this->config->get('unknown');
        $this->assertNull($text);
    }

    public function testGetResolvesLinkFragmentsToStrings()
    {
        $text = $this->config->get('web_link_field');
        $this->assertSame('resolved-url', $text);
    }

    public function testGetReturnsAllElseAsText()
    {
        $value = $this->config->get('rich_text_field');
        $this->assertSame('Some rich text', $value);
    }

    public function testGetHtmlReturnsExpectedText()
    {
        $value = $this->config->getHtml('rich_text_field');
        $this->assertSame('<p>Some rich text</p>', $value);
    }

    public function testGetHtmlReturnsNullForUnknownField()
    {
        $text = $this->config->getHtml('unknown');
        $this->assertNull($text);
    }

    public function testGetUrlReturnsExpectedValueForWebLink()
    {
        $text = $this->config->getUrl('web_link_field');
        $this->assertSame('resolved-url', $text);
    }

    public function testGetUrlReturnsImageUrl()
    {
        $text = $this->config->getUrl('image');
        $this->assertSame('https://example.com/img.jpg', $text);
    }

    public function testGetUrlReturnsNullForUnknownField()
    {
        $text = $this->config->getUrl('unknown');
        $this->assertNull($text);
    }

    public function testGetLatitudeLongitudeOnlyReturnsValueForGeoPoint()
    {
        $this->assertNull($this->config->getLatitude('image'));
        $this->assertNull($this->config->getLongitude('image'));

        $this->assertEquals($this->config->getLatitude('coords'), 50.0);
        $this->assertEquals($this->config->getLongitude('coords'), -3.0);
    }

    public function testGetImageUrlCanReturnViewUrls()
    {
        $text = $this->config->getImageUrl('image');
        $this->assertSame('https://example.com/img.jpg', $text);
        $text = $this->config->getImageUrl('image', 'small');
        $this->assertSame('https://example.com/img-small.jpg', $text);

        $this->assertNull($this->config->getImageUrl('unknown'));
    }

}
