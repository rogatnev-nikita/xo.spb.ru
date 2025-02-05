<?php
/* ============================================================================ */
/* Remove - Add action, filter WooCommerce ==================================== */
/* ============================================================================ */
/*
 * Remove action woocommerce
 */
add_action('init', 'elessi_remove_action_woo');
if(!function_exists('elessi_remove_action_woo')) :
    function elessi_remove_action_woo() {
        if(!NASA_WOO_ACTIVED) {
            return;
        }
        
        global $nasa_opt, $yith_woocompare;
        
        /* UNREGISTRER DEFAULT WOOCOMMERCE HOOKS */
        remove_action('woocommerce_single_product_summary', 'woocommerce_breadcrumb', 20);
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_show_messages', 10);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
        
        remove_action('woocommerce_cart_collaterals', 'woocommerce_cart_totals');
        
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
        
        if (isset($nasa_opt['disable-cart']) && $nasa_opt['disable-cart']) {
            remove_action('woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30);
            remove_action('woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30);
            remove_action('woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30);
        }
        
        // Remove compare default
        if($yith_woocompare) {
            $nasa_compare = isset($yith_woocompare->obj) ? $yith_woocompare->obj : $yith_woocompare;
            remove_action('woocommerce_after_shop_loop_item', array($nasa_compare, 'add_compare_link'), 20);
            remove_action('woocommerce_single_product_summary', array($nasa_compare, 'add_compare_link'), 35);
        }
        
        /**
         * For content-product
         */
        remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');
        remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title');
        
        /**
         * Shop page
         */
        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
        remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
        remove_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 10);

        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price');
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating');
        
        /**
         * Sale-Flash
         */
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
        
        /**
         * Remove wishlist btn in detail product
         */
        if(NASA_WISHLIST_ENABLE) {
            add_filter('yith_wcwl_positions', 'elessi_remove_btn_wishlist_single_product');
        }
    }
endif;

/*
 * Add action woocommerce
 */
add_action('init', 'elessi_add_action_woo');
if(!function_exists('elessi_add_action_woo')) :
    function elessi_add_action_woo() {
        if(!NASA_WOO_ACTIVED) {
            return;
        }
        
        global $nasa_opt, $yith_woocompare, $loadmoreStyle;
        
        // add_action('nasa_root_cats', 'elessi_get_root_categories');
        add_action('nasa_child_cat', 'elessi_get_childs_category', 10, 2);
        
        // Results count in shop page
        $disable_ajax_product = false;
        if(
            (isset($nasa_opt['disable_ajax_product']) && $nasa_opt['disable_ajax_product']) ||
            get_option('woocommerce_shop_page_display', '') != '' || 
            get_option('woocommerce_category_archive_display', '') != ''
        ) :
            $disable_ajax_product = true;
        endif;
        
        $pagination_style = isset($nasa_opt['pagination_style']) ? $nasa_opt['pagination_style'] : 'style-2';
        
        if(isset($_REQUEST['paging-style']) && in_array($_REQUEST['paging-style'], $loadmoreStyle)) {
            $pagination_style = $_REQUEST['paging-style'];
        }
        
        if($disable_ajax_product) :
            $pagination_style = $pagination_style == 'style-2' ? 'style-2' : 'style-1';
        endif;
        
        if(in_array($pagination_style, $loadmoreStyle)) {
            add_action('nasa_shop_category_count', 'elessi_result_count', 20);
        } else {
            add_action('nasa_shop_category_count', 'woocommerce_result_count', 20);
        }
        
        add_action('nasa_change_view', 'elessi_nasa_change_view', 10, 3);
        add_action('woocommerce_cart_collaterals', 'woocommerce_cart_totals', 9);
        
        add_action('woocommerce_single_product_lightbox_summary', 'woocommerce_template_loop_rating', 10);
        add_action('woocommerce_single_product_lightbox_summary', 'woocommerce_template_single_price', 15);
        add_action('woocommerce_single_product_lightbox_summary', 'woocommerce_template_single_excerpt', 20);
        
        // Deal time for Quickview product
        if(!isset($nasa_opt['single-product-deal']) || $nasa_opt['single-product-deal']) {
            add_action('woocommerce_single_product_lightbox_summary', 'elessi_deal_time_quickview', 29);
        }
        
        if (!isset($nasa_opt['disable-cart']) || !$nasa_opt['disable-cart']) {
            add_action('woocommerce_single_product_lightbox_summary', 'woocommerce_template_single_add_to_cart', 30);
        }
        
        add_action('woocommerce_single_product_lightbox_summary', 'woocommerce_template_single_meta', 40);
        add_action('woocommerce_single_product_lightbox_summary', 'elessi_combo_in_quickview', 31);
        add_action('woocommerce_single_product_lightbox_summary', 'woocommerce_template_single_sharing', 50);
        
        add_action('nasa_single_product_layout', 'elessi_single_product_layout', 1);
        /**
         * add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 1);
         */
        add_action('woocommerce_after_single_product_summary', 'woocommerce_template_single_meta', 11);
        
        add_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
        
        add_action('woocommerce_single_product_summary', 'elessi_ProductShowReviews', 15);
        add_action('woocommerce_single_review', 'elessi_ProductShowReviews', 10);
        
        /**
         * add_action('woocommerce_single_product_summary', 'elessi_single_availability', 15);
         */
        add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 20);
        // add_action('woocommerce_single_product_summary', 'elessi_single_hr', 21);
        add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 25);
        
        // Deal time for Single product
        if(!isset($nasa_opt['single-product-deal']) || $nasa_opt['single-product-deal']) {
            add_action('woocommerce_single_product_summary', 'elessi_deal_time_single', 29);
        }
        
        add_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 40);
        
        /**
         * Add compare product
         */
        if($yith_woocompare) {
            if (get_option('yith_woocompare_compare_button_in_product_page') == 'yes') {
                add_action('product_video_btn', 'elessi_add_compare_in_detail', 32);
            }
            
            if (get_option('yith_woocompare_compare_button_in_products_list') == 'yes') {
                add_action('nasa_show_buttons_loop', 'elessi_add_compare_in_list', 50);
            }
        }
        
        add_action('nasa_show_buttons_loop', 'elessi_add_to_cart_in_list', 10);
        
        add_action('nasa_show_buttons_loop', 'elessi_add_wishlist_in_list', 20);
        if (!isset($nasa_opt['disable-quickview']) || !$nasa_opt['disable-quickview']) {
            add_action('nasa_show_buttons_loop', 'elessi_quickview_in_list', 40);
        }
        add_action('nasa_show_buttons_loop', 'elessi_bundle_in_list', 60, 1);
        
        // Nasa ADD BUTTON BUY NOW
        add_action('woocommerce_after_add_to_cart_button', 'elessi_add_buy_now_btn');
        
        // Nasa Add Custom fields
        add_action('woocommerce_after_add_to_cart_button', 'elessi_add_custom_field_detail_product', 25);
        
        // nasa_top_sidebar_shop
        add_action('nasa_top_sidebar_shop', 'elessi_top_sidebar_shop', 10, 1);
        add_action('nasa_sidebar_shop', 'elessi_side_sidebar_shop', 10 , 1);
        
        // For Product content
        add_action('nasa_get_content_products', 'elessi_get_content_products', 10, 1);
        add_action('woocommerce_before_shop_loop_item_title', 'elessi_loop_product_content_thumbnail', 10);
        add_action('woocommerce_before_shop_loop_item_title', 'elessi_loop_product_content_btns', 15);
        add_action('woocommerce_before_shop_loop_item_title', 'elessi_gift_featured', 20);
        
        /**
         * Sale flash
         */
        add_action('woocommerce_before_shop_loop_item_title', 'elessi_add_custom_sale_flash', 11);
        add_action('woocommerce_before_single_product_summary', 'elessi_add_custom_sale_flash', 11);
        
        add_action('woocommerce_shop_loop_item_title', 'elessi_loop_product_cats', 5);
        add_action('woocommerce_shop_loop_item_title', 'elessi_loop_product_content_title', 10);
        
        add_action('woocommerce_after_shop_loop_item_title', 'elessi_loop_product_price', 10);
        add_action('woocommerce_after_shop_loop_item_title', 'elessi_loop_product_description', 15);
        
        /**
         * Add to wishlist in detail
         */
        add_action('product_video_btn', 'elessi_add_wishlist_in_detail', 31);
        
        add_filter('woocommerce_checkout_coupon_message', 'elessi_wrap_coupon_toggle');
        
        // for woo 3.3
        if(version_compare(wc()->version, '3.3.0', ">=")) {
            if(!isset($nasa_opt['show_uncategorized']) || !$nasa_opt['show_uncategorized']) {
                add_filter('woocommerce_product_subcategories_args', 'elessi_hide_uncategorized');
            }
        }
        
        /**
         * Share icon in Detail
         */
        add_action('woocommerce_share', 'elessi_before_woocommerce_share', 5);
        add_action('woocommerce_share', 'elessi_woocommerce_share', 10);
        add_action('woocommerce_share', 'elessi_after_woocommerce_share', 15);
        
        /**
         * Add src image large for variation
         */
        add_filter('woocommerce_available_variation', 'elessi_src_large_image_single_product');
        
        /**
         * Add class Sub Categories
         */
        add_filter('product_cat_class', 'elessi_add_class_sub_categories');
        
        /**
         * Filter redirect checkout buy now
         */
        add_filter('woocommerce_add_to_cart_redirect', 'elessi_buy_now_to_checkout');
        
        /**
         * Filter Single Stock
         */
        if(!isset($nasa_opt['enable_progess_stock']) || $nasa_opt['enable_progess_stock']) {
            add_filter('woocommerce_get_stock_html', 'elessi_single_stock', 10, 2);
        }
        
        /**
         * Disable redirect Search one product to single product
         */
        add_filter('woocommerce_redirect_single_search_result', '__return_false');
        
        /**
         * Support Yith WooCommerce Product Add ons in Quick view
         */
        if(class_exists('YITH_WAPO')) {
            $yith_wapo = YITH_WAPO::instance();
            $yith_wapo_frontend = $yith_wapo->frontend;
            add_action('woocommerce_single_product_lightbox_summary', array($yith_wapo_frontend, 'check_variable_product'));
        }
    }
endif;
/* ========================================================================== */
/* END Remove - Add action, filter WooCommerce ============================== */
/* ========================================================================== */

/**
 * Single Product stock
 */
