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

class Edgerule
{
    public const TYPE_ORIGIN_URL = 2;
    public const TYPE_ORIGIN_STORAGE = 17;
    private bool $enabled;
    private string $description;
    private int $actionType;
    private ?string $actionParameter1;
    private ?string $actionParameter2;
    /** @var EdgeruleTrigger[] */
    private array $triggers;

    /**
     * @param EdgeruleTrigger[] $triggers
     */
    public function __construct(bool $enabled, string $description, int $actionType, ?string $actionParameter1, ?string $actionParameter2, array $triggers)
    {
        $this->enabled = $enabled;
        $this->description = $description;
        $this->actionType = $actionType;
        $this->actionParameter1 = $actionParameter1;
        $this->actionParameter2 = $actionParameter2;
        $this->triggers = $triggers;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getActionType(): int
    {
        return $this->actionType;
    }

    public function getActionParameter1(): ?string
    {
        return $this->actionParameter1;
    }

    public function getActionParameter2(): ?string
    {
        return $this->actionParameter2;
    }

    /**
     * @return EdgeruleTrigger[]
     */
    public function getTriggers(): array
    {
        return $this->triggers;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromApiResponse(array $data): self
    {
        $triggers = array_map(fn ($item) => EdgeruleTrigger::fromApiResponse($item), $data['Triggers']);

        return new self($data['Enabled'], $data['Description'], $data['ActionType'], $data['ActionParameter1'], $data['ActionParameter2'], $triggers);
    }
}
