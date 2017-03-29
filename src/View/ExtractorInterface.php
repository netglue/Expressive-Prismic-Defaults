<?php

declare(strict_types=1);

namespace ExpressivePrismic\View;

interface ExtractorInterface
{

    public function extract($document) : array;

}
