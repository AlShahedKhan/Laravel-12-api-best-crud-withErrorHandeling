<?php
namespace App\Traits;

use App\Exceptions\RequestTimeoutException;

trait ChecksRequestTimeout
{
    protected function checkRequestTimeout(float $startTime, int $limitSeconds = 1)
    {
        $duration = microtime(true) - $startTime;
        if ($duration > $limitSeconds) {
            throw new RequestTimeoutException("Request took too long ({$duration}s)");
        }
    }
}