if(!function_exists('elessi_single_stock')) :
    function elessi_single_stock($html, $product) {
        if($html == '' || !$product) {
            return $html;
        }
        
        $productId = $product->get_id();
        $productId = $product->get_id();
        $type = $product->get_type();
        $stock = get_post_meta($productId, '_stock', true);
        
        if(!$stock && $type == 'variation') {
            global $product;
            $productId = $product->get_id();
            $stock = get_post_meta($productId, '_stock', true);
        }
        
        if(!$stock) {
            return $html;
        }
        
        $total_sales = get_post_meta($productId, 'total_sales', true);
        $stock_sold = $total_sales ? round($total_sales) : 0;
        $stock_available = $stock ? round($stock) : 0;
        $percentage = $stock_available > 0 ? round($stock_sold/($stock_available + $stock_sold) * 100) : 0;
        
        $html = '<div class="stock nasa-single-product-stock">';
        $html .= '<span class="stock-sold">';
        $html .= sprintf(esc_html__('HURRY! ONLY %s LEFT IN STOCK.', 'elessi-theme'), $stock_available);
        $html .= '</span>';
        $html .= '<div class="nasa-product-stock-progress">';
        $html .= '<span class="nasa-product-stock-progress-bar" style="width:' . $percentage . '%"></span>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
endif;

/**
 * Buy Now button
 */
if(!function_exists('elessi_add_buy_now_btn')) :
    function elessi_add_buy_now_btn() {
        global $nasa_opt;
        
        if ((isset($nasa_opt['disable-cart']) && $nasa_opt['disable-cart']) ||
            (isset($nasa_opt['enable_buy_now']) && !$nasa_opt['enable_buy_now'])) {
            return;
        }
        
        echo '<input type="hidden" name="nasa_buy_now" value="0" />';
        echo '<button class="nasa-buy-now margin-top-15">' . esc_html__('BUY NOW', 'elessi-theme') . '</button>';
    }
endif;

/**
 * Redirect to Checkout page after click buy now
 */
if(!function_exists('elessi_buy_now_to_checkout')) :
    function elessi_buy_now_to_checkout($redirect_url) {
        if (isset($_REQUEST['nasa_buy_now']) && $_REQUEST['nasa_buy_now'] === '1') {
            $redirect_url = wc_get_checkout_url();
        }

        return $redirect_url;
    }
endif;

/**
 * Add class Sub Categories
 */
if(!function_exists('elessi_add_class_sub_categories')) :
    function elessi_add_class_sub_categories($classes) {
        $classes[] = 'product-warp-item';
        return $classes;
    }
endif;

/**
 * Override hover effect animated product
 */
add_action('wp_head', 'elessi_effect_animated_products');
if(!function_exists('elessi_effect_animated_products')) :
    function elessi_effect_animated_products() {
        if(!NASA_WOO_ACTIVED) {
            return;
        }

        $is_product = is_product();
        $is_product_cat = is_product_category();

        if($is_product_cat || $is_product) {
            global $wp_query, $nasa_root_term_id;
            $effect_product = '';

            /**
             * Check Single product
             */
            if($is_product) {
                if(!$nasa_root_term_id) {
                    $product_cats = get_the_terms($wp_query->get_queried_object_id(), 'product_cat');
                    if($product_cats) {
                        foreach ($product_cats as $cat) {
                            $term_id = $cat->term_id;
                            break;
                        }
                    }
                } else {
                    $term_id = $nasa_root_term_id;
                }
            }

            /**
             * Check Category product
             */
            elseif($is_product_cat) {
                $query_obj = $wp_query->get_queried_object();
                $term_id = isset($query_obj->term_id) ? $query_obj->term_id : false;
            }

            if($term_id) {
                $effect_product = get_term_meta($term_id, 'cat_effect_hover', true);

                if(!$effect_product) {
                    if($nasa_root_term_id) {
                        $term_id = $nasa_root_term_id;
                    } else {
                        $ancestors = get_ancestors($term_id, 'product_cat');
                        $term_id = $ancestors ? end($ancestors) : 0;
                        $GLOBALS['nasa_root_term_id'] = $term_id;
                    }

                    if($term_id) {
                        $effect_product = get_term_meta($term_id, 'cat_effect_hover', true);
                    }
                }

                if($effect_product) {
                    if($effect_product == 'no') {
                        $GLOBALS['nasa_animated_products'] = '';
                    }
                    else {
                        $GLOBALS['nasa_animated_products'] = $effect_product;
                    }
                }
            }
        }
    }
endif;

/**
 * Deal time in Single product page
 */
if(!function_exists('elessi_deal_time_single')) :
    function elessi_deal_time_single() {
        global $product;
        
        if($product->get_stock_status() == 'outofstock') {
            return;
        }
        
        $product_type = $product->get_type();
        
        // For variation of Variation product
        if($product_type == 'variable') {
            echo '<div class="nasa-detail-product-deal-countdown nasa-product-variation-countdown"></div>';
            return;
        }
        
        if($product_type != 'simple') {
            return;
        }
        
        $productId = $product->get_id();

        $time_from = get_post_meta($productId, '_sale_price_dates_from', true);
        $time_to = get_post_meta($productId, '_sale_price_dates_to', true);
        $time_sale = ((int) $time_to < NASA_TIME_NOW || (int) $time_from > NASA_TIME_NOW) ? false : (int) $time_to;
        if (!$time_sale) {
            return;
        }
        
        echo '<div class="nasa-detail-product-deal-countdown">';
        echo elessi_time_sale($time_sale);
        echo '</div>';
    }
endif;

/**
 * Deal time in Quick view product
 */
if(!function_exists('elessi_deal_time_quickview')) :
    function elessi_deal_time_quickview() {
        global $product;
        
        if($product->get_stock_status() == 'outofstock') {
            return;
        }
        
        $product_type = $product->get_type();
        
        // For variation of Variation product
        if($product_type == 'variable') {
            echo '<div class="nasa-quickview-product-deal-countdown nasa-product-variation-countdown"></div>';
            return;
        }
        
        if($product_type != 'simple') {
            return;
        }
        
        $productId = $product->get_id();

        $time_from = get_post_meta($productId, '_sale_price_dates_from', true);
        $time_to = get_post_meta($productId, '_sale_price_dates_to', true);
        $time_sale = ((int) $time_to < NASA_TIME_NOW || (int) $time_from > NASA_TIME_NOW) ? false : (int) $time_to;
        if (!$time_sale) {
            return;
        }
        
        echo '<div class="nasa-quickview-product-deal-countdown">';
        echo elessi_time_sale($time_sale);
        echo '</div>';
    }
endif;

if(!function_exists('elessi_src_large_image_single_product')) :
    function elessi_src_large_image_single_product($variation) {
        if(!isset($variation['image_single_page'])) {
            $image = wp_get_attachment_image_src($variation['image_id'], 'shop_single');
            $variation['image_single_page'] = isset($image[0]) ? $image[0] : '';
        }
        
        return $variation;
    }
endif;

if(!function_exists('elessi_result_count')) :
    function elessi_result_count() {
        if (! wc_get_loop_prop('is_paginated') || !woocommerce_products_will_display()) {
            return;
        }
        
        $total = wc_get_loop_prop('total');
        $per_page = wc_get_loop_prop('per_page');
        
        echo '<p class="woocommerce-result-count">';
        if ( $total <= $per_page || -1 === $per_page ) {
            printf(_n('Showing the single result', 'Showing all %d results', $total, 'elessi-theme'), $total);
	} else {
            $current = wc_get_loop_prop('current_page');
            $showed = $per_page * $current;
            if($showed > $total) {
                $showed = $total;
            }
            
            printf(_n('Showing the single result', 'Showing %d results', $total, 'elessi-theme' ), $showed);
	}
        echo '</p>';
    }
endif;

// **********************************************************************//
// ! Tiny account
// **********************************************************************//
if (!function_exists('elessi_tiny_account')) {

    function elessi_tiny_account($icon = false, $user = false, $redirect = false) {
        global $nasa_opt;
        if(isset($nasa_opt['hide_tini_menu_acc']) && $nasa_opt['hide_tini_menu_acc']) {
            return '';
        }
        
        $login_url = '#';
        $register_url = '#';
        $profile_url = '#';
        
        /* Active woocommerce */
        if (NASA_WOO_ACTIVED) {
            $myaccount_page_id = get_option('woocommerce_myaccount_page_id');
            if ($myaccount_page_id) {
                $login_url = get_permalink($myaccount_page_id);
                $register_url = $login_url;
                $profile_url = $login_url;
            }
        } else {
            $login_url = wp_login_url();
            $register_url = wp_registration_url();
            $profile_url = admin_url('profile.php');
        }

        $result = '<ul class="nasa-menus-account">';
        if (!NASA_CORE_USER_LOGIGED && !$user) {
            global $nasa_opt;
            $login_ajax = (!isset($nasa_opt['login_ajax']) || $nasa_opt['login_ajax'] == 1) ? '1' : '0';
            $span = $icon ? '<span class="pe7-icon pe-7s-user"></span>' : '';
            $result .= '<li class="menu-item color"><a class="nasa-login-register-ajax" data-enable="' . $login_ajax . '" href="' . esc_url($login_url) . '" title="' . esc_attr__('Register or sign in', 'elessi-theme') . '">' . $span . '<span class="nasa-login-title">' . esc_html__('Register or sign in', 'elessi-theme') . '</span></a></li>';
        } else {
            if(!$redirect) {
                global $wp;
                $redirect = home_url(add_query_arg(array(), $wp->request));
            }
            $logout_url = wp_logout_url($redirect);
        
            $span1 = $icon ? '<span class="pe7-icon pe-7s-user"></span>' : '';
            $span2 = $icon ? '<span class="pe7-icon pe-7s-back"></span>' : '';
            $result .= 
                '<li class="menu-item"><a href="' . esc_url($profile_url) . '" title="' . esc_attr__('My Account', 'elessi-theme') . '">' . $span1 . esc_html__('My Account', 'elessi-theme') . '</a></li>' .
                '<li class="menu-item"><a class="nav-top-link" href="' . esc_url($logout_url) . '" title="' . esc_attr__('Logout', 'elessi-theme') . '">' . $span2 . esc_html__('Logout', 'elessi-theme') . '</a></li>';
        }
        
        $result .= '</ul>';
        
        return apply_filters('nasa_tiny_account_ajax', $result);
    }

}

// **********************************************************************//
// Mini cart icon *******************************************************//
// **********************************************************************//
if (!function_exists('elessi_mini_cart')) {

    function elessi_mini_cart($show = true) {
        global $woocommerce, $nasa_opt, $nasa_mini_cart;
        
        if (!$woocommerce || (isset($nasa_opt['disable-cart']) && $nasa_opt['disable-cart'])) {
            return;
        }
        
        if (!$nasa_mini_cart) {
            $slClass = $show ? '' : ' hidden-tag';
            
            $slClass .= $woocommerce->cart->cart_contents_count == 0 ? ' nasa-product-empty' : '';
            $icon_number = isset($nasa_opt['mini-cart-icon']) ? $nasa_opt['mini-cart-icon'] : '1';
            $nasaSl = $woocommerce->cart->cart_contents_count > 9 ? '9+' : $woocommerce->cart->cart_contents_count;

            switch ($icon_number) {
                case '2':
                    $icon_class = 'icon-nasa-cart-2';
                    break;
                case '3':
                    $icon_class = 'icon-nasa-cart-4';
                    break;
                case '4':
                    $icon_class = 'pe-7s-cart';
                    break;
                case '5':
                    $icon_class = 'fa fa-shopping-cart';
                    break;
                case '6':
                    $icon_class = 'fa fa-shopping-bag';
                    break;
                case '7':
                    $icon_class = 'fa fa-shopping-basket';
                    break;
                case '1':
                default:
                    $icon_class = 'icon-nasa-cart-3';
                    break;
            }
            
            $GLOBALS['nasa_mini_cart'] = 
            '<div class="mini-cart cart-inner mini-cart-type-full inline-block">' .
                '<a href="javascript:void(0);" class="cart-link" title="' . esc_attr__('Cart', 'elessi-theme') . '">' .
                    '<span class="nasa-icon cart-icon icon ' . $icon_class . '"></span>' .
                    '<span class="products-number' . $slClass . '">' .
                        '<span class="nasa-sl">' .
                            apply_filters('nasa_mini_cart_total_items', $nasaSl) .
                        '</span>' .
                        '<span class="hidden-tag nasa-sl-label last">' . esc_html__('Items', 'elessi-theme') . '</span>' .
                    '</span>' .
                '</a>' .
            '</div>';
        }
        
        return $nasa_mini_cart ? apply_filters('nasa_mini_cart', $nasa_mini_cart) : '';
    }

}

// *************************************************************************//
// ! Add to cart dropdown - Refresh mini cart content. Input from header type
// *************************************************************************//
add_filter('woocommerce_add_to_cart_fragments', 'elessi_add_to_cart_refresh');
if (!function_exists('elessi_add_to_cart_refresh')) :
    function elessi_add_to_cart_refresh($fragments) {

        $fragments['.cart-inner'] = elessi_mini_cart();
        $fragments['div.widget_shopping_cart_content'] = elessi_mini_cart_sidebar(true);

        return $fragments;
    }
endif;

// **********************************************************************//
// ! Mini cart sidebar
// **********************************************************************//
if (!function_exists('elessi_mini_cart_sidebar')) :

    function elessi_mini_cart_sidebar($str = false) {
        global $woocommerce, $nasa_opt;
        
        $empty = '<p class="empty"><i class="nasa-empty-icon icon-nasa-cart-2"></i>' . esc_html__('No products in the cart.', 'elessi-theme') . '<a href="javascript:void(0);" class="button nasa-sidebar-return-shop">' . esc_html__('RETURN TO SHOP', 'elessi-theme') . '</a></p>';
        
        if (!$woocommerce || (isset($nasa_opt['disable-cart']) && $nasa_opt['disable-cart'])){
            if ($str) {
                return $empty;
            }
            
            echo '<div class="empty hidden-tag">' . $empty . '</div>';
            
            return;
        }
        
        // Check cart items are valid.
        do_action('woocommerce_check_cart_items');
        wc()->cart->calculate_totals();
        
        ob_start();
        $file = ELESSI_CHILD_PATH . '/includes/nasa-sidebar-cart.php';
        include is_file($file) ? $file : ELESSI_THEME_PATH . '/includes/nasa-sidebar-cart.php';
        $content = ob_get_clean();
        
        if ($str) {
            return $content;
        }
        
        echo '<div class="empty hidden-tag">' . $empty . '</div>';
        echo $content;
    }

endif;

// **********************************************************************//
// ! Mini wishlist sidebar
// **********************************************************************//
if (!function_exists('elessi_mini_wishlist_sidebar')) {

    function elessi_mini_wishlist_sidebar($return = false) {
        global $woocommerce;
        if (!$woocommerce){
            return '';
        }
        
        ob_start();
        $file = ELESSI_CHILD_PATH . '/includes/nasa-sidebar-wishlist.php';
        include is_file($file) ? $file : ELESSI_THEME_PATH . '/includes/nasa-sidebar-wishlist.php';
        $content = ob_get_clean();

        if ($return) {
            return $content;
        }
        
        echo $content;
    }

}

if(!function_exists('elessi_add_to_cart_in_wishlist')) :
    function elessi_add_to_cart_in_wishlist() {
        global $product, $nasa_opt;

        if (isset($nasa_opt['disable-cart']) && $nasa_opt['disable-cart']) {
            return '';
        }

        $title = $product->add_to_cart_text();
        $product_type = $product->get_type();
        $productId = $product->get_id();
        $enable_button_ajax = false;
        if($product->is_in_stock() && $product->is_purchasable()) {
            if($product_type == 'simple' || ($product_type == NASA_COMBO_TYPE && $product->all_items_in_stock())) {
                $url = 'javascript:void(0);';
                $enable_button_ajax = true;
            } else {
                /**
                 * Bundle product
                 */
                if($product_type == 'woosb') {
                    $url = '?add-to-cart=' . $productId;
                    if(NASA_CORE_USER_LOGIGED && get_option('yith_wcwl_remove_after_add_to_cart') == 'yes') {
                        $url .= '&remove_from_wishlist_after_add_to_cart=' . $productId;
                    }
                }
                /**
                 * Normal product
                 */
                else {
                    $url = esc_url($product->add_to_cart_url());
                }
            }
        }
        else {
            return '';
        }

        return apply_filters(
            'woocommerce_loop_add_to_cart_link',
            sprintf(
                '<a href="%s" rel="nofollow" data-quantity="1" data-product_id="%s" data-product_sku="%s" class="button-in-wishlist small btn-from-wishlist %s product_type_%s add-to-cart-grid" data-type="%s" title="%s">' .
                    '<span class="cart-icon nasa-icon icon-nasa-cart-3"></span>' .
                    '<span class="add_to_cart_text">%s</span>' .
                '</a><div class="quick-view nasa-view-from-wishlist hidden-tag" data-prod="%s" data-from_wishlist="1">%s</div>',
                $url, //link
                $productId, //product id
                esc_attr($product->get_sku()), //product sku
                $enable_button_ajax ? 'nasa_add_to_cart_from_wishlist' : '', //class name
                esc_attr($product_type), esc_attr($product_type), //product type
                $title,
                $title,
                $productId,
                esc_html__('Quick View', 'elessi-theme')
            ),
            $product
        );
    }
endif;

/**
 * Custom icon cart in grid
 */
add_filter('woocommerce_loop_add_to_cart_link', 'elessi_custom_icon_add_to_cart');
if (!function_exists('elessi_custom_icon_add_to_cart')) :
    function elessi_custom_icon_add_to_cart($addToCart) {
        global $nasa_opt;
        
        $icon_number = isset($nasa_opt['cart-icon-grid']) ? $nasa_opt['cart-icon-grid'] : '1';
        switch ($icon_number) {
            case '2':
                $icon_class = 'icon-nasa-cart-3';
                break;
            case '3':
                $icon_class = 'icon-nasa-cart-2';
                break;
            case '4':
                $icon_class = 'icon-nasa-cart-4';
                break;
            case '5':
                $icon_class = 'pe-7s-cart';
                break;
            case '6':
                $icon_class = 'fa fa-shopping-cart';
                break;
            case '7':
                $icon_class = 'fa fa-shopping-bag';
                break;
            case '8':
                $icon_class = 'fa fa-shopping-basket';
                break;
            case '1':
            default:
                return $addToCart;
        }
        
        return str_replace('fa fa-plus', $icon_class, $addToCart);
    }
endif;

// **********************************************************************//
// ! Add to cart button
// **********************************************************************//
if (!function_exists('elessi_add_to_cart_btn')):
    function elessi_add_to_cart_btn($echo = true, $customClass = '') {
        global $product, $nasa_opt;

        if (isset($nasa_opt['disable-cart']) && $nasa_opt['disable-cart']) {
            return '';
        }

        $productId = $product->get_id();
        $product_type = $product->get_type();
        $productVariable = null;
        $class_btn = $data_type = '';
        
        if($product->is_purchasable() && $product->is_in_stock()) {
            if($product_type == 'simple') {
                $class_btn .= 'yes' === get_option('woocommerce_enable_ajax_add_to_cart') ? 'add_to_cart_button ajax_add_to_cart' : '';
            }
            
            elseif ($product_type == NASA_COMBO_TYPE) {
                $class_btn .= 'yes' === get_option('woocommerce_enable_ajax_add_to_cart') ? 'add_to_cart_button nasa_bundle_add_to_cart' : 'add_to_cart_button';
                $data_type = ' data-type="' . esc_attr($product_type) . '"';
            }
            
            elseif ($product_type == 'variation') {
                $product_type = 'variable';
                $parent_id = wp_get_post_parent_id($productId);
                $productVariable = wc_get_product($parent_id);
            }
        }
        
        if ('yes' !== get_option('woocommerce_enable_ajax_add_to_cart')) {
            $class_btn .= ' nasa-disable-ajax';
        }
        
        $class_btn .= $customClass != '' ? ' ' . $customClass : $customClass;
        $result = '';
        
        // add to cart text;
        $title = !$productVariable ? $product->add_to_cart_text() : $productVariable->add_to_cart_text();
        $result .= apply_filters(
            'woocommerce_loop_add_to_cart_link',
            sprintf(
                '<div class="add-to-cart-btn btn-link add-to-cart-icon">' .
                    '<a href="%s" rel="nofollow" data-quantity="1" data-product_id="%s" data-product_sku="%s" class="%s product_type_%s add-to-cart-grid" title="%s"' . $data_type . '>' .
                        '<span class="add_to_cart_text">%s</span>' .
                        '<span class="cart-icon fa fa-plus"></span>' .
                    '</a>' .
                '</div>',
                esc_url($product->add_to_cart_url()), //link
                esc_attr($productId), //product id
                esc_attr($product->get_sku()), //product sku
                esc_attr($class_btn), //class name
                esc_attr($product_type), //product type
                esc_attr($title),
                $title
            ),
            $product
        );

        if (!$echo) {
            return $result;
        }
        
        echo $result;
    }
endif;

// Product group button
if (!function_exists('elessi_product_group_button')):
    function elessi_product_group_button($combo_show_type = 'popup') {
        ob_start();
        $file = ELESSI_CHILD_PATH . '/includes/nasa-product-buttons.php';
        include is_file($file) ? $file : ELESSI_THEME_PATH . '/includes/nasa-product-buttons.php';

        return ob_get_clean();
    }
endif;

// **********************************************************************//
// ! Wishlist link
// **********************************************************************//
if (!function_exists('elessi_tini_wishlist')):
    function elessi_tini_wishlist($icon = false) {
        if (!NASA_WOO_ACTIVED || !NASA_WISHLIST_ENABLE) {
            return;
        }

        $tini_wishlist = '';
        $wishlist_page_id = get_option('yith_wcwl_wishlist_page_id');
        if (function_exists('icl_object_id')) {
            $wishlist_page_id = icl_object_id($wishlist_page_id, 'page', true);
        }
        $wishlist_page = get_permalink($wishlist_page_id);

        $span = $icon ? '<span class="icon-nasa-like"></span>' : '';
        $tini_wishlist .= '<a href="' . esc_url($wishlist_page) . '" title="' . esc_attr__('Wishlist', 'elessi-theme') . '">' . $span . esc_html__('Wishlist', 'elessi-theme') . '</a>';

        return $tini_wishlist;
    }
endif;

// **********************************************************************//
// ! Wishlist link
// **********************************************************************//
if (!function_exists('elessi_icon_wishlist')):
    function elessi_icon_wishlist() {
        if (!NASA_WOO_ACTIVED || !NASA_WISHLIST_ENABLE) {
            return;
        }

        global $nasa_icon_wishlist;
        if(!$nasa_icon_wishlist) {
            $show = defined('NASA_PLG_CACHE_ACTIVE') && NASA_PLG_CACHE_ACTIVE ? false : true;
            $count = elessi_get_count_wishlist($show);
            
            $href = '';
            $class = 'wishlist-link';
            if(defined('YITH_WCWL_PREMIUM')) {
                $class .= ' wishlist-link-premium';
                $wishlist_page_id = get_option('yith_wcwl_wishlist_page_id');
                if (function_exists('icl_object_id') && $wishlist_page_id) {
                    $wishlist_page_id = icl_object_id($wishlist_page_id, 'page', true);
                }
                
                $href = $wishlist_page_id ? get_permalink($wishlist_page_id) : home_url('/');
            }
            
            $GLOBALS['nasa_icon_wishlist'] = 
            '<a class="' . $class . '" href="' . ($href != '' ? esc_url($href) : 'javascript:void(0);') . '" title="' . esc_attr__('Wishlist', 'elessi-theme') . '">' .
                '<i class="nasa-icon icon-nasa-like"></i>' .
                $count .
            '</a>';
        }

        return $nasa_icon_wishlist ? $nasa_icon_wishlist : '';
    }
endif;

if (!function_exists('elessi_get_count_wishlist')):
    function elessi_get_count_wishlist($show = true) {
        if (!NASA_WOO_ACTIVED || !NASA_WISHLIST_ENABLE) {
            return '';
        }
        
        $count = yith_wcwl_count_products();
        $hasEmpty = (int) $count == 0 ? ' nasa-product-empty' : '';
        $sl = $show ? '' : ' hidden-tag';
        $nasaSl = (int) $count > 9 ? '9+' : (int) $count;
        
        return '<span class="nasa-wishlist-count wishlist-number' . $hasEmpty . '">' .
                    '<span class="nasa-text hidden-tag">' . esc_html__('Wishlist', 'elessi-theme') . '</span>' .
                    '<span class="nasa-sl' . $sl . '">' . apply_filters('nasa_mini_wishlist_total_items', $nasaSl) . '</span>' .
                '</span>';
    }
endif;

// **********************************************************************//
// ! Compare link
// **********************************************************************//
if (!function_exists('elessi_icon_compare')):

    function elessi_icon_compare() {
        if (!NASA_WOO_ACTIVED || !defined('YITH_WOOCOMPARE')) {
            return;
        }

        global $nasa_icon_compare, $nasa_opt;
        if(!$nasa_icon_compare) {
            global $yith_woocompare;
            
            if(!isset($nasa_opt['nasa-product-compare']) || $nasa_opt['nasa-product-compare']) {
                $view_href = isset($nasa_opt['nasa-page-view-compage']) && (int) $nasa_opt['nasa-page-view-compage'] ? get_permalink((int) $nasa_opt['nasa-page-view-compage']) : home_url('/');
                $class = 'nasa-show-compare';
            } else {
                $view_href = add_query_arg(array('iframe' => 'true'), $yith_woocompare->obj->view_table_url());
                $class = 'compare';
            }
            
            $show = defined('NASA_PLG_CACHE_ACTIVE') && NASA_PLG_CACHE_ACTIVE ? false : true;
            $count = elessi_get_count_compare($show);
            
            $GLOBALS['nasa_icon_compare'] = 
            '<span class="yith-woocompare-widget">' .
                '<a href="' . esc_url($view_href) . '" title="' . esc_attr__('Compare', 'elessi-theme') . '" class="' . esc_attr($class) . '">' .
                    '<i class="nasa-icon icon-nasa-refresh"></i>' .
                    $count .
                '</a>' .
            '</span>';
        }
        
        return $nasa_icon_compare ? $nasa_icon_compare : '';
    }

endif;

if (!function_exists('elessi_get_count_compare')):
    function elessi_get_count_compare($show = true) {
        if (!NASA_WOO_ACTIVED || !defined('YITH_WOOCOMPARE')) {
            return '';
        }
        
        global $yith_woocompare;
        
        $count = count($yith_woocompare->obj->products_list);
        $hasEmpty = (int) $count == 0 ? ' nasa-product-empty' : '';
        
        $sl = $show ? '' : ' hidden-tag';
        $nasaSl = (int) $count > 9 ? '9+' : (int) $count;
        
        return '<span class="nasa-compare-count compare-number' . $hasEmpty . '">' .
                    '<span class="nasa-text hidden-tag">' . esc_html__('Compare ', 'elessi-theme') . ' </span>' .
                    '<span class="nasa-sl' . $sl . '">' . apply_filters('nasa_mini_compare_total_items', $nasaSl) . '</span>' .
                '</span>';
    }
endif;

if (!function_exists('elessi_get_cat_header')):

    function elessi_get_cat_header($catId = null) {
        global $nasa_opt;
        if (isset($nasa_opt['enable_cat_header']) && $nasa_opt['enable_cat_header'] != '1') {
            return '';
        }

        $content = '<div class="cat-header nasa-cat-header padding-top-20">';
        $do_content = '';
        
        if ((int) $catId > 0) {
            $shortcode = function_exists('get_term_meta') ? get_term_meta($catId, 'cat_header', false) : get_woocommerce_term_meta($catId, 'cat_header', false);
            $do_content = isset($shortcode[0]) ? do_shortcode($shortcode[0]) : '';
        }

        if (trim($do_content) === '') {
            if (isset($nasa_opt['cat_header']) && $nasa_opt['cat_header'] != '') {
                $do_content = do_shortcode($nasa_opt['cat_header']);
            }
        }

        if (trim($do_content) === '') {
            return '';
        }

        $content .= $do_content . '</div>';

        return $content;
    }

endif;

if (!function_exists('elessi_get_product_meta_value')):

    function elessi_get_product_meta_value($post_id, $field_id = null) {
        $meta_value = get_post_meta($post_id, 'wc_productdata_options', true);
        if (isset($meta_value[0]) && $field_id) {
            return isset($meta_value[0][$field_id]) ? $meta_value[0][$field_id] : '';
        }

        return isset($meta_value[0]) ? $meta_value[0] : $meta_value;
    }

endif;

add_action('nasa_search_by_cat', 'elessi_search_by_cat', 10, 1);
if (!function_exists('elessi_search_by_cat')):
    function elessi_search_by_cat($echo = true) {
        global $nasa_opt;
        
        $select = '';
        if(NASA_WOO_ACTIVED && (!isset($nasa_opt['search_by_cat']) || $nasa_opt['search_by_cat'] == 1)){
            $select .= '<select name="product_cat">';
            $select .= '<option value="">' . esc_html__('Categories', 'elessi-theme') . '</option>';
            
            $slug = get_query_var('product_cat');
            $nasa_catActive = $slug ? $slug : '';
            $nasa_terms = get_terms(apply_filters('woocommerce_product_attribute_terms', array(
                'taxonomy' => 'product_cat',
                'parent' => 0,
                'hide_empty' => false,
                'orderby' => 'name'
            )));
            
            if($nasa_terms) {
                foreach ($nasa_terms as $v) {
                    $select .= '<option data-term_id="' . esc_attr($v->term_id) . '" value="' . esc_attr($v->slug) . '"' . (($nasa_catActive == $v->slug) ? ' selected' : '') . '>' . esc_attr($v->name) . '</option>';
                    elessi_get_child($v, $select, $nasa_catActive);
                }
            }
            
            $select .= '</select>';
        }
        
        if(!$echo){
            return $select;
        }
        
        echo ($select);
    }

endif;

if (!function_exists('elessi_get_child')):
    function elessi_get_child($obj, &$select, $nasa_catActive, $pad = '') {
        $childs = get_terms(apply_filters('woocommerce_product_attribute_terms', array(
            'taxonomy' => 'product_cat',
            'parent' => $obj->term_id,
            'hide_empty' => false,
            'orderby' => 'name'
        )));

        if(!empty($childs)){
            $pad .= '&nbsp;&nbsp;&nbsp;';
            foreach ($childs as $v){
                $select .= '<option data-term_id="' . esc_attr($v->term_id) . '" value="' . esc_attr($v->slug) . '"' . (($nasa_catActive == $v->slug) ? ' selected' : '') . '>' . $pad . esc_attr($v->name) . '</option>';
                elessi_get_child($v, $select, $nasa_catActive, $pad);
            }
        }
    }
endif;

// Nasa root categories in Shop Top bar
if (!function_exists('elessi_get_root_categories')):
    
    function elessi_get_root_categories() {
        global $nasa_opt;
        
        $content = '';
        
        if(isset($nasa_opt['top_filter_rootcat']) && !$nasa_opt['top_filter_rootcat']) {
            echo ($content);
            return;
        }
        
        if (!is_post_type_archive('product') && !is_tax(get_object_taxonomies('product'))) {
            echo ($content);
            return;
        }
        
        if(NASA_WOO_ACTIVED){
            $nasa_terms = get_terms(apply_filters('woocommerce_product_attribute_terms', array(
                'taxonomy' => 'product_cat',
                'parent' => 0,
                'hide_empty' => false,
                'orderby' => 'name'
            )));
            
            if($nasa_terms) {
                $slug = get_query_var('product_cat');
                $nasa_catActive = $slug ? $slug : '';
                $content .= '<div class="nasa-transparent-topbar"></div>';
                $content .= '<div class="nasa-root-cat-topbar-warp hidden-tag"><ul class="nasa-root-cat product-categories">';
                $content .= '<li class="nasa_odd"><span class="nasa-root-cat-header">' . esc_html__('CATEGORIES', 'elessi-theme'). '</span></li>';
                $li_class = 'nasa_even';
                foreach ($nasa_terms as $v) {
                    $class_active = $nasa_catActive == $v->slug ? ' nasa-active' : '';
                    $content .= '<li class="cat-item cat-item-' . esc_attr($v->term_id) . ' cat-item-accessories root-item ' . $li_class . '">';
                    $content .= '<a href="' . esc_url(get_term_link($v)) . '" data-id="' . esc_attr($v->term_id) . '" class="nasa-filter-by-cat' . $class_active . '" title="' . esc_attr($v->name) . '" data-taxonomy="product_cat">' . esc_attr($v->name) . '</a>';
                    $content .= '</li>';
                    $li_class = $li_class == 'nasa_even' ? 'nasa_odd' : 'nasa_even';
                }
                
                $content .= '</ul></div>';
            }
        }
        
        $icon = $content != '' ? '<div class="nasa-icon-cat-topbar"><a href="javascript:void(0);"><i class="pe-7s-menu"></i><span class="inline-block">' . esc_html__('BROWSE', 'elessi-theme') . '</span></a></div>' : '';
        $content = $icon . $content;
        
        echo ($content);
    }

endif;

// Nasa childs category in Shop Top bar
if (!function_exists('elessi_get_childs_category')):
    
    function elessi_get_childs_category($term = null, $instance = array()) {
        $content = '';
        
        if(NASA_WOO_ACTIVED){
            global $wp_query;
            
            $term = $term == null ? $wp_query->get_queried_object() : $term;
            $parent_id = is_numeric($term) ? $term : (isset($term->term_id) ? $term->term_id : 0);
            
            $nasa_terms = get_terms(apply_filters('woocommerce_product_attribute_terms', array(
                'taxonomy' => 'product_cat',
                'parent' => $parent_id,
                'hierarchical' => true,
                'hide_empty' => false,
                'orderby' => 'name'
            )));
            
            if (!$nasa_terms) {
                $term_root = get_ancestors($parent_id, 'product_cat');
                $term_parent = isset($term_root[0]) ? $term_root[0] : 0;
                $nasa_terms = get_terms(apply_filters('woocommerce_product_attribute_terms', array(
                    'taxonomy' => 'product_cat',
                    'parent' => $term_parent,
                    'hierarchical' => true,
                    'hide_empty' => false,
                    'orderby' => 'name'
                )));
            }
            
            if($nasa_terms) {
                $show = isset($instance['show_items']) ? (int) $instance['show_items'] : 0;
                $content .= '<ul class="nasa-children-cat product-categories nasa-product-child-cat-top-sidebar">';
                $items = 0;
                foreach ($nasa_terms as $v) {
                    $class_active = $parent_id == $v->term_id ? ' nasa-active' : '';
                    $class_li = ($show && $items >= $show) ? ' nasa-show-less' : '';
                    
                    $icon = '';
                    if (isset($instance['cat_' . $v->slug]) && trim($instance['cat_' . $v->slug]) != '') {
                        $icon = '<i class="' . $instance['cat_' . $v->slug] . '"></i>';
                        $icon .= '&nbsp;&nbsp;';
                    }
                    
                    $content .= '<li class="cat-item cat-item-' . esc_attr($v->term_id) . ' cat-item-accessories root-item' . $class_li . '">';
                    $content .= '<a href="' . esc_url(get_term_link($v)) . '" data-id="' . esc_attr($v->term_id) . '" class="nasa-filter-by-cat' . $class_active . '" title="' . esc_attr($v->name) . '" data-taxonomy="product_cat">';
                    $content .= '<div class="nasa-cat-warp">';
                    $content .= '<h5 class="nasa-cat-title">';
                    $content .= $icon . esc_attr($v->name);
                    $content .= '</h5>';
                    $content .= '</div>';
                    $content .= '</a>';
                    $content .= '</li>';
                    $items++;
                }
                
                if ($show && ($items > $show)) {
                    $content .= '<li class="nasa_show_manual"><a data-show="1" class="nasa-show" href="javascript:void(0);">' . esc_html__('+ Show more', 'elessi-theme') . '</a><a data-show="0" class="nasa-hidden" href="javascript:void(0);">' . esc_html__('- Show less', 'elessi-theme') . '</a></li>';
                }
                
                $content .= '</ul>';
            }
        }
        
        echo ($content);
    }

endif;

function elessi_category_thumbnail($category, $type = 'shop_thumbnail') {
    $small_thumbnail_size = apply_filters('single_product_small_thumbnail_size', $type);
    $thumbnail_id = function_exists('get_term_meta') ? get_term_meta($category->term_id, 'thumbnail_id', true) : get_woocommerce_term_meta($category->term_id, 'thumbnail_id', true);

    if ($thumbnail_id) {
        $image = wp_get_attachment_image_src($thumbnail_id, $small_thumbnail_size);
        $image = $image[0];
    } else {
        $image = wc_placeholder_img_src();
    }

    if ($image) {
        $image = str_replace(' ', '%20', $image);
        return '<img src="' . esc_url($image) . '" alt="' . esc_attr($category->name) . '" />';
    }
    
    return '';
}

// Login Or Register Form
add_action('nasa_login_register_form', 'elessi_login_register_form', 10, 1);
if(!function_exists('elessi_login_register_form')) :
    function elessi_login_register_form($prefix = false) {
        global $woocommerce, $nasa_opt;
        if(!$woocommerce) {
            return;
        }
        
        include ELESSI_THEME_PATH . '/includes/nasa-login-register-form.php';
    }
endif;

// Get term description
if(!function_exists('elessi_term_description')) :
    function elessi_term_description($term_id, $type_taxonomy) {
        if(!NASA_WOO_ACTIVED) {
            return '';
        }
        
        if((int) $term_id < 1) {
            $shop_page = get_post(wc_get_page_id('shop'));
            $desc = $shop_page ? wc_format_content($shop_page->post_content) : '';
        } else {
            $term = get_term($term_id, $type_taxonomy);
            $desc = isset($term->description) ? $term->description : '';
        }
        
        return trim($desc) != '' ? '<div class="page-description">' . do_shortcode($desc) . '</div>' : '';
    }
endif;

// get value custom field nasa-core
if(!function_exists('elessi_get_custom_field_value')) :
function elessi_get_custom_field_value($post_id, $field_id) {
    $meta_value = get_post_meta($post_id, 'wc_productdata_options', true);
    if ($meta_value) {
        $meta_value = $meta_value[0];
    }

    return isset($meta_value[$field_id]) ? $meta_value[$field_id] : '';
}
endif;

// Add action archive-product get content product.
if(!function_exists('elessi_get_content_products')) :
    function elessi_get_content_products($nasa_sidebar = 'top') {
        global $nasa_opt, $wp_query;

        $file = ELESSI_CHILD_PATH . '/includes/nasa-get-content-products.php';
        include is_file($file) ? $file : ELESSI_THEME_PATH . '/includes/nasa-get-content-products.php';
    }
endif;

// Number post_per_page shop/archive_product
add_filter('loop_shop_per_page', 'elessi_loop_shop_per_page', 20);
if(!function_exists('elessi_loop_shop_per_page')) :
    function elessi_loop_shop_per_page($post_per_page) {
        global $nasa_opt;
        return (isset($nasa_opt['products_pr_page']) && (int) $nasa_opt['products_pr_page']) ? (int) $nasa_opt['products_pr_page'] : get_option('posts_per_page');
    }
endif;

// Number relate products
add_filter('woocommerce_output_related_products_args', 'elessi_output_related_products_args');
if(!function_exists('elessi_output_related_products_args')) :
    function elessi_output_related_products_args($args) {
        global $nasa_opt;
        $args['posts_per_page'] = (isset($nasa_opt['release_product_number']) && (int) $nasa_opt['release_product_number']) ? (int) $nasa_opt['release_product_number'] : 12;
        return $args;
    }
endif;

// Compare list in bot site
add_action('nasa_show_mini_compare', 'elessi_show_mini_compare');
if(!function_exists('elessi_show_mini_compare')) :
    function elessi_show_mini_compare() {
        global $nasa_opt, $yith_woocompare;
        
        if(isset($nasa_opt['nasa-product-compare']) && !$nasa_opt['nasa-product-compare']) {
            echo '';
            return;
        }
        
        $nasa_compare = isset($yith_woocompare->obj) ? $yith_woocompare->obj : $yith_woocompare;
        if(!$nasa_compare) {
            echo '';
            return;
        }
        
        if(!isset($nasa_opt['nasa-page-view-compage']) || !(int) $nasa_opt['nasa-page-view-compage']) {
            $pages = get_pages(array(
                'meta_key' => '_wp_page_template',
                'meta_value' => 'page-view-compare.php'
            ));
            
            if($pages) {
                foreach ($pages as $page) {
                    $nasa_opt['nasa-page-view-compage'] = (int) $page->ID;
                    break;
                }
            }
        }
        
        $view_href = isset($nasa_opt['nasa-page-view-compage']) && (int) $nasa_opt['nasa-page-view-compage'] ? get_permalink((int) $nasa_opt['nasa-page-view-compage']) : home_url('/');
        
        $nasa_compare_list = $nasa_compare->get_products_list();
        $max_compare = isset($nasa_opt['max_compare']) ? (int) $nasa_opt['max_compare'] : 4;
        
        $file = ELESSI_CHILD_PATH . '/includes/nasa-mini-compare.php';
        include is_file($file) ? $file : ELESSI_THEME_PATH . '/includes/nasa-mini-compare.php';
    }
endif;

/**
 * Default page compare
 */
if(!function_exists('elessi_products_compare_content')) :
    function elessi_products_compare_content() {
        global $nasa_opt, $yith_woocompare;
        
        if(isset($nasa_opt['nasa-product-compare']) && !$nasa_opt['nasa-product-compare']) {
            return '';
        }
        
        $nasa_compare = isset($yith_woocompare->obj) ? $yith_woocompare->obj : $yith_woocompare;
        if(!$nasa_compare) {
            return '';
        }
        
        ob_start();
        $file = ELESSI_CHILD_PATH . '/includes/nasa-view-compare.php';
        include is_file($file) ? $file : ELESSI_THEME_PATH . '/includes/nasa-view-compare.php';
        
        return ob_get_clean();
    }
endif;

/* NEXT NAV ON PRODUCT PAGES */
add_action('next_prev_product', 'elessi_next_product');
if(!function_exists('elessi_next_product')) :
    function elessi_next_product() {
        $next_post = get_next_post(true, '', 'product_cat');
        if (is_a($next_post, 'WP_Post')) {
            $product_obj = new WC_Product($next_post->ID);
            $title = get_the_title($next_post->ID);
            $link = get_the_permalink($next_post->ID);
            ?>
            <div class="next-product next-prev-buttons">
                <a href="<?php echo esc_url($link); ?>" rel="next" class="icon-next-prev pe-7s-angle-right next" title="<?php echo esc_attr($title); ?>"></a>
                <div class="dropdown-wrap">
                    <a title="<?php echo esc_attr($title); ?>" href="<?php echo esc_url($link); ?>">
                        <?php echo get_the_post_thumbnail($next_post->ID, apply_filters('single_product_small_thumbnail_size', 'shop_thumbnail')); ?>
                        <div>
                            <span class="product-name"><?php echo ($title); ?></span>
                            <span class="price"><?php echo ($product_obj->get_price_html()); ?></span>
                        </div>
                    </a>
                </div>
            </div>
            <?php
        }
    }
endif;

/* PRVE NAV ON PRODUCT PAGES */
add_action('next_prev_product', 'elessi_prev_product');
if(!function_exists('elessi_prev_product')) :
    function elessi_prev_product() {
        $prev_post = get_previous_post(true, '', 'product_cat');
        if (is_a($prev_post, 'WP_Post')) {
            $product_obj = new WC_Product($prev_post->ID);
            $title = get_the_title($prev_post->ID);
            $link = get_the_permalink($prev_post->ID);
            ?>
            <div class="prev-product next-prev-buttons">
                <a href="<?php echo esc_url($link); ?>" rel="prev" class="icon-next-prev pe-7s-angle-left prev" title="<?php echo esc_attr($title); ?>"></a>
                <div class="dropdown-wrap">
                    <a title="<?php echo esc_attr($title); ?>" href="<?php echo esc_url($link); ?>">
                        <?php echo get_the_post_thumbnail($prev_post->ID, apply_filters('single_product_small_thumbnail_size', 'shop_thumbnail')); ?>
                        <div>
                            <span class="product-name"><?php echo ($title); ?></span>
                            <span class="price"><?php echo ($product_obj->get_price_html()); ?></span>
                        </div>
                    </a>
                </div>
            </div>
            <?php
        }
    }
endif;

/* ==========================================================================
 * ADD VIDEO PLAY BUTTON ON PRODUCT DETAIL PAGE
 * ======================================================================= */
add_action('product_video_btn', 'elessi_product_video_btn_function', 1);
if (!function_exists('elessi_product_video_btn_function')) {

    function elessi_product_video_btn_function() {
        $id = get_the_ID();
        if ($video_link = elessi_get_custom_field_value($id, '_product_video_link')) {
            ?>
            <a class="product-video-popup tip-top" data-tip="<?php esc_attr_e('View video', 'elessi-theme'); ?>" href="<?php echo esc_url($video_link); ?>">
                <span class="pe-7s-play"></span>
                <span class="nasa-play-video-text"><?php esc_html_e('Play Video', 'elessi-theme'); ?></span>
            </a>

            <?php
            $height = '800';
            $width = '800';
            $iframe_scale = '100%';
            $custom_size = elessi_get_custom_field_value($id, '_product_video_size');
            if ($custom_size) {
                $split = explode("x", $custom_size);
                $height = $split[0];
                $width = $split[1];
                $iframe_scale = ($width / $height * 100) . '%';
            }
            $style = '.has-product-video .mfp-iframe-holder .mfp-content{max-width: ' . $width . 'px;}';
            $style .= '.has-product-video .mfp-iframe-scaler{padding-top: ' . $iframe_scale . ';}';
            wp_add_inline_style('product_detail_css_custom', $style);
        }
    }
}

/*
 * elessi add wishlist in list
 */
if(!function_exists('elessi_add_wishlist_in_list')) :
    function elessi_add_wishlist_in_list() {
        if(NASA_WISHLIST_ENABLE) {
            global $product, $yith_wcwl;
            if(!$yith_wcwl) {
                return;
            }
            $variation = false;
            $productId = $product->get_id();
            if($product->get_type() == 'variation') {
                $variation_product = $product;
                $productId = wp_get_post_parent_id($productId);
                $GLOBALS['product'] = wc_get_product($productId);
                $variation = true;
            }

            ?>
            <a href="javascript:void(0);" class="btn-wishlist btn-link wishlist-icon tip-top" data-prod="<?php echo (int) $productId; ?>" data-tip="<?php esc_attr_e('Wishlist', 'elessi-theme'); ?>" title="<?php esc_attr_e('Wishlist', 'elessi-theme'); ?>">
                <span class="nasa-icon icon-nasa-like"></span>
                <span class="hidden-tag nasa-icon-text no-added"><?php esc_html_e('Wishlist', 'elessi-theme'); ?></span>
            </a>
            <div class="add-to-link hidden-tag">
                <?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
            </div>

            <?php

            if($variation) {
                $GLOBALS['product'] = $variation_product;
            }
        }
    }
endif;

/*
 * elessi add wishlist in list
 */
if(!function_exists('elessi_add_wishlist_in_detail')) :
    function elessi_add_wishlist_in_detail() {
        echo '<div class="product-interactions">';
            elessi_add_wishlist_in_list();
        echo '</div>';
    }
endif;

/*
 * elessi quickview in list
 */
if(!function_exists('elessi_quickview_in_list')) :
    function elessi_quickview_in_list() {
        global $product;
        $type = $product->get_type();
        ?>
        <a href="javascript:void(0);" class="quick-view btn-link quick-view-icon tip-top" data-prod="<?php echo (int) $product->get_id(); ?>" data-tip="<?php echo $type !== 'woosb' ? esc_attr__('Quick View', 'elessi-theme') : esc_attr__('View', 'elessi-theme'); ?>" title="<?php echo $type !== 'woosb' ? esc_attr__('Quick View', 'elessi-theme') : esc_attr__('View', 'elessi-theme'); ?>" data-product_type="<?php echo esc_attr($type); ?>" data-href="<?php the_permalink(); ?>">
            <span class="pe-icon pe-7s-look"></span>
            <span class="hidden-tag nasa-icon-text"><?php echo $type !== 'woosb' ? esc_html__('Quick View', 'elessi-theme') : esc_html__('View', 'elessi-theme'); ?></span>
        </a>
        <?php
    }
endif;

/*
 * elessi add to cart in list
 */
if(!function_exists('elessi_add_to_cart_in_list')) :
    function elessi_add_to_cart_in_list() {
        elessi_add_to_cart_btn();
    }
endif;

/*
 * elessi gift icon in list
 */
if(!function_exists('elessi_bundle_in_list')) :
    function elessi_bundle_in_list($combo_show_type) {
        global $product;
        if(!defined('YITH_WCPB') || $product->get_type() != NASA_COMBO_TYPE) {
            return;
        }
        ?>
        <a href="javascript:void(0);" class="btn-combo-link btn-link gift-icon tip-top" data-prod="<?php echo (int) $product->get_id(); ?>" data-tip="<?php esc_attr_e('Promotion Gifts', 'elessi-theme'); ?>" title="<?php esc_attr_e('Promotion Gifts', 'elessi-theme'); ?>" data-show_type="<?php echo esc_attr($combo_show_type); ?>">
            <span class="pe-icon pe-7s-gift"></span>
            <span class="hidden-tag nasa-icon-text"><?php esc_html_e('Promotion Gifts', 'elessi-theme'); ?></span>
        </a>
        <?php
    }
endif;

/*
 * Nasa Gift icon Featured
 */
if(!function_exists('elessi_gift_featured')) :
    function elessi_gift_featured() {
        global $product, $nasa_opt;
        
        if(isset($nasa_opt['enable_gift_featured']) && !$nasa_opt['enable_gift_featured']) {
            return;
        }
        
        $product_type = $product->get_type();
        if(!defined('YITH_WCPB') || $product_type != NASA_COMBO_TYPE) {
            return;
        }
        
        $class_effect = isset($nasa_opt['enable_gift_effect']) && $nasa_opt['enable_gift_effect'] == 1 ? '' : ' nasa-transition';
        
        echo 
        '<div class="nasa-gift-featured-wrap">' .
            '<div class="nasa-gift-featured">' .
                '<div class="gift-icon">' .
                    '<a href="javascript:void(0);" class="nasa-gift-featured-event' . $class_effect . '" title="' . esc_attr__('View the promotion gifts', 'elessi-theme') . '">' .
                        '<span class="pe-icon pe-7s-gift"></span>' .
                        '<span class="hidden-tag nasa-icon-text">' . 
                            esc_html__('Promotion Gifts', 'elessi-theme') . 
                        '</span>' .
                    '</a>' .
                '</div>' .
            '</div>' .
        '</div>';
    }
endif;

/*
 * elessi add compare in list
 */
if(!function_exists('elessi_add_compare_in_list')) :
    function elessi_add_compare_in_list() {
        global $product, $nasa_opt;
        $productId = $product->get_id();
        
        $nasa_compare = (!isset($nasa_opt['nasa-product-compare']) || $nasa_opt['nasa-product-compare']) ? true : false;
        ?>
        <a href="javascript:void(0);" class="btn-compare btn-link compare-icon tip-top<?php echo ($nasa_compare) ? ' nasa-compare' : ''; ?>" data-prod="<?php echo (int) $productId; ?>" data-tip="<?php esc_attr_e('Compare', 'elessi-theme'); ?>" title="<?php esc_attr_e('Compare', 'elessi-theme'); ?>">
            <span class="nasa-icon icon-nasa-refresh"></span>
            <span class="hidden-tag nasa-icon-text"><?php esc_html_e('Compare', 'elessi-theme'); ?></span>
        </a>
        
        <?php if(!$nasa_compare) : ?>
            <div class="add-to-link woocommerce-compare-button hidden-tag">
                <?php echo do_shortcode('[yith_compare_button]'); ?>
            </div>
        <?php endif;
    }
endif;

/*
 * elessi add compare in detail
 */
if(!function_exists('elessi_add_compare_in_detail')) :
    function elessi_add_compare_in_detail() {
        global $product, $nasa_opt;
        $productId = $product->get_id();
        
        $nasa_compare = (!isset($nasa_opt['nasa-product-compare']) || $nasa_opt['nasa-product-compare']) ? true : false;
        ?>
        <div class="product-interactions">
            <a href="javascript:void(0);" class="btn-compare btn-link compare-icon<?php echo ($nasa_compare) ? ' nasa-compare' : ''; ?> tip-top" data-prod="<?php echo (int) $productId; ?>" data-tip="<?php esc_attr_e('Compare', 'elessi-theme'); ?>" title="<?php esc_attr_e('Compare', 'elessi-theme'); ?>">
                <span class="nasa-icon icon-nasa-compare-2"></span>
                <span class="nasa-icon-text"><?php esc_html_e('Add to Compare', 'elessi-theme'); ?></span>
            </a>
        
            <?php if(!$nasa_compare) : ?>
                <div class="add-to-link woocommerce-compare-button hidden-tag">
                    <?php echo do_shortcode('[yith_compare_button]'); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php
    }
endif;

if(!function_exists('elessi_single_availability')) :
    function elessi_single_availability() {
        global $product;
        // Availability
        $availability = $product->get_availability();

        if ($availability['availability']) :
            echo apply_filters('woocommerce_stock_html', '<p class="stock ' . esc_attr($availability['class']) . '">' . wp_kses(__('<span>Availability:</span> ', 'elessi-theme'), array('span' => array())) . esc_html($availability['availability']) . '</p>', $availability['availability']);
        endif;
    }
endif;

// custom fields product
if(!function_exists('elessi_add_custom_field_detail_product')) :
    function elessi_add_custom_field_detail_product() {
        global $product, $product_lightbox;
        if($product_lightbox) {
            $product = $product_lightbox;
        }
        
        $product_type = $product->get_type();
        // 'woosb' Bundle product
        if(in_array($product_type, array('external', 'woosb')) || (!defined('YITH_WCPB') && $product_type == NASA_COMBO_TYPE)) {
            return;
        }
        
        global $nasa_opt;

        $nasa_btn_ajax_value = ('yes' === get_option('woocommerce_enable_ajax_add_to_cart') && (!isset($nasa_opt['enable_ajax_addtocart']) || $nasa_opt['enable_ajax_addtocart'] == '1')) ? '1' : '0';
        echo '<div class="nasa-custom-fields hidden-tag">';
        echo '<input type="hidden" name="nasa-enable-addtocart-ajax" value="' . $nasa_btn_ajax_value . '" />';
        echo '<input type="hidden" name="data-product_id" value="' . esc_attr($product->get_id()) . '" />';
        echo '<input type="hidden" name="data-type" value="' . esc_attr($product_type) . '" />';
        $nasa_has_wishlist = (isset($_REQUEST['nasa_wishlist']) && $_REQUEST['nasa_wishlist'] == '1') ? '1' : '0';
        echo '<input type="hidden" name="data-from_wishlist" value="' . esc_attr($nasa_has_wishlist) . '" />';
        echo '</div>';
    }
endif;

if(!function_exists('elessi_single_hr')) :
    function elessi_single_hr() {
        echo '<hr class="nasa-single-hr" />';
    }
endif;

/**
 * Before - After wrap extra buttons (quick view - wishlist - combo)
 */
if(!function_exists('elessi_before_wrap_extra_btn')) :
    function elessi_before_wrap_extra_btn() {
        echo '<div class="nasa-wrap-extra-btns"><div class="nasa-inner-extra-btns">';
    }
endif;

if(!function_exists('elessi_after_wrap_extra_btn')) :
    function elessi_after_wrap_extra_btn() {
        echo '</div></div>';
    }
endif;

/**
 * Toggle coupon
 */
if(!function_exists('elessi_wrap_coupon_toggle')) :
    function elessi_wrap_coupon_toggle($content) {
        return '<div class="nasa-toggle-coupon-checkout text-right">' . $content . '</div>';
    }
endif;

/**
 * Images in content product
 */
if(!function_exists('elessi_loop_product_content_thumbnail')) :
    function elessi_loop_product_content_thumbnail() {
        global $product, $nasa_animated_products;
        
        $nasa_link = $product->get_permalink(); // permalink
        $nasa_title = $product->get_name(); // Title
        $image_size = apply_filters('single_product_archive_thumbnail_size', 'shop_catalog');
        $main_img = $product->get_image($image_size);
        
        $attachment_ids = $nasa_animated_products != '' ? $product->get_gallery_image_ids() : false;
        ?>
        <div class="product-img">
            <a href="<?php echo esc_url($nasa_link); ?>" title="<?php echo esc_attr($nasa_title); ?>">
                <div class="main-img"><?php echo ($main_img); ?></div>
                <?php if ($attachment_ids) :
                    foreach ($attachment_ids as $attachment_id) :
                        $image_link = wp_get_attachment_url($attachment_id);
                        if (!$image_link):
                            continue;
                        endif;
                        printf('<div class="back-img back">%s</div>', wp_get_attachment_image($attachment_id, $image_size));
                        break;
                    endforeach;
                endif; ?>
            </a>
        </div>
    <?php
    }
endif;

/**
 * Buttons in content product
 */
if(!function_exists('elessi_loop_product_content_btns')) :
    function elessi_loop_product_content_btns() {
        echo '<div class="nasa-product-grid nasa-btns-product-item">';
        echo elessi_product_group_button('popup');
        echo '</div>';
    }
endif;

/**
 * Categories in content product
 */
if(!function_exists('elessi_loop_product_cats')) :
    function elessi_loop_product_cats() {
        global $product;
        echo '<div class="nasa-list-category hidden-tag">';
        echo wc_get_product_category_list($product->get_id(), ', ');
        echo '</div>';
    }
endif;

/**
 * Title in content product
 */
if(!function_exists('elessi_loop_product_content_title')) :
    function elessi_loop_product_content_title() {
        global $product, $nasa_opt;
        
        $nasa_link = $product->get_permalink(); // permalink
        $nasa_title = $product->get_name(); // Title
        $class_title = (!isset($nasa_opt['cutting_product_name']) || $nasa_opt['cutting_product_name'] == '1') ? ' nasa-show-one-line' : '';
        ?>
        <div class="name<?php echo esc_attr($class_title); ?>">
            <a href="<?php echo esc_url($nasa_link); ?>" title="<?php echo esc_attr($nasa_title); ?>">
                <?php echo ($nasa_title); ?>
            </a>
        </div>
    <?php
    }
endif;

/**
 * Price in content product
 */
if(!function_exists('elessi_loop_product_price')) :
    function elessi_loop_product_price() {
        echo '<div class="price-wrap">';
        woocommerce_template_loop_price();
        echo '</div>';
    }
endif;

/**
 * Description in content product
 */
if(!function_exists('elessi_loop_product_description')) :
    function elessi_loop_product_description() {
        global $post;
        echo 
        '<div class="info_main product-des-wrap">' .
            '<hr class="nasa-list-hr hidden-tag" />' .
            '<div class="product-des">' .
                apply_filters('woocommerce_short_description', $post->post_excerpt) .
            '</div>' .
        '</div>';
    }
endif;

if (!function_exists('elessi_combo_tab')) :
    function elessi_combo_tab($nasa_viewmore = true) {
        global $woocommerce, $nasa_opt, $product;

        if (!$woocommerce || !$product || $product->get_type() != NASA_COMBO_TYPE || !$combo = $product->get_bundled_items()) {
            return false;
        }

        $file = ELESSI_CHILD_PATH . '/includes/nasa-combo-products-in-detail.php';
        $file = is_file($file) ? $file : ELESSI_THEME_PATH . '/includes/nasa-combo-products-in-detail.php';
        ob_start();
        include $file;

        return ob_get_clean();
    }
endif;

/**
 * nasa product budles in quickview
 */
if(!function_exists('elessi_combo_in_quickview')) :
    function elessi_combo_in_quickview() {
        global $woocommerce, $nasa_opt, $product;

        if (!$woocommerce || !$product || $product->get_type() != NASA_COMBO_TYPE || !($combo = $product->get_bundled_items())) {
            echo '';
        }
        else {
            $file = ELESSI_CHILD_PATH . '/includes/nasa-combo-products-quickview.php';
            $file = is_file($file) ? $file : ELESSI_THEME_PATH . '/includes/nasa-combo-products-quickview.php';

            include $file;
        }
    }
endif;

/**
 * Top side bar shop
 */
if(!function_exists('elessi_top_sidebar_shop')) :
    function elessi_top_sidebar_shop($type = '') {
        $type_top = !$type ? '1' : $type;
        $class = 'nasa-relative hidden-tag';
        $class .= $type_top == '1' ? ' large-12 columns nasa-top-sidebar' : ' nasa-top-sidebar-' . $type_top;
        $sidebar_run = 'shop-sidebar';
        
        if(is_tax('product_cat')) {
            global $wp_query;
            $query_obj = $wp_query->get_queried_object();
            $sidebar_cats = get_option('nasa_sidebars_cats');
            
            if(isset($sidebar_cats[$query_obj->slug])) {
                $sidebar_run = $query_obj->slug;
            }
            else {
                global $nasa_root_term_id;
                
                if(!$nasa_root_term_id) {
                    $ancestors = get_ancestors($query_obj->term_id, 'product_cat');
                    $nasa_root_term_id = $ancestors ? end($ancestors) : 0;
                }
                
                if($nasa_root_term_id) {
                    $GLOBALS['nasa_root_term_id'] = $nasa_root_term_id;
                    $rootTerm = get_term_by('term_id', $nasa_root_term_id, 'product_cat');
                    if($rootTerm && isset($sidebar_cats[$rootTerm->slug])) {
                        $sidebar_run = $rootTerm->slug;
                    }
                }
            }
        } ?>

        <div class="<?php echo esc_attr($class); ?>">
            <?php
            if (is_active_sidebar($sidebar_run)) :
                dynamic_sidebar($sidebar_run);
            endif;
            ?>
        </div>
    <?php
    }
endif;

/**
 * Side bar shop
 */
if(!function_exists('elessi_side_sidebar_shop')) :
    function elessi_side_sidebar_shop($nasa_sidebar = 'left') {
        $sidebar_run = 'shop-sidebar';
        if(is_tax('product_cat')) {
            global $wp_query;
            $query_obj = $wp_query->get_queried_object();
            $sidebar_cats = get_option('nasa_sidebars_cats');
            
            if(isset($sidebar_cats[$query_obj->slug])) {
                $sidebar_run = $query_obj->slug;
            }
            
            else {
                global $nasa_root_term_id;
                
                if(!$nasa_root_term_id) {
                    $ancestors = get_ancestors($query_obj->term_id, 'product_cat');
                    $nasa_root_term_id = $ancestors ? end($ancestors) : 0;
                }
                
                if($nasa_root_term_id) {
                    $GLOBALS['nasa_root_term_id'] = $nasa_root_term_id;
                    $rootTerm = get_term_by('term_id', $nasa_root_term_id, 'product_cat');
                    if($rootTerm && isset($sidebar_cats[$rootTerm->slug])) {
                        $sidebar_run = $rootTerm->slug;
                    }
                }
            }
        }
        
        switch ($nasa_sidebar) :
            case 'right' :
                $class = 'nasa-side-sidebar nasa-sidebar-right';
                break;
            
            case 'left-classic' :
                $class = 'large-3 left columns col-sidebar';
                break;
            
            case 'right-classic' :
                $class = 'large-3 right columns col-sidebar';
                break;
            
            case 'left' :
            default:
                $class = 'nasa-side-sidebar nasa-sidebar-left';
                break;
        endswitch;
        ?>
        
        <div class="<?php echo esc_attr($class); ?>">
            <?php
            if (is_active_sidebar($sidebar_run)) :
                dynamic_sidebar($sidebar_run);
            endif;
            ?>
        </div>
    <?php
    }
endif;

/**
 * Sale flash | Badges
 */
if(!function_exists('elessi_add_custom_sale_flash')) :
    function elessi_add_custom_sale_flash() {
        global $product;
        
        $badges = '';
        
        /**
         * Custom Badge
         */
        $nasa_bubble_hot = elessi_get_custom_field_value($product->get_id(), '_bubble_hot');
        $badges .= $nasa_bubble_hot ? '<div class="badge hot-label">' . $nasa_bubble_hot . '</div>' : '';

        if ($product->is_on_sale()):
            
            /**
             * Sale
             */
            $product_type = $product->get_type();
            if ($product_type == 'variable') :
                $badges .= '<div class="badge sale-label"><span class="sale-label-text sale-variable">' . esc_html__('SALE', 'elessi-theme') . '</span></div>';
                
            else :
                $price = '';
                $maximumper = 0;
                $regular_price = $product->get_regular_price();
                $sales_price = $product->get_sale_price();
                if(is_numeric($sales_price)) :
                    $percentage = $regular_price ? round(((($regular_price - $sales_price) / $regular_price) * 100), 0) : 0;
                    if ($percentage > $maximumper) :
                        $maximumper = $percentage;
                    endif;
                    
                    $badges .= '<div class="badge sale-label"><span class="sale-label-text">' . esc_html__('SALE', 'elessi-theme') . '</span>' . '-' . $price . sprintf(esc_html__('%s', 'elessi-theme'), $maximumper . '%') . '</div>';
                endif;
            endif;
            
            /**
             * Style show with Deal product
             */
            $badges .= '<div class="badge deal-label">' . esc_html__('LIMITED', 'elessi-theme') . '</div>';
        endif;
        
        /**
         * Out of stock
         */
        $stock_status = $product->get_stock_status();
        if ($stock_status == "outofstock"):
            $badges .= '<div class="badge out-of-stock-label">' . esc_html__('Sold Out', 'elessi-theme') . '</div>';
        endif;
        
        echo ('' !== $badges) ? '<div class="nasa-badges-wrap">' . $badges . '</div>' : '';
    }
endif;

/**
 * Get All categories product filter in top
 */
if(!function_exists('elessi_get_all_categories')) :
    function elessi_get_all_categories($only_show_child = false, $main = false, $hierarchical = true, $order = 'order') {
        if(!NASA_WOO_ACTIVED) {
            return;
        }
        
        if(!$only_show_child) {
            global $nasa_top_filter;
        }
        
        if(!isset($nasa_top_filter)) {
            global $nasa_opt, $wp_query, $post;

            $current_cat = false;
            $cat_ancestors = array();
            
            $rootId = 0;
            
            /**
             * post type page
             */
            if (
                isset($nasa_opt['disable_top_level_cat']) &&
                $nasa_opt['disable_top_level_cat'] &&
                isset($post->ID) &&
                $post->post_type == 'page'
            ) {
                $current_slug = get_post_meta($post->ID, '_nasa_root_category', true);
                
                if($current_slug) {
                    $current_cat = get_term_by('slug', $current_slug, 'product_cat');
                    if($current_cat && isset($current_cat->term_id)) {
                        $cat_ancestors = get_ancestors($current_cat->term_id, 'product_cat');
                    }
                }
            }
            
            /**
             * Archive product category
             */
            elseif (is_tax('product_cat')) {
                $current_cat = $wp_query->queried_object;
                $cat_ancestors = get_ancestors($current_cat->term_id, 'product_cat');
            }
            
            /**
             * Single product page
             */
            elseif (is_singular('product')) {
                $product_category = wc_get_product_terms($post->ID, 'product_cat', array('orderby' => 'parent'));

                if ($product_category) {
                    $current_cat = end($product_category);
                    $cat_ancestors = get_ancestors($current_cat->term_id, 'product_cat');
                }
            }
            
            if($only_show_child && $current_cat && $current_cat->term_id) {
                $terms_chilren = get_terms(apply_filters('woocommerce_product_attribute_terms', array(
                    'taxonomy' => 'product_cat',
                    'parent' => $current_cat->term_id,
                    'hierarchical' => $hierarchical,
                    'hide_empty' => false
                )));

                if(! $terms_chilren) {
                    $term_root = get_ancestors($current_cat->term_id, 'product_cat');
                    $rootId = isset($term_root[0]) ? $term_root[0] : $rootId;
                } else {
                    $rootId = $current_cat->term_id;
                }
            }
            
            elseif((isset($nasa_opt['disable_top_level_cat']) && $nasa_opt['disable_top_level_cat'])) {
                $rootId = $cat_ancestors ? end($cat_ancestors) : ($current_cat ? $current_cat->term_id : $rootId);
            }
            
            $menu_cat = new Elessi_Product_Cat_List_Walker();
            $args = array(
                'taxonomy' => 'product_cat',
                'show_count' => 0,
                'hierarchical' => 1,
                'hide_empty' => false
            );
            
            $args['menu_order'] = false;
            if ($order == 'order') {
                $args['menu_order'] = 'asc';
            } else {
                $args['orderby'] = 'title';
            }
            
            $args['walker'] = $menu_cat;
            $args['title_li'] = '';
            $args['pad_counts'] = 1;
            $args['show_option_none'] = esc_html__('No product categories exist.', 'elessi-theme');
            $args['current_category'] = $current_cat ? $current_cat->term_id : '';
            $args['current_category_ancestors'] = $cat_ancestors;
            $args['child_of'] = $rootId;
            
            if(version_compare(wc()->version, '3.3.0', ">=") && (!isset($nasa_opt['show_uncategorized']) || !$nasa_opt['show_uncategorized'])) {
                $args['exclude'] = get_option('default_product_cat');
            }

            $nasa_top_filter = '<ul class="nasa-top-cat-filter product-categories nasa-accordion">';
            
            ob_start();
            wp_list_categories(apply_filters('woocommerce_product_categories_widget_args', $args));
            $nasa_top_filter .= ob_get_clean();
            
            $nasa_top_filter .= '<li class="nasa-current-note"></li>';
            $nasa_top_filter .= '</ul>';
            
            if(!$only_show_child) {
                $GLOBALS['nasa_top_filter'] = $nasa_top_filter;
            }
        }
        
        $result = $nasa_top_filter;
        if($main) {
            $result = '<div id="nasa-main-cat-filter">' . $result . '</div>';
        }
        
        return $result;
    }
endif;

/**
 * elessi_nasa_change_view
 */
if(!function_exists('elessi_nasa_change_view')) :
    function elessi_nasa_change_view($nasa_change_view = true, $typeShow = 'grid-4', $nasa_sidebar = 'no') {
        if(!$nasa_change_view) :
            return;
        endif;
        
        $classic = in_array($nasa_sidebar, array('left-classic', 'right-classic', 'top-push-cat'));
        echo ($classic) ? '<input type="hidden" name="nasa-data-sidebar" value="' . esc_attr($nasa_sidebar) . '" />' : '';
        
        ?>
        <ul class="filter-tabs">
            <?php if(!$classic) : ?>
                <li class="nasa-change-layout productGrid grid-5<?php echo ($typeShow == 'grid-5') ? ' active' : ''; ?>" data-columns="5">
                    <i class="icon-nasa-5column"></i>
                </li>
            <?php endif; ?>
            <li class="nasa-change-layout productGrid grid-4<?php echo (($typeShow == 'grid-4') || ($typeShow == 'grid-5' && $classic)) ? ' active' : ''; ?>" data-columns="4">
                <i class="icon-nasa-4column"></i>
            </li>
            <li class="nasa-change-layout productGrid grid-3<?php echo ($typeShow == 'grid-3') ? ' active' : ''; ?>" data-columns="3">
                <i class="icon-nasa-3column"></i>
            </li>
            <li class="nasa-change-layout productList list<?php echo ($typeShow == 'list') ? ' active' : ''; ?>" data-columns="1">
                <i class="icon-nasa-list"></i>
            </li>
        </ul>
        <?php
    }
endif;

/**
 * Remove wishlit btn in detail product
 */
if(!function_exists('elessi_remove_btn_wishlist_single_product')) :
    function elessi_remove_btn_wishlist_single_product($hook) {
        $hook['add-to-cart'] = array('hook' => '', 'priority' => 0);
        return $hook;
    }
endif;

/**
 * elessi_single_product_layout
 */
if(!function_exists('elessi_single_product_layout')) :
    function elessi_single_product_layout() {
        global $product, $nasa_opt;

        /**
         * Layout: New | Classic
         */
        $layout = (isset($nasa_opt['product_detail_layout']) && $nasa_opt['product_detail_layout'] == 'classic') ? $nasa_opt['product_detail_layout'] : 'new';
        $layout = (isset($_GET['layout']) && $_GET['layout'] == 'classic') ? 'classic' : $layout;

        /**
         * Image Layout Style
         */
        $image_layout = 'single';
        $image_style = 'slide';
        if($layout == 'new') {
            $image_layout = (!isset($nasa_opt['product_image_layout']) || $nasa_opt['product_image_layout'] == 'double') ? 'double' : 'single';
            $image_layout = (isset($_GET['image-layout']) && in_array($_GET['image-layout'], array('double', 'single'))) ? $_GET['image-layout'] : $image_layout;

            $image_style = (!isset($nasa_opt['product_image_style']) || $nasa_opt['product_image_style'] == 'slide') ? 'slide' : 'scroll';
            $image_style = (isset($_GET['image-style']) && in_array($_GET['image-style'], array('slide', 'scroll'))) ? $_GET['image-style'] : $image_style;
        }

        $nasa_sidebar = isset($nasa_opt['product_sidebar']) ? $nasa_opt['product_sidebar'] : 'no';
        $nasa_actsidebar = is_active_sidebar('product-sidebar');

        // Check $_GET['sidebar']
        if (isset($_GET['sidebar'])):
            switch ($_GET['sidebar']) :
                case 'right' :
                    $nasa_sidebar = 'right';
                    break;

                case 'left' :
                    $nasa_sidebar = 'left';
                    break;

                case 'no' :
                default:
                    $nasa_sidebar = 'no';
                    break;
            endswitch;
        endif;

        // Class
        switch ($nasa_sidebar) :
            case 'right' :
                if($layout == 'classic') {
                    $main_class = 'large-9 columns left';
                    $bar_class = 'large-3 columns col-sidebar product-sidebar-right right';
                } else {
                    $main_class = 'large-12 columns';
                    $bar_class = 'nasa-side-sidebar nasa-sidebar-right';
                }

                break;

            case 'no' :
                $main_class = 'large-12 columns';
                $bar_class = '';
                break;

            default:
            case 'left' :
                if($layout == 'classic') {
                    $main_class = 'large-9 columns right';
                    $bar_class = 'large-3 columns col-sidebar product-sidebar-left left';
                }  else {
                    $main_class = 'large-12 columns';
                    $bar_class = 'nasa-side-sidebar nasa-sidebar-left';
                }

                break;

        endswitch;
        
        $main_class .= ' nasa-single-product-' . $image_style;
        $main_class .= $image_style == 'scroll' && $image_layout == 'double' ? ' nasa-single-product-2-columns': '';
        
        $file = ELESSI_CHILD_PATH . '/includes/nasa-single-product-' . $layout . '.php';
        $file = is_file($file) ? $file : ELESSI_THEME_PATH . '/includes/nasa-single-product-' . $layout . '.php';
        
        include_once $file;
    }
endif;

if (!function_exists('elessi_ProductShowReviews')) :
    function elessi_ProductShowReviews() {
        if (comments_open()) {
            global $wpdb, $post;

            $count = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(meta_value) FROM $wpdb->commentmeta
                LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
                WHERE meta_key = %s
                AND comment_post_ID = %s
                AND comment_approved = %s
                AND meta_value > %s", 'rating', $post->ID, '1', '0'
            ));

            $rating = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(meta_value) FROM $wpdb->commentmeta
                LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
                WHERE meta_key = %s
                AND comment_post_ID = %s
                AND comment_approved = %s", 'rating', $post->ID, '1'
            ));

            if ($count > 0) {
                $average = number_format($rating / $count, 2);

                echo '<a href="#tab-reviews" class="scroll-to-reviews"><div class="star-rating tip-top" data-tip="' . $count . ' review(s)"><span style="width:' . ($average * 16) . 'px"><span class="rating"><span>' . $average . '</span><span class="hidden">' . $count . '</span></span> ' . esc_html__('out of 5', 'elessi-theme') . '</span></div></a>';
            }
        }
    }
