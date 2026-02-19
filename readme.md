# Digital Content Waiver (Checkout)

Adds a required EU digital content waiver checkbox to WooCommerce checkout for virtual products, with support for both classic and block checkout.

## Features

- Supports WooCommerce classic checkout.
- Supports WooCommerce block-based checkout.
- Enforces server-side validation.
- Stores order audit data (confirmation + timestamp).
- Shows waiver confirmation in WooCommerce admin order details.

## Requirements

- WordPress 6.0+
- WooCommerce (active)
- PHP 7.4+

## Installation

1. Copy this plugin directory to `wp-content/plugins/checkout-digital-waiver`.
2. Activate **Digital Content Waiver (Checkout)** in WordPress admin.
3. Confirm WooCommerce is active.
4. Run a test checkout with a virtual product.

## How it works

When at least one virtual product is in the cart, customers must confirm:

- immediate delivery of digital content
- acknowledgment of withdrawal-right loss once delivery begins

The confirmation is validated and saved on the order for audit purposes.

## Changelog

### 1.0.0

- Initial release.
- Added classic checkout waiver field.
- Added Checkout Block waiver field support.
- Added validation and order-level audit trail storage.

## License

GPLv2 or later. See `LICENSE`.
