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

class Offloader
{
    public const STORAGE_REGION_SSD_MAIN = 'de';
    public const STORAGE_REGIONS_SSD = ['jh' => 'Africa (Johannesburg)', 'hk' => 'Asia (Hong Kong)', 'sg' => 'Asia (Singapore)', 'jp' => 'Asia (Tokyo)', 'uk' => 'Europe (London)', 'es' => 'Europe (Madrid)', 'cz' => 'Europe (Prague)', 'se' => 'Europe (Stockholm)', 'br' => 'LATAM (Sao Paulo)', 'syd' => 'Oceania (Sydney)', 'ny' => 'US East (New York)', 'mi' => 'US East (Miami)', 'la' => 'US West (Los Angeles)', 'wa' => 'US West (Seattle)'];
    private bool $enabled;
    private bool $configured;
    private string $storagePassword;
    private string $storageZone;
    private int $storageZoneId;
    private bool $syncExisting;
    private ?string $syncTokenHash;

    public function __construct(bool $enabled, bool $configured, string $storageZone, int $storageZoneId, string $storagePassword, bool $syncExisting, ?string $syncTokenHash)
    {
        $this->enabled = $enabled;
        $this->configured = $configured;
        $this->storagePassword = $storagePassword;
        $this->storageZone = $storageZone;
        $this->storageZoneId = $storageZoneId;
        $this->syncExisting = $syncExisting;
        $this->syncTokenHash = $syncTokenHash;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isConfigured(): bool
    {
        return $this->configured;
    }

    public function getStoragePassword(): string
    {
        return $this->storagePassword;
    }

    public function getStorageZone(): string
    {
        return $this->storageZone;
    }

    public function getStorageZoneId(): int
    {
        return $this->storageZoneId;
    }

    public function isSyncExisting(): bool
    {
        return $this->syncExisting;
    }

    public function getSyncTokenHash(): ?string
    {
        return $this->syncTokenHash;
    }

    public function hasStorageZone(): bool
    {
        return !empty($this->storageZone) && !empty($this->storagePassword);
    }

    public function setStorageZone(\Bunny\Wordpress\Api\Storagezone\Details $storageZone): void
    {
        $this->storageZoneId = $storageZone->getId();
        $this->storageZone = $storageZone->getName();
        $this->storagePassword = $storageZone->getPassword();
    }

    /**
     * @param array<string, mixed> $postData
     */
    public function handlePost(array $postData): void
    {
        $this->enabled = isset($postData['enabled']) && '1' === $postData['enabled'];
        $this->syncExisting = isset($postData['sync_existing']) && '1' === $postData['sync_existing'];
        if (!empty($postData['storage_password'])) {
            $this->storagePassword = (string) $postData['storage_password'];
        }
    }

    public function saveToWpOptions(): void
    {
        update_option('bunnycdn_offloader_enabled', $this->enabled);
        update_option('bunnycdn_offloader_storage_password', $this->storagePassword);
        update_option('bunnycdn_offloader_sync_existing', $this->syncExisting);
    }

    public static function fromWpOptions(): self
    {
        $enabled = (bool) get_option('bunnycdn_offloader_enabled', false);
        $storageZone = (string) get_option('bunnycdn_offloader_storage_zone', '');
        $storageZoneId = (int) get_option('bunnycdn_offloader_storage_zoneid', 0);
        $storagePassword = (string) get_option('bunnycdn_offloader_storage_password', '');
        $syncExisting = (bool) get_option('bunnycdn_offloader_sync_existing', false);
        $syncTokenHash = (string) get_option('bunnycdn_offloader_sync_token_hash', '');
        $configured = !empty($storageZone) && !empty($storagePassword);
        $syncTokenHash = '' === $syncTokenHash ? null : $syncTokenHash;

        return new self($enabled, $configured, $storageZone, $storageZoneId, $storagePassword, $syncExisting, $syncTokenHash);
    }

    public function saveSyncOptions(string $pathPrefix, string $syncTokenHash): void
    {
        update_option('bunnycdn_offloader_sync_path_prefix', $pathPrefix);
        update_option('bunnycdn_offloader_sync_token_hash', $syncTokenHash);
        update_option('_bunnycdn_offloader_last_sync', time());
        $this->syncTokenHash = $syncTokenHash;
    }
}
