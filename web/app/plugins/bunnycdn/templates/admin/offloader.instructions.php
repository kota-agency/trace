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
 * @var bool $showApiKeyAlert
 * @var bool $showCdnAccelerationAlert
 * @var bool $suggestAcceleration
 */
?>
<form class="container bg-gradient bn-p-0" method="POST" autocomplete="off">
    <section class="bn-section bn-section-hero bn-p-5">
        <div>
            <h1>Bunny Offloader</h1>
            <p class="bn-text-200-regular">
                Automatically move content from your WordPress platform to Bunny Storage, our high-performance and cost-effective
                cloud storage service for optimal latency, global replication, and maximum throughput. After activating it,
                any new content you upload to WordPress will automatically be transferred to Bunny Storage, providing your
                users with up to 5x faster download speeds compared to traditional object storage solutions.
            </p>
        </div>
        <img src="<?= $this->assetUrl('offloader-header.svg') ?>" alt="bunny offloader">
    </section>
    <?php if ($showApiKeyAlert): ?>
        <div class="alert red bn-m-5">Could not connect to api.bunny.net. Please make sure the API key is correct.</div>
    <?php endif; ?>
    <?php if ($showCdnAccelerationAlert): ?>
    <div class="bn-m-5"><?= $this->renderPartialFile('cdn-acceleration.alert.php'); ?></div>
    <?php endif; ?>
    <div class="bn-px-5">
        <?php if ($suggestAcceleration): ?>
            <section class="bn-section bn-px-0 bn-section--no-divider">
                <div id="cdn-acceleration-enable-section" class="bn-alert-cdn-acceleration">
                    <p>We detected you're using Bunny DNS with CDN acceleration, but this plugin isn't set up for this. Please enable CDN acceleration to use the Content Offloading feature.</p>
                    <button type="button" class="bn-button bn-button--secondary bn-button--lg" id="cdn-acceleration-enable">Enable CDN acceleration</button>
                    <div class="alert bn-mt-4 bn-d-none"></div>
                </div>
            </section>
        <?php endif; ?>
        <section class="bn-section statistics">
            <?= $this->renderPartialFile('offloader.statistics.php', ['attachments' => $attachments, 'config' => $config, 'attachmentsWithError' => 0]) ?>
        </section>
        <section class="bn-section bn-px-0 bn-section--no-divider">
            <p class="bn-text-200-regular">To enable Bunny Offloader and unlock up to 5X faster performance for uncached content, you must first enable Bunny DNS with CDN Proxy in your bunny.net account.</p>
            <a class="bn-button bn-button--primary bn-mt-4" href="https://support.bunny.net/hc/en-us/articles/12936040570012-How-to-enable-CDN-acceleration-in-Bunny-DNS" target="_blank">Enable Bunny DNS</a>
        </section>
    </div>
    <?= wp_nonce_field('bunnycdn-save-cdn') ?>
</form>
