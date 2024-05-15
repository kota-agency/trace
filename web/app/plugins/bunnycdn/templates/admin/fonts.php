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
 * @var \Bunny\Wordpress\Config\Fonts $config
 * @var bool $showSuccess
 */
?>
<form method="POST" class="container bg-gradient bn-p-0" autocomplete="off">
    <section class="bn-section bn-section-hero">
        <div>
            <h1>Bunny Fonts</h1>
            <h2>What are Bunny Fonts?</h2>
            <p>Bunny Fonts is an open-source, privacy-first web font platform designed to put privacy back into the internet. With a zero-tracking and no-logging policy, Bunny Fonts helps you stay fully GDPR compliant and puts your user's personal data into their own hands.</p>
            <a href="https://bunny.net/fonts/" target="_blank" class="bn-link bn-link--external">More Information</a>
        </div>
        <img src="<?= $this->assetUrl('fonts-header.svg') ?>" alt="bunny fonts">
    </section>
    <div class="bn-px-5">
        <section class="bn-section bn-px-0">
            <?php if (true === $showSuccess): ?>
                <div class="alert green">
                    The configuration was saved.
                </div>
            <?php endif; ?>
            <input type="checkbox" class="bn-toggle" name="fonts[enabled]" value="1" id="fonts-config-enabled" <?= $config->isEnabled() ? 'checked' : '' ?> autocomplete="off" />
            <label for="fonts-config-enabled">Rewrite Fonts</label>
            <p class="bn-pt-2 bn-pb-4">Improve privacy for your site and users with one click! This feature will automatically rewrite all Google Fonts to the GDPR compliant, drop in replacement <a href="https://fonts.bunny.net/about">bunny.net fonts</a> - the open-source, privacy-first web font platform with no tracking and zero logging.</p>
            <input type="submit" value="Save Settings" class="bn-button bn-button--primary bn-button--lg">
        </section>
        <section class="bn-section bn-px-0">
            <div class="bn-section__title bn-mb-5">Frequently Asked Questions</div>
            <dl class="bn-faq bn-is-max-width">
                <dt>What personal data do you collect?</dt>
                <dd>When using Bunny Fonts, no personal data or logs are stored. All the requests are processed completely anonymously.</dd>
                <dt>Are Bunny Fonts compatible with Google Fonts?</dt>
                <dd>Yes! Bunny Fonts were designed as a privacy-friendly drop-in replacement for Google Fonts holding the same API format.</dd>
                <dt>Fully GDPR Compliant</dt>
                <dd>Bunny Fonts are hosted by BunnyWay d.o.o. - an EU-based company - and were designed to help you stay fully GDPR compliant. No data or logs are ever collected or passed to a third party. Simply put: we cannot track or monitor your end-users in any way or form.</dd>
            </dl>
        </section>
    </div>
    <?= wp_nonce_field('bunnycdn-save-fonts') ?>
</form>
