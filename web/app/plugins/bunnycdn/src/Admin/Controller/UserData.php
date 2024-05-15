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
use Bunny\Wordpress\Api\User;

class UserData implements ControllerInterface
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run(bool $isAjax): void
    {
        if (!$isAjax) {
            $this->container->renderTemplateFile('index.error.php', ['error' => 'Invalid page.'], ['cssClass' => 'index'], '_base.index.php');

            return;
        }
        $user = get_option('bunnycdn_api_user', null);
        if (!$user instanceof User) {
            $mode = get_option('bunnycdn_wizard_mode', 'standalone');
            if ('agency' === $mode) {
                wp_send_json_error(['message' => 'User information not available.']);

                return;
            }
            try {
                $user = $this->container->getApiClient()->getUser();
                update_option('bunnycdn_api_user', $user);
            } catch (\Exception $e) {
                wp_send_json_error(['message' => $e->getMessage()]);

                return;
            }
        }
        wp_send_json_success(['name' => $user->getName(), 'email' => $user->getEmail(), 'avatar_url' => $user->getAvatarUrl()]);
    }
}
