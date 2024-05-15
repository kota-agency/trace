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

class Info
{
    private int $id;
    private string $name;
    private string $originUrl;
    /** @var string[] */
    private array $hostnames;
    /** @var string[] */
    private array $corsHeaderExtensions;

    /**
     * @param string[] $hostnames
     * @param string[] $corsHeaderExtensions
     */
    public function __construct(int $id, string $name, string $originUrl, array $hostnames, array $corsHeaderExtensions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->originUrl = $originUrl;
        $this->hostnames = $hostnames;
        $this->corsHeaderExtensions = $corsHeaderExtensions;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOriginUrl(): string
    {
        return $this->originUrl;
    }

    /**
     * @return string[]
     */
    public function getHostnames(): array
    {
        return $this->hostnames;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function fromApiResponse(array $data): self
    {
        $hostnames = array_map(fn ($item) => $item['Value'], $data['Hostnames']);

        return new self($data['Id'], $data['Name'], $data['OriginUrl'], $hostnames, $data['AccessControlOriginHeaderExtensions']);
    }

    /** @return string[] */
    public function getCorsHeaderExtensions(): array
    {
        return $this->corsHeaderExtensions;
    }
}
