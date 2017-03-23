<?php

namespace ExpressivePrismic\Service;

use ExpressivePrismic\Paginator\PaginatorFactoryInterface;

use Prismic;

class SearchService
{

    /**
     * An array of document types to restrict the search to
     * @var array
     */
    private $types = [];

    /**
     * An array of fragment -> value pairs to exclude in searches
     * @var array
     */
    private $exclude = [];

    /**
     * @var Prismic\Api
     */
    private $api;

    /**
     * @var PaginatorFactoryInterface|null
     */
    private $pagerFactory;

    /**
     * @param Prismic\Api $api
     * @param array $types Restrict the search to the given document types
     * @param PaginatorFactoryInterface $pagerFactory an optional instance capable of creating a pager with a search form
     */
    public function __construct(Prismic\Api $api, array $types = [], array $exclude = [], PaginatorFactoryInterface $pagerFactory = null)
    {
        $this->api = $api;
        $this->types = $types;
        $this->exclude = $exclude;
        $this->pagerFactory = $pagerFactory;
    }

    /**
     * Search the api using the given query
     * @param string $query The full text search term
     * @param int $page Page offset if no paginator is being used
     * @param int $perPage Page size if no paginator is being used
     * @return mixed Return a paginator if a pager factory has been supplied or a Prismic\Response if not
     */
    public function search(string $query, int $page = null, int $perPage = null)
    {
        $ref = (string) $this->api->ref();
        $query = str_replace('"','\"', $query);

        $predicates = [Prismic\Predicates::fulltext("document", $query)];

        if (count($this->types)) {
            $predicates[] = Prismic\Predicates::any("document.type", $this->types);
        }

        foreach ($this->exclude as $field => $value) {
            $predicates[] = Prismic\Predicates::not($field, $value);
        }

        $form = $this->api->forms()->everything
                ->ref($ref)
                ->query($predicates);

        /**
         * The pager gets an unsubmitted form so that it can be used
         * to control page size and offset before retrieving the results
         */
        if ($this->pagerFactory) {
            $pager = $this->pagerFactory->getPaginator($form);
            $pager->setItemCountPerPage($perPage);
            $pager->setCurrentPageNumber($page);
            return $pager;
        }

        /**
         * If there is no pager, set the page size and offset on the form if
         * provided
         */

        if ($page) {
            $form = $form->page($page);
        }

        if ($perPage) {
            $form = $form->pageSize($perPage);
        }

        return $form->submit();
    }

}
