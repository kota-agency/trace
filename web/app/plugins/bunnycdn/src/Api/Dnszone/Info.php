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

class Info
{
    private int $id;
    private string $domain;
    /** @var Record[] */
    private array $records;

    private function __construct(int $id, string $domain)
    {
        $this->id = $id;
        $this->domain = $domain;
        $this->records = [];
    }

    /**
     * @param array<string, mixed> $item
     */
    public static function fromArray(array $item): self
    {
        $zone = new self($item['Id'], $item['Domain']);
        foreach ($item['Records'] as $row) {
            $pullzoneId = 0 === $row['AcceleratedPullZoneId'] || true !== $row['Accelerated'] ? null : $row['AcceleratedPullZoneId'];
            $zone->records[] = new Record($zone, $row['Id'], $row['Type'], $row['Name'], $row['Value'], $row['Accelerated'], $pullzoneId);
        }

        return $zone;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return Record[]
     */
    public function getRecords(): array
    {
        return $this->records;
    }
}
