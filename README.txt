=== Faramoj Advanced Search ===
Contributors: faramoj-sazan
Tags: search, live search, ajax, multilingual, acf, wpml, polylang
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later

A high-performance, AJAX-driven live search plugin optimized for technical telecommunication products, articles, and documentation.

== Description ==

Faramoj Advanced Search (FAS) is a highly optimized, modular live search plugin utilizing custom WP REST API endpoints instead of the standard slow admin-ajax.php.
Features include native multilingual support (WPML & Polylang), ACF field indexing and search (e.g. frequency bands, gain, technical specifications), modern minimalist dark mode / corporate white overlays, and search result category tabs (Products, News, Docs).

== Installation ==

1. Upload the `faramoj-advanced-search` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure settings under Settings -> Faramoj Search.
4. Add the shortcode `[fas_search_trigger]` anywhere on your site to display the search trigger button.

== Frequently Asked Questions ==

= How do I trigger the search? =
You can use the shortcode `[fas_search_trigger]` to output a search button/trigger, or use any element with the class `.fas-search-trigger` to open the overlay.

= Does it support ACF? =
Yes, it queries meta keys like `technical_specifications` and `frequency_range` to find matching technical products.
