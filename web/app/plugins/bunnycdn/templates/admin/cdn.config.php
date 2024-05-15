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
 * @var string[] $hostnames
 * @var string|null $hostnameWarning
 * @var bool $showApiKeyAlert
 * @var bool $showCdnAccelerationAlert
 * @var bool $showSuccess
 * @var bool $suggestAcceleration
 * @var string|null $error
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
    <div class="bn-px-5">
        <section class="bn-section bn-px-0">
            <?php if (null !== $error): ?>
                <div class="alert red">
                    <?= esc_html($error) ?>
                </div>
            <?php endif; ?>
            <?php if (true === $showSuccess): ?>
                <div class="alert green">
                    The configuration was saved.
                </div>
            <?php endif; ?>
            <?php if ($showApiKeyAlert): ?>
                <div class="alert red">Could not connect to api.bunny.net. Please make sure the API key is correct.</div>
            <?php endif; ?>
            <?php if ($showCdnAccelerationAlert): ?>
                <div class="bn-m-5"><?= $this->renderPartialFile('cdn-acceleration.alert.php'); ?></div>
            <?php endif; ?>
            <div>
                <input type="checkbox" class="bn-toggle" id="cdn-enabled" name="cdn[enabled]" value="1" <?= $config->isEnabled() ? 'checked' : '' ?> />
                <label for="cdn-enabled">Bunny CDN</label>
            </div>
            <?php if ($suggestAcceleration): ?>
                <div id="cdn-acceleration-enable-section" class="bn-alert-cdn-acceleration">
                    <p>We detected you're using Bunny DNS with CDN acceleration, but this plugin isn't set up for this. Please enable CDN acceleration, so the plugin works seamlessly.</p>
                    <button type="button" class="bn-button bn-button--secondary bn-button--lg" id="cdn-acceleration-enable">Enable CDN acceleration</button>
                    <div class="alert bn-mt-4 bn-d-none"></div>
                </div>
            <?php endif; ?>
            <p class="bn-mt-4">Enabling bunny.net acceleration will enable global CDN caching to ensure your site is super fast and always only a hop away from your global audience.</p>
            <input type="submit" value="Save Settings" class="bn-button bn-button--primary bn-button--lg bn-mt-4">
        </section>
        <section class="bn-section config bn-px-0 hide-disabled <?= $config->isEnabled() ? '' : 'bn-d-none' ?>">
            <ul class="bn-m-0">
                <li class="bn-section bn-px-0 bn-section--split" id="cdn-cache-purge-section">
                    <label class="bn-section__title">Purge Zone Cache</label>
                    <div class="bn-section__content">
                        <p>Purging the cache will remove your files from the <a href="https://bunny.net" target="_blank">bunny.net</a> CDN cache and re-download them from your origin server.</p>
                        <p class="bn-my-4">Purging your cache might temporarily slow down your website performance as the content is repopulated to the <a href="https://bunny.net" target="_blank">bunny.net</a> CDN.</p>
                        <?php if ($config->isAgencyMode()): ?>
                            <div class="alert red">There is no API key configured, so the Zone Cache can only be purged at <a href="https://dash.bunny.net" target="_blank">dash.bunny.net</a>.</div>
                        <?php else: ?>
                            <button type="button" class="bn-button bn-button--primary bn-button--lg" id="cdn-cache-purge">Purge CDN Cache</button>
                            <div class="alert bn-mt-2 bn-d-none"></div>
                        <?php endif; ?>
                    </div>
                </li>
                <li class="bn-section bn-px-0 bn-section--split">
                    <label class="bn-section__title" for="cdn-config-pullzone">Pull Zone</label>
                    <div class="bn-section__content">
                        <input type="text" class="bn-input bn-is-max-width" value="<?= esc_attr($config->getPullzoneName()) ?>" id="cdn-config-pullzone" name="cdn[pullzone]" disabled>
                        <p class="bn-mt-4">This is your pullzone's name. To change this to a different pullzone, please reset the plugin and reconfigure.</p>
                    </div>
                </li>
                <li class="bn-section bn-px-0 bn-section--split">
                    <label class="bn-section__title" for="cdn-config-hostname">CDN Hostname</label>
                    <div class="bn-section__content">
                        <select id="cdn-config-hostname" class="bn-select bn-is-max-width" name="cdn[hostname]" <?= $config->isAgencyMode() ? 'disabled' : '' ?>>
                            <?php foreach ($hostnames as $hostname): ?>
                                <option value="<?= esc_attr($hostname) ?>" <?= $hostname === $config->getHostname() ? 'selected' : '' ?>><?= esc_html($hostname) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (null !== $hostnameWarning): ?>
                            <div class="alert red bn-mt-4">
                                <?= esc_html($hostnameWarning) ?>
                            </div>
                        <?php endif; ?>
                        <p class="bn-mt-4">This value selects the hostname that will be used to deliver your files. To configure a custom hostname, please visit your Pull Zone settings on <a href="https://dash.bunny.net" target="_blank">dash.bunny.net</a>.</p>
                    </div>
                </li>
                <li class="bn-section bn-px-0 bn-section--split">
                    <label class="bn-section__title" for="cdn-config-url">Site URL</label>
                    <div class="bn-section__content">
                        <input type="text" class="bn-input bn-is-max-width" id="cdn-config-url" name="cdn[url]" value="<?= esc_url($config->getUrl()) ?>">
                        <p class="bn-mt-4">The public URL where your website is accessible. This helps the plugin determine which URLs to rewrite. In most cases, this should be the same as the default value.</p>
                        <p class="bn-mt-4">Default value: <?= esc_html(site_url()) ?></p>
                    </div>
                </li>
                <li class="bn-section bn-px-0 bn-section--split" id="cdn-config-excluded-combobox">
                    <label class="bn-section__title" for="cdn-config-excluded">Excluded Extensions</label>
                    <div class="bn-section__content">
                        <input type="text" class="bn-input bn-is-max-width" id="cdn-config-excluded" placeholder="Add new..." aria-controls="options" role="combobox" autocomplete="off">
                        <div class="combobox-options">
                            <template data-role="create">
                                <span role="option" data-create="true" data-combobox-option-default="true"></span>
                            </template>
                        </div>
                        <ul class="combobox-selected" aria-live="polite">
                            <template>
                                <li role="listitem">
                                    <p>.{{value}}</p>
                                    <input type="hidden" name="cdn[excluded][]" value="{{value}}">
                                    <button type="button" role="remove">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                            <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                                        </svg>
                                    </button>
                                </li>
                            </template>
                            <?php foreach ($config->getExcluded() as $excluded): ?>
                                <li role="listitem">
                                    <p><?= esc_html($excluded) ?></p>
                                    <input type="hidden" name="cdn[excluded][]" value="<?= esc_attr($excluded) ?>">
                                    <button type="button" role="remove">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                            <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                                        </svg>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="bn-mt-4">Files ending with these extensions will be excluded from the CDN.</p>
                        <p class="bn-mt-4">Default value: .php</p>
                    </div>
                </li>
                <li class="bn-section bn-px-0 bn-section--split" id="cdn-config-included-combobox">
                    <label class="bn-section__title" for="cdn-config-included">Included Directories</label>
                    <div class="bn-section__content">
                        <input type="text" class="bn-input bn-is-max-width" id="cdn-config-included" placeholder="Add new..." aria-controls="options" role="combobox" autocomplete="off">
                        <div class="combobox-options">
                            <template data-role="create">
                                <span role="option" data-create="true" data-combobox-option-default="true"></span>
                            </template>
                        </div>
                        <ul class="combobox-selected" aria-live="polite">
                            <template>
                                <li role="listitem">
                                    <p>{{value}}</p>
                                    <input type="hidden" name="cdn[included][]" value="{{value}}">
                                    <button type="button" role="remove">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                            <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                                        </svg>
                                    </button>
                                </li>
                            </template>
                            <?php foreach ($config->getIncluded() as $included): ?>
                                <li role="listitem">
                                    <p><?= esc_html($included) ?></p>
                                    <input type="hidden" name="cdn[included][]" value="<?= esc_attr($included) ?>">
                                    <button type="button" role="remove">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                            <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                                        </svg>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="bn-mt-4">Only the files inside the listed directories will be served through the CDN.</p>
                        <p class="bn-mt-4">Default value: wp-includes/, wp-content/themes/, wp-content/uploads/</p>
                    </div>
                </li>
                <li class="bn-section bn-px-0 bn-section--split">
                    <label class="bn-section__title" for="cdn-config-disable-admin">Disable for Admin</label>
                    <div class="bn-section__content">
                        <input type="checkbox" class="bn-toggle" id="cdn-config-disable-admin" name="cdn[disable_admin]" value="1" <?= $config->isDisableAdmin() ? 'checked' : '' ?> />
                        <label for="cdn-config-disable-admin" class="bn-text-200-regular">Do not serve content from the CDN for users logged in as administrators</label>
                        <p class="bn-mt-4">Enable/disable CDN rewriting when signed in as an administrator.</p>
                    </div>
                </li>
                <li class="bn-section bn-section--no-divider bn-pb-0 bn-px-0 bn-section--split">
                    <label class="bn-section__title" for="cdn-config-api-key">API key</label>
                    <div class="bn-section__content">
                        <div class="<?= $config->isAgencyMode() ? '' : 'bn-input-with-addons' ?> bn-is-max-width">
                            <input type="text" class="bn-input" id="cdn-config-api-key" name="cdn[api_key]" <?= !$config->isAgencyMode() ? 'placeholder="********" readonly' : '' ?>>
                            <?php if (!$config->isAgencyMode()): ?>
                            <div class="bn-input-addons">
                                <button type="button" data-field-edit="cdn-config-api-key"><svg width="18" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z"/></svg></button>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($config->isAgencyMode()): ?>
                            <p>Inform an API key to enable features like Bunny Optimizer and Content Offloading. You can obtain the API key from <a href="https://dash.bunny.net/account/settings" target="_blank">dash.bunny.net</a>.</p>
                        <?php else: ?>
                            <p>You can obtain the API key from <a href="https://dash.bunny.net/account/settings" target="_blank">dash.bunny.net</a>.</p>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </section>
        <section class="bn-section bn-px-0 bn-section--no-divider hide-disabled <?= $config->isEnabled() ? '' : 'bn-d-none' ?>">
            <input type="submit" value="Save Settings" class="bn-button bn-button--primary bn-button--lg">
        </section>
    </div>
    <script type="importmap">
        {
            "imports": {
                "@github/combobox-nav": "<?= $this->assetUrl('combobox.github.js') ?>"
            }
        }
    </script>
    <script type="module">
        import Combobox from '<?= $this->assetUrl('combobox.js') ?>';
        new Combobox('#cdn-config-excluded', '#cdn-config-excluded-combobox .combobox-options', '#cdn-config-excluded-combobox .combobox-selected', {
            preCreateTransform: (content) => content.replace(/^\.+/, ''),
        });
        new Combobox('#cdn-config-included', '#cdn-config-included-combobox .combobox-options', '#cdn-config-included-combobox .combobox-selected');
    </script>
    <?= wp_nonce_field('bunnycdn-save-cdn') ?>
</form>
