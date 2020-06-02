<?php

namespace Klepak\RestClient\Tests\Stubs;

use Klepak\RestClient\Interfaces\TokenInterface;

class MyToken implements TokenInterface
{

    public function getAccessToken()
    {
        // TODO: Implement getAccessToken() method.
    }

    public function getRefreshToken()
    {
        // TODO: Implement getRefreshToken() method.
    }

    public function getExpiry()
    {
        // TODO: Implement getExpiry() method.
    }

    public function getType()
    {
        // TODO: Implement getType() method.
    }
}