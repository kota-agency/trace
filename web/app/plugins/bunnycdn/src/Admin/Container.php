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

namespace Bunny\Wordpress\Admin;

use Bunny\Wordpress\Admin\Controller\Attachment as AttachmentController;
use Bunny\Wordpress\Api\Client as ApiClient;
use Bunny\Wordpress\Api\Config as ApiConfig;
use Bunny\Wordpress\Config\Cdn as CdnConfig;
use Bunny\Wordpress\Config\Fonts as FontsConfig;
use Bunny\Wordpress\Config\Offloader as OffloaderConfig;
use Bunny\Wordpress\Container as AppContainer;
use Bunny\Wordpress\Service\AttachmentCounter;
use Bunny\Wordpress\Service\AttachmentMover;
use Bunny\Wordpress\Service\CdnAcceleration;
use Bunny\Wordpress\Service\OffloaderSetup;
use Bunny\Wordpress\Utils\Offloader as OffloaderUtils;
use Bunny\Wordpress\Utils\Wizard as WizardUtils;

class Container
{
    private AppContainer $container;
    private string $baseUrl;
    private string $templatePath;

    public function __construct(AppContainer $container, string $baseUrl, string $templatePath)
    {
        $this->container = $container;
        $this->baseUrl = $baseUrl;
        $this->templatePath = $templatePath;
    }

    public function newApiClient(ApiConfig $config): ApiClient
    {
        return $this->container->newApiClient($config);
    }

    public function getApiClient(): ApiClient
    {
        return $this->container->getApiClient();
    }

    /**
     * @param class-string<Controller\ControllerInterface> $className
     */
    public function newController(string $className): Controller\ControllerInterface
    {
        return new $className($this);
    }

    /**
     * @param array<string, mixed> $variables
     * @param array<string, mixed> $baseVariables
     */
    public function renderTemplateFile(string $filename, array $variables = [], array $baseVariables = [], string $base = '_base.php'): void
    {
        $baseVariables['contents'] = $this->renderFile($filename, $variables);
        $baseVariables['mode'] = get_option('bunnycdn_wizard_mode', 'standalone');
        echo $this->renderFile($base, $baseVariables);
    }

    /**
     * @param array<string, mixed> $variables
     */
    public function renderPartialFile(string $filename, array $variables = []): string
    {
        return $this->renderFile('partial/'.$filename, $variables);
    }

    /**
     * @param array<string, mixed> $variables
     */
    private function renderFile(string $filename, array $variables = []): string
    {
        ob_start();
        extract($variables);
        require sprintf('%s/%s', $this->templatePath, $filename);

        return ob_get_clean() ?: '';
    }

    /**
     * @param array<string, string> $items
     */
    public function renderMenu(iterable $items, string $cssClass = ''): string
    {
        return $this->renderPartialFile('menu.php', ['items' => $items, 'current' => $_GET['section'] ?: '', 'cssClass' => $cssClass]);
    }

    public function assetUrl(string $asset): string
    {
        return sprintf('%sassets/%s', $this->baseUrl, $asset);
    }

    public function getVersion(): string
    {
        return BUNNYCDN_WP_VERSION;
    }

    public function redirect(string $url): void
    {
        if (headers_sent()) {
            echo $this->renderPartialFile('redirect.php', ['url' => $url]);
        } else {
            wp_redirect($url);
        }
    }

    public function getCdnAcceleration(): CdnAcceleration
    {
        static $instance;
        if (null !== $instance) {
            return $instance;
        }
        $instance = new CdnAcceleration($this->getApiClient(), $_SERVER, $this->getAttachmentCounter(), $this->getCdnConfig()->isAgencyMode(), $this->getOffloaderConfig()->isEnabled(), $this->getOffloaderConfig()->isConfigured(), $this->getCdnConfig()->getPullzoneId());

        return $instance;
    }

    public function newCdnAccelerationForWizard(bool $isAgencyMode): CdnAcceleration
    {
        return new CdnAcceleration($this->getApiClient(), $_SERVER, $this->getAttachmentCounter(), $isAgencyMode, false, false, null);
    }

    public function getCdnConfig(): CdnConfig
    {
        return $this->container->getCdnConfig();
    }

    public function getFontsConfig(): FontsConfig
    {
        return $this->container->getFontsConfig();
    }

    public function getOffloaderConfig(): OffloaderConfig
    {
        return $this->container->getOffloaderConfig();
    }

    public function reloadOffloaderConfig(): OffloaderConfig
    {
        return $this->container->reloadOffloaderConfig();
    }

    public function newOffloaderSetup(): OffloaderSetup
    {
        return new OffloaderSetup($this->getApiClient(), $this->getCdnAcceleration(), $this->getOffloaderUtils());
    }

    public function getAttachmentCounter(): AttachmentCounter
    {
        return $this->container->getAttachmentCounter();
    }

    public function getOffloaderUtils(): OffloaderUtils
    {
        return $this->container->getOffloaderUtils();
    }

    public function getWizardUtils(): WizardUtils
    {
        static $instance;
        if (null !== $instance) {
            return $instance;
        }
        $instance = new WizardUtils();

        return $instance;
    }

    public function newAttachmentMover(): AttachmentMover
    {
        return $this->container->newAttachmentMover();
    }

    /**
     * @param array<string, mixed> $params
     */
    public function getAdminUrl(string $section, array $params = []): string
    {
        $params['page'] = 'bunnycdn';
        $params['section'] = $section;
        if ('index' === $section) {
            unset($params['section']);
        }

        return add_query_arg($params, admin_url('admin.php'));
    }

    public function newAttachmentController(): AttachmentController
    {
        return new AttachmentController($this->container->getStorageClientFactory()->newWithConfig($this->container->getOffloaderConfig()));
    }
}
