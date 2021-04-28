<?php
class BunnyCDN 
{


	/**
		Returns the array of all the options with their default values in case they are not set
	*/
	public static function getOptions() {
        return wp_parse_args(
			get_option('bunnycdn'),
			array(
				"advanced_edit" => 		0,
				"pull_zone" => 			"",
				"cdn_domain_name" => 	"",
				"excluded" => 			BUNNYCDN_DEFAULT_EXCLUDED,
				"directories" => 		BUNNYCDN_DEFAULT_DIRECTORIES,
				"site_url" =>			get_option('home'),
				"disable_admin" => 		0,
				"api_key" => 			""
			)
		);
    }	

	/**
		Returns the option value for the given option name. If the value is not set, the default is returned.
	*/
	public static function getOption($option)
	{
		$options = BunnyCDN::getOption();
		return $options[$option];
	}

		
	
	public static function validateSettings($data)
	{
		$cdn_domain_name = BunnyCDN::cleanHostname($data['cdn_domain_name']);
		$pull_zone = BunnyCDN::cleanHostname($data['pull_zone']);

		if(strlen($cdn_domain_name) > 0 && BunnyCDN::endsWith($cdn_domain_name, BUNNYCDN_PULLZONEDOMAIN))
		{
			$pull_zone = substr($cdn_domain_name, 0, strlen($cdn_domain_name) - strlen(BUNNYCDN_PULLZONEDOMAIN) - 1);
		}
		else {
			$pull_zone = "";
		}
		
		$siteUrl = $data["site_url"];
		while(substr($siteUrl, -1) == '/') {
			$siteUrl = substr($siteUrl, 0, -1);
		}

		return array(
				"advanced_edit" => 		(int)($data['advanced_edit']),
				"pull_zone" => 			$pull_zone,
				"cdn_domain_name" => 	$cdn_domain_name,
				"excluded" => 			esc_attr($data['excluded']),
				"directories" => 		esc_attr($data['directories']),
				"site_url" =>			$siteUrl,
				"disable_admin" =>		(int)($data['disable_admin']),
				"api_key" => 			$data['api_key'],
			);
	}

	public static function cleanHostname($hostname)
	{
		$hostname = str_replace("http://", "", $hostname);
		$hostname = str_replace("https://", "", $hostname);

		return str_replace("/", "", $hostname);
	}

	public static function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	public static function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}
}


class BunnyCDNSettings
{
	// Initialize the settigs page
	public static function initialize()
	{
		add_menu_page(
			"bunny.net", 				//$page_title
			"bunny.net", 				//$menu_title
			"manage_options", 			//$capability
			"bunnycdn", 				//$menu_slug
			array(						//$function 
				'BunnyCDNSettings',
				'outputSettingsPage'
			), 
			"dashicons-carrot");		//$icon_url
		
		register_setting('bunnycdn', 'bunnycdn', array("BunnyCDN", "validateSettings"));
	}

