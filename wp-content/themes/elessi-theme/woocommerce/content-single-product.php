<?php
/**
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     3.6.0
 */

do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}

do_action('nasa_single_product_layout');
do_action('woocommerce_after_single_product');
