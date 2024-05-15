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
 * @var string $registerUrl
 * @var string $loginUrl
 */
?>
<div class="container no-nav bn-p-0">
    <section class="bn-section bg-gradient-reverse welcome">
        <img src="<?= $this->assetUrl('homepage-welcome.png') ?>">
        <h2>Start your <strong>14-Day FREE</strong> Trial</h2>
        <a href="<?= $registerUrl ?>" target="_blank" class="bn-button bn-button--primary bn-button--xxl">Create An Account</a>
        <p>Already have an account? <a href="<?= esc_url($loginUrl) ?>">Log in</a>.</p>
    </section>
    <section class="bn-section subtext bn-py-7 bn-px-6">
        <p>Supercharge your website in under <strong>5 minutes</strong>.</p>
    </section>
    <section class="bn-section columns-2">
        <div class="bn-text-center">
            <img src="<?= $this->assetUrl('homepage-cdn.svg') ?>">
        </div>
        <div>
            <h3>Bunny CDN</h3>
            <h4>Hop ahead of the competition</h4>
            <p>
                Hop on a lightning fast global content delivery network with 123 PoPs and deliver consistent experience
                to everyone, no matter where in the world they are!
            </p>
        </div>
    </section>
    <section class="bn-section columns-2">
        <div class="bn-text-center">
            <img src="<?= $this->assetUrl('homepage-optimizer.svg') ?>">
        </div>
        <div>
            <h3>Bunny Optimizer</h3>
            <h4>Image optimization. Made easy.</h4>
            <p>
                Automatically reduce the size of your images by up to 80%, and resize them to best fit the screen of your
                user's devices. Compress and minify your CSS and JavaScript files and make your website truly hop.
            </p>
        </div>
    </section>
    <section class="bn-section columns-2">
        <div class="bn-text-center">
            <img src="<?= $this->assetUrl('homepage-offloader.svg') ?>">
        </div>
        <div>
            <h3>Bunny Offloader</h3>
            <h4>Simplified Storage Offloading & Replication</h4>
            <p>
                Automatically move content from your WordPress platform to Bunny Storage, our high-performance and
                cost-effective cloud storage service for optimal latency, global replication, and maximum throughput.
                After activating it, any new content you upload to WordPress will automatically be transferred to Bunny
                Storage, providing your users with up to 5x faster download speeds compared to traditional object storage
                solutions.
            </p>
        </div>
    </section>
    <section class="bn-section columns-2">
        <div class="bn-text-center">
            <img src="<?= $this->assetUrl('homepage-fonts.svg') ?>">
        </div>
        <div>
            <h3>Bunny Fonts</h3>
            <h4>Take control of your fonts</h4>
            <p>
                Push your privacy to the next level. Prevent your users from being tracked by 3rd party websites and
                simplify GDPR compliance!
            </p>
        </div>
    </section>
</div>
