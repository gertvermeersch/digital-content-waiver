<?php
/**
 * Plugin Name: Digital Content Waiver (Checkout)
 * Description: git 
 * Version: 1.0.0
 * Author: Stormlabs
 * License: GPLv2 or later
 * Text Domain: checkout-digital-waiver
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

const CHECKOUT_DIGITAL_WAIVER_FIELD_ID = 'checkout-digital-waiver/digital_waiver';
const CHECKOUT_DIGITAL_WAIVER_POST_KEY = 'checkout_digital_waiver';
const CHECKOUT_DIGITAL_WAIVER_META_KEY = '_checkout_digital_waiver';
const CHECKOUT_DIGITAL_WAIVER_TS_META_KEY = '_checkout_digital_waiver_ts';

/**
 * Check whether the current cart contains at least one virtual product.
 */
function checkout_digital_waiver_cart_has_virtual_product(): bool {
    if ( ! function_exists('WC') || ! WC()->cart ) {
        return false;
    }

    foreach ( WC()->cart->get_cart() as $item ) {
        $product = $item['data'] ?? null;
        if ( $product && $product->is_virtual() ) {
            return true;
        }
    }

    return false;
}

/**
 * Human-readable waiver message text.
 */
function checkout_digital_waiver_text(): string {
    return __('I request immediate delivery of digital content and acknowledge that I lose my right of withdrawal once delivery begins.', 'checkout-digital-waiver');
}

/**
 * Validation error used in both classic checkout and block checkout.
 */
function checkout_digital_waiver_error_text(): string {
    return __('Please confirm immediate delivery of digital content and the loss of the withdrawal right.', 'checkout-digital-waiver');
}

/**
 * Store waiver audit values on an order.
 */
function checkout_digital_waiver_store_audit( WC_Order $order ): void {
    $order->update_meta_data(CHECKOUT_DIGITAL_WAIVER_META_KEY, 'yes');
    $order->update_meta_data(CHECKOUT_DIGITAL_WAIVER_TS_META_KEY, (string) time());
}

/**
 * Register the waiver field for Checkout Block.
 */
add_action('woocommerce_init', function () {
    if ( ! function_exists('woocommerce_register_additional_checkout_field') ) {
        return;
    }

    woocommerce_register_additional_checkout_field([
        'id'            => CHECKOUT_DIGITAL_WAIVER_FIELD_ID,
        'label'         => checkout_digital_waiver_text(),
        'location'      => 'order',
        'type'          => 'checkbox',
        'required'      => true,
        'error_message' => checkout_digital_waiver_error_text(),
    ]);
});

/**
 * Extra guard for block checkout validation.
 */
add_action('woocommerce_validate_additional_field', function ( WP_Error $errors, string $field_key, $field_value ) {
    if ( CHECKOUT_DIGITAL_WAIVER_FIELD_ID !== $field_key ) {
        return;
    }

    if ( ! checkout_digital_waiver_cart_has_virtual_product() ) {
        return;
    }

    if ( ! wc_string_to_bool((string) $field_value) ) {
        $errors->add('checkout_digital_waiver_missing', checkout_digital_waiver_error_text());
    }
}, 10, 3);

/**
 * Persist audit data for block checkout orders.
 */
add_action('woocommerce_store_api_checkout_update_order_meta', function ( WC_Order $order ) {
    $block_meta_key = '_wc_other/' . CHECKOUT_DIGITAL_WAIVER_FIELD_ID;
    $value          = $order->get_meta($block_meta_key, true);

    if ( wc_string_to_bool((string) $value) ) {
        checkout_digital_waiver_store_audit($order);
    }
});

/**
 * Render the required checkbox at checkout (after terms & conditions).
 */
add_action('woocommerce_checkout_after_terms_and_conditions', function () {
    if ( is_admin() && ! defined('DOING_AJAX') ) {
        return;
    }

    if ( ! checkout_digital_waiver_cart_has_virtual_product() ) {
        return;
    }


    $terms_url = get_permalink( wc_get_page_id('terms') );

    woocommerce_form_field(CHECKOUT_DIGITAL_WAIVER_POST_KEY, [
        'type'     => 'checkbox',
        'class'    => ['form-row', 'privacy'],
        'required' => true,
        'label'    => sprintf(
            /* translators: %s = Terms URL */
            __('%1$s (%2$s)', 'checkout-digital-waiver'),
            checkout_digital_waiver_text(),
            '<a href="' . esc_url($terms_url) . '" target="_blank" rel="noopener">' . esc_html__('more info', 'checkout-digital-waiver') . '</a>'
        ),
    ], WC()->checkout ? WC()->checkout->get_value(CHECKOUT_DIGITAL_WAIVER_POST_KEY) : '');
});

/**
 * Validate the checkbox.
 */
add_action('woocommerce_checkout_process', function () {
    if ( ! checkout_digital_waiver_cart_has_virtual_product() ) {
        return;
    }

    if ( empty($_POST[ CHECKOUT_DIGITAL_WAIVER_POST_KEY ]) ) {
        wc_add_notice(checkout_digital_waiver_error_text(), 'error');
    }
});

/**
 * Store an audit trail on the order.
 */
add_action('woocommerce_checkout_create_order', function ( $order ) {
    if ( ! $order instanceof WC_Order ) {
        return;
    }

    if ( isset($_POST[ CHECKOUT_DIGITAL_WAIVER_POST_KEY ]) ) {
        checkout_digital_waiver_store_audit($order);
    }
}, 10, 1);

/**
 * Show it in WooCommerce admin order screen.
 */
add_action('woocommerce_admin_order_data_after_billing_address', function ( $order ) {
    if ( ! $order instanceof WC_Order ) {
        return;
    }

    $val = $order->get_meta(CHECKOUT_DIGITAL_WAIVER_META_KEY);
    if ( $val === 'yes' ) {
        $ts = (int) $order->get_meta(CHECKOUT_DIGITAL_WAIVER_TS_META_KEY);
        $when = $ts ? esc_html( gmdate('Y-m-d H:i:s', $ts) ) . ' UTC' : esc_html__('recorded', 'checkout-digital-waiver');
        echo '<p><strong>Digital content waiver:</strong> confirmed (' . $when . ')</p>';
    }
});
