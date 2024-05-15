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

namespace Bunny\Wordpress\Utils;

class Number
{
    public static function bytesToString(int $size, int $precision = 0): string
    {
        $value = size_format($size, $precision);
        if (false === $value) {
            return '0 bytes';
        }

        return $value;
    }

    public static function floatToMoney(float $value, int $precision = 2): string
    {
        return '$'.number_format($value, $precision, '.', ',');
    }
}
