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

use Bunny\Wordpress\Config\Exception\PluginNotConfiguredException;

class Cdn
{
    public const DEFAULT_VALUES = ['status' => self::STATUS_ENABLED, 'excluded' => ['.php'], 'included' => ['wp-includes/', 'wp-content/themes/', 'wp-content/uploads/'], 'disable_admin' => false];
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;
    public const STATUS_ACCELERATED = 2;
    private int $status;
    private ?int $pullzoneId;
    private string $pullzoneName;
    private string $hostname;
    private string $url;
    /** @var string[] */
    private array $excluded;
    /** @var string[] */
    private array $included;
    private bool $disableAdmin;
    private bool $isAgencyMode;

    /**
     * @param string[] $excluded
     * @param string[] $included
     */
    public function __construct(int $status, ?int $pullzoneId, string $pullzoneName, string $hostname, string $url, array $excluded, array $included, bool $disableAdmin, bool $isAgencyMode)
    {
        $this->status = $status;
        $this->pullzoneId = $pullzoneId;
        $this->pullzoneName = $pullzoneName;
        $this->hostname = $hostname;
        $this->url = $url;
        $this->excluded = $excluded;
        $this->included = $included;
        $this->disableAdmin = $disableAdmin;
        $this->isAgencyMode = $isAgencyMode;
    }

    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function getPullzoneId(): ?int
    {
        return $this->pullzoneId;
    }

    public function getPullzoneName(): string
    {
        return $this->pullzoneName;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string[]
     */
    public function getExcluded(): array
    {
        return $this->excluded;
    }

    /**
     * @return string[]
     */
    public function getIncluded(): array
    {
        return $this->included;
    }

    public function isDisableAdmin(): bool
    {
        return $this->disableAdmin;
    }

    public function isAgencyMode(): bool
    {
        return $this->isAgencyMode;
    }

    public static function fromWpOptions(): self
    {
        $status = (int) get_option('bunnycdn_cdn_status', self::DEFAULT_VALUES['status']);
        $pullzone = (array) get_option('bunnycdn_cdn_pullzone', []);
        $hostname = (string) get_option('bunnycdn_cdn_hostname', '');
        $url = (string) get_option('bunnycdn_cdn_url', '');
        $excluded = (array) get_option('bunnycdn_cdn_excluded', self::DEFAULT_VALUES['excluded']);
        $included = (array) get_option('bunnycdn_cdn_included', self::DEFAULT_VALUES['included']);
        $disableAdmin = (bool) get_option('bunnycdn_cdn_disable_admin', self::DEFAULT_VALUES['disable_admin']);
        $isAgencyMode = 'agency' === get_option('bunnycdn_wizard_mode', 'standalone');
        if (empty($pullzone)) {
            throw new PluginNotConfiguredException();
        }
        if (0 === strlen($url)) {
            $url = site_url();
        }

        return new self($status, $pullzone['id'] ?? null, $pullzone['name'], $hostname, $url, $excluded, $included, $disableAdmin, $isAgencyMode);
    }

    /**
     * @param array<string, mixed> $postData
     */
    public function handlePost(array $postData): void
    {
        if (!empty($postData['hostname'])) {
            $this->hostname = (string) $postData['hostname'];
        }
        // normalize excluded extensions
        $excluded = $postData['excluded'] ?: [];
        $excluded = array_map(fn ($item): string => trim($item, " \t\n\r\x00\v."), $excluded);
        $excluded = array_filter($excluded, fn ($item): bool => strlen($item) > 0);
        $excluded = array_unique($excluded);
        $excluded = array_map(fn ($item): string => '.'.$item, $excluded);
        $this->status = '1' === ($postData['enabled'] ?? '0') ? self::STATUS_ENABLED : self::STATUS_DISABLED;
        $this->url = $postData['url'] ?: '';
        $this->excluded = $excluded;
        $this->included = $postData['included'] ?: [];
        $this->disableAdmin = '1' === ($postData['disable_admin'] ?? '0');
    }

    public function saveToWpOptions(): void
    {
        update_option('bunnycdn_cdn_status', $this->status);
        update_option('bunnycdn_cdn_hostname', $this->hostname);
        update_option('bunnycdn_cdn_url', $this->url);
        update_option('bunnycdn_cdn_excluded', $this->excluded);
        update_option('bunnycdn_cdn_included', $this->included);
        update_option('bunnycdn_cdn_disable_admin', $this->disableAdmin);
    }

    public function isEnabled(): bool
    {
        return self::STATUS_ENABLED === $this->status;
    }

    public function isAccelerated(): bool
    {
        return self::STATUS_ACCELERATED === $this->status;
    }
}
