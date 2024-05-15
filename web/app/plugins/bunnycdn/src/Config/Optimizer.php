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

class Optimizer
{
    public const WATERMARK_POSITIONS = [0 => 'Bottom Left', 1 => 'Bottom Right', 2 => 'Top Left', 3 => 'Top Right', 4 => 'Center', 5 => 'Center Stretched'];
    private bool $enabled;
    private bool $webpCompression;
    private bool $imageApi;
    private bool $minifyCss;
    private bool $minifyJs;
    private bool $smartImageEnabled;
    private int $smartImageDesktopWidthMax;
    private int $smartImageMobileWidthMax;
    private int $smartImageDesktopQuality;
    private int $smartImageMobileQuality;
    private bool $watermarkEnabled;
    private string $watermarkUrl;
    private int $watermarkImageMin;
    private int $watermarkBorder;
    private int $watermarkPosition;

    public function __construct(bool $enabled, bool $webpCompression, bool $imageApi, bool $minifyCss, bool $minifyJs, bool $smartImageEnabled, int $smartImageDesktopWidthMax, int $smartImageMobileWidthMax, int $smartImageDesktopQuality, int $smartImageMobileQuality, bool $watermarkEnabled, string $watermarkUrl, int $watermarkImageMin, int $watermarkBorder, int $watermarkPosition)
    {
        $this->enabled = $enabled;
        $this->webpCompression = $webpCompression;
        $this->imageApi = $imageApi;
        $this->minifyCss = $minifyCss;
        $this->minifyJs = $minifyJs;
        $this->smartImageEnabled = $smartImageEnabled;
        $this->smartImageDesktopWidthMax = $smartImageDesktopWidthMax;
        $this->smartImageMobileWidthMax = $smartImageMobileWidthMax;
        $this->smartImageDesktopQuality = $smartImageDesktopQuality;
        $this->smartImageMobileQuality = $smartImageMobileQuality;
        $this->watermarkEnabled = $watermarkEnabled;
        $this->watermarkUrl = $watermarkUrl;
        $this->watermarkImageMin = $watermarkImageMin;
        $this->watermarkBorder = $watermarkBorder;
        $this->watermarkPosition = $watermarkPosition;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isWebpCompression(): bool
    {
        return $this->webpCompression;
    }

    public function isImageApi(): bool
    {
        return $this->imageApi;
    }

    public function isMinifyCss(): bool
    {
        return $this->minifyCss;
    }

    public function isMinifyJs(): bool
    {
        return $this->minifyJs;
    }

    public function isSmartImageEnabled(): bool
    {
        return $this->smartImageEnabled;
    }

    public function getSmartImageDesktopWidthMax(): int
    {
        return $this->smartImageDesktopWidthMax;
    }

    public function getSmartImageMobileWidthMax(): int
    {
        return $this->smartImageMobileWidthMax;
    }

    public function getSmartImageDesktopQuality(): int
    {
        return $this->smartImageDesktopQuality;
    }

    public function getSmartImageMobileQuality(): int
    {
        return $this->smartImageMobileQuality;
    }

    public function isWatermarkEnabled(): bool
    {
        return $this->watermarkEnabled;
    }

    public function getWatermarkUrl(): string
    {
        return $this->watermarkUrl;
    }

    public function getWatermarkImageMin(): int
    {
        return $this->watermarkImageMin;
    }

    public function getWatermarkBorder(): int
    {
        return $this->watermarkBorder;
    }

    public function getWatermarkPosition(): int
    {
        return $this->watermarkPosition;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromApiResponse(array $data): self
    {
        return new self($data['OptimizerEnabled'] ?: false, $data['OptimizerEnableWebP'] ?: false, $data['OptimizerEnableManipulationEngine'] ?: false, $data['OptimizerMinifyCSS'] ?: false, $data['OptimizerMinifyJavaScript'] ?: false, $data['OptimizerAutomaticOptimizationEnabled'] ?: false, (int) $data['OptimizerDesktopMaxWidth'], (int) $data['OptimizerMobileMaxWidth'], (int) $data['OptimizerImageQuality'], (int) $data['OptimizerMobileImageQuality'], $data['OptimizerWatermarkEnabled'] ?: false, (string) $data['OptimizerWatermarkUrl'], (int) $data['OptimizerWatermarkMinImageSize'], (int) $data['OptimizerWatermarkOffset'], (int) $data['OptimizerWatermarkPosition']);
    }

    /**
     * @return array<string, mixed>
     */
    public function toApiPostRequest(): array
    {
        return ['OptimizerEnabled' => $this->enabled, 'OptimizerEnableWebP' => $this->webpCompression, 'OptimizerEnableManipulationEngine' => $this->imageApi, 'OptimizerMinifyCSS' => $this->minifyCss, 'OptimizerMinifyJavaScript' => $this->minifyJs, 'OptimizerAutomaticOptimizationEnabled' => $this->smartImageEnabled, 'OptimizerDesktopMaxWidth' => (string) $this->smartImageDesktopWidthMax, 'OptimizerMobileMaxWidth' => $this->smartImageMobileWidthMax, 'OptimizerImageQuality' => $this->smartImageDesktopQuality, 'OptimizerMobileImageQuality' => $this->smartImageMobileQuality, 'OptimizerWatermarkEnabled' => $this->watermarkEnabled, 'OptimizerWatermarkUrl' => $this->watermarkUrl, 'OptimizerWatermarkMinImageSize' => $this->watermarkImageMin, 'OptimizerWatermarkOffset' => $this->watermarkBorder, 'OptimizerWatermarkPosition' => $this->watermarkPosition];
    }

    /**
     * @param array<string, mixed> $postData
     */
    public function handlePost(array $postData): void
    {
        $this->enabled = '1' === ($postData['enabled'] ?? '0');
        $this->webpCompression = '1' === ($postData['webp_compression'] ?? '0');
        $this->imageApi = '1' === ($postData['image_api'] ?? '0');
        $this->minifyCss = '1' === ($postData['minify_css'] ?? '0');
        $this->minifyJs = '1' === ($postData['minify_js'] ?? '0');
        if (isset($postData['smart_image'])) {
            $this->smartImageEnabled = '1' === ($postData['smart_image']['enabled'] ?? '0');
            $this->smartImageDesktopWidthMax = (int) ($postData['smart_image']['desktop_width_max'] ?: 1600);
            $this->smartImageMobileWidthMax = (int) ($postData['smart_image']['mobile_width_max'] ?: 800);
            $this->smartImageDesktopQuality = (int) ($postData['smart_image']['desktop_quality'] ?: 85);
            $this->smartImageMobileQuality = (int) ($postData['smart_image']['mobile_quality'] ?: 70);
        }
        if (isset($postData['watermark'])) {
            $this->watermarkEnabled = '1' === ($postData['watermark']['enabled'] ?? '0');
            $this->watermarkUrl = (string) ($postData['watermark']['url'] ?: '');
            $this->watermarkImageMin = (int) ($postData['watermark']['image_min'] ?: 300);
            $this->watermarkBorder = (int) ($postData['watermark']['border'] ?: 3);
            $this->watermarkPosition = (int) ($postData['watermark']['position'] ?: 0);
        }
    }
}
