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
<div class="alert red">
    <p>This plugin is configured to use CDN acceleration, but we couldn't detect it being active on your website. Because the Content Offloader is in use, <strong>some images of this website might be broken</strong>.</p>
    <p>Please double-check that your website is working accordingly. <a href="https://support.bunny.net/hc/en-us/articles/12936106861596-The-bunny-net-WordPress-plugin-says-CDN-acceleration-is-not-enabled" target="_blank">Read more</a>.</p>
</div>
