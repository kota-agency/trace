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

namespace Bunny\Wordpress;

use Bunny\Storage\Client as StorageClient;
use Bunny\Wordpress\Config\Offloader as OffloaderConfig;
use Bunny\Wordpress\Utils\Offloader as OffloaderUtils;
use Bunny\Wordpress\Utils\StorageClientFactory;

class Offloader
{
    /** @var string[] */
    private array $delete_original = [];
    private \Closure $storageFactory;
    private ?StorageClient $storage = null;

    public function __construct(\Closure $storageFactory)
    {
        // using Closure, so StorageClient is only created when needed
        $this->storageFactory = $storageFactory;
    }

    public static function register(): void
    {
        // no container, as this is loaded in the frontend
        $config = OffloaderConfig::fromWpOptions();
        if (!$config->isEnabled()) {
            return;
        }
        $instance = new self(fn () => StorageClientFactory::createFromConfig($config));
        add_filter('wp_handle_upload_overrides', [$instance, 'wp_handle_upload_overrides']);
        add_filter('update_attached_file', [$instance, 'update_attached_file'], 10, 2);
        add_action('delete_attachment', [$instance, 'delete_attachment'], 10, 2);
        add_filter('wp_delete_file', [$instance, 'wp_delete_file'], 10, 1);
        add_filter('image_make_intermediate_size', [$instance, 'image_make_intermediate_size'], 10, 1);
        add_filter('wp_generate_attachment_metadata', [$instance, 'wp_generate_attachment_metadata'], 10, 3);
        add_action('updated_postmeta', [$instance, 'updated_postmeta'], 10, 4);
    }

    /**
     * @param array<string, mixed>|false $overrides
     *
     * @return array<string, mixed>
     */
    public function wp_handle_upload_overrides($overrides): array
    {
        if (false === $overrides) {
            return [];
        }
        $overrides['unique_filename_callback'] = function ($dir, $name, $ext) {
            $remote_dir = dirname($this->toRemotePath($dir.'/'.$name));
            $number = 1;
            $filename = $name;
            $filenameBase = pathinfo($name, \PATHINFO_FILENAME);
            while ($this->getStorage()->exists(path_join($remote_dir, $filename)) || file_exists(path_join($dir, $filename))) {
                $filename = sprintf('%s-%d%s', $filenameBase, $number, $ext);
                ++$number;
            }

            return $filename;
        };

        return $overrides;
    }

    public function update_attached_file(string $file, int $attachment_id): string
    {
        global $action;
        if (!$this->is_uploading_new_attachment() && !$this->is_attachment_handled_by_bunny($attachment_id)) {
            return $file;
        }
        try {
            $remote_path = $this->toRemotePath($file);
            $this->getStorage()->upload($file, $remote_path);
            $this->delete_original[] = $file;
            update_post_meta($attachment_id, OffloaderUtils::WP_POSTMETA_KEY, true);
        } catch (\Bunny\Storage\Exception $e) {
            if ('image-editor' !== $action) {
                throw $e;
            }
        }

        return $file;
    }

    public function delete_attachment(int $post_id, \WP_Post $post): void
    {
        $file = (string) get_attached_file($post_id);
        if (empty($file)) {
            return;
        }
        $metadata = wp_get_attachment_metadata($post_id);
        $file = $this->toRemotePath($file);
        $to_delete = [$file];
        if (isset($metadata['original_image'])) {
            $to_delete[] = path_join(dirname($file), $metadata['original_image']);
        }
        if (!empty($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $size) {
                $to_delete[] = path_join(dirname($file), $size['file']);
            }
        }
        $backup_sizes = get_post_meta($post_id, '_wp_attachment_backup_sizes', true);
        if (is_array($backup_sizes)) {
            foreach ($backup_sizes as $size) {
                $to_delete[] = path_join(dirname($file), $size['file']);
            }
        }
        $to_delete = array_unique($to_delete);
        $errors = $this->getStorage()->deleteMultiple($to_delete);
        foreach ($errors as $path => $error) {
            error_log(sprintf('bunnycdn: failed to delete %s: %s', $path, $error), \E_USER_WARNING);
        }
    }

    public function wp_delete_file(string $file): string
    {
        $remote_path = $this->toRemotePath($file);
        try {
            $this->getStorage()->delete($remote_path);
        } catch (\Bunny\Storage\FileNotFoundException $e) {
            // noop: this has likely already been deleted in the delete_attachment() method
        } catch (\Exception $e) {
            error_log(sprintf('bunnycdn: failed to remove "%s" from storage zone: %s', $file, $e->getMessage()), \E_USER_WARNING);
        }
        if (file_exists($file)) {
            return $file;
        }

        return '';
    }

    public function image_make_intermediate_size(string $filename): string
    {
        global $action;
        if ('image-editor' === $action) {
            $attachment_id = (int) ($_POST['postid'] ?? 0);
            if (!$this->is_attachment_handled_by_bunny($attachment_id)) {
                return $filename;
            }
        }
        $this->getStorage()->upload($filename, $this->toRemotePath($filename));
        $this->delete_original[] = $filename;

        return $filename;
    }

    /**
     * @param array<string, mixed> $metadata
     *
     * @return array<string, mixed>
     */
    public function wp_generate_attachment_metadata(array $metadata, int $attachment_id, string $action): array
    {
        if ('create' === $action && count($this->delete_original) > 0) {
            foreach ($this->delete_original as $file_to_delete) {
                if (!file_exists($file_to_delete)) {
                    continue;
                }
                @unlink($file_to_delete);
            }
            $this->delete_original = [];
        }

        return $metadata;
    }

    /**
     * @param mixed $meta_value
     */
    public function updated_postmeta(int $meta_id, int $object_id, string $meta_key, $meta_value): void
    {
        // making sure the original image isn't deleted before generating the subsizes
        if ($this->is_uploading_new_attachment() && '_wp_attachment_metadata' === $meta_key) {
            return;
        }
        if ('_wp_attachment_metadata' !== $meta_key) {
            return;
        }
        foreach ($this->delete_original as $file_to_delete) {
            if (!file_exists($file_to_delete)) {
                continue;
            }
            @unlink($file_to_delete);
        }
        $this->delete_original = [];
    }

    private function is_attachment_handled_by_bunny(int $post_id): bool
    {
        return (bool) get_post_meta($post_id, OffloaderUtils::WP_POSTMETA_KEY, true);
    }

    private function toRemotePath(string $file): string
    {
        static $offset = null;
        if (null === $offset) {
            $offset = strlen(wp_get_upload_dir()['basedir']) + 1;
        }

        return 'wp-content/uploads/'.ltrim(substr($file, $offset), '/');
    }

    private function is_uploading_new_attachment(): bool
    {
        global $pagenow, $wp;
        if ('async-upload.php' === $pagenow || 'media-new.php' === $pagenow) {
            return true;
        }
        if ('index.php' === $pagenow && isset($wp->query_vars['rest_route']) && '/wp/v2/media' === $wp->query_vars['rest_route']) {
            return true;
        }

        return false;
    }

    private function getStorage(): StorageClient
    {
        if (null !== $this->storage) {
            return $this->storage;
        }
        $this->storage = ($this->storageFactory)();

        return $this->storage;
    }
}
