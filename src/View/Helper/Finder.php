<?php

/**
 * Sometimes, you need to be able to retireve a document from the context of view/template
 */

namespace ExpressivePrismic\View\Helper;

use Prismic;

class Finder
{

    /**
     * @var Prismic\Api
     */
    private $api;

    public function __construct(Prismic\Api $api)
    {
        $this->api = $api;
    }

    public function __invoke() : Finder
    {
        return $this;
    }

    /**
     * @return Prismic\Document|null
     */
    public function findById(string $id)
    {
        return $this->api->getByID($id);
    }

}
