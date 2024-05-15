=== bunny.net - WordPress CDN Plugin ===
Contributors: bunnycdn
Tags: cdn, content delivery network, performance, bandwidth
Requires at least: 6.0
Tested up to: 6.5
Stable tag: 2.2.6
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Enable Bunny CDN to speed up your WordPress website and enjoy greatly improved loading times around the world.

== Description ==

Turbocharge your website's performance effortlessly with the Bunny WordPress CDN Plugin. This powerful tool effortlessly integrates bunny.net's next-generation delivery optimization services into your WordPress site, providing you with a configuration wizard to simplify setup, all without requiring complex configuration or coding on your part. 

Benefit from global delivery with optimal latency, automatically transfer your media to the cloud with multi-region replication, seamlessly compress media files without coding, and enhance user privacy and GDPR compliance with our open-source non-tracked fonts.

= How does it work? =
This plugin makes it easy to activate Bunny CDN, Bunny Offloader, Bunny Optimizer, and Bunny Fonts with our new configuration wizard. 

* Bunny CDN configures your WordPress to utilize our CDN, substituting existing static content links with lightning-fast CDN links. 
* Bunny Optimizer automatically processes and compresses files and images, achieving significant reductions in file size. 
* Bunny Offloader seamlessly transfers your media files to Bunny Storage, providing scalable cloud storage with automatic multi-region replication and high-throughput performance. 
* Bunny Fonts, available free of charge to all users, offers a vast selection of fonts to choose from. Simply select your favorite font from our extensive library and effortlessly import it into your site using a simple CSS @import or HTML <link> tag.


= Features =
* New Configuration Wizard: Streamlines the entire setup process in under 5 minutes, ensuring a hassle-free experience.
* Enhanced Dashboard: Easily access vital information such as account balance, last month's usage, content delivery bandwidth, cache hit ratios, and request statistics with our intuitive new dashboard interface, all without leaving the WordPress Admin panel.
* Bunny CDN: Next-generation CDN boasting a lightning-fast 24ms latency, equipped with edge rules, optimized for video content, advanced caching capabilities, real-time logging, free SSL certificates, and global distribution utilizing NVMe+ SSD servers.
* Bunny Offloader: Seamlessly automates the offloading of media content from WordPress to Bunny Storage.
* Bunny Storage: Benefit from multi-region replication, with no egress or API costs, and unparalleled latency performance for your stored content.
* Bunny Fonts: Rest easy with our font service featuring zero logging, full GDPR compliance, and no data sharing, all hosted within the EU for maximum privacy and security.
* Bunny Optimizer: Automatic processing and compression of images, CSS & JavaScript files, optimizing your website's performance effortlessly.

= System Requirements =
* PHP >=7.4
* WordPress >=6.0

= Author =
* [bunny.net](https://bunny.net "bunny.net")

== Changelog ==

= 2.2.6
* Offloader: support PDF files

= 2.2.5
* Offloader: fixed a type error with post metadata

= 2.2.4
* Offloader: fixed an error with mismatching date formats

= 2.2.3 =
* Added sync conflict resolution interface
* Added support for WP_PROXY_* constants

= 2.2.2 =
* Improved compatibility with other plugins

= 2.2.1 =
* Fixed a fatal error introduced in 2.2.0

= 2.2.0 =
* Added support for WordPress 6.5

= 2.1.3 =
* Improved compatibility with plugins that use older composer versions

= 2.1.2 =
* Improved compatibility with MariaDB
* Improved compatibility with Divi Theme Builder
* Improved compatibility with plugins that use older composer versions

= 2.1.1 =
* Improved Offloader error handling for sync failures

= 2.1.0 =
* Added support for PHP 7.4

= 2.0.3 =
* Allow plugin to be converted into Agency Mode

= 2.0.2 =
* CDN: support srcset without width/pixel density descriptor
* Offloader: only show "in sync" if "sync existing" is enabled

= 2.0.1 =
* Fixed link to the Bunny DNS guide

= 2.0.0 =
* Redesigned plugin, with Bunny Fonts, Bunny Optimizer and Bunny Offloader support

= 1.0.8 =
* Updated the branding to bunny.net

= 1.0.7 =
* Increased pull zone name length limit

= 1.0.6 =
* Added an option to disable BunnyCDN while logged in as an admin user
* Added a built in Clear Cache functionality
* Added automatic dns prefetch headers

= 1.0.5 =
* Fixed the domain name input to allow hyphens

= 1.0.4 =
* Logo update

= 1.0.3 =
* Bug fixes

= 1.0.2 =
* Added a Site URL setting for configurations using a non-standard URL configuration.

= 1.0.1 =
* Fixed a problem with HTTPS URL handling

= 1.0.0 =
* Initial release

== Screenshots ==

1. Setup wizard
2. Overview page
3. Bunny CDN configuration
4. Bunny Offloader configuration
5. Bunny Optimizer configuration
6. Bunny Fonts configuration
