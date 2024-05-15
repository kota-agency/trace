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

namespace Bunny\Wordpress\Api;

class Config
{
    private ?string $apiKey;

    public function __construct(?string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public static function fromWpOptions(): self
    {
        $apiKey = get_option('bunnycdn_api_key');
        if (!is_string($apiKey) || 0 === strlen($apiKey)) {
            $apiKey = null;
        }

        return new Config($apiKey);
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }
}
