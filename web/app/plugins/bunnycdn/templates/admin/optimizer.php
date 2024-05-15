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
 * @var \Bunny\Wordpress\Config\Optimizer $config
 * @var bool $showSuccess
 * @var string|null $error
 */
?>
<form class="container bg-gradient bn-p-0" method="POST" autocomplete="off">
    <section class="bn-section bn-section-hero bn-p-5">
        <div>
            <h1>Bunny Optimizer</h1>
            <h2>What is Bunny Optimizer?</h2>
            <p>Automatically optimize your images, CSS files, and JavaScript files to improve your website performance. Reduce load times and increase conversion rates in just a few clicks. No coding, or server reconfiguration required.</p>
            <a href="https://bunny.net/optimizer/" target="_blank" class="bn-link bn-link--external">More Information</a>
        </div>
        <img src="<?= $this->assetUrl('optimizer-header.svg') ?>" alt="bunny optimizer">
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
            <div>
                <input type="checkbox" class="bn-toggle" id="optimizer-enabled" name="optimizer[enabled]" value="1" <?= $config->isEnabled() ? 'checked' : '' ?> />
                <label for="optimizer-enabled">Bunny Optimizer<span class="bn-badge bn-badge--info bn-ms-1">$9.50/month</span></label>
            </div>
            <p class="bn-mt-2">Bunny Optimizer helps maximize performance and reduce traffic usage. Automatically compress image sizes by up to 80%, optimize your images for desktop and mobile devices, minify CSS and JavaScript files and build your website with ease with on the fly image manipulation, for a flat fee of <strong>$9.50/month</strong>.</p>
            <div class="alert blue bn-mt-2 bn-mb-2 <?= $config->isEnabled() ? 'bn-d-none' : '' ?>">
                If you enable Bunny Optimizer you will be charged a flat fee of $9.50/month.
            </div>
            <input type="submit" value="Save Settings" class="bn-button bn-button--primary bn-button--lg bn-mt-2 hide-enabled <?= $config->isEnabled() ? 'bn-d-none' : '' ?>">
        </section>
        <section class="bn-section bn-px-0 columns-2 hide-disabled <?= $config->isEnabled() ? '' : 'bn-d-none' ?>">
            <div class="bn-block">
                <input type="checkbox" class="bn-toggle" id="optimizer-webp-compression" name="optimizer[webp_compression]" value="1" <?= $config->isWebpCompression() ? 'checked' : '' ?> />
                <label for="optimizer-webp-compression">WebP Image Compression</label>
                <p class="bn-mt-2">Images will be automatically optimized into an efficient WebP format when supported by the client to greatly reduce file size and improve load times without any URL changes.</p>
            </div>
            <div class="bn-block">
                <input type="checkbox" class="bn-toggle" id="optimizer-image-api" name="optimizer[image_api]" value="1" <?= $config->isImageApi() ? 'checked' : '' ?> />
                <label for="optimizer-image-api">Dynamic Image API</label>
                <p class="bn-mt-2">Enable on the fly image manipulation engine for dynamic URL based image manipulation.</p>
                <a href="https://docs.bunny.net/docs/stream-image-processing" target="_blank" class="bn-link bn-link--blue bn-link--chain bn-mt-2">See Documentation</a>
            </div>
            <div class="bn-block">
                <input type="checkbox" class="bn-toggle" id="optimizer-minify-css" name="optimizer[minify_css]" value="1" <?= $config->isMinifyCss() ? 'checked' : '' ?> />
                <label for="optimizer-minify-css">Minify CSS Files</label>
                <p class="bn-mt-2">CSS files will be automatically minified to reduce their file size without modifying the functionality.</p>
            </div>
            <div class="bn-block">
                <input type="checkbox" class="bn-toggle" id="optimizer-minify-js" name="optimizer[minify_js]" value="1" <?= $config->isMinifyJs() ? 'checked' : '' ?> />
                <label for="optimizer-minify-js">Minify JavaScript</label>
                <p class="bn-mt-2">JavaScript files will be automatically minified to reduce their file size without modifying the functionality.</p>
                <a href="https://bunny.net/academy/cdn/what-is-website-compression-css-js-minifying/" target="_blank" class="bn-link bn-link--blue bn-link--chain bn-mt-2">See Documentation</a>
            </div>
        </section>
        <section class="bn-section bn-px-0 hide-disabled <?= $config->isEnabled() ? '' : 'bn-d-none' ?>">
            <div>
                <input type="checkbox" class="bn-toggle" id="optimizer-smart-image" name="optimizer[smart_image][enabled]" value="1" <?= $config->isSmartImageEnabled() ? 'checked' : '' ?> />
                <label for="optimizer-smart-image">Smart Image Optimization</label>
            </div>
            <p class="bn-mt-2 bn-mb-7">Bunny Optimizer will automatically resize and compress images for desktop and mobile devices.</p>
            <div class="columns-2">
                <div class="bn-block">
                    <label class="bn-block__title" for="optimizer-smart-image-desktop-max-width">Maximum Desktop Image Width</label>
                    <p>The image width that will be returned for desktop devices. Images bigger than that will be automatically downsized to the desired width.</p>
                    <div class="bn-input-with-addons">
                        <input type="number" class="bn-input" name="optimizer[smart_image][desktop_width_max]" id="optimizer-smart-image-desktop-max-width" value="<?= esc_html($config->getSmartImageDesktopWidthMax()) ?>">
                        <div class="bn-input-addons"><span>px</span></div>
                    </div>
                </div>
                <div class="bn-block">
                    <label class="bn-block__title" for="optimizer-smart-image-mobile-max-width">Maximum Mobile Image Width</label>
                    <p>The image width that will be returned for mobile devices. Images bigger than that will be automatically downsized to the desired width.</p>
                    <div class="bn-input-with-addons">
                        <input type="number" class="bn-input" name="optimizer[smart_image][mobile_width_max]" id="optimizer-smart-image-mobile-max-width" value="<?= esc_attr($config->getSmartImageMobileWidthMax()) ?>">
                        <div class="bn-input-addons"><span>px</span></div>
                    </div>
                </div>
                <div class="bn-block">
                    <label class="bn-block__title" for="optimizer-smart-image-desktop-quality">Desktop Image Quality</label>
                    <p>The image quality in which the optimized images will be served on desktop devices. 0 being the lowest and 100% being the highest quality available.</p>
                    <div class="bn-input-with-addons">
                        <input type="number" class="bn-input" name="optimizer[smart_image][desktop_quality]" id="optimizer-smart-image-desktop-quality" value="<?= esc_attr($config->getSmartImageDesktopQuality()) ?>">
                        <div class="bn-input-addons"><span>%</span></div>
                    </div>
                </div>
                <div class="bn-block">
                    <label class="bn-block__title" for="optimizer-smart-image-mobile-quality">Mobile Image Quality</label>
                    <p>The image quality in which the optimized images will be served on mobile devices. 0 being the lowest and 100% being the highest quality available.</p>
                    <div class="bn-input-with-addons">
                        <input type="number" class="bn-input" name="optimizer[smart_image][mobile_quality]" id="optimizer-smart-image-mobile-quality" value="<?= esc_attr($config->getSmartImageMobileQuality()) ?>">
                        <div class="bn-input-addons"><span>%</span></div>
                    </div>
                </div>
            </div>
        </section>
        <section class="bn-section bn-px-0 hide-disabled <?= $config->isEnabled() ? '' : 'bn-d-none' ?>">
            <div>
                <input type="checkbox" class="bn-toggle" id="optimizer-watermark" name="optimizer[watermark][enabled]" value="1" <?= $config->isWatermarkEnabled() ? 'checked' : '' ?> />
                <label for="optimizer-watermark">Watermark Images</label>
            </div>
            <p class="bn-mt-2 bn-mb-7">Bunny Optimizer will automatically place a watermark on your images.</p>
            <div class="columns-2">
                <div class="bn-block">
                    <label class="bn-block__title" for="optimizer-watermark-url">Image URL</label>
                    <p>The URL to the watermark image. The URL must be a publicly accessible image file.</p>
                    <input type="text" class="bn-input" name="optimizer[watermark][url]" id="optimizer-watermark-url" value="<?= esc_attr($config->getWatermarkUrl()) ?>">
                </div>
                <div class="bn-block">
                    <label class="bn-block__title" for="optimizer-watermark-image-min">Minimum Image Size</label>
                    <p>The minimum image width or height required to contain a watermark. Smaller images will not be watermarked.</p>
                    <div class="bn-input-with-addons">
                        <input type="number" class="bn-input" name="optimizer[watermark][image_min]" id="optimizer-watermark-image-min" value="<?= esc_attr($config->getWatermarkImageMin()) ?>">
                        <div class="bn-input-addons"><span>px</span></div>
                    </div>
                </div>
                <div class="bn-block">
                    <label class="bn-block__title" for="optimizer-watermark-border">Border Offset</label>
                    <p>The border offset where the watermark image will be placed at.</p>
                    <div class="bn-input-with-addons">
                        <input type="number" class="bn-input" name="optimizer[watermark][border]" id="optimizer-watermark-border" value="<?= esc_attr($config->getWatermarkBorder()) ?>">
                        <div class="bn-input-addons"><span>%</span></div>
                    </div>
                </div>
                <div class="bn-block">
                    <label class="bn-block__title" for="optimizer-watermark-position">Position</label>
                    <p>The position on the image where the watermark will be placed on.</p>
                    <select class="bn-select" name="optimizer[watermark][position]" id="optimizer-watermark-position">
                        <?php foreach (\Bunny\Wordpress\Config\Optimizer::WATERMARK_POSITIONS as $value => $label): ?>
                            <option value="<?= esc_attr($value) ?>" <?= $value === $config->getWatermarkPosition() ? 'selected' : '' ?>><?= esc_html($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </section>
        <section class="bn-section bn-section--no-divider bn-px-0 hide-disabled <?= $config->isEnabled() ? '' : 'bn-d-none' ?>">
            <input type="submit" value="Save Settings" class="bn-button bn-button--primary bn-button--lg">
        </section>
    </div>
    <?= wp_nonce_field('bunnycdn-save-optimizer') ?>
</form>
