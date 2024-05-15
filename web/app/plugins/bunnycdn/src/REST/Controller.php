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

namespace Bunny\Wordpress\REST;

use Bunny\Wordpress\Config\Offloader as OffloaderConfig;
use Bunny\Wordpress\Service\AttachmentCounter;
use Bunny\Wordpress\Service\AttachmentMover;

class Controller
{
    private AttachmentCounter $attachmentCounter;
    private AttachmentMover $attachmentMover;
    private OffloaderConfig $config;

    public function __construct(AttachmentCounter $attachmentCounter, AttachmentMover $attachmentMover, OffloaderConfig $config)
    {
        $this->attachmentCounter = $attachmentCounter;
        $this->attachmentMover = $attachmentMover;
        $this->config = $config;
    }

    public function sync(\WP_REST_Request $request): \WP_REST_Response
    {
        if (!$this->config->isConfigured() || !$this->config->isEnabled() || !$this->config->isSyncExisting()) {
            error_log('bunnycdn: offloader: This feature is not available', \E_USER_WARNING);

            return new \WP_REST_Response(['success' => false, 'message' => 'This feature is not available'], 404);
        }
        // authentication
        $token = $request->get_header('X-Bunny-WP-Token');
        $tokenHash = $this->config->getSyncTokenHash();
        if (null === $token || null === $tokenHash || !password_verify($token, $tokenHash)) {
            error_log('bunnycdn: offloader: Invalid authentication token', \E_USER_WARNING);

            return new \WP_REST_Response(['success' => false, 'message' => 'Invalid authentication token'], 401);
        }
        // check if there are files left to sync
        $count = $this->attachmentCounter->count();
        if (0 === $count[AttachmentCounter::LOCAL]) {
            return new \WP_REST_Response(['success' => false, 'message' => 'There are no attachments to sync', 'remaining_files' => 0], 200);
        }
        // move attachments
        $batchSize = $request->get_param('batch_size') ?? 5;
        $result = $this->attachmentMover->perform($batchSize);
        update_option('_bunnycdn_offloader_last_sync', time());
        // response
        $count = $this->attachmentCounter->count();
        if (false === $result['success']) {
            error_log('bunnycdn: offloader: '.$result['data']['message'], \E_USER_WARNING);
        }

        return new \WP_REST_Response(['success' => $result['success'], 'message' => $result['data']['message'], 'remaining_files' => $count[AttachmentCounter::LOCAL]], 200);
    }
}
