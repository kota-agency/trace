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

class EdgeruleTrigger
{
    public const PATTERN_MATCH_ANY = 0;
    public const PATTERN_MATCH_ALL = 1;
    public const PATTERN_MATCH_NONE = 2;
    private int $type;
    /** @var string[] */
    private array $patternMatches;
    private int $patternMatchingType;

    /**
     * @param string[] $patternMatches
     */
    public function __construct(int $type, array $patternMatches, int $patternMatchingType)
    {
        $this->type = $type;
        $this->patternMatches = $patternMatches;
        $this->patternMatchingType = $patternMatchingType;
    }

    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string[]
     */
    public function getPatternMatches(): array
    {
        return $this->patternMatches;
    }

    /**
     * 0 = MatchAny, 1 = MatchAll, 2 = MatchNone.
     */
    public function getPatternMatchingType(): int
    {
        return $this->patternMatchingType;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromApiResponse(array $data): self
    {
        return new self($data['Type'], $data['PatternMatches'], $data['PatternMatchingType']);
    }
}
