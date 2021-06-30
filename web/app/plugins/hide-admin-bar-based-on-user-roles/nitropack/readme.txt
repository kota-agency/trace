=== NitroPack ===
Contributors: nitropack
Tags: cache,perfomance,optimize,pagespeed,lazy load,cdn,critical css,compression,defer css javascript,minify css,minify,webp
Requires at least: 4.7
Tested up to: 5.7
Requires PHP: 5.3
Stable tag: 1.5.3
License: GNU General Public License, version 2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Everything you need for a fast website. Simple set up, easy to use, awesome support. Caching, Lazy Loading, Minification, Defer CSS/JS, CDN and more!

== Description ==
NitroPack is the all-in-one performance optimization service. It combines **everything** you need for a lightning-fast website. Image optimization, code minification, caching, CDN, lazy loading - you name it, NitroPack has it.

[youtube https://www.youtube.com/watch?v=IKbHcOZ3Plw]

NitroPack performs all optimizations in the cloud. This makes it a **very lightweight** solution with a **lower CPU overhead** compared to standard caching plugins.

Our service provides you with the following (typically paid extra) functionalities **included without the need for additional configuration**:

* **Amazon CloudFront CDN** – we automatically serve your static assets from a CDN based on Amazon’s CloudFront service
* **Image Optimization** – we optimize all of your images automatically  and convert them to WebP
* **Cache Warmup** – we keep your most important pages optimized at all times

Apart from these, NitroPack offers other unique features and benefits like:

* **Incredibly Simple Setup** – getting started with NitroPack is a breeze. There’s no technical configuration or a 15-step installation process. And you don’t need to be a developer to set it up. Simply follow [these 4 steps](https://wordpress.org/plugins/nitropack/#installation "Installation Instructions") and you’ll be done in no time
* **No risk of damaging your original site files** – NitroPack works on copies of your site files. You don’t like the results from an optimization? No problem. Purge your cache and start over. Even if you decide to disable NitroPack your site will go back to the state it was in before activating our plugin
* **Cache Invalidation** – you can invalidate cache files instead of purging them. This allows NitroPack to keep serving your visitors from cache while a fresh copy of the cache is being generated in the background. Cache invalidation is awesome for **high traffic situations** like campaigns. It allows you to keep updating your site while still serving cache to your clients. And with NitroPack, **cache invalidation happens automatically**. (For more info on this, scroll down to the “NitroPack and Campaigns” section)
* **Critical CSS tailored to each of your unique layouts** – most plugins that provide critical CSS functionality prepare a single critical CSS file per post type. Even when you have multiple pages with the same post type but different layouts. NitroPack detects this and generates **unique critical CSS for each unique layout**. Oh, and because desktop and mobile devices have different viewports, NitroPack also uses different critical CSS for each device type ;)
* **Optimize resources linked statically into your theme files** – NitroPack will discover and optimize all resources linked into your theme, even ones that come hardcoded into your CSS files (even if they are multiple levels down an @import chain)

**The configuration requires no technical knowledge.** All you need to do is select your desired optimization level: Standard, Medium, Strong or Ludicrous. Besides that, NitroPack does all the work.

## Other Key Features

* Minify HTML
* Minify CSS files and inline CSS defined in style tags and attributes
* Minify JavaScript files and inline script tags
* HTML, CSS and JavaScript compression
* Gzip and Brotli compression
* Optimize images
* Convert to WebP
* Lazy load images (including CSS background images)
* Lazy load iframes
* Amazon CDN
* Image previews for YouTube and Vimeo embeds
* Defer CSS  
* Defer JavaScript
* Font rendering optimization
* DNS prefetch
* Compatible with mobile, tablet and desktop devices out of the box
* Multisite ready
* Support for scheduled posts
* eCommerce compatibility
* Multilingual support
* Advanced resource loading mechanism
* Resource preloading using web workers
* Automatic cache management – NitroPack will automatically update its cache files when you update content on your site
* Option to exclude certain pages from being cached
* Option to exclude any resource from being optimized
* Option to ignore URL parameters that do not modify the content of your pages (e.g. campaign parameters like utm_source, utm_campaign, etc.)
* Cloudflare integration
* Sucuri integration
* Generic reverse proxy integration (NGINX, Varnish, etc.)
* No database connection needed

## Running Marketing Campaigns with NitroPack

Two major issues often come up when running a campaign. First, each visitor hits your server with a **unique URL** request. Second, you lose your **cache** if you update content on your site. 
Most optimization plugins come up short when it comes to both issues. 
At the same time, NitroPack has two powerful features that help you thrive in these high traffic situations:

 1. NitroPack recognizes **campaign parameters in the URL** and ignores them when looking up a cache file for the campaign request.
 2. **Cache invalidation** - typically, when you update content on your site, caching plugins have to purge their cache and start rebuilding it.  When a purge occurs during a high traffic period your visitors will no longer be served from cache. And your server will have to work extra hard to generate new cache files. As a result, the user experience on your website takes a hit. NitroPack solves this problem by **invalidating the cache, instead of purging it**. This method allows NitroPack to refresh the cache files in the background. At the same time, you still serve your clients from the slightly outdated cache files. As we already said, this happens **automatically**. You don’t need to worry about caching during an important campaign.

Whether you’re running a big campaign or your site suddenly becomes trending, both features are crucial for keeping your visitors happy.
So, if you want a fast website right now, **go over to the [Installation](https://wordpress.org/plugins/nitropack/#installation "Installation Instructions") section and download NitroPack**.

## Incompatible Plugins

WordPress is designed to have only a single active page cache solution at a time, otherwise conflicts can arise. We do not recommend using NitroPack together with another caching plugin, like:

* WP Rocket
* Autoptimize
* Swift Performance
* WP Fastest Cache
* WP Fastest Cache Premium
* Powerpack (WPTouchPro)
* W3 Total Cache
* Breeze
* PhastPress
* WP Super Cache
* Litespeed Cache
* Swift Performance
* PageSpeed Ninja
* Comet Cache by WP Sharks
* Hummingbird
* SG Optimizer
* WP-Optimize - only the page caching must be disabled, not the entire plugin
* Smush - only the lazy load option must be disabled, not the entire plugin
* JetPack - only the lazy load option must be disabled, not the entire plugin
* ShortPixel - only the WebP conversion option must be disabled, not the entire plugin

## 3rd Party Services Used By The NitroPack Plugin

The NitroPack plugin acts as a service. It calls/sends data to our API servers, which perform all of the optimizations. 
As a result, our infrastructure does all the heavy lifting. That’s how NitroPack ensures low CPU overhead for your servers.
To learn more about what NitroPack provides as a service as well as what data it collects and uses, please visit:

* Our website - [https://nitropack.io/](https://nitropack.io/)
* Terms of Use - [https://nitropack.io/page/terms](https://nitropack.io/page/terms)
* Privacy Policy - [https://nitropack.io/page/privacy](https://nitropack.io/page/privacy)

NitroPack also uses Amazon CloudFront and Bunny CDN to accelerate content delivery across the globe.
For more information about these services, please visit:

* The official CloudFront page - [https://aws.amazon.com/cloudfront/](https://aws.amazon.com/cloudfront/)
* The AWS Service Terms page - [https://aws.amazon.com/service-terms/](https://aws.amazon.com/service-terms/)
* BunnyCDN’s website - [https://bunnycdn.com/](https://bunnycdn.com/)
* BunnyCDN’s Terms of Service - [https://bunnycdn.com/tos](https://bunnycdn.com/tos)
* Bunny CDN’s Privacy & Data Policy - [https://bunnycdn.com/privacy](https://bunnycdn.com/privacy)

== Installation ==

1. Click the “Download” button on this page. You’ll get a .zip file, which you can save on your computer.
2. Go to your website’s dashboard, open the “Plugins” menu and click “Add new”. After that, choose the nitropack.zip file and click “Install Now”
3. You now need to **connect your website to NitroPack**. Simply go to [https://nitropack.io/](https://nitropack.io/) and create an account. After you log in, you’ll see a “Connect Your Website” menu on the left. There, you’ll find a Site ID and Site Secret.
4. Go back to your website’s dashboard. Click “Settings” and find the NitroPack option. Finally, enter your Site ID and Site Secret and click “Connect to NitroPack”.

That’s all there is to it!



== Frequently Asked Questions ==

= Does NitroPack modify site files? =

No. NitroPack works on copies of your site files. However, it does modify your wp-config.php file if WP_CACHE is not enabled yet.

= I installed NitroPack but my pages are still slow. Why? =

After installing and activating NitroPack, you must log into your account, go to “Connect Your Website” and use the provided Site ID and Site Secret to connect the plugin to our cloud service.

= Why my scores are still low after connecting NitroPack? =

After connecting NitroPack you need to select your desired optimization mode - Standard, Medium, Strong or Ludicrous. Once you do that, please visit your home page and allow NitroPack a minute to prepare an optimized version of your page. You can then run the tests again.

= How long does it take for pages to get optimized? =

It usually takes NitroPack a few seconds to optimize your pages.

= Can I use another page caching plugin and NitroPack at the same time? =

WordPress’s design allows you to use only one page cache solution at a time. Other page cache solutions are designed this way too. However, you can use other non-page cache optimization solutions perfectly well with NitroPack.io (e.g. database optimization plugins, object caching, etc.).

= What if I have a question? =

You can contact us anytime at https://m.me/getnitropack

= Will NitroPack slow down my server? =

No. We’ve designed NitroPack to be a very lightweight solution that adds no CPU overhead to your server.

== Screenshots ==
1. Connect your store
2. Dashboard - see and manage the data in your Nitropack.io

== Changelog ==

= 1.5.4 =
* Change: Bump up the tested-up-to version for WP 5.7
* Bug fix: Resolve a constant already defined error
* Bug fix: Resolve an issue with the positioning of the status dot in the admin bar

= 1.5.3 =
* New Feature: Safe Mode toggle within the plugin's dashboard
* Improvement: Add support for an upcoming improvement in the compatibility with reverse proxies like Cloudflare and Sucuri
* Improvement: Add a filter to allow the list of cacheable post types to be extended - `nitropack_cacheable_post_types`
* Improvement: Better handling of WooCommerce price updates
* Bug fix: Cache warmup was not being triggerd when posting a new article. This is resolved now.

= 1.5.2 =
* Improvement: Faster cache purge via the webhook
* Improvement: Purging/Invalidating cache via WP-CLI is now direct and provides better feedback of the result
* Bug fix: Any cache purge was triggering full cache purge on the local server. This is now resolved.

= 1.5.1 =
* Bug fix: Resolve an issue causing a fatal error related to undefined class name

= 1.5.0 =
* New Feature: Compatibility with Cloudflare APO
* Improvement: Better resilience to network related issues
* Improvement: Faster cache purge
* Improvement: Overall stability improvements
* Deprecation: Removed the Invalidate All Cache option. The invalidate action is much better suited for single page invalidations.

= 1.4.1 =
* Improvement: Performance improvements in content updates
* Improvement: Better compatibility with Download Monitor

= 1.4.0 =
* New feature: Extended WP-CLI compatibility with ability to purge/invalidate by URL or tag
* New feature: Add a method for dynamically preventing automated purge/invalidate
* Improvement: Compatibility with jQuery 3
* Improvement: Better compatibility with SiteGround's dynamic cache layer
* Improvement: Overall stability improvements
* Bug fix: Resolve an issue with undefined HTTP_HOST key

= 1.3.20 =
* Bug fix: Resolve an issue with reverse proxy cache purge through the webhook

= 1.3.19 =
* Improvement: Stability improvements

= 1.3.18 =
* New Feature: Pagely compatibility
* Improvement: Even better compatibility with WooCommerce's Geolocate option
* Improvement: More accurate sync with Avada's date based containers
* Improvement: Better compatibility with reverse proxies
* Improvement: Stability improvements

= 1.3.17 =
* New Feature: Much simpler and easier connect method
* New Feature: Support a new "nitropack_meta_box" capability which allows you to grant access to cache purge on different user roles
* Improvement: Better status notices
* Improvement: Stability improvements

= 1.3.16 =
* Improvement: Better compatibility with ShortPixel Adaptive Images 2.x
* Improvement: Overall stability and performance improvements

= 1.3.15 =
* Improvement: Handling of stock quantity changes in WooCommerce via the REST API
* Improvement: Overall stability and performance improvements

= 1.3.14 =
* Improvement: Better handling of stock quantity changes in WooCommerce
* Improvement: Better handling of updates to non-public post types
* Improvement: More efficient use of our API
* Bug fix: Fix an issue with slow cache propagation after full purge

= 1.3.13 =
* New feature: Add admin bar entry with quick links to useful actions like purge cache
* New feature: Add WP-CLI methods for invalidating/purging cache
* New feature: Add WP-CLI method for running cache warmup
* Improvement: Automatically detect outdated cache files restored from a backup and do not serve them to clients
* Improvement: Automatically detect connection issues and suggest steps to resolve these
* Improvement: Automatically start optimizing after the plugin is connected successfully
* Improvement: Performance improvements

= 1.3.12 =
* Bug fix: Resolve a fatal error in the SDK on certain PHP versions

= 1.3.11 =
* Improvement: Workaround for issue with communicating to our servers in LiteSpeed environments

= 1.3.10 =
* Bug fix: Resolve an issue with domains starting with "www." which was introduced in v1.3.9

= 1.3.9 =
* New feature: Ability to connect/disconnect using WP-CLI
* Improvement: Recognize updates to WooCommerce Cart Reports post types to reduce cache purges
* Improvement: Better Ezoic compatibility
* Bug fix: Resolve an issue with saving the compression status when configured manually
* Overall stability improvements

= 1.3.8 =
* New feature: Add support for local AJAX caching
* Improvement: Better SiteGround compatibility
* Improvement: Ezoic compatibility
* Improvement: NGINX Helper compatibility
* Bug fix: Resolve an issue which caused problems in WP CLI
* Improvements in the automated cache purge
* Overall stability improvements

= 1.3.7 =
* Bug fix: Resolve an issue causing insufficient permissions error

= 1.3.6 =
* Bug fix: Resolve an issue with nonces in REST requests

= 1.3.5 =
* Improvement: Show instructions for configuring recommended hosting settings if needed
* Improvement: Better detection of taxonomies and archive pages
* Improvement: Better compatibility with ShortPixel
* Improvement: Better WP Engine compatibility
* Improvement: Updated nonce handling
* Bug fix: Category pages were not being optimized if archive optimization was disabled. This is now fixed.
* Bug fix: Fix an issue with custom cache expiration for The Events Calendar

= 1.3.4 =
* Improvement: Better compatibility with Kinsta
* Improvement: Improved handling of post status transiotions
* Improvement: Allow optimizations for archive pages

= 1.3.3 =
* Improvement: Optimize all post/page types by default ot avoid confusion why a certain URL is not optimized.
* Improvement: Automatically refresh cache based on comment actions (posting, approving, unapproving, etc.)

= 1.3.2 =
* Improvement: Workaround for an issue in the WP Engine environment which causes timeouts in certain network communication scenarios. This resolves slow post/page updates in the admin area.

= 1.3.1 =
* Improvement: Nicer cache purge reason messages
* Bug fix: Resolve an issue where the home page was not always updated after publishing new posts/pages

= 1.3 =
* New feature: Option select which post types and taxonomies get optimized
* New feature: Option to enable/disable the automated cache purges
* New feature: Automatically warmup new posts/pages
* New feature: Add meta box to allow cache purge/invalidate from the post/page edit screens
* New feature: New and improved way of tracking relationships between pages allowing for smarter automated cache purges, which affect less cache files
* Resolve layout issues in the admin panel on mobile
* Add compatibility with GoDaddy's managed WordPress hosting

= 1.2.3 =
* Stability improvements

= 1.2.2 =
* Synchronize the nonce and page cache life times
* Improve cache synchronization when updating menu entries
* Improve cache synchronization when making appearance customizations
* Fix false "plugin conflict" error with WP Optimize
* Stability improvements

= 1.2.1 =
* Added support for Fusion Builder's container expiration
* Added compatibility with the Post Expirator plugin
* Added compatibility with the Portfolio Sorting plugin
* Stability improvements

= 1.2 =
* Stability improvements

= 1.1.5 =
* Improved cache management for scheduled posts
* Fix cache expiration for posts scheduled for dates in the past
* Better update handling

= 1.1.4 =
* Stability improvements

= 1.1.3 =
* Prevent crashes originating from missing functions.php file

= 1.1.2 =
* Better handling of automated updates

= 1.1.1 =
* Automatically update the advanced-cache.php file after plugin update

= 1.1 =
* Performance and stability improvements

= 1.0 =
* Initial release
