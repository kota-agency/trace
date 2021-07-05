<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

if (!class_exists('UpdraftPlus_RemoteStorage_Addons_Base_v2')) require_once(UPDRAFTPLUS_DIR.'/methods/addon-base-v2.php');

class UpdraftPlus_AddonStorage_viastream extends UpdraftPlus_RemoteStorage_Addons_Base_v2 {

	public function __construct($method, $desc) {
		$this->method = $method;
		$this->desc = $desc;
	}

	/**
	 * Delete a single file from the service
	 *
	 * @param Boolean      $ret         - value to return
	 * @param Array|String $files       - array of file names to delete
	 * @param Array        $storage_arr - service details
	 * @return Boolean|String - either a boolean true or an error code string
	 */
	public function delete_files($ret, $files, $storage_arr = false) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		if (is_string($files)) $files = array($files);

		if ($storage_arr) {
			$url = $storage_arr['url'];
		} else {
			$this->bootstrap();
			$options = $this->get_options();
			if (!array($options) || !isset($options['url'])) {
				$this->log('No '.$this->desc.' settings were found');
				$this->log(sprintf(__('No %s settings were found', 'updraftplus'), $this->desc), 'error');
				return 'authentication_fail';
			}
			$url = untrailingslashit($options['url']);
		}

		$logurl = preg_replace('/:([^\@:]*)\@/', ':(password)@', $url);
		
		$ret = true;
		
