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

namespace Bunny\Wordpress\Admin\Controller;

use Bunny\Wordpress\Admin\Container;
use Bunny\Wordpress\Api\Exception\AuthorizationException;

class Overview implements ControllerInterface
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run(bool $isAjax): void
    {
        if ($isAjax) {
            if (isset($_GET['perform']) && 'get-api-data' === $_GET['perform']) {
                $this->handleGetApiData();

                return;
            }
            wp_send_json_error(['message' => 'Invalid request'], 400);

            return;
        }
        wp_enqueue_script('echarts', $this->container->assetUrl('echarts.min.js'), ['jquery']);
        try {
            $this->container->getOffloaderUtils()->updateStoragePassword();
            $showCdnAccelerationAlert = $this->container->getCdnAcceleration()->shouldShowAlert();
        } catch (AuthorizationException $e) {
            $showCdnAccelerationAlert = false;
        }
        $this->container->renderTemplateFile('overview.php', ['showCdnAccelerationAlert' => $showCdnAccelerationAlert], ['cssClass' => 'overview loading']);
    }

    private function handleGetApiData(): void
    {
        $pullzoneId = $this->container->getCdnConfig()->getPullzoneId();
        if (null === $pullzoneId) {
            wp_send_json_error(['message' => 'Could not find the associated pullzone.']);

            return;
        }
        $api = $this->container->getApiClient();
        $dateToday = new \DateTime();
        $date30Days = new \DateTime('30 days ago');
        $date60Days = new \DateTime('60 days ago');
        try {
            $billing = $api->getBilling();
            $details = $api->getPullzoneDetails($pullzoneId);
            $statistics = $api->getPullzoneStatistics($pullzoneId, $date30Days, $dateToday);
            $statisticsPreviousPeriod = $api->getPullzoneStatistics($pullzoneId, $date60Days, $date30Days);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);

            return;
        }
        $trendBandwidth = 0 === $statisticsPreviousPeriod->getBandwidth() ? 0 : round($statistics->getBandwidth() / $statisticsPreviousPeriod->getBandwidth() * 100);
        $trendCache = $statisticsPreviousPeriod->getCacheHitRate() < 0.001 ? 0.0 : round($statistics->getCacheHitRate() / $statisticsPreviousPeriod->getCacheHitRate() * 100);
        $trendRequests = 0 === $statisticsPreviousPeriod->getRequestsServed() ? 0 : round($statistics->getRequestsServed() / $statisticsPreviousPeriod->getRequestsServed() * 100);
        wp_send_json_success(['overview' => ['billing' => ['balance' => $billing->getBalanceHumanReadable()], 'month' => ['bandwidth' => $details->getBandwidthUsedHumanReadable(), 'bandwidth_avg_cost' => $details->getBandwidthAverageCostHumanReadable(), 'charges' => $details->getChargesHumanReadable()]], 'bandwidth' => ['total' => $statistics->getBandwidthHumanReadable(), 'trend' => ['value' => sprintf('%.2f%%', $trendBandwidth), 'direction' => 0 != $trendBandwidth ? $trendBandwidth > 0 ? 'up' : 'down' : 'equal']], 'cache' => ['total' => $statistics->getCacheHitRateHumanReadable(), 'trend' => ['value' => sprintf('%.2f%%', $trendCache), 'direction' => 0 != $trendCache ? $trendCache > 0 ? 'up' : 'down' : 'equal']], 'requests' => ['total' => $statistics->getRequestsTotal(), 'trend' => ['value' => sprintf('%.2f%%', $trendRequests), 'direction' => 0 != $trendRequests ? $trendRequests > 0 ? 'up' : 'down' : 'equal']], 'chart' => ['bandwidth' => $this->convertChartData($statistics->getBandwidthHistory()), 'cache' => $this->convertChartData($statistics->getCacheHistory()), 'requests' => $this->convertChartData($statistics->getRequestsHistory())]]);
    }

    /**
     * @param array<string, int> $data
     *
     * @return array<array{string, int}>
     */
    private function convertChartData(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = [substr($key, 0, 10), $value];
        }

        return $result;
    }
}