endif;

/**
 * nasa_archive_get_sub_categories
 */
add_action('nasa_archive_get_sub_categories', 'nasa_archive_get_sub_categories');
if(!function_exists('nasa_archive_get_sub_categories')) :
    function nasa_archive_get_sub_categories() {
        $GLOBALS['nasa_cat_loop_delay'] = 0;
        
        echo '<div class="nasa-archive-sub-categories-wrap">';
        woocommerce_product_subcategories(array(
            'before' => '<div class="row"><div class="large-12 columns"><h3>' . esc_html__('Subcategories: ', 'elessi-theme') . '</h3></div></div><div class="row">',
            'after' => '</div><div class="row"><div class="large-12 columns margin-bottom-20 margin-top-20 text-center"><hr class="margin-left-20 margin-right-20" /></div></div>'
        ));
        echo '</div>';
    }
endif;

if(!function_exists('elessi_maybe_show_product_subcategories') && NASA_WOO_ACTIVED && version_compare(wc()->version, '3.3.0', ">=")) :
    function elessi_maybe_show_product_subcategories($loop_html) {
        $display_type = woocommerce_get_loop_display_mode();
        
        // If displaying categories, append to the loop.
        if ('subcategories' === $display_type || 'both' === $display_type) {
            $before = '<div class="row"><div class="large-12 columns"><h3>' . esc_html__('Subcategories: ', 'elessi-theme') . '</h3></div></div><div class="row">';
            $after = '</div><div class="row"><div class="large-12 columns margin-bottom-20 margin-top-20 text-center"><hr class="margin-left-20 margin-right-20" /></div></div>';
            ob_start();
            woocommerce_output_product_categories(array(
                'parent_id' => is_product_category() ? get_queried_object_id() : 0,
            ));
            $loop_html .= $before . ob_get_clean() . $after;

            if ('subcategories' === $display_type) {
                wc_set_loop_prop('total', 0);

                // This removes pagination and products from display for themes not using wc_get_loop_prop in their product loops. @todo Remove in future major version.
                global $wp_query;

                if ($wp_query->is_main_query()) {
                    $wp_query->post_count    = 0;
                    $wp_query->max_num_pages = 0;
                }
            }
        }

        return $loop_html;
    }
