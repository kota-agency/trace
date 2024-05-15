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
 * @var array<string, int> $attachments
 * @var \Bunny\Wordpress\Config\Offloader $config
 */
?>
<div class="container bg-gradient bn-p-0 bn-pb-5">
    <section class="bn-section bn-section-hero bn-p-5">
        <div>
            <h1>Bunny Storage</h1>
            <h2>What is Content Offloading?</h2>
            <p>Improve your website performance and user experience. Reduce load times and increase conversion rates in just a few clicks. Get hopping in under 5 minutes without writing a single line of code.</p>
        </div>
        <img src="<?= $this->assetUrl('offloader-header.svg') ?>" alt="bunny offloader">
    </section>
    <div class="bn-m-5">
        <?= $this->renderPartialFile('cdn-acceleration.alert.php'); ?>
    </div>
    <section class="bn-section statistics bn-section--no-divider">
        <?= $this->renderPartialFile('offloader.statistics.php', ['attachments' => $attachments, 'config' => $config, 'attachmentsWithError' => 0]) ?>
    </section>
</div>
