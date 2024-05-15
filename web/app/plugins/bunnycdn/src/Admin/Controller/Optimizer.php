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

namespace Bunny\Wordpress\Admin\Controller;

use Bunny\Wordpress\Admin\Container;

class Optimizer implements ControllerInterface
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run(bool $isAjax): void
    {
        try {
            $cdnConfig = $this->container->getCdnConfig();
            $pullzoneId = $cdnConfig->getPullzoneId();
            if ($cdnConfig->isAgencyMode()) {
                $this->container->renderTemplateFile('error.api-unavailable.php', ['error' => 'There is no API key configured.']);

                return;
            }
            if (null === $pullzoneId) {
                throw new \Exception('Could not find the associated pullzone.');
            }
            $api = $this->container->getApiClient();
            $config = $api->getPullzoneDetails($pullzoneId)->getConfig();
        } catch (\Exception $e) {
            $this->container->renderTemplateFile('error.api-unavailable.php', ['error' => $e->getMessage()]);

            return;
        }
        $showSuccess = false;
        $error = null;
        if (!empty($_POST)) {
            check_admin_referer('bunnycdn-save-optimizer');
            $config->handlePost($_POST['optimizer'] ?: []);
            try {
                $api->saveOptimizerConfig($config, $pullzoneId);
                $showSuccess = true;
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        $this->container->renderTemplateFile('optimizer.php', ['config' => $config, 'showSuccess' => $showSuccess, 'error' => $error], ['cssClass' => 'optimizer']);
    }
}
