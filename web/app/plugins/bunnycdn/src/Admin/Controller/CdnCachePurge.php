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

class CdnCachePurge implements ControllerInterface
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run(bool $isAjax): void
    {
        if (false === check_ajax_referer('bunnycdn-save-cdn', false, false)) {
            if ($isAjax) {
                wp_send_json_error(null, 400);
            } else {
                $this->container->renderTemplateFile('index.error.php', ['error' => 'Invalid request.']);
            }

            return;
        }
        if ('POST' !== $_SERVER['REQUEST_METHOD'] || !$isAjax) {
            wp_send_json_error(null, 400);

            return;
        }
        $config = $this->container->getCdnConfig();
        if (!$config->isEnabled() && !$config->isAccelerated()) {
            wp_send_json_error(['message' => 'The Bunny CDN is disabled.']);

            return;
        }
        if ($config->isAgencyMode()) {
            wp_send_json_error(['message' => 'There is no API key configured, so the Zone Cache can only be purged at dash.bunny.net.']);

            return;
        }
        $pullzoneId = $config->getPullzoneId();
        if (null === $pullzoneId) {
            wp_send_json_error(['message' => 'Could not find the associated pullzone.']);

            return;
        }
        try {
            $this->container->getApiClient()->purgePullzoneCache($pullzoneId);
            wp_send_json_success(['message' => 'The cache was purged.']);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
}
