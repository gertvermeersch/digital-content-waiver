=== Digital Content Waiver (Checkout) ===
Contributors: stormlabs
Tags: woocommerce, checkout, digital products, eu, compliance
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Requires Plugins: woocommerce
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a required EU digital content waiver checkbox to WooCommerce checkout for virtual products, with support for classic and block checkout.

== Description ==

This plugin helps WooCommerce stores comply with EU digital content rules.

When the cart contains virtual products, it requires customers to confirm that they request immediate delivery of digital content and acknowledge the loss of withdrawal rights once delivery starts.

Features:

* Works with WooCommerce classic checkout.
* Works with WooCommerce block-based checkout.
* Adds server-side validation to prevent bypassing the checkbox.
* Stores an order audit trail with confirmation timestamp.
* Displays waiver confirmation in WooCommerce admin order details.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate **Digital Content Waiver (Checkout)** through the WordPress Plugins screen.
3. Ensure WooCommerce is active.
4. Test checkout with a virtual product to confirm the waiver checkbox appears and is required.

== Frequently Asked Questions ==

= When is the waiver required? =
The waiver is required when the cart contains one or more virtual products.

= Does this support WooCommerce Checkout Block? =
Yes. The plugin supports both classic checkout and block-based checkout.

= Where is proof of consent stored? =
On the order meta, including a confirmation flag and UTC timestamp.

== Changelog ==

= 1.0.0 =
* Initial release.
* Added required digital content waiver checkbox for classic checkout.
* Added block checkout support using WooCommerce additional checkout fields.
* Added validation and order audit trail storage.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