	public static function outputSettingsPage()
	{
		$options = BunnyCdn::getOptions();
		
		?> 
		<div class="tead" style="width: 550px; padding-top: 20px; margin-left: auto; margin-right: auto; position: relative;">
			<a href="https://bunnycdn.com" target="_blank"><img width="250" src="<?php echo plugins_url('bunnynet-logo.svg', __FILE__ ); ?>?v=2"></img></a>
			<?php
				if(strlen(trim($options["cdn_domain_name"])) == 0)
				{
					echo '<h2>Enable bunny.net Content Delivery Network</h2>';
				}
				else 
				{
					echo '<h2>Configure bunny.net Content Delivery Network</h2>';
				}
			?>
			
			<form id="bunnycdn_options_form" method="post" action="options.php">
				<?php settings_fields('bunnycdn') ?>
				
				<input type="hidden" name="bunnycdn[advanced_edit]" id="bunnycdn_advanced_edit" value="<?php echo $options['advanced_edit']; ?>" />
				<input type="hidden" name="bunnycdn[disable_admin]" id="bunnycdn_disable_admin" value="<?php echo $options['disable_admin']; ?>" />

				<!-- Simple settings -->
				<div id="bunnycdn-simple-settings" <?php if($options["advanced_edit"]) { echo 'style="display: none;"'; }?>>
					<p>To set up, enter the name of your Pull Zone that you have created on your bunny.net dashboard. If you haven't done that, you can <a href="https://bunnycdn.com/dashboard/pullzones/add?originUrl=<?php echo urlencode(get_option('home')); ?>" target="_blank">create a new pull zone now</a>. It should only take a minute. After that, just click on the Enable bunny.net button and enjoy a faster website.</p>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								Pull Zone Name:
							</th>
							<td>
								<input type="text" maxlength="40" placeholder="mypullzone" name="bunnycdn[pull_zone]" id="bunnycdn_pull_zone" value="<?php echo $options['pull_zone']; ?>" size="64" class="regular-text code" />
								<p class="description">The name of the pull zone that you have created for this site. <strong>Do not include the .<?php echo BUNNYCDN_PULLZONEDOMAIN; ?></strong>. Leave empty to disable CDN integration.</p>
							</td>
						</tr>
					</table>
				</div>
				
				
				<!-- Advanced settings -->
				<table id="bunnycdn-advanced-settings" class="form-table" <?php if(!$options["advanced_edit"]) { echo 'style="display: none;"'; }?>>
					<tr valign="top">
						<th scope="row">
							CDN Domain Name:
						</th>
						<td>
							<input type="text" name="bunnycdn[cdn_domain_name]" placeholder="cdn.mysite.com" id="bunnycdn_cdn_domain_name" value="<?php echo $options['cdn_domain_name']; ?>" size="64" class="regular-text code" />
							<p class="description">The CDN domain that you you wish to use to rewrite your links to. This must be a fully qualified domain name and not the name of your pull zone. Leave empty to disable CDN integration.</p>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							Site URL:
						</th>
						<td>
							<input type="text" name="bunnycdn[site_url]" id="bunnycdn_site_url" value="<?php echo $options['site_url']; ?>" size="64" class="regular-text code" />
							<p class="description">The public URL where your website is accessible. Default for your configuration <code><?php echo get_option('home'); ?></code>.
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							Excluded:
						</th>
						<td>
							<input type="text" name="bunnycdn[excluded]" id="bunnycdn_excluded" value="<?php echo $options['excluded']; ?>" size="64" class="regular-text code" />
							<p class="description">The links containing the listed phrases will be excluded from the CDN. Enter a <code>,</code> separated list without spaces.<br/><br/>Default value: <code><?php echo BUNNYCDN_DEFAULT_EXCLUDED; ?></code></p>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							Included Directories:
						</th>
						<td>
							<input type="text" name="bunnycdn[directories]" id="bunnycdn_directories" value="<?php echo $options['directories']; ?>" size="64" class="regular-text code" />
							<p class="description">Only the files linking inside of this directory will be pointed to their CDN url. Enter a <code>,</code> separated list without spaces.<br/><br/>Default value: <code><?php echo BUNNYCDN_DEFAULT_DIRECTORIES; ?></code></p>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							API Key (Optional):
						</th>
						<td>
							<input type="text" name="bunnycdn[api_key]" id="bunnycdn_api_key" value="<?php echo $options['api_key']; ?>" size="64" class="regular-text code" />
							<p class="description">The bunny.net API key to manage the zone. Adding this will enable features such as cache purging. You can find the key in your <a href="https://bunnycdn.com/dashboard/account" target="_blank">account settings</a>.</p>
							<p id="bunnycdn_api_key_notice" class="description" style="display: none; color: red;">To clear the cache, please first set your API key.</p>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							Disable CDN for admin user:
						</th>
						<td>
							<p class="description"><input type="checkbox" id="bunnycdn_disable_admin_checkbox" class="regular-text code" <?php echo ($options['disable_admin'] == true ? "checked" : ""); ?> /> If checked, bunny.net will be disabled while signed in as an admin user.</p>
						</td>
					</tr>
				</table>

				<div>
					<p class="submit">
						<input type="submit" name="bunnycdn-save-button" id="bunnycdn-save-button" class="button submit" value="<?php echo (strlen(trim($options['cdn_domain_name'])) == 0 ? 'Enable bunny.net' : 'Update CDN Settings'); ?>">
						&nbsp;
						<input type="button" id="bunnycdn-clear-cache-button" class="button submit" value="Clear Cache">
					</p>
				</div>

				
				<a id="advanced-switch-url" href="#"><?php echo ($options["advanced_edit"]  ? "Switch To Simple View" : "Switch To Advanced View"); ?></a>
				<script>
					jQuery("#bunnycdn-clear-cache-button").click(function(e) {
						var apiKey = bunnycdn_getApiKey();
						if(apiKey.length == 0) {
							if(!bunnycdn_isAdvancedSettingsVisible())
							{
								bunnycdn_showAdvancedSettings();
							}

							jQuery("#bunnycdn_api_key_notice").show();

							// Scroll to the warning
							jQuery([document.documentElement, document.body]).animate({
						        scrollTop: jQuery("#bunnycdn_api_key_notice").offset().top
						    }, 1000);
						    jQuery("#bunnycdn_api_key").focus();
						}
						else {
							bunnycdn_showPopupMessage("Clearing Cache ...");
							jQuery.ajax({
							    type: "POST",
							    url: "https://bunnycdn.com/api/pullzone/purgeCacheByHostname?hostname=" + "<?php echo urlencode($options['cdn_domain_name']); ?>",
							    beforeSend: function(xhr){
					  				xhr.setRequestHeader('AccessKey', apiKey);
							    },
							}).done(function() {
								setTimeout(function() {
									bunnycdn_hidePopupMessage();
								}, 300);
								
							}).fail(function() {
								bunnycdn_hidePopupMessage();
								alert("Clearing cache failed. Please check your API key.");
							});
						}
					});
					jQuery("#bunnycdn_pull_zone").keydown(function (e) {
						// Allow: backspace, delete, tab, escape, enter
						if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
							 // Allow: Ctrl+A, Command+A
							(e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
							 // Allow: home, end, left, right, down, up
							(e.keyCode >= 35 && e.keyCode <= 40) || 
							(e.keyCode >= 65 && e.keyCode <= 90)) {
								 // let it happen, don't do anything
								 return;
						}
						// Ensure that it is a number and stop the keypress
						if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
							e.preventDefault();
						}
					});
					jQuery("#bunnycdn_disable_admin_checkbox").click(function(event) {
						var disableAdminChecked = jQuery("#bunnycdn_disable_admin_checkbox").is(":checked");
						jQuery("#bunnycdn_disable_admin").val(disableAdminChecked ? "1" : "0");
					});
					jQuery("#bunnycdn_cdn_domain_name").keydown(function (e) {
						// Allow: backspace, delete, tab, escape, enter
						if (jQuery.inArray(e.keyCode, [109, 189, 46, 8, 9, 27, 13, 110, 190]) !== -1 ||
							 // Allow: Ctrl+A, Command+A
							(e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
							 // Allow: home, end, left, right, down, up
							(e.keyCode >= 35 && e.keyCode <= 40) || 
							(e.keyCode >= 65 && e.keyCode <= 90)) {
								 // let it happen, don't do anything
								 return;
						}
						// Ensure that it is a number and stop the keypress
						if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
							e.preventDefault();
						}
					});
					function bunnycdn_getApiKey() {
						return '<?php echo htmlspecialchars($options["api_key"]); ?>'.trim();
					}
					function bunnycdn_isAdvancedSettingsVisible()
					{
						return jQuery('#bunnycdn-advanced-settings').css("display") != "none";
					}
					function bunnycdn_showAdvancedSettings() {
						jQuery('#bunnycdn-advanced-settings').fadeIn("fast");
						jQuery('#bunnycdn-simple-settings').fadeOut("fast");
						jQuery("#advanced-switch-url").text("Switch To Simple View");
						jQuery("#bunnycdn_cdn_domain_name").focus();
						jQuery("#bunnycdn_advanced_edit").val("1");
					}
					function bunnycdn_showSimpleSettings() {
						jQuery('#bunnycdn-advanced-settings').fadeOut("fast");
						jQuery('#bunnycdn-simple-settings').fadeIn("fast");
						jQuery("#advanced-switch-url").text("Switch To Advanced View");
						jQuery("#bunnycdn_cdn_domain_name").focus();
						jQuery("#bunnycdn_advanced_edit").val("0");
					}
					function bunnycdn_showPopupMessage(message) {
						jQuery("#bunnycdn_popupMessage").text(message);
						jQuery("#bunnycdn_popupBackground").show("fast");
						jQuery("#bunnycdn_popupBox").show("fast");

						jQuery([document.documentElement, document.body]).animate({
					        scrollTop: 0
					    }, 500);
					}
					function bunnycdn_hidePopupMessage() {
						jQuery("#bunnycdn_popupBackground").hide("fast");
						jQuery("#bunnycdn_popupBox").hide("fast");
					}

