<?php

namespace Klepak\RestClient\Interfaces;

interface TokenInterface
{
    public function getAccessToken();
    public function getRefreshToken();
    public function getExpiry();
    public function getType();
}
