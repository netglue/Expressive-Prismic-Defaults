<?php
declare(strict_types=1);

namespace ExpressivePrismic\Service;

use Prismic;
use Prismic\Fragment;

class UserConfig
{

    /**
     * @var Prismic\LinkResolver
     */
    private $linkResolver;

    /**
     * @var Prismic\Document
     */
    private $document;

    /**
     * @param Prismic\Api $api
     */
    public function __construct(Prismic\Document $document, Prismic\LinkResolver $linkResolver)
    {
        $this->document     = $document;
        $this->linkResolver = $linkResolver;
    }

    /**
     * Invoke
     *
     * Returns self so that the service can easily be used as a view helper
     *
     * @return UserConfig
     */
    public function __invoke() : UserConfig
    {
        return $this;
    }

    /**
     * Return the config document
     * @return Prismic\Document
     */
    public function getDocument() : Prismic\Document
    {
        return $this->document;
    }

    /**
     * Return the fragment identifed by name
     * @param string $name
     * @return Fragment\FragmentInterface|null
     */
    public function getFragment(string $name)
    {
        return $this->getDocument()->get(
            $this->name($name)
        );
    }

    /**
     * Return text value of the fragment identified by name
     * @param string $name
     * @return string|null
     */
    public function get(string $name)
    {
        /**
         * Prismic\Document::getText($name) does not cover
         * all possible fragment types, whereas $fragment->asText() always
         * returns a string representation of the fragment
         */
        $frag = $this->getFragment($name);
        if ($frag instanceof Fragment\Link\LinkInterface) {
            return $this->linkResolver->resolve($frag);
        }
        if ($frag) {
            return $frag->asText();
        }
        return null;
    }

    /**
     * Return HTML value of the fragment identified by name
     * @param string $name
     * @return string|null
     */
    public function getHtml(string $name)
    {
        $frag = $this->getFragment($name);
        if ($frag) {
            return $frag->asHtml($this->linkResolver);
        }
        return null;
    }

    /**
     * Return the URL value of the fragment identified by name
     *
     * URLs can only be returned for the following types:
     * - Embed
     * - Image
     * - Link\LinkInterface
     *
     * @param string $name
     * @return string|null
     */
    public function getUrl(string $name)
    {
        $fragment = $this->getFragment($name);
        if ($fragment instanceof Fragment\Link\LinkInterface) {
            return $this->linkResolver->resolve($fragment);
        }

        if (
            ($fragment instanceof Fragment\Image)
            ||
            ($fragment instanceof Fragment\Embed)
        ) {
            return $fragment->asText();
        }

        return null;
    }

    /**
     * Return latitude of the geopoint fragment identified by name
     * @param string $name
     * @return string|null
     */
    public function getLatitude(string $name)
    {
        $frag = $this->getFragment($name);
        if ($frag instanceof Fragment\GeoPoint) {
            return $frag->getLatitude();
        }
        return null;
    }

    /**
     * Return longitude of the geopoint fragment identified by name
     * @param string $name
     * @return string|null
     */
    public function getLongitude(string $name)
    {
        $frag = $this->getFragment($name);
        if ($frag instanceof Fragment\GeoPoint) {
            return $frag->getLongitude();
        }
        return null;
    }

    /**
     * Return the image URL of the fragment identified by name
     * @param string $name Fragment Name
     * @param string $view The image view to return the url for
     * @return string
     */
    public function getImageUrl(string $name, string $view = 'main')
    {
        $frag = $this->getFragment($name);
        if ($frag instanceof Fragment\Image) {
            if ($image = $frag->getView($view)) {
                return $image->getUrl();
            }
        }
        return null;
    }


    /**
     * Normalise a fragment name to include the document type
     * @param string $name
     * @return string
     */
    private function name(string $name) : string
    {
        $type = $this->getDocument()->getType();
        if (strpos($name, $type) === 0) {
            return $name;
        }
        if (strpos($name, '.') !== false) {
            throw new \RuntimeException(sprintf(
                'Found a dot in the fragment name [%s] but does not match configured mask/type of %s',
                $name,
                $type
            ));
        }
        return sprintf('%s.%s', $type, $name);
    }

}
