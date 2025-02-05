<?php
add_shortcode('nasa_title', 'nasa_title');

/* TITLE */
function nasa_title($atts, $content = null) {
    extract(shortcode_atts(array(
        'title_text' => '',
        'title_color' => '',
        'title_bg' => '#FFFFFF',
        'title_type' => 'h3',
        'title_hr' => 'simple',
        'title_desc' => '',
        'title_align' => '',
        'first_special' => '0',
        'el_class' => ''
    ), $atts));
    
    if($title_text == '') {
        return '';
    }
    
    $style_bg = array();
    $color_desc = $color_hr = '';
    if($title_bg != '') {
        $style_bg[] = 'background: ' . $title_bg;
    }
    
    if($title_color != '') {
        $style_bg[] = 'color: ' . $title_color;
        $color_desc = ' style="' . 'color: ' . $title_color . ';"';
        $color_hr = ' style="' . 'border-color: ' . $title_color . ';"';
    }
    $style_bg = !empty($style_bg) ? ' style="' . implode('; ', $style_bg) . ';"' : '';
    
    $hwrap = in_array($title_type, array('h1', 'h2', 'h3', 'h4', 'h5')) ? $title_type : 'h3';
    $title = $title_text ? '<span' . $style_bg . '>' . $title_text . '</span><span class="nasa-title-hr"' . $color_hr . '></span>' : '';
    if($first_special) {
        $texts = $title_text ? explode(' ', $title_text) : array('');
        $first = $texts[0];
        unset($texts[0]);
        if($first) {
            $title = '<span' . $style_bg . '><span class="nasa-first-word">' . $first . '</span>' . (count($texts) ? ' ' . implode(' ', $texts) : '') . '</span><hr class="nasa-title-hr" />';
        }
    }
    
    $title = '<' . $hwrap . ' class="nasa-heading-title"><span class="nasa-title-wrap">' . $title . '</span></' . $hwrap . '>';
    $title_desc = trim($title_desc) != '' ? '<div class="nasa-title-desc"' . $color_desc . '>' . $title_desc . '</div>' : '';
    
    $style_output = 'nasa-title clearfix';
    $style_output .= ($title_hr != '') ? ' hr-type-' . $title_hr : ''; 
    $style_output .= ($title_align != '') ? ' ' . $title_align : ''; 
    $style_output .= $el_class != '' ? ' ' . $el_class : '';
    
    return 
        '<div class="' . $style_output . '">' .
            '<div class="nasa-wrap">' .
                $title .
                $title_desc .
            '</div>' .
        '</div>';
    
}

add_action('init', 'nasa_register_title');
function nasa_register_title(){
    // **********************************************************************// 
    // ! Register New Element: nasa Title
    // **********************************************************************//
    // first_special
    $nasa_title_params = array(
        "name" => esc_html__("Title", 'nasa-core'),
        "base" => "nasa_title",
        'icon' => 'icon-wpb-nasatheme',
        'description' => esc_html__("Create title of section.", 'nasa-core'),
        "content_element" => true,
        "category" => 'Nasa Core',
        "params" => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Title text', 'nasa-core'),
                'param_name' => 'title_text',
                'admin_label' => true,
                'value' => '',
                'description' => ''
            ),
            array(
                "type" => "colorpicker",
                "heading" => esc_html__("Color title", 'nasa-core'),
                "param_name" => "title_color",
                "value" => ""
            ),
            array(
                "type" => "dropdown",
                "heading" => esc_html__('Title type heading', 'nasa-core'),
                "param_name" => 'title_type',
                "value" => array(
                    esc_html__('H1', 'nasa-core') => 'h1',
                    esc_html__('H2', 'nasa-core') => 'h2',
                    esc_html__('H3', 'nasa-core') => 'h3',
                    esc_html__('H4', 'nasa-core') => 'h4',
                    esc_html__('H5', 'nasa-core') => 'h5'
                ),
                'std' => 'h3',
                'admin_label' => true
            ),
            array(
                "type" => "colorpicker",
                "heading" => esc_html__("Background title", 'nasa-core'),
                "param_name" => "title_bg",
                "value" => "#FFFFFF"
            ),
            array(
                "type" => "dropdown",
                "heading" => esc_html__('Title HR', 'nasa-core'),
                "param_name" => 'title_hr',
                "value" => array(
                    esc_html__('Simple', 'nasa-core') => 'simple',
                    esc_html__('Full', 'nasa-core') => 'full',
                    esc_html__('Vertical', 'nasa-core') => 'vertical',
                    esc_html__('Baby', 'nasa-core') => 'baby',
                    esc_html__('None', 'nasa-core') => 'none'
                ),
                'std' => 'simple',
                'admin_label' => true
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Title description', 'nasa-core'),
                'param_name' => 'title_desc',
                'admin_label' => true,
                'value' => '',
                'description' => ''
            ),
            array(
                "type" => "dropdown",
                "heading" => esc_html__('Title Alignment', 'nasa-core'),
                "param_name" => 'title_align',
                "value" => array(
                    esc_html__('Left', 'nasa-core') => '',
                    esc_html__('Center', 'nasa-core') => 'text-center',
                    esc_html__('Right', 'nasa-core') => 'text-right'
                ),
                "dependency" => array(
                    "element" => "title_hr",
                    "value" => array(
                        'simple', 'full', 'baby', 'none'
                    )
                ),
            ),
            
            array(
                "type" => "dropdown",
                "heading" => esc_html__('Title Style', 'nasa-core'),
                "param_name" => 'first_special',
                "value" => array(
                    esc_html__('None Special First word', 'nasa-core') => '0',
                    esc_html__('Special First word', 'nasa-core') => '1'
                ),
                "std" => '0'
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Extra class name", 'nasa-core'),
                "param_name" => "el_class",
                "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'nasa-core')
            )
        )
    );
    
    vc_map($nasa_title_params);
}