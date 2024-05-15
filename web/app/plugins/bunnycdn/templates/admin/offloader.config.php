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
 * @var int $attachmentsWithError
 * @var string $cdnUrl
 * @var \Bunny\Wordpress\Config\Offloader $config
 * @var string|null $errorMessage
 * @var array<string, string> $replicationRegions
 * @var bool $showApiKeyAlert
 * @var bool $showCdnAccelerationAlert
 * @var bool $showOffloaderSyncErrors
 * @var string|null $successMessage
 * @var string $viewOriginFileUrlTemplate
 * @var string $viewStorageFileUrlTemplate
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
        <div class="bn-m-5 bn-mb-0"><?= $this->renderPartialFile('cdn-acceleration.alert.php'); ?></div>
    <?php endif; ?>
    <section class="bn-section statistics">
        <?= $this->renderPartialFile('offloader.statistics.php', ['attachments' => $attachments, 'config' => $config, 'attachmentsWithError' => $attachmentsWithError]) ?>
    </section>
    <?php if ($showOffloaderSyncErrors): ?>
    <section class="bn-section sync-errors">
        <h2 class="bn-section__title bn-mb-4">Sync errors</h2>
        <div class="bn-section__content">
            <table id="offloader-sync-errors" class="loading">
                <thead>
                    <tr>
                        <th>File</Th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2">
                            <span class="loading">Loading...</span>
                        </td>
                    </tr>
                </tbody>
                <template class="tbody">
                    <tr data-attachment-id="{{id}}">
                        <td>
                            <span class="filename">{{filename}}</span>
                            <br />
                            <span class="reason">{{reason}}</span>
                            <ul class="links">
                                <li><a href="<?= $viewOriginFileUrlTemplate ?>" target="_blank">Local</a></li>
                                <li><a href="<?= $viewStorageFileUrlTemplate ?>" target="_blank">Remote</a></li>
                            </ul>
                        </td>
                        <td>
                            <div class="actions">
                                <button type="button" class="bn-button bn-button--secondary" data-keep="origin" data-attachment-id="{{id}}">Keep local</button>
                                <button type="button" class="bn-button bn-button--secondary" data-keep="storage" data-attachment-id="{{id}}">Keep remote</button>
                            </div>
                        </td>
                    </tr>
                </template>
            </table>
        </div>
    </section>
    <?php endif; ?>
    <div class="bn-px-5">
        <section class="bn-section bn-px-0">
            <?php if (null !== $successMessage): ?>
                <div class="alert green"><?= esc_html($successMessage) ?></div>
            <?php endif; ?>
            <?php if (null !== $errorMessage): ?>
                <div class="alert red"><?= esc_html($errorMessage) ?></div>
            <?php endif; ?>
            <div>
                <input type="checkbox" class="bn-toggle" id="offloader-enabled" name="offloader[enabled]" value="1" <?= $config->isEnabled() ? 'checked' : '' ?> />
                <label for="offloader-enabled">Content Offloading</label>
            </div>
            <p class="bn-mt-2">New media uploads will be stored <em>exclusively</em> in the Bunny Storage.</p>
            <input type="submit" value="Save Settings" class="bn-button bn-button--primary bn-button--lg bn-mt-2 hide-enabled <?= $config->isEnabled() ? 'bn-d-none' : '' ?>">
        </section>
        <section class="bn-section bn-px-0 bn-pt-0 hide-disabled bn-section--no-divider <?= $config->isEnabled() ? '' : 'bn-d-none' ?>">
            <ul class="bn-m-0">
                <li class="bn-section bn-px-0 bn-section--split">
                    <label class="bn-section__title" for="offloader-storage-region">Storage Region</label>
                    <div class="bn-section__content">
                        <input type="text" id="offloader-storage-region" class="bn-input bn-is-max-width" value="Europe (Frankfurt)" disabled>
                        <?php if (!$config->isConfigured()): ?>
                            <p>The primary region for your data where file uploads will be performed.</p>
                        <?php endif; ?>
                    </div>
                </li>
                <?php if (!$config->hasStorageZone()): ?>
                <li class="bn-section bn-px-0 bn-section--split">
                    <label class="bn-section__title" for="offloader-replication">Storage Replication</label>
                    <div class="bn-section__content">
                        <ul id="offloader-replication">
                            <?php foreach ($replicationRegions as $key => $label): ?>
                                <li>
                                    <input type="checkbox" name="offloader[storage_replication][]" value="<?= esc_attr($key) ?>" id="offloader-replication-<?= esc_attr($key) ?>" class="bn-toggle">
                                    <label for="offloader-replication-<?= esc_attr($key) ?>" class="bn-text-200-regular"><?= esc_html($label) ?></label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="bn-text-200-regular">If any selected, your data will automatically be replicated into the selected regions and delivered from the closest region to the CDN PoP.</p>
                        <p class="bn-text-100-regular">You can add more regions later, but you can't remove them after the Storage Zone is created.</p>
                    </div>
                </li>
                <li class="bn-section bn-px-0 bn-section--split">
                    <label class="bn-section__title" for="offloader-price">Price</label>
                    <div class="bn-section__content">
                        <p class="bn-text-200-regular">$<span id="offloader-price">0.02</span>/GB/month</p>
                    </div>
                </li>
                <?php else: ?>
                    <li class="bn-section bn-px-0 bn-section--split">
                        <label class="bn-section__title" for="offloader_config_storage_zone">Storage Zone</label>
                        <div class="bn-section__content">
                            <input type="text" class="bn-input bn-is-max-width" id="offloader_config_storage_zone" name="offloader[storage_zone]" value="<?= esc_attr($config->getStorageZone()) ?>" readonly>
                        </div>
                    </li>
                    <?php if ($config->isConfigured()): ?>
                    <li class="bn-section bn-px-0 bn-section--split">
                        <label class="bn-section__title" for="offloader_config_storage_password">Password</label>
                        <div class="bn-section__content">
                            <div class="bn-input-with-addons bn-is-max-width">
                                <input type="text" class="bn-input" id="offloader_config_storage_password" name="offloader[storage_password]" placeholder="********" readonly>
                                <div class="bn-input-addons">
                                    <button type="button" data-field-edit="offloader_config_storage_password"><svg width="18" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z"/></svg></button>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endif; ?>
                <?php endif; ?>
                <li class="bn-section bn-px-0 bn-section--split">
                    <label class="bn-section__title bn-mt-0" for="offloader-sync-existing">Sync existing files</label>
                    <div class="bn-section__content">
                        <input type="checkbox" name="offloader[sync_existing]" id="offloader-sync-existing" class="bn-toggle" value="1" <?= $config->isSyncExisting() ? 'checked' : '' ?>>
                        <label for="offloader-sync-existing" class="bn-text-200-regular">Enable sync for existing media files</label>
                    </div>
                </li>
            </ul>
        </section>
        <section class="bn-section bn-px-0 hide-disabled bn-section--no-divider  <?= $config->isEnabled() ? '' : 'bn-d-none' ?>">
            <input type="submit" value="Save Settings" class="bn-button bn-button--primary bn-button--lg">
        </section>
    </div>
    <?= wp_nonce_field('bunnycdn-save-offloader') ?>
    <input type="hidden" name="offloader[enable_confirmed]" value="0" id="modal-offloader-enable-confirmed">
    <input type="hidden" name="offloader[disable_confirmed]" value="0" id="modal-offloader-disable-confirmed">
