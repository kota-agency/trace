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
 * @var \Bunny\Wordpress\Config\Cdn $config
 * @var string|null $error
 * @var string $resetUrl
 * @var bool $isAccelerated
 * @var bool $showApiKeyAlert
 * @var bool $showCdnAccelerationAlert
 * @var bool $showSuccess
 * @var \Bunny\Wordpress\Api\Pullzone\Info[]|null $pullzones
 * @var string|null $url
 */
?>
<form class="container bg-gradient bn-p-0" method="POST" autocomplete="off">
    <section class="bn-section bn-section-hero">
        <div>
            <h1>Bunny CDN</h1>
            <h2>What is Bunny CDN?</h2>
            <p>Bunny CDN helps you accelerate your website and supercharge your web presence. Through a network of over 100 global datacenters, Bunny CDN stores your files right next to your users and delivers them with lightning speed.</p>
            <a href="https://bunny.net/cdn/" target="_blank" class="bn-link bn-link--external">More Information</a>
        </div>
        <img src="<?= esc_url($this->assetUrl('cdn-header.svg')) ?>" alt="bunny CDN">
    </section>
    <?php if ($showApiKeyAlert): ?>
        <div class="alert red bn-m-5">Could not connect to api.bunny.net. Please make sure the API key is correct.</div>
    <?php elseif ($showCdnAccelerationAlert): ?>
        <div class="bn-m-5"><?= $this->renderPartialFile('cdn-acceleration.alert.php'); ?></div>
    <?php elseif (!$isAccelerated): ?>
        <div id="cdn-acceleration-disable-section" class="bn-alert-cdn-acceleration">
            <?php if ($config->isAgencyMode()): ?>
                <section class="bn-section bn-px-0 bn-py-0 bn-section--no-divider">
                    <p>This plugin is configured to use CDN acceleration, but we couldn't detect it being active on your website. Please <a href="<?= esc_url($resetUrl) ?>">reset the plugin</a> to re-enable the CDN functionality.</p>
                </section>
            <?php else: ?>
                <section class="bn-section bn-px-0">
                    <p>This plugin is configured to use CDN acceleration, but we couldn't detect it being active on your website. Let's reconfigure it, so we can keep your website hopping.</p>
                </section>
                <section class="bn-section bn-px-0">
                    <ul class="bn-m-0">
                        <li class="bn-section bn-px-0 bn-section--split">
                            <label class="bn-section__title" for="website-url">Website URL:</label>
                            <div class="bn-section__content">
                                <input type="text" class="bn-input" id="website-url" name="url" value="<?= esc_attr($url) ?>" <?= $isAccelerated ? 'readonly' : '' ?>>
                            </div>
                        </li>
                        <li class="bn-section bn-px-0 bn-section--split bn-section--no-divider">
                            <label class="bn-section__title" for="pullzone-id">Pull Zone</label>
                            <div class="bn-section__content">
                                <select class="bn-select" name="pullzone_id" id="pullzone-id">
                                    <option value="0" selected>Create a new pullzone</option>
                                    <?php foreach ($pullzones as $pullzone): ?>
                                        <option value="<?= esc_attr($pullzone->getId()) ?>"><?= esc_html($pullzone->getName()) ?> (<?= esc_html($pullzone->getId()) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </li>
                    </ul>
                </section>
                <section class="bn-section bn-px-0 bn-section--no-divider">
                    <button type="button" class="bn-button bn-button--secondary bn-button--lg" id="cdn-acceleration-disable">Disable CDN acceleration</button>
                    <div class="alert bn-mt-4 bn-d-none"></div>
                </section>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="bn-px-5">
        <?php if ($isAccelerated): ?>
            <section class="bn-section bn-px-0">
                <?php if (null === $error): ?>
                    <div class="alert green">Your website is being accelerated!</div>
                <?php else: ?>
                    <div class="alert red"><?= esc_html($error) ?></div>
                <?php endif; ?>
                <p class="bn-mt-4">This website is using Bunny DNS and the CDN acceleration feature is enabled, so you don't have to worry about any configurations.</p>
            </section>
        <?php endif; ?>
        <section class="bn-section bn-px-0 <?= $config->isAgencyMode() ? 'bn-section--no-divider' : '' ?>">
            <ul class="bn-m-0">
                <li class="bn-section bn-px-0 bn-section--split">
                    <label class="bn-section__title" for="cdn-config-pullzone">Pull Zone</label>
                    <div class="bn-section__content">
                        <input type="text" class="bn-input bn-is-max-width" value="<?= esc_attr($config->getPullzoneName()) ?>" id="cdn-config-pullzone" name="cdn[pullzone]" disabled>
                        <p class="bn-mt-4">This is your pullzone's name.</p>
                    </div>
                </li>
                <li class="bn-section bn-px-0 bn-section--split <?= $config->isAgencyMode() ? 'bn-section--no-divider' : '' ?>" id="cdn-cache-purge-section">
                    <label class="bn-section__title">Purge Zone Cache</label>
                    <div class="bn-section__content">
                        <p>Purging the cache will remove your files from the <a href="https://bunny.net" target="_blank">bunny.net</a> CDN cache and re-download them from your origin server.</p>
                        <p class="bn-my-4">Purging your cache might temporarily slow down your website performance as the content is repopulated to the <a href="https://bunny.net" target="_blank">bunny.net</a> CDN.</p>
                        <?php if ($config->isAgencyMode()): ?>
                            <div class="alert red">
                                There is no API key configured, so the Zone Cache can only be purged at <a href="https://dash.bunny.net" target="_blank">dash.bunny.net</a>.
                            </div>
                        <?php else: ?>
                            <button type="button" class="bn-button bn-button--primary bn-button--lg" id="cdn-cache-purge">Purge CDN Cache</button>
                            <div class="alert green bn-mt-2 bn-d-none">The cache was purged.</div>
                        <?php endif; ?>
                    </div>
                </li>
                <?php if (!$config->isAgencyMode()): ?>
                    <li class="bn-section bn-section--no-divider bn-pb-0 bn-px-0 bn-section--split">
                        <label class="bn-section__title" for="cdn-config-api-key">API key</label>
                        <div class="bn-section__content">
                            <div class="bn-input-with-addons bn-is-max-width">
                                <input type="text" class="bn-input" id="cdn-config-api-key" name="cdn[api_key]" placeholder="********" readonly>
                                <div class="bn-input-addons">
                                    <button type="button" data-field-edit="cdn-config-api-key"><svg width="18" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z"/></svg></button>
                                </div>
                            </div>
                            <p>You can obtain the API key from <a href="https://dash.bunny.net/account/settings" target="_blank">dash.bunny.net</a>.</p>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </section>
        <?php if (!$config->isAgencyMode()): ?>
            <section class="bn-section bn-px-0 bn-section--no-divider">
                <input type="submit" value="Save Settings" class="bn-button bn-button--primary bn-button--lg">
            </section>
        <?php endif; ?>
    </div>
    <?= wp_nonce_field('bunnycdn-save-cdn') ?>
</form>
