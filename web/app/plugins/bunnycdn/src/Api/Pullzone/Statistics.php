<?php

// bunny.net WordPress Plugin
// Copyright (C) 2024  BunnyWay d.o.o.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
declare(strict_types=1);

namespace Bunny\Wordpress\Api\Pullzone;

use Bunny\Wordpress\Utils\Number;

class Statistics
{
    private int $bandwidth;
    private float $cacheHitRate;
    private int $requestsServed;
    /** @var array<string, int> */
    private array $bandwidthHistory;
    /** @var array<string, int> */
    private array $cacheHistory;
    /** @var array<string, int> */
    private array $requestsHistory;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->bandwidth = $data['TotalBandwidthUsed'];
        $this->cacheHitRate = $data['CacheHitRate'];
        $this->requestsServed = $data['TotalRequestsServed'];
        $this->bandwidthHistory = $data['BandwidthUsedChart'];
        $this->cacheHistory = $data['CacheHitRateChart'];
        $this->requestsHistory = $data['RequestsServedChart'];
    }

    public function getBandwidth(): int
    {
        return $this->bandwidth;
    }

    public function getCacheHitRate(): float
    {
        return $this->cacheHitRate;
    }

    public function getRequestsServed(): int
    {
        return $this->requestsServed;
    }

    public function getBandwidthHumanReadable(): string
    {
        return Number::bytesToString($this->bandwidth, 1);
    }

    /**
     * @return array<string, int>
     */
    public function getBandwidthHistory(): array
    {
        return $this->bandwidthHistory;
    }

    /**
     * @return array<string, int>
     */
    public function getCacheHistory(): array
    {
        return $this->cacheHistory;
    }

    /**
     * @return array<string, int>
     */
    public function getRequestsHistory(): array
    {
        return $this->requestsHistory;
    }

    public function getCacheHitRateHumanReadable(): string
    {
        return sprintf('%.02f%%', $this->cacheHitRate);
    }

    public function getRequestsTotal(): string
    {
        return (string) $this->requestsServed;
    }
}
