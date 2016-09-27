<?php
/**
 * A view helper that returns an instance of the LinkResolver
 */

namespace ExpressivePrismic\View\Helper;

use Prismic\LinkResolver as Resolver;

class LinkResolver
{

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @param Resolver $resolver
     */
    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Either return the resolved url for the given target, or the link resolver itself
     *
     * To return an url for a link, provide a \Prismic\Fragment\Link\LinkInterface as the target
     * If no argument is given, the link resolver itself is returned
     *
     * @return string|null|Resolver
     * @param \Prismic\Fragment\Link\LinkInterface $target
     */
    public function __invoke($target = null)
    {
        if (null !== $target) {
            return $this->resolver->resolve($target);
        }

        return $this->resolver;
    }

}