endif;

/**
 * Hide Uncategorized
 */
if(!function_exists('elessi_hide_uncategorized')) :
    function elessi_hide_uncategorized($args) {
        $args['exclude'] = get_option('default_product_cat');
        return $args;
    }
endif;

/**
 * Pagination product pages
 */
if(!function_exists('elessi_get_pagination_ajax')) :
    function elessi_get_pagination_ajax(
        $total = 1,
        $current = 1,
        $type = 'list',
        $prev_text = 'PREV', 
        $next_text = 'NEXT',
        $end_size = 3, 
        $mid_size = 3,
        $prev_next = true,
        $show_all = false
    ) {

        if ($total < 2) {
            return;
        }

        if ($end_size < 1) {
            $end_size = 1;
        }

        if ($mid_size < 0) {
            $mid_size = 2;
        }

        $r = '';
        $page_links = array();

        // PREV Button
        if ($prev_next && $current && 1 < $current){
            $page_links[] = '<a class="nasa-prev prev page-numbers" data-page="' . ((int)$current - 1) . '" href="javascript:void(0);">' . $prev_text . '</a>';
        }

        // PAGE Button
        $moreStart = false;
        $moreEnd = false;
        for ($n = 1; $n <= $total; $n++){
            $page = number_format_i18n($n);
            if ($n == $current){
                $page_links[] = '<a class="nasa-current current page-numbers" data-page="' . $page . '" href="javascript:void(0);">' . $page . '</a>';
            }
            
            else {
                if ($show_all || ($current && $n >= $current - $mid_size && $n <= $current + $mid_size)) {
                    $page_links[] = '<a class="nasa-page page-numbers" data-page="' . $page . '" href="javascript:void(0);">' . $page . "</a>";
                }
                
                elseif ($n == 1 || $n == $total) {
                    $page_links[] = '<a class="nasa-page page-numbers" data-page="' . $page . '" href="javascript:void(0);">' . $page . "</a>";
                }
                
                elseif (!$moreStart && $n <= $end_size + 1) {
                    $moreStart = true;
                    $page_links[] = '<span class="nasa-page-more">' . esc_html__('...', 'elessi-theme') . '</span>';
                }
                
                elseif (!$moreEnd && $n > $total - $end_size - 1) {
                    $moreEnd = true;
                    $page_links[] = '<span class="nasa-page-more">' . esc_html__('...', 'elessi-theme') . '</span>';
                }
            }
        }

        // NEXT Button
        if ($prev_next && $current && ($current < $total || -1 == $total)){
            $page_links[] = '<a class="nasa-next next page-numbers" data-page="' . ((int)$current + 1)  . '" href="javascript:void(0);">' . $next_text . '</a>';
        }
        // DATA Return
        switch ($type) {
            case 'array' :
                return $page_links;

            case 'list' :
                $r .= '<ul class="page-numbers nasa-pagination-ajax"><li>';
                $r .= implode('</li><li>', $page_links);
                $r .= '</li></ul>';
                break;

            default :
                $r = implode('', $page_links);
                break;
        }

        return $r;
    }
endif;

/**
 * Before Share WooCommerce
 */
if(!function_exists('elessi_before_woocommerce_share')) :
    function elessi_before_woocommerce_share() {
        echo '<hr class="nasa-single-hr" /><div class="nasa-single-share">';
    }
endif;

/**
 * Custom Share WooCommerce
 */
if(!function_exists('elessi_woocommerce_share')) :
    function elessi_woocommerce_share() {
        echo shortcode_exists('nasa_share') ? do_shortcode('[nasa_share]') : '';
    }
endif;

/**
 * After Share WooCommerce
 */
if(!function_exists('elessi_after_woocommerce_share')) :
    function elessi_after_woocommerce_share() {
        echo '</div>';
    }
endif;
