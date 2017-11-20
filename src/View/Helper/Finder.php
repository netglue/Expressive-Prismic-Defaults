<?php
/**
 * This file is part of the Expressive Prismic Defaults Package
 * Copyright 2016 Net Glue Ltd (https://netglue.uk).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sometimes, you need to be able to retireve a document from the context of view/template
 */

namespace ExpressivePrismic\View\Helper;

use Prismic;

/**
 * Class Finder
 *
 * @package ExpressivePrismic\View\Helper
 */
class Finder
{

    /**
     * @var Prismic\Api
     */
    private $api;

    /**
     * Finder constructor.
     *
     * @param Prismic\Api $api
     */
    public function __construct(Prismic\Api $api)
    {
        $this->api = $api;
    }

    /**
     * Invoke
     *
     * @return Finder
     */
    public function __invoke() : Finder
    {
        return $this;
    }

    /**
     * Locate the document identified by $id
     *
     * @param string $id
     * @return Prismic\Document|null
     */
    public function findById(string $id)
    {
        return $this->api->getByID($id);
    }

    /**
     * Locate a document with a 'bookmark'
     *
     * @param string $bookmark
     * @return Prismic\Document|null
     */
    public function findByBookmark(string $bookmark)
    {
        $id = $this->api->bookmark($bookmark);
        if (empty($id)) {
            return null;
        }
        return $this->findById($id);
    }

}
