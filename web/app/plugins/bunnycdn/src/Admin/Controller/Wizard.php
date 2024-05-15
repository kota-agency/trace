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
use Bunny\Wordpress\Api\Exception\AccountNotActivatedException;
use Bunny\Wordpress\Api\Exception\PullzoneLocalUrlException;
use Bunny\Wordpress\Api\Pullzone;
use Bunny\Wordpress\Config\Cdn as CdnConfig;

class Wizard implements ControllerInterface
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run(bool $isAjax): void
    {
        if ('1' === get_option('bunnycdn_wizard_finished')) {
            if (!isset($_GET['step']) || '3' !== $_GET['step']) {
                $url = $this->container->getAdminUrl('overview');
                $this->container->redirect($url);

                return;
            }
        }
        $step = 1;
        if (isset($_GET['step'])) {
            $step = (int) $_GET['step'];
        }
        switch ($step) {
            case 1:
                $this->step1();

                return;
            case 2:
                $this->step2();

                return;
            case 3:
                $this->step3();

                return;
        }
        $wizardUrl = $this->container->getAdminUrl('wizard');
        $this->container->renderTemplateFile('wizard.error.php', ['error' => 'Invalid wizard step.', 'wizardUrl' => $wizardUrl], ['cssClass' => 'wizard error'], '_base.wizard.php');
    }

    private function step1(): void
    {
        $continueUrl = $this->container->getAdminUrl('wizard', ['step' => 2]);
        $agencyModeUrl = $this->container->getAdminUrl('wizard', ['step' => 2, 'mode' => 'agency']);
        $this->container->renderTemplateFile('wizard.1.php', ['continueUrl' => $continueUrl, 'agencyModeUrl' => $agencyModeUrl], ['cssClass' => 'wizard', 'step' => 1], '_base.wizard.php');
    }

    private function step2(): void
    {
        $url = site_url();
        $mode = 'standalone';
        if ('agency' === ($_GET['mode'] ?? 'standalone') || 'agency' === ($_POST['mode'] ?? 'standalone')) {
            $mode = 'agency';
        }
        $backUrl = $this->container->getAdminUrl('wizard');
        $formUrl = $this->container->getAdminUrl('wizard', ['step' => 2, 'mode' => $mode]);
        $pullzone = null;
        $hostname = null;
        $useCdnAcceleration = false;
        $cdnAcceleration = $this->container->newCdnAccelerationForWizard('agency' === $mode);
        if ($cdnAcceleration->isRequestAccelerated()) {
            try {
                $hostname = $cdnAcceleration->getRequestHost();
                $record = $this->container->getApiClient()->findDnsRecordForHostname($hostname);
                if (null === $record) {
                    throw new \Exception('We could not find a Bunny DNS zone for this domain.');
                }
                $pullzoneId = $record->getAcceleratedPullzoneId();
                if (null === $pullzoneId) {
                    throw new \Exception('We could not find a pullzone for this domain.');
                }
                $pullzone = $this->container->getApiClient()->getPullzoneById($pullzoneId);
                $useCdnAcceleration = true;
                $url = (is_ssl() ? 'https://' : 'http://').$hostname.$this->container->getWizardUtils()->getPathPrefix();
            } catch (\Exception $e) {
                $useCdnAcceleration = false;
            }
        }
        if (!empty($_POST)) {
            check_admin_referer('bunnycdn-save-wizard-step2');
            if (empty($_POST['url']) || empty($_POST['mode'])) {
                $this->container->renderTemplateFile('wizard.error.php', ['error' => 'Invalid data provided.', 'wizardUrl' => $formUrl], ['cssClass' => 'wizard error'], '_base.wizard.php');

                return;
            }
            $url = strlen($_POST['url']) > 0 ? $_POST['url'] : $url;
            $url = $this->container->getWizardUtils()->normalizeUrl($url);
            $pullzoneId = -1;
            if (isset($_POST['pullzone_id'])) {
                $pullzoneId = (int) $_POST['pullzone_id'];
            }
            $matchingPullzones = [];
            if (!$useCdnAcceleration) {
                try {
                    foreach ($this->container->getApiClient()->listPullzones() as $matchingPullzone) {
                        if ($matchingPullzone->getOriginUrl() === $url) {
                            $matchingPullzones[] = $matchingPullzone;
                        }
                    }
                    if (-1 === $pullzoneId) {
                        if (count($matchingPullzones) > 0) {
                            throw new \Exception('We found a pullzone configured for this URL.');
                        }
                        $pullzone = $this->step2CreatePullzone($url);
                    } elseif (0 === $pullzoneId) {
                        $pullzone = $this->step2CreatePullzone($url);
                    } else {
                        $matchingIds = array_map(fn (Pullzone\Info $item) => $item->getId(), $matchingPullzones);
                        if (!in_array($pullzoneId, $matchingIds, true)) {
                            throw new \Exception('We could not find the specified pullzone.');
                        }
                        $pullzone = $this->container->getApiClient()->getPullzoneById($pullzoneId);
                    }
                    if (0 === count($pullzone->getHostnames())) {
                        throw new \Exception('Invalid pullzone, hostname is unavailable.');
                    }
                    $hostname = current($pullzone->getHostnames());
                } catch (\Exception $e) {
                    $this->container->renderTemplateFile('wizard.2.php', ['formUrl' => $formUrl, 'url' => $url, 'backUrl' => $backUrl, 'mode' => $mode, 'error' => $e->getMessage(), 'pullzones' => $matchingPullzones, 'isAccelerated' => $cdnAcceleration->isRequestAccelerated()], ['cssClass' => 'wizard', 'step' => 2], '_base.wizard.php');

                    return;
                }
            }
            if (null === $pullzone || null === $hostname) {
                throw new \Exception('Invalid pullzone.');
            }
            $cdnConfig = new CdnConfig($cdnAcceleration->isRequestAccelerated() ? CdnConfig::STATUS_ACCELERATED : CdnConfig::STATUS_ENABLED, $pullzone->getId(), $pullzone->getName(), $hostname, $url, CdnConfig::DEFAULT_VALUES['excluded'], CdnConfig::DEFAULT_VALUES['included'], CdnConfig::DEFAULT_VALUES['disable_admin'], 'agency' === $mode);
            $cdnConfig->saveToWpOptions();
            update_option('bunnycdn_cdn_pullzone', ['id' => $pullzone->getId(), 'name' => $pullzone->getName()]);
            update_option('bunnycdn_wizard_mode', $mode);
            update_option('bunnycdn_wizard_finished', '1', true);
            delete_option('_bunnycdn_migration_warning');
            if ('agency' === $mode) {
                delete_option('bunnycdn_api_key');
                delete_option('bunnycdn_api_user');
            }
            $redirectUrl = $this->container->getAdminUrl('wizard', ['step' => 3]);
            $this->container->redirect($redirectUrl);

            return;
        }
        $this->container->renderTemplateFile('wizard.2.php', ['formUrl' => $formUrl, 'url' => $url, 'backUrl' => $backUrl, 'mode' => $mode, 'error' => null, 'pullzones' => null, 'isAccelerated' => $cdnAcceleration->isRequestAccelerated()], ['cssClass' => 'wizard', 'step' => 2], '_base.wizard.php');
    }

    private function step3(): void
    {
        $overviewUrl = $this->container->getAdminUrl('index');
        $this->container->renderTemplateFile('wizard.3.php', ['overviewUrl' => $overviewUrl], ['cssClass' => 'wizard', 'step' => 3], '_base.wizard.php');
    }

    private function step2CreatePullzone(string $originUrl): Pullzone\Info
    {
        for ($i = 0; $i < 5; ++$i) {
            try {
                $name = 'bunny-wp-pullzone-'.strtolower(wp_generate_password(10, false));
                $pullzone = $this->container->getApiClient()->createPullzoneForCdn($name, $originUrl);
                update_option('_bunnycdn_migrated_wp65', true);

                return $pullzone;
            } catch (PullzoneLocalUrlException $e) {
                throw $e;
            } catch (\Exception $e) {
                if ('Your account is not currently allowed to add new zones' === $e->getMessage()) {
                    throw new AccountNotActivatedException();
                }
                if ('The pull zone name is already taken.' === $e->getMessage()) {
                    continue;
                }
                throw $e;
            }
        }
        throw new \Exception('Could not create a new pullzone.');
    }
}