		foreach ($files as $file) {
			$this->log("Delete remote: $logurl/$file");
			if (!unlink("$url/$file")) {
				$this->log("Delete failed");
				$ret = 'file_delete_error';
			}
		}
		return $ret;
	}

	public function chunked_upload($file, $url) {

		global $updraftplus;

		$orig_file_size = filesize($file);

		$start_offset = 0;
		$GLOBALS['updraftplus_404_should_be_logged'] = false;
		if (is_file($url)) {
			$url_size = filesize($url);
			if ($url_size == $orig_file_size) {
				$this->log("This file has already been successfully uploaded");
				return true;
			} elseif ($url_size > $orig_file_size) {
				$this->log("A larger file than expected ($url_size > $orig_file_size) already exists");
				return false;
			}
			$this->log("$url_size bytes already uploaded; resuming");
			$start_offset = $url_size;
		}
		
		$GLOBALS['updraftplus_404_should_be_logged'] = true;
		
		$chunks = floor($orig_file_size / 2097152);
		// There will be a remnant unless the file size was exactly on a 5MB boundary
		if ($orig_file_size % 2097152 > 0) $chunks++;

		if (!$fh = fopen($url, 'a')) {
			$this->log('Failed to open remote file');
			return false;
		}
		if (!$rh = fopen($file, 'rb')) {
			$this->log('Failed to open local file');
			return false;
		}

		// A hack, to pass information to a modified version of the PEAR library
		if ('webdav' == $this->method) {
			global $updraftplus_webdav_filepath;
			$updraftplus_webdav_filepath = $file;
		}
		
		/*
		 * This is used for increase chunk size for webdav stream wrapper. WebDav stream wrapper chunk size is 8kb by default. This chunk size impacts on speed of upload
		 */
		$read_buffer_size = 131072;
		if (isset($this->upload_stream_chunk_size) && function_exists('stream_set_chunk_size')) {
			// stream_set_chunk_size() exists in PHP 5.4+
			// @codingStandardsIgnoreLine
			$ret_set_chunk_size = stream_set_chunk_size($fh, $this->upload_stream_chunk_size);
			if (false === $ret_set_chunk_size) {
				$this->log(sprintf("Upload chunk size: failed to change to %d bytes", $this->upload_stream_chunk_size));
			} else {
				$read_buffer_size = min($this->upload_stream_chunk_size, 1048576);
				$this->log(sprintf("Upload chunk size: successfully changed to %d bytes", $this->upload_stream_chunk_size));
			}
		}

		$last_time = time();
		for ($i = 1; $i <= $chunks; $i++) {

			$chunk_start = ($i-1)*2097152;
			$chunk_end = min($i*2097152-1, $orig_file_size);

			if ($start_offset > $chunk_end) {
				$this->log("Chunk $i: Already uploaded");
			} else {

				fseek($fh, $chunk_start);
				fseek($rh, $chunk_start);

				$bytes_left = $chunk_end - $chunk_start;
				while ($bytes_left > 0) {
					if ($buf = fread($rh, $read_buffer_size)) {
						if (fwrite($fh, $buf, strlen($buf))) {
							$bytes_left = $bytes_left - strlen($buf);
							if (time()-$last_time > 15) {
								$last_time = time();
								touch($file);
							}
						} else {
							$this->log(sprintf(__("Chunk %s: A %s error occurred", 'updraftplus'), $i, 'write'), 'error');
							return false;
						}
					} else {
						$this->log(sprintf(__("Chunk %s: A %s error occurred", 'updraftplus'), $i, 'read'), 'error');
						return false;
					}
				}
			}

			$updraftplus->record_uploaded_chunk(round(100*$i/$chunks, 1), "$i", $file);

		}

		// N.B. fclose() always returns true for stream wrappers - stream wrappers' return values are ignored - http://php.net/manual/en/streamwrapper.stream-close.php (29-Jan-2015)
		try {
			if (!fclose($fh)) {
				$this->log('Upload failed (fclose error)');
				$this->log(__('Upload failed', 'updraftplus'), 'error');
				return false;
			}
		} catch (Exception $e) {
			$this->log('Upload failed (fclose exception; class='.get_class($e).'): '.$e->getMessage());
			$this->log(__('Upload failed', 'updraftplus'), 'error');
			return false;
		}
		fclose($rh);

		return true;

	}

	public function listfiles($match = 'backup_') {

		$storage = $this->bootstrap();
		if (is_wp_error($storage)) return $storage;

		$options = $this->get_options();
		if (!array($options) || empty($options['url'])) return new WP_Error('no_settings', sprintf(__('No %s settings were found', 'updraftplus'), $this->desc));

		$url = trailingslashit($options['url']);

		// A change to how WebDAV settings are saved resulted in near-empty URLs being saved, like webdav:/// . Detect 'empty URLs'.
		if (preg_match('/^[a-z]+:$/', untrailingslashit($url))) {
			return new WP_Error('no_settings', sprintf(__('No %s settings were found', 'updraftplus'), $this->desc));
		}
		
		if (false == ($handle = opendir($url))) return new WP_Error('no_access', sprintf('Failed to gain %s access', $this->desc));

		$results = array();

		while (false !== ($entry = readdir($handle))) {
			if (is_file($url.$entry) && 0 === strpos($entry, $match)) {
				$results[] = array('name' => $entry, 'size' => filesize($url.$entry));
			}
		}

		return $results;

	}

	public function upload_files($ret, $backup_array) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		global $updraftplus;

		$storage = $this->bootstrap();

		if (is_wp_error($storage)) {
			foreach ($storage->get_error_messages() as $msg) {
				$this->log($msg);
				$this->log($msg, 'error');
			}
			return false;
		}

		$options = $this->get_options();
		if (!array($options) || !isset($options['url'])) {
			$this->log('No '.$this->desc.' settings were found');
			$this->log(sprintf(__('No %s settings were found', 'updraftplus'), $this->desc), 'error');
			return false;
		}

		$any_failures = false;

		$updraft_dir = untrailingslashit($updraftplus->backups_dir_location());
		$url = untrailingslashit($options['url']);

		foreach ($backup_array as $file) {
			$this->log("upload: attempt: $file");
			if ($this->chunked_upload($updraft_dir.'/'.$file, $url.'/'.$file)) {
				$updraftplus->uploaded_file($file);
			} else {
				$any_failures = true;
				$this->log('ERROR: '.$this->desc.': Failed to upload file: '.$file);
				$this->log(__('Error', 'updraftplus').': '.$this->desc.': '.sprintf(__('Failed to upload to %s', 'updraftplus'), $file), 'error');
			}
		}

		return ($any_failures) ? null : array('url' => $url);

	}

	/**
	 * Get the pre configuration template (directly output)
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {

		$classes = $this->get_css_classes(false);
		
		?>
		<tr class="<?php echo $classes . ' ' . $this->method . '_pre_config_container';?>">
			<td colspan="2">
				<h3><?php echo $this->desc; ?></h3>
			</td>
		</tr>

		<?php
	}

	/**
	 * Get the configuration template
	 *
	 * @return String - the template, ready for substitutions to be carried out
	 */
	public function get_configuration_template() {
		$template_str = $this->get_configuration_middlesection_template();
		$template_str .= $this->get_test_button_html($this->desc);
		return $template_str;
	}

	public function download_file($ret, $files) {

		global $updraftplus;

		if (is_string($files)) $files = array($files);

		$storage = $this->bootstrap();
		if (is_wp_error($storage)) {
			foreach ($storage->get_error_messages() as $msg) {
				$this->log($msg);
				$this->log($msg, 'error');
			}
			return false;
		}

		$options = $this->get_options();

		if (!array($options) || !isset($options['url'])) {
			$this->log('No '.$this->desc.' settings were found');
			$this->log(sprintf(__('No %s settings were found', 'updraftplus'), $this->desc), 'error');
			return false;
		}

		$ret = true;
		foreach ($files as $file) {

			$fullpath = $updraftplus->backups_dir_location().'/'.$file;
			$url = untrailingslashit($options['url']).'/'.$file;

			$start_offset = (file_exists($fullpath)) ? filesize($fullpath) : 0;

			if (@filesize($url) == $start_offset) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				$ret = false;
				continue;
			}

			if (!$fh = fopen($fullpath, 'a')) {
				$this->log("Error opening local file: Failed to download: $file");
				$this->log("$file: ".sprintf(__("%s Error", 'updraftplus'), $this->desc).": ".__('Error opening local file: Failed to download', 'updraftplus'), 'error');
				$ret = false;
				continue;
			}

			if (!$rh = fopen($url, 'rb')) {
				$this->log("Error opening remote file: Failed to download: $file");
				$this->log("$file: ".sprintf(__("%s Error", 'updraftplus'), $this->desc).": ".__('Error opening remote file: Failed to download', 'updraftplus'), 'error');
				$ret = false;
				continue;
			}
			
			$read_buffer_size = 262144;
			
			/*
			 * This is used for increase chunk size for webdav stream wrapper. WebDav stream wrapper chunk size is 8kb by default. This chunk size impacts on speed of download
			 */
			 
			// stream_set_chunk_size function exist in >= 5.4.0 Php version
			if (isset($this->download_stream_chunk_size) && function_exists('stream_set_chunk_size')) {
				// @codingStandardsIgnoreLine
				$ret_set_chunk_size = stream_set_chunk_size($rh, $this->download_stream_chunk_size);
				if (false === $ret_set_chunk_size) {
					$this->log(sprintf(__("Download chunk size failed to change to %d", 'updraftplus'), $this->download_stream_chunk_size));
				} else {
					$read_buffer_size = $this->download_stream_chunk_size;
					$this->log(sprintf(__("Download chunk size successfully changed to %d", 'updraftplus'), $this->download_stream_chunk_size));
				}
			}

			if ($start_offset) {
				fseek($fh, $start_offset);
				fseek($rh, $start_offset);
			}

			while (!feof($rh) && $buf = fread($rh, $read_buffer_size)) {
				if (!fwrite($fh, $buf, strlen($buf))) {
					$this->log("Error: Local write failed: Failed to download: $file");
					$this->log("$file: ".sprintf(__("%s Error", 'updraftplus'), $this->desc).": ".__('Local write failed: Failed to download', 'updraftplus'), 'error');
					$ret = false;
					continue;
				}
			}
		}

		return $ret;

	}
	
	/**
	 * Perform a credentials test. The results are echoed.
	 *
	 * @param String $url - where test file will be put
	 */
	public function credentials_test_go($url) {
		
		$storage = $this->bootstrap();

		if (is_wp_error($storage) || true !== $storage) {
			echo __("Failed", 'updraftplus').": ";
			foreach ($storage->get_error_messages() as $msg) {
				echo "$msg\n";
			}
			return;
		}

		 @mkdir($url);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		
		// $updraftplus_webdav_filepath shold have readable file path when file is being send on the webdav filesystem
		if ('webdav' == $this->method) {
			global $updraftplus, $updraftplus_webdav_filepath;
			$updraftplus_webdav_filepath = $updraftplus->backups_dir_location().'/index.html';
		}
		$testfile = $url.'/'.md5(time().rand());
		if (file_put_contents($testfile, 'test')) {
			_e("Success", 'updraftplus');
			@unlink($testfile);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		} else {
			_e("Failed: We were not able to place a file in that directory - please check your credentials.", 'updraftplus');
		}
	}
}