</form>

<div id="modal-offloader-enable" class="modal">
    <div class="modal-container">
        <img src="<?= $this->assetUrl('icon-alert.svg') ?>">
        <h2>WARNING</h2>
        <p>
            With the offloader enabled, newly uploaded images in WordPress are stored in Bunny Storage, not on your server.
            To reverse this, you must manually transfer the images back, understanding the associated risks.
        </p>
        <p>
            For image acceleration without altering storage, just use Bunny <a href="<?= esc_attr($cdnUrl) ?>">CDN</a>.
        </p>
        <div class="modal-confirm">
            <input type="checkbox" class="bn-toggle" id="modal-offloader-enable-checkbox">
            <label for="modal-offloader-enable-checkbox" class="bn-text-200-regular">I understand this action transfers my content to bunny.net</label>
        </div>
        <div class="modal-buttons">
            <button class="bn-button bn-button--danger bn-button--lg" id="modal-offloader-enable-confirm" disabled>Enable Content Offloading</button>
            <button class="bn-button bn-button--secondary bn-button--lg" id="modal-offloader-enable-cancel">Cancel</button>
        </div>
    </div>
</div>

<div id="modal-offloader-disable" class="modal">
    <div class="modal-container">
        <img src="<?= $this->assetUrl('icon-alert.svg') ?>">
        <h2>WARNING</h2>
        <p>
            With the offloader disabled, new images uploaded to your WordPress will be stored in your server, and will not
            be copied to Bunny Storage. Offloaded images will continue to work as long as you are a bunny.net customer and
            the configurations related to the Bunny Storage are kept.
        </p>
        <p>
            <b>Important</b>: the offloaded images will NOT be copied back to your WordPress server. To copy the offloaded
            images back into your WordPress server, read <a href="https://support.bunny.net/hc/en-us/articles/12935895460892-How-to-move-files-from-Bunny-Storage-back-into-WordPress" target="_blank">this article</a>.
        </p>
        <div class="modal-confirm">
            <input type="checkbox" class="bn-toggle" id="modal-offloader-disable-checkbox">
            <label for="modal-offloader-disable-checkbox" class="bn-text-200-regular">I understand this action might break my website</label>
        </div>
        <div class="modal-buttons">
            <button class="bn-button bn-button--danger bn-button--lg" id="modal-offloader-disable-confirm" disabled>Disable Content Offloader</button>
            <button class="bn-button bn-button--secondary bn-button--lg" id="modal-offloader-disable-cancel">Cancel</button>
        </div>
    </div>
</div>
