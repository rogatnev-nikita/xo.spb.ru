<?php
add_shortcode('nasa_products_special_deal', 'nasa_sc_products_special_deal');
function nasa_sc_products_special_deal($atts, $content = null) {
    global $woocommerce, $nasa_opt;
    
    if (!$woocommerce) {
        return $content;
    }
    $dfAttr = array(
        'title' => '',
        'desc_shortcode' => '',
        'limit' => '4',
        'columns_number' => '1',
        'columns_number_small' => '1',
        'columns_number_tablet' => '1',
        'cat' => '',
        'style' => 'simple',
        'position_nav' => 'right',
        'date_sc' => '',
        'statistic' => '1',
        'arrows' => 1,
        'auto_slide' => 'true',
        'el_class' => '',
        'is_ajax' => 'yes',
        'min_height' => 'auto'
    );
    extract(shortcode_atts($dfAttr, $atts));
    
    // Optimized speed
    if (!isset($nasa_opt['enable_optimized_speed']) || $nasa_opt['enable_optimized_speed'] == 1) {
        $atts['is_ajax'] = !isset($atts['is_ajax']) ? $is_ajax : $atts['is_ajax'];
        if (isset($atts['is_ajax']) && $atts['is_ajax'] == 'yes' &&
            (!isset($_REQUEST['nasa_load_ajax']) || $_REQUEST['nasa_load_ajax'] != '1')) {
            
            return nasa_shortcode_text('nasa_products_special_deal', $atts);
        }

        // Load ajax
        elseif($atts['is_ajax'] == 'yes' && $_REQUEST['nasa_load_ajax'] == '1') {
            extract(shortcode_atts($dfAttr, nasa_shortcode_vars($atts)));
        }
    }
    
    $deal_time = $date_sc ? strtotime($date_sc) : 0;
    if($style == 'for_time' && $deal_time < NASA_TIME_NOW) {
        return;
    }
    
    $number = (int) $limit ? (int) $limit : 4;
    $specials = nasa_woocommerce_query('deals', $number, $cat, '', array(), $deal_time);
    $style = in_array($style, array('simple', 'multi', 'multi-2', 'for_time')) ? $style : 'simple';
    $file_include = NASA_CORE_PRODUCT_LAYOUTS . 'nasa_products_deal/product_special_deal_' . $style . '.php';
    if (is_file($file_include) && $_total = $specials->post_count) :
        ob_start();
        ?>
        <div class="woocommerce nasa-products-special-deal<?php echo ' nasa-products-special-deal-' . $style . ($el_class != '' ? ' ' . $el_class : ''); ?>">
            <?php include $file_include; ?>
        </div>
    <?php
        wp_reset_postdata();
        $content = ob_get_clean();
    endif;
    
    return $content;
}