					jQuery("#advanced-switch-url").click(function(event) {
						if(!bunnycdn_isAdvancedSettingsVisible())
						{
							bunnycdn_showAdvancedSettings();
						}
						else
						{
							bunnycdn_showSimpleSettings();
						}
					});
					
					jQuery("#bunnycdn_pull_zone").change(function(event) {
						var name = jQuery("#bunnycdn_pull_zone").val();
						if(name.length > 0) 
						{
							var hostname = name + ".<?php echo BUNNYCDN_PULLZONEDOMAIN; ?>";
							jQuery("#bunnycdn_cdn_domain_name").val(hostname);
						}
						else {
							jQuery("#bunnycdn_cdn_domain_name").val("");
						}
					});
				</script>
			</form>

			<div id="bunnycdn_popupBackground" style="display: none; position: absolute; top: 0px; left: 0px; height: 100%; width: 100%; background-color: #f1f1f1; opacity: 0.93;"></div>
			<div id="bunnycdn_popupBox" style="display: none; position: absolute; top: 0px; left: 0px; height: 100%; width: 100%;">
				<img style="margin-left: auto; margin-right: auto; display: block; margin-top: 110px;" src="<?php echo plugins_url('loading-bunny.gif', __FILE__ ); ?>"></img>
				<h3 id="bunnycdn_popupMessage" style="text-align: center;"></h4>
			</div>
		</div><?php
	}
}

?>