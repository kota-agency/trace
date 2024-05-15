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
 */
?>
<div class="banner"></div>
<div class="container">
    <section class="bn-section bn-p-0">
        <div class="bn-section__title">About bunny.net</div>
        <p class="bn-pt-2 bn-pb-4"><a href="https://bunny.net/" target="_blank">bunny.net</a> is on a mission to help build and accelerate the internet of tomorrow. We obsess about customer experience and strive towards constant innovation, with a goal of helping companies and developers build a faster, safer, and more reliable internet.</p>
    </section>
    <section class="bn-section bn-px-0 bn-is-max-width">
        <div class="bn-section__title">Pricing</div>
        <section class="bn-block bn-mt-5">
            <div class="bn-block__title">Bunny CDN</div>
            <p>Take advantage of a simple pay-as-you-go pricing model where you're only charged for the traffic you deliver. Pricing starts as low as <strong>$1 per month</strong>.</p>
            <a href="https://bunny.net/pricing/cdn/" target="_blank" class="bn-link bn-link--blue bn-link--chain">Learn more about CDN Pricing</a>
        </section>
        <section class="bn-block bn-mt-5">
            <div class="bn-block__title">Bunny Storage</div>
            <p>Store your content strategically with simple pricing, no egress costs, commitments, or minimums. This plugin utilizes Bunny Storage Edge Tier SSD at $0.02/GB/Region. Please note CDN costs apply.</p>
            <a href="https://bunny.net/pricing/storage/" target="_blank" class="bn-link bn-link--blue bn-link--chain">Learn more about Storage Pricing</a>
        </section>
        <section class="bn-block bn-mt-5">
            <div class="bn-block__title">Bunny Optimizer</div>
            <p>Eliminate expensive optimization and transformation services with a fixed monthly price regardless of your traffic scale. Excellent solution for any sized project at only <strong>$9.50/month</strong> per website.</p>
            <a href="https://bunny.net/pricing/optimizer/" target="_blank" class="bn-link bn-link--blue bn-link--chain bn-mt-2">Learn more about Optimizer Pricing</a>
        </section>
        <section class="bn-block bn-mt-5">
            <div class="bn-block__title">Bunny Fonts</div>
            <p>Bunny Fonts is an open-source, privacy-first web font platform designed to put privacy back into the internet. It is completely free of charge and can be used by anyone.</p>
            <a href="https://bunny.net/fonts/" target="_blank" class="bn-link bn-link--blue bn-link--chain bn-mt-2">Learn more about Fonts</a>
        </section>
    </section>
    <section class="bn-section bn-section--no-divider bn-px-0 bn-pb-0">
        <div class="bn-section__title">Plugin Changelog</div>
        <section class="bn-block bn-mt-5">
            <div class="bn-block__title">Version 2.2.0</div>
            <p>Added support for WordPress 6.5</p>
        </section>
        <section class="bn-block bn-mt-5">
            <div class="bn-block__title">Version 2.1.0</div>
            <p>Added support for PHP 7.4</p>
        </section>
        <section class="bn-block bn-mt-5">
            <div class="bn-block__title">Version 2.0.0</div>
            <p>We're excited to introduce a completely revamped plugin, to make it easier to get your website hopping like a bunny.</p>
        </section>
    </section>
</div>
