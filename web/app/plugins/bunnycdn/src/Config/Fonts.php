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

namespace Bunny\Wordpress\Config;

class Fonts
{
    private bool $enabled;

    public function __construct(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public static function fromWpOptions(): self
    {
        $enabled = (bool) get_option('bunnycdn_fonts_enabled', false);

        return new self($enabled);
    }

    /**
     * @param array<string, mixed> $postData
     */
    public function handlePost(array $postData): void
    {
        $this->enabled = '1' === ($postData['enabled'] ?? '0');
    }

    public function saveToWpOptions(): void
    {
        update_option('bunnycdn_fonts_enabled', $this->enabled);
    }
}