add_action('init', 'nasa_register_product_special_deals');
function nasa_register_product_special_deals(){
    // **********************************************************************// 
    // ! Register New Element: Nasa products special Deal
    // **********************************************************************//
    vc_map(array(
        "name" => esc_html__("Product special deal Schedule", 'nasa-core'),
        "base" => "nasa_products_special_deal",
        'icon' => 'icon-wpb-nasatheme',
        'description' => esc_html__("Display products deal.", 'nasa-core'),
        "class" => "",
        "category" => 'Nasa Core',
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => esc_html__("Limit deal products", 'nasa-core'),
                "param_name" => "limit",
                "std" => '4'
            ),
            
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Product Category", 'nasa-core'),
                "param_name" => "cat",
                "value" => nasa_get_cat_product_array(),
                "description" => esc_html__("Input the category name here.", 'nasa-core')
            ),
            
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Style", 'nasa-core'),
                "param_name" => "style",
                "value" => array(
                    esc_html__('No Nav Items', 'nasa-core') => 'simple',
                    esc_html__('Has Nav 2 Items', 'nasa-core') => 'multi',
                    esc_html__('Has Nav 4 Items', 'nasa-core') => 'multi-2',
                    esc_html__('Deal Before Time', 'nasa-core') => 'for_time',
                ),
                'std' => 'simple'
            ),
            
            array(
                "type" => "textfield",
                "heading" => esc_html__('Title', 'nasa-core'),
                "param_name" => 'title',
                "dependency" => array(
                    "element" => "style",
                    "value" => array(
                        'simple',
                        'multi-2',
                        'for_time'
                    )
                ),
            ),
            
            array(
                "type" => "textfield",
                "heading" => esc_html__('Short description', 'nasa-core'),
                "param_name" => 'desc_shortcode',
                "dependency" => array(
                    "element" => "style",
                    "value" => array(
                        'for_time'
                    )
                ),
            ),
            
            array(
                "type" => "textfield",
                "heading" => esc_html__('End date show deals (yyyy-mm-dd | yyyy/mm/dd)', 'nasa-core'),
                "param_name" => 'date_sc',
                'std' => '',
                "dependency" => array(
                    "element" => "style",
                    "value" => array(
                        'for_time'
                    )
                ),
                "admin_label" => true
            ),
            
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Columns number", 'nasa-core'),
                "param_name" => "columns_number",
                "value" => array(5, 4, 3, 2, 1),
                "std" => 1,
                "dependency" => array(
                    "element" => "style",
                    "value" => array(
                        "simple",
                        'for_time'
                    )
                ),
                "description" => esc_html__("Select columns count.", 'nasa-core')
            ),
            
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Columns number small", 'nasa-core'),
                "param_name" => "columns_number_small",
                "value" => array(2, 1),
                "std" => 1,
                "dependency" => array(
                    "element" => "style",
                    "value" => array(
                        "simple",
                        'for_time'
                    )
                ),
                "description" => esc_html__("Select columns count small display.", 'nasa-core')
            ),
            
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Columns number tablet", 'nasa-core'),
                "param_name" => "columns_number_tablet",
                "value" => array(3, 2, 1),
                "std" => 1,
                "dependency" => array(
                    "element" => "style",
                    "value" => array(
                        "simple",
                        'for_time'
                    )
                ),
                "description" => esc_html__("Select columns count in tablet.", 'nasa-core')
            ),
            
            array(
                "type" => "dropdown",
                "heading" => esc_html__('Show Available - Sold', 'nasa-core'),
                "param_name" => 'statistic',
                "value" => array(
                    esc_html__('No, thank', 'nasa-core') => '0',
                    esc_html__('Yes, please', 'nasa-core') => '1'
                ),
                "std" => '1',
                "dependency" => array(
                    "element" => "style",
                    "value" => array(
                        'simple',
                        'multi',
                        'multi-2'
                    )
                ),
                "description" => esc_html__("Show Available - Sold.", 'nasa-core')
            ),
            
            array(
                "type" => "dropdown",
                "heading" => esc_html__('Show arrows', 'nasa-core'),
                "param_name" => 'arrows',
                "value" => array(
                    esc_html__('Yes, please', 'nasa-core') => 1,
                    esc_html__('No, thank', 'nasa-core') => 0
                ),
                "std" => 1,
                "description" => esc_html__("Show arrows", 'nasa-core')
            ),
            
            array(
                "type" => "dropdown",
                "heading" => esc_html__('Auto Slide', 'nasa-core'),
                "param_name" => 'auto_slide',
                "value" => array(
                    esc_html__('Yes, please', 'nasa-core') => 'true',
                    esc_html__('No, thank', 'nasa-core') => 'false'
                ),
                "std" => 'true',
                "description" => esc_html__("Auto slide.", 'nasa-core')
            ),
            
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Optimized speed", 'nasa-core'),
                "param_name" => "is_ajax",
                "value" => array(
                    esc_html__('Yes', 'nasa-core') => 'yes',
                    esc_html__('No', 'nasa-core') => 'no'
                ),
                "std" => 'yes',
                "admin_label" => true
            ),
            
            array(
                "type" => "textfield",
                "heading" => esc_html__('Min height (px)', 'nasa-core'),
                "param_name" => "min_height",
                "std" => 'auto',
                "description" => esc_html__('Only use when Optimized speed "Yes"', 'nasa-core')
            ),
            
            array(
                "type" => "textfield",
                "heading" => esc_html__("Extra class name", 'nasa-core'),
                "param_name" => "el_class",
                "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'nasa-core')
            )
        )
    ));
}
