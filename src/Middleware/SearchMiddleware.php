<?php
declare(strict_types=1);

/**
 * Middleware that processes search queries and sets the search results as
 * attributes of the request
 */

namespace ExpressivePrismic\Middleware;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ExpressivePrismic\Service\SearchService;

class SearchMiddleware implements MiddlewareInterface
{

    /**
     * @var searchService
     */
    private $searchService;

    /**
     * @var array
     */
    private $config;

    /**
     * @param SearchService $searchService
     */
    public function __construct(SearchService $searchService, array $config)
    {
        $this->searchService = $searchService;
        $this->config = $config;
    }

    /**
     * @param  Request           $request
     * @param  DelegateInterface $delegate
     * @return Response
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        $term = $this->getQuery($request);
        if (!empty($term)) {
            $request = $this->injectResults($request, $term);
        }

        return $delegate->process($request);
    }

    /**
     * Return modified request with search attributes
     * @param Request $request
     * @return Request
     */
    private function injectResults(Request $request, $term) : Request
    {
        $pageNum = $this->getPageNumber($request);
        $perPage = $this->getPerPage($request);

        $pager   = $this->searchService->search($term, $pageNum, $perPage);

        $data = [
            'page' => $pageNum,
            'per_page' => $perPage,
            'term' => $term,
            'results' => $pager,
        ];

        return $request->withAttribute(self::class, $data);
    }

    /**
     * Return the query term
     * @param Request $request
     * @return string
     */
    private function getQuery(Request $request) : string
    {
        $param  = isset($this->config['query_param'])
                ? $this->config['query_param']
                : 'q';
        $term   = $request->getAttribute($param, '');
        $params = $request->getQueryParams();

        $term   = isset($params[$param])
                ? $params[$param]
                : $term;

        return trim($term);
    }

    /**
     * Return current page number or 1 from attributes of the request or query params
     * @param Request $request
     * @return int
     */
    private function getPageNumber(Request $request) : int
    {
        $param = isset($this->config['page_param'])
               ? $this->config['page_param']
               : 'page';

        // Try to find it in the matched route params first
        $page = (int) $request->getAttribute($param, 1);
        // Query string takes precendence
        $params = $request->getQueryParams();
        $page = (isset($params[$param]) && is_numeric($params[$param]))
              ? (int) $params[$param]
              : $page;

        $page = ($page < 1) ? 1 : $page; // We don't want page zero
        return $page;
    }

    /**
     * Return the result count per page from the request atrs or query string
     * @param Request $request
     * @return int
     */
    private function getPerPage(Request $request) : int
    {
        $default = isset($this->config['default_per_page'])
                 ? (int) $this->config['default_per_page']
                 : 10;

        $param   = isset($this->config['per_page_param'])
                 ? $this->config['per_page_param']
                 : 'per_page';

        // Try to find it in the matched route params first
        $perPage = (int) $request->getAttribute($param, $default);
        // Query string takes precendence
        $params = $request->getQueryParams();
        $perPage = (isset($params[$param]) && is_numeric($params[$param]))
                 ? (int) $params[$param]
                 : $perPage;
        $perPage = ($perPage < 1) ? $default : $perPage; // We don't want zero items per page
        return $perPage;
    }

}
