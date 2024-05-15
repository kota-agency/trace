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
 * @var string $formUrl
 * @var string $url
 * @var string $backUrl
 * @var string $mode
 * @var string|null $error
 * @var \Bunny\Wordpress\Api\Pullzone\Info[]|null $pullzones
 * @var bool $isAccelerated
 */
?>
<form method="POST" action="<?= esc_url($formUrl) ?>" class="container bg-transparent" autocomplete="off">
    <section class="bn-section">
        <div class="bn-section__title bn-m-0">Configure your website details</div>
    </section>
    <section class="bn-section">
        <?php if (null !== $error): ?>
            <div class="alert red">
                <?= esc_html($error) ?>
            </div>
        <?php endif; ?>
        <div>
            <label class="bn-color-bunny-dark" for="website-url">Website URL:</label>
            <input type="text" class="bn-input bn-mt-2" id="website-url" name="url" value="<?= esc_attr($url) ?>" <?= $isAccelerated ? 'readonly' : '' ?>>
        </div>
        <p class="bn-py-3">Please confirm the URL from which the bunny.net Pull Zone will fetch files. This is usually your public website URL. This URL will also help the plugin to understand where and which URLs to accelerate with bunny.net. The default value was automatically configured based on your WordPress configuration.</p>
        <p>You should only change this if your website is hosted on a different address than configured in the WordPress settings, or if the value was not correctly detected.</p>
        <?php if (empty($pullzones)): ?>
            <div class="bn-mt-3">
                <input type="submit" value="Confirm URL" class="bn-button bn-button--primary">
                <a href="<?= esc_attr($backUrl) ?>" class="bn-button bn-button--secondary bn-ms-3">Go back</a>
            </div>
        <?php endif; ?>
    </section>
    <?php if (!empty($pullzones)): ?>
        <section class="bn-section">
            <div class="alert gray">
                <div>
                    <div class="alert__title">We found one or more related pullzone(s).</div>
                    <p>We've found one or more Pull Zones that already match your website URL. To create a new Pull Zone, or reuse an existing one, you can use the dropdown below.</p>
                </div>
            </div>
            <div class="bn-mt-3">
                <label for="pullzone-id">Pullzone</label>
                <select class="bn-select bn-mt-2" name="pullzone_id" id="pullzone-id">
                    <option value="0" selected>Create a new pullzone</option>
                    <?php foreach ($pullzones as $pullzone): ?>
                        <option value="<?= esc_attr($pullzone->getId()) ?>"><?= esc_html($pullzone->getName()) ?> (<?= esc_html($pullzone->getId()) ?>)</option>
                    <?php endforeach; ?>
                </select>
                <div>
                    <div class="bn-mt-3">
                        <input type="submit" value="Confirm URL" class="bn-button bn-button--primary">
                        <a href="<?= esc_attr($backUrl) ?>" class="bn-button bn-button--secondary">Go back</a>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <input type="hidden" name="mode" value="<?= esc_attr($mode) ?>">
    <?= wp_nonce_field('bunnycdn-save-wizard-step2') ?>
</form>
