<?php
namespace ExpressivePrismic\Middleware;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Prismic;
use ExpressivePrismic\Service\MetaDataAutomator;

class MetaDataAutomatorMiddleware implements MiddlewareInterface
{

    /**
     * @var MetaDataAutomator
     */
    private $automator;

    public function __construct(MetaDataAutomator $automator)
    {
        $this->automator = $automator;
    }

    /**
     * @param  Request           $request
     * @param  DelegateInterface $delegate
     * @return Response
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        if ($document = $request->getAttribute(Prismic\Document::class)) {
            $this->automator->apply($document);
        }

        return $delegate->process($request);
    }


}
