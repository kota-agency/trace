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

namespace Bunny\Wordpress\Service;

use Bunny\Wordpress\Utils\Offloader as OffloaderUtils;

class AttachmentCounter
{
    public const BUNNY = 'Bunny Storage';
    public const LOCAL = 'Local';
    private \wpdb $db;

    public function __construct(\wpdb $db)
    {
        $this->db = $db;
    }

    /**
     * @return array{"Bunny Storage": int, "Local": int}
     */
    public function count(): array
    {
        $attachmentCount = [self::LOCAL => 0, self::BUNNY => 0];
        $sql = $this->db->prepare("\n                    SELECT COUNT(p.ID) AS count, pm.meta_key\n                    FROM {$this->db->posts} p\n                    LEFT JOIN {$this->db->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = %s\n                    LEFT JOIN {$this->db->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = %s\n                    WHERE p.post_type = %s AND pm2.meta_value IS NULL\n                    GROUP BY pm.meta_key\n            ", OffloaderUtils::WP_POSTMETA_KEY, '_wp_attachment_context', 'attachment');
        if (null === $sql) {
            throw new \Exception('Invalid SQL query');
        }
        /** @var array<string, string>[]|null $results */
        $results = $this->db->get_results($sql, ARRAY_A);
        if (null === $results) {
            error_log('bunnycdn: could not count attachments', \E_USER_WARNING);

            return $attachmentCount;
        }
        foreach ($results as $row) {
            if (OffloaderUtils::WP_POSTMETA_KEY === $row['meta_key']) {
                $attachmentCount[self::BUNNY] = (int) $row['count'];
                continue;
            }
            $attachmentCount[self::LOCAL] = (int) $row['count'];
        }

        return $attachmentCount;
    }

    public function countWithError(): int
    {
        /** @var string $sql */
        $sql = $this->db->prepare("\n                    SELECT COUNT(p.ID) AS count\n                    FROM {$this->db->posts} p\n                    INNER JOIN {$this->db->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = %s\n                    WHERE p.post_type = %s\n            ", OffloaderUtils::WP_POSTMETA_ERROR, 'attachment');
        $result = $this->db->get_row($sql);
        if (null !== $result && isset($result->count)) {
            return (int) $result->count;
        }
        throw new \Exception('bunnycdn: could not count attachments');
    }

    /**
     * @return array{ID: int, filename: string, reason: string}[]
     */
    public function listFilesWithError(): array
    {
        /** @var string $sql */
        $sql = $this->db->prepare("\n                    SELECT p.ID as id, pm1.meta_value AS path, pm2.meta_value AS reason\n                    FROM {$this->db->posts} p\n                    INNER JOIN {$this->db->postmeta} pm1 ON pm1.post_id = p.ID AND pm1.meta_key = %s\n                    INNER JOIN {$this->db->postmeta} pm2 ON pm2.post_id = p.ID AND pm2.meta_key = %s\n                    WHERE p.post_type = %s\n                    LIMIT 100\n            ", '_wp_attached_file', OffloaderUtils::WP_POSTMETA_ERROR, 'attachment');
        $results = $this->db->get_results($sql, ARRAY_A);
        if (is_array($results)) {
            return $results;
        }
        throw new \Exception('bunnycdn: could not list attachments');
    }
}
