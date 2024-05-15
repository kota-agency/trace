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

namespace Bunny\Wordpress\Api\Dnszone;

class Record
{
    private Info $zone;
    private int $id;
    private int $type;
    private string $name;
    private string $value;
    private bool $accelerated;
    private ?int $acceleratedPullzoneId;

    public function __construct(Info $zone, int $id, int $type, string $name, string $value, bool $accelerated, ?int $acceleratedPullzoneId)
    {
        $this->zone = $zone;
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->accelerated = $accelerated;
        $this->acceleratedPullzoneId = $acceleratedPullzoneId;
    }

    public function getZone(): Info
    {
        return $this->zone;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * 0 = A
     * 1 = AAAA
     * 2 = CNAME
     * 3 = TXT
     * 4 = MX
     * 5 = Redirect
     * 6 = Flatten
     * 7 = PullZone
     * 8 = SRV
     * 9 = CAA
     * 10 = PTR
     * 11 = Script
     * 12 = NS.
     */
    public function getType(): int
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isAccelerated(): bool
    {
        return $this->accelerated;
    }

    public function getAcceleratedPullzoneId(): ?int
    {
        return $this->acceleratedPullzoneId;
    }
}
