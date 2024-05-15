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

class Router
{
    /**
     * @var array<string, class-string<Controller\ControllerInterface>>
     */
    private const SECTIONS = ['about' => Controller\About::class, 'cdn' => Controller\Cdn::class, 'cdn-cache-purge' => Controller\CdnCachePurge::class, 'fonts' => Controller\Fonts::class, 'index' => Controller\Index::class, 'offloader' => Controller\Offloader::class, 'optimizer' => Controller\Optimizer::class, 'overview' => Controller\Overview::class, 'reset' => Controller\Reset::class, 'user-data' => Controller\UserData::class, 'wizard' => Controller\Wizard::class];
    private const BEFORE_SETUP_SECTIONS = ['index', 'user-data', 'wizard'];
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function route(bool $isAjax = false): void
    {
        $section = 'index';
        if (isset($_REQUEST['section'])) {
            if (is_string($_REQUEST['section'])) {
                $section = $_REQUEST['section'];
            } else {
                $section = '404';
            }
        }
        if ('1' !== get_option('bunnycdn_wizard_finished') && !in_array($section, self::BEFORE_SETUP_SECTIONS, true)) {
            $url = $this->container->getAdminUrl('index');
            $this->container->redirect($url);

            return;
        }
        if (isset(self::SECTIONS[$section])) {
            $controllerName = self::SECTIONS[$section];
            $this->container->newController($controllerName)->run($isAjax);

            return;
        }
        $this->container->renderTemplateFile('index.error.php', ['error' => 'Page not found'], ['cssClass' => 'index'], '_base.index.php');
    }
}
