<?php

namespace ExpressivePrismicTest;
use ExpressivePrismic\DefaultsConfigProvider;

class DefaultsConfigProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testReturnsArray()
    {
        $config = new DefaultsConfigProvider;
        $this->assertInternalType('array', $config());
    }

}
