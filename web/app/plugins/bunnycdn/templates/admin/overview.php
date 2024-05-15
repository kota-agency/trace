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
 * @var bool $showCdnAccelerationAlert
 */
$monthLabel = date('F');

?>
<div class="container bg-gradient bg-gradient--xl">
    <div class="alert red bn-d-none">Error loading the information for this account. Please try again later.</div>
    <?php if ($showCdnAccelerationAlert): ?>
    <div class="bn-m-0"><?= $this->renderPartialFile('cdn-acceleration.alert.php'); ?></div>
    <?php endif; ?>
    <section class="bn-mb-9">
        <div class="bn-section__title bn-mb-5">Balance and Usage</div>
        <div class="bn-columns">
            <section class="bn-card block-balance bn-sm-mb-3 bn-md-mb-7">
                <div>
                    <div class="bn-card__title">Account Balance</div>
                    <div class="bn-card__value" data-api="overview-billing-balance">$0.00</div>
                    <a href="https://dash.bunny.net/account/billing" target="_blank" class="bn-button bn-button--primary">Recharge Account</a>
                </div>
                <img src="<?= $this->assetUrl('overview-balance.svg') ?>">
            </section>
            <section class="bn-card bn-align-self-stretch block-usage">
                <div>
                    <div class="bn-card__title">Usage in <?= esc_html($monthLabel) ?></div>
                    <div class="bn-card__value" data-api="overview-month-charges">$0.00</div>
                    <p>Your total monthly bandwidth usage in <?= esc_html($monthLabel) ?> is <span data-api="overview-month-bandwidth">0 B</span> with an average cost of <span data-api="overview-month-bandwidth-avg-cost">$0.0000</span> / GB.</p>
                </div>
                <img src="<?= $this->assetUrl('overview-usage.svg') ?>">
            </section>
        </div>
    </section>
    <section>
        <div class="bn-section__title">Bandwidth Used</div>
        <p class="bn-section__description">Keep track of your bandwidth usage.</p>
        <div class="bn-columns bn-align-items-end">
            <div class="bn-card">
                <div class="bn-card__title">Bandwidth</div>
                <div class="bn-card__value bn-d-flex bn-align-items-center">
                    <span data-api="bandwidth-total">0.0 B</span>
                    <span class="bn-badge bn-badge--stat bn-ms-2" data-api="bandwidth-trend">
                        <span class="bn-badge__icon"></span>
                        <span class="bn-badge__text">0%</span>
                    <span>
                </div>
                <p>in the last 30 days</p>
            </div>
            <div class="img bn-text-center bn-md-my-4">
                <img src="<?= $this->assetUrl('overview-bandwidth.svg') ?>">
            </div>
        </div>
        <div class="bn-chart bn-mt-5 bn-p-0">
            <div class="bn-section__title bn-px-5 bn-pt-5 bn-mb-0">Bandwidth Usage</div>
            <div data-chart="bandwidth">
                <div class="bn-mt-6 bn-px-5">Loading...</div>
            </div>
        </div>
    </section>
    <section class="bn-mt-7">
        <div class="bn-section__title">Cache Hit Ratio</div>
        <p class="bn-section__description">Cache rate is the ratio of cache HIT requests versus MISS requests.</p>
        <div class="bn-columns bn-align-items-center bn-mt-5">
            <div class="bn-card">
                <div class="bn-card__title">Cache Hit Rate</div>
                <div class="bn-card__value bn-d-flex bn-align-items-center">
                    <span data-api="cache-total">0.00 %</span>
                    <span class="bn-badge bn-badge--stat bn-ms-2" data-api="cache-trend">
                        <span class="bn-badge__icon"></span>
                        <span class="bn-badge__text">0%</span>
                    <span>
                </div>
                <p>in the last 30 days</p>
            </div>
            <div class="img bn-text-center bn-md-my-4">
                <img src="<?= $this->assetUrl('overview-cache.svg') ?>">
            </div>
        </div>
        <div class="bn-chart bn-p-0">
            <div class="bn-section__title bn-px-5 bn-pt-5 bn-mb-0">Cache HIT Rate</div>
            <div data-chart="cache">
                <div class="bn-mt-6 bn-px-5">Loading...</div>
            </div>
        </div>
    </section>
    <section class="bn-mt-7">
        <div class="bn-section__title">Total Requests Served</div>
        <p class="bn-section__description">The total number of requests served through the CDN.</p>
        <div class="bn-columns bn-align-items-center bn-mt-5">
            <div class="bn-card">
                <div class="bn-card__title">Requests Served</div>
                <div class="bn-card__value bn-d-flex bn-align-items-center">
                    <span data-api="requests-total">0</span>
                    <span class="bn-badge bn-badge--stat bn-ms-2" data-api="requests-trend">
                        <span class="bn-badge__icon"></span>
                        <span class="bn-badge__text">0%</span>
                    <span>
                </div>
                <p>in the last 30 days</p>
            </div>
            <div class="img bn-text-center bn-md-my-4">
                <img src="<?= $this->assetUrl('overview-requests.svg') ?>">
            </div>
        </div>
        <div class="bn-chart bn-p-0">
            <div class="bn-section__title bn-px-5 bn-pt-5 bn-mb-0">Total Requests</div>
            <div data-chart="requests">
                <div class="bn-mt-6 bn-px-5">Loading...</div>
            </div>
        </div>
    </section>
</div>
