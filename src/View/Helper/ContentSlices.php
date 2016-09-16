<?php

namespace ExpressivePrismic\View\Helper;

use ExpressivePrismic\Service\CurrentDocument;
use Zend\Expressive\Template\TemplateRendererInterface;
use Prismic;

class ContentSlices
{

    /**
     * Hash table of slice names to template names
     * @var array
     */
    private $templates = [];

    /**
     * Renderer
     * @var TemplateRendererInterface
     */
    private $renderer;

    /**
     * Current Document Registry
     * @var CurrentDocument
     */
    private $documentRegistry;

    /**
     *
     */
    public function __construct(array $templates, TemplateRendererInterface $renderer, CurrentDocument $documentRegistry)
    {
        $this->documentRegistry = $documentRegistry;
        $this->templates = $templates;
        $this->renderer = $renderer;
    }

    /**
     * Render the slice zone identified by fragment name to a string
     *
     * @param string $fragmentName Can be fully qualified with document type or not
     * @param Prismic\Document $document Optionally use a document other than the current document for the request
     * @return string
     */
    public function __invoke(string $fragmentName, Prismic\Document $document = null) : string
    {
        if (!$document) {
            $document = $this->documentRegistry->getDocument();
        }

        if (!$document) {
            throw new \RuntimeException('A document cannot be found with which to render slice content');
        }

        $out = '';
        if ($zone = $this->getSliceZone($document, $fragmentName)) {
            foreach($zone->getSlices() as $slice) {
                $out .= $this->sliceAsString($document, $slice);
            }
        }

        return $out;
    }

    /**
     * Return the slice zone from the document
     *
     * The fragment must exist in the document and it must also be a Fragment\SliceZone
     * otherwise null is returned
     *
     * @param  Prismic\Document $document
     * @param  string $fragmentName
     *
     * @return Prismic\Fragment\SliceZone|null
     */
    private function getSliceZone(Prismic\Document $document, string $fragmentName)
    {
        $type = $document->getType();
        if(strpos($fragmentName, $type.'.') === false) {
            $fragmentName = sprintf('%s.%s', $type, $fragmentName);
        }
        if ($zone = $document->get($fragName)) {
            if ($zone instanceof Prismic\Fragment\SliceZone) {
                return $zone;
            }
        }
    }

    /**
     * Render a single slice
     *
     * @param  Prismic\Document $document
     * @param  Prismic\Fragment\Slice $slice
     *
     * @return string
     */
    private function sliceAsString(Prismic\Document $document, Prismic\Fragment\Slice $slice) : string
    {
        $type = $slice->getSliceType();
        if (isset($this->templates[$type])) {
            $model = [
                'document' => $document,
                'slice'    => $slice,
            ];

            return (string) $this->renderer->render($this->templates[$type], $model);
        }

        return '';
    }


}
