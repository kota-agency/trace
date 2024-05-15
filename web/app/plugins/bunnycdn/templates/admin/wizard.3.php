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
 * @var string $overviewUrl
 */
?>
<section class="bn-section">
    <div class="bn-section__title bn-mb-0">Yaaay, success!</div>
</section>
<section class="bn-section">
    <img src="<?= $this->assetUrl('wizard-step3.svg') ?>" height="200">
    <div class="alert green bn-mt-3">
        Your site is now connected to bunny.net.
    </div>
    <a href="<?= esc_url($overviewUrl) ?>" class="bn-button bn-button--primary">Go to overview page</a>
</section>
