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

// Don't load directly.
if (!defined('ABSPATH')) {
    exit('-1');
}

/**
 * @var \Bunny\Wordpress\Admin\Container $this
 * @var string $continueUrl
 * @var string $agencyModeUrl
 */
?>
<section class="bn-section">
    <div class="bn-section__title bn-m-0">Welcome to the bunny.net configurator!</div>
</section>
<section class="bn-section">
    <p class="bn-mb-3">The Integration Wizard guides you through a simple and straightforward process of integrating your WordPress website with bunny.net acceleration features. The plugin will guide you through 3 basic steps to help you get your website hopping in just a few minutes.</p>
    <div class="alert blue">
        "Agency mode" is designed for administrators who manage multiple WordPress integrations with the same bunny.net dashboard login and API key. The plugin functionality in this mode is limited. Your API keys will not be kept on this WordPress instance and you will need to use dash.bunny.net to manage your settings.
    </div>
    <div>
        <a href="<?= esc_url($continueUrl) ?>" class="bn-button bn-button--primary">Integration Wizard</a>
        <a href="<?= esc_url($agencyModeUrl) ?>" class="bn-button bn-button--secondary bn-ms-3">Agency Mode</a>
    </div>
</section>
