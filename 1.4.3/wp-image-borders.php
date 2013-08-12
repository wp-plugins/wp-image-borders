<?php
/*
Plugin Name: WP Image Borders
Description: WP Image Borders makes it easy to add or remove image borders from pictures in your blog posts. 
Version: 1.4.3
Author: Ben Sibley
Author URI: http://profiles.wordpress.org/bensibley
License: GPLv2
*/

/* Copyright 2013 Ben Sibley (email: bens233@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function supportDashInit () {
    require_once('supportdash.php');
    new SupportDash("WP Image Borders", "0", "51f70736aa001e0200000001");
}
add_action('init', 'supportDashInit');

// Hook into admin_menu to build my submenu
add_action('admin_menu', 'bs_wib_add_page');

// 
function bs_wib_add_page() {
    add_options_page( 
        'WP Image Borders Options', // Page Title
        'WP Image Borders', // Title of Menu
        'manage_options', // capability level
        'wp-image-borders', // Menu slug
        'wp_image_borders_options' // callback function
    );
}

// creates content in options page and calls settings sections & fields
function wp_image_borders_options() {
    ?>
    <div class="wrap">
        <?php screen_icon( 'themes' ); ?>
        <h2>WP Image Borders Options</h2>
        <form action="options.php" method="post">
            <?php settings_fields( 'wp_image_borders_options' ); ?>
            <?php do_settings_sections( 'wp-image-borders' ); ?>
            <input class="button-primary" name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
        </form>
        <p>If you liked this plugin, please take <a href="http://wordpress.org/support/view/plugin-reviews/wp-image-borders">1 minute to leave a review</a>.</p>
    </div><?php 
}

// Add settings link on plugin page
function bs_wib_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=wp-image-borders.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'bs_wib_settings_link' );

// hook into the admin initialization
add_action( 'admin_init', 'bs_wib_admin_init');

//register setting and create the section and field
function bs_wib_admin_init() {
    register_setting( 
        'wp_image_borders_options', // $option_group variable created in settings_field 
        'wp_image_borders_options', // $option_name to be sanitized and saved
        'bs_wib_validation' // validation function
    );
    // this section contains remove checkbox
    add_settings_section(
        'bs_wib_main', // string used in 'id' attribute of tags
        'Remove Image Borders/Shadows', // title of the section
        'bs_wib_section_text', // callback that will echo content
        'wp-image-borders' // $page ($menu_slug used in add_options_page)
    );
    // this section contains the custom border options
    add_settings_section(
        'bs_wib_border_styles', // string used in 'id' attribute of tags
        'Customize Image Borders', // title of the section
        'bs_wib_borders_section_text', // callback that will echo content
        'wp-image-borders' // $page ($menu_slug used in add_options_page)
    );
    // this section contains the box shadow options
    add_settings_section(
        'bs_wib_box_shadows', // string used in 'id' attribute of tags
        'Add Drop Shadows to Images', // title of the section
        'bs_wib_shadow_section_text', // callback that will echo content
        'wp-image-borders' // $page ($menu_slug used in add_options_page)
    );
    // this field contains the remove checkbox
    add_settings_field(
        'bs_wib_checkbox', // string used for 'id' in attribute tags
        'Remove Image Borders?', // Title of the field
        'bs_wib_checkbox_display', // callback that creates the input field
        'wp-image-borders', // $page ($menu_slug used in add_options_page)
        'bs_wib_main' // the section the field is placed in (the id from add_settings_section above)
    );
    // Border options fields below
    // this field adds the style input for the custom border options
    add_settings_field(
        'bs_wib_border_style', // string used for 'id' in attribute tags
        'Border Style:', // Title of the field
        'bs_wib_border_style_display', // callback that creates the input field
        'wp-image-borders', // $page ($menu_slug used in add_options_page)
        'bs_wib_border_styles' // the section the field is placed in (the id from add_settings_section above)
    );
    // this field adds the width input for the custom border options
    add_settings_field(
        'bs_wib_border_width', // string used for 'id' in attribute tags
        'Border Width (in pixels):', // Title of the field
        'bs_wib_border_width_display', // callback that creates the input field
        'wp-image-borders', // $page ($menu_slug used in add_options_page)
        'bs_wib_border_styles' // the section the field is placed in (the id from add_settings_section above)
    );
    // this field adds the border radius input for the custom border options
    add_settings_field(
        'bs_wib_border_radius', // string used for 'id' in attribute tags
        'Border Radius (in pixels):', // Title of the field
        'bs_wib_border_radius_display', // callback that creates the input field
        'wp-image-borders', // $page ($menu_slug used in add_options_page)
        'bs_wib_border_styles' // the section the field is placed in (the id from add_settings_section above)
    );
    // this field adds the color input for the custom border options
    add_settings_field(
        'bs_wib_border_color', // string used for 'id' in attribute tags
        'Border Color:', // Title of the field
        'bs_wib_border_color_display', // callback that creates the input field
        'wp-image-borders', // $page ($menu_slug used in add_options_page)
        'bs_wib_border_styles' // the section the field is placed in (the id from add_settings_section above)
    );
    // Box shadow fields here
    // this field adds the drop shadow options for the custom border options
    add_settings_field(
        'bs_wib_box_shadow_horizontal', // string used for 'id' in attribute tags
        'Horizontal Distance:', // Title of the field
        'bs_wib_box_shadow_horizontal_display', // callback that creates the input field
        'wp-image-borders', // $page ($menu_slug used in add_options_page)
        'bs_wib_box_shadows' // the section the field is placed in (the id from add_settings_section above)
    );
    add_settings_field(
        'bs_wib_box_shadow_vertical', // string used for 'id' in attribute tags
        'Vertical Distance:', // Title of the field
        'bs_wib_box_shadow_vertical_display', // callback that creates the input field
        'wp-image-borders', // $page ($menu_slug used in add_options_page)
        'bs_wib_box_shadows' // the section the field is placed in (the id from add_settings_section above)
    );
    add_settings_field(
        'bs_wib_box_shadow_blur', // string used for 'id' in attribute tags
        'Blur Radius:', // Title of the field
        'bs_wib_box_shadow_blur_display', // callback that creates the input field
        'wp-image-borders', // $page ($menu_slug used in add_options_page)
        'bs_wib_box_shadows' // the section the field is placed in (the id from add_settings_section above)
    );
    add_settings_field(
        'bs_wib_box_shadow_spread', // string used for 'id' in attribute tags
        'Spread Radius:', // Title of the field
        'bs_wib_box_shadow_spread_display', // callback that creates the input field
        'wp-image-borders', // $page ($menu_slug used in add_options_page)
        'bs_wib_box_shadows' // the section the field is placed in (the id from add_settings_section above)
    );
    add_settings_field(
        'bs_wib_box_shadow_color', // string used for 'id' in attribute tags
        'Box Shadow Color:', // Title of the field
        'bs_wib_box_shadow_color_display', // callback that creates the input field
        'wp-image-borders', // $page ($menu_slug used in add_options_page)
        'bs_wib_box_shadows' // the section the field is placed in (the id from add_settings_section above)
    );
}

// Adds text to remove borders section
function bs_wib_section_text() {
    echo '<p></p>';
}

// Adds text to border styles section
function bs_wib_borders_section_text() {
    echo '<p>Enter your custom border styles here:</p>';
}

// Adds text to box shadow section
function bs_wib_shadow_section_text() {
    echo '<p>Add drop shadows to your images here (Optional):</p>';
}

// adds the checkbox with it set to be already checked
function bs_wib_checkbox_display() {
    $options = get_option( 'wp_image_borders_options' );
    $html = '<input type="checkbox" id="bs_wib_checkbox" name="wp_image_borders_options[bs_wib_checkbox]" value="1"' . checked( 1, $options['bs_wib_checkbox'], false ) . '/>';  
    echo $html;
}

// adds the style drop-down
function bs_wib_border_style_display() {
    $options = get_option( 'wp_image_borders_options' );   
    $html = '<select id="bs_wib_border_style" name="wp_image_borders_options[bs_wib_border_style]">';  
        $html .= '<option value="default">Select a border style...</option>';  
        $html .= '<option value="solid"' . selected( $options['bs_wib_border_style'], 'solid', false) . '>Solid</option>';  
        $html .= '<option value="dotted"' . selected( $options['bs_wib_border_style'], 'dotted', false) . '>Dotted</option>';  
        $html .= '<option value="dashed"' . selected( $options['bs_wib_border_style'], 'dashed', false) . '>Dashed</option>';  
        $html .= '<option value="double"' . selected( $options['bs_wib_border_style'], 'double', false) . '>Double</option>';  
        $html .= '<option value="groove"' . selected( $options['bs_wib_border_style'], 'groove', false) . '>Groove</option>';  
        $html .= '<option value="ridge"' . selected( $options['bs_wib_border_style'], 'ridge', false) . '>Ridge</option>';  
        $html .= '<option value="inset"' . selected( $options['bs_wib_border_style'], 'inset', false) . '>Inset</option>';  
        $html .= '<option value="outset"' . selected( $options['bs_wib_border_style'], 'outset', false) . '>Outset</option>';  
    $html .= '</select>';  
    echo $html; 
}

// adds the border width option
function bs_wib_border_width_display() {
    $options = get_option( 'wp_image_borders_options' );
    if ( empty($options['bs_wib_border_width']) ) {
        $options['bs_wib_border_width'] = 0; 
    }
    $html = "<input type='number' id='bs_wib_border_width' min='0' name='wp_image_borders_options[bs_wib_border_width]' value='{$options['bs_wib_border_width']}' />";  
    echo $html;
}

// adds the border radius option
function bs_wib_border_radius_display() {
    $options = get_option( 'wp_image_borders_options' );
    if ( empty($options['bs_wib_border_radius']) ) {
        $options['bs_wib_border_radius'] = 0; 
    }
    $html = "<input type='number' min='0' id='bs_wib_border_radius' name='wp_image_borders_options[bs_wib_border_radius]' value='{$options['bs_wib_border_radius']}' />";  
    echo $html;
}

// Add the built-in WordPress Iris color picker
add_action( 'admin_enqueue_scripts', 'mw_enqueue_color_picker' );
function mw_enqueue_color_picker( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'color-picker', plugins_url('js/color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

// Adds the color picker field/form
function bs_wib_border_color_display() {
    $options = get_option( 'wp_image_borders_options' );
    $html = "<input id='bs_wib_border_color' name='wp_image_borders_options[bs_wib_border_color]' type='text' value='{$options['bs_wib_border_color']}' class='my-color-field' data-default-color='#000000' />";
    echo $html;
}

// Adds the horizontal box shadow field
function bs_wib_box_shadow_horizontal_display() {
    $options = get_option( 'wp_image_borders_options' );
    if ( empty($options['bs_wib_box_shadow_horizontal']) ) {
        $options['bs_wib_box_shadow_horizontal'] = 0; 
    }
    $html ="<input type='number' default='0' id='bs_wib_box_shadow_horizontal' name='wp_image_borders_options[bs_wib_box_shadow_horizontal]' value='{$options['bs_wib_box_shadow_horizontal']}' />";
    echo $html;
}

// Adds the vertical box shadow field
function bs_wib_box_shadow_vertical_display() {
    $options = get_option( 'wp_image_borders_options' );
    if ( empty($options['bs_wib_box_shadow_vertical']) ) {
        $options['bs_wib_box_shadow_vertical'] = 0; 
    }
    $html ="<input type='number' default='0' id='bs_wib_box_shadow_vertical' name='wp_image_borders_options[bs_wib_box_shadow_vertical]' value='{$options['bs_wib_box_shadow_vertical']}' />";
    echo $html;
}

// Adds the blur box shadow field
function bs_wib_box_shadow_blur_display() {
    $options = get_option( 'wp_image_borders_options' );
    if ( empty($options['bs_wib_box_shadow_blur']) ) {
        $options['bs_wib_box_shadow_blur'] = 0; 
    }
    $html ="<input type='number' default='0' min='0' id='bs_wib_box_shadow_blur' name='wp_image_borders_options[bs_wib_box_shadow_blur]' value='{$options['bs_wib_box_shadow_blur']}' />";
    echo $html;
}

// Adds the spread box shadow field
function bs_wib_box_shadow_spread_display() {
    $options = get_option( 'wp_image_borders_options' );
    if ( empty($options['bs_wib_box_shadow_spread']) ) {
        $options['bs_wib_box_shadow_spread'] = 0; 
    }
    $html ="<input type='number' default='0' min='0' id='bs_wib_box_shadow_spread' name='wp_image_borders_options[bs_wib_box_shadow_spread]' value='{$options['bs_wib_box_shadow_spread']}' />";
    echo $html;
}

// Adds the spread box shadow field
function bs_wib_box_shadow_color_display() {
    $options = get_option( 'wp_image_borders_options' );
    $html = "<input id='bs_wib_box_shadow_color' name='wp_image_borders_options[bs_wib_box_shadow_color]' type='text' value='{$options['bs_wib_box_shadow_color']}' class='my-color-field' data-default-color='#000000' />";
    echo $html;
}

// ATTENTION: Validation will need to be added back to register_setting and then defined here


add_action( 'wp_head', 'bs_wib_remove_borders' );

// checks to see if the checkbox to remove borders is checked
function bs_wib_remove_borders() {
    $options = get_option( 'wp_image_borders_options' );
    if ( 1 == $options['bs_wib_checkbox'] ) { 
        // adds style to header to remove borders
        echo '<style type="text/css" media="screen">.wib-img img { 
           border: none !important; 
           border-radius: 0px !important; 
           -moz-box-shadow: 0 0 0 0 #fff !important; 
           -webkit-box-shadow: 0 0 0 0 #fff !important; 
           box-shadow: 0 0 0 0 #fff !important;
           } </style>';
    } else {
        echo '<style type="text/css" media="screen">.wib-img img { 
           border-style: ' . $options["bs_wib_border_style"] . ' !important; 
           border-width: ' . $options["bs_wib_border_width"] . 'px !important; 
           border-radius: ' . $options["bs_wib_border_radius"] . 'px !important;
           border-color: ' . $options["bs_wib_border_color"] . ' !important; 
           -moz-box-shadow: ' . $options["bs_wib_box_shadow_horizontal"] . 'px ' . $options["bs_wib_box_shadow_vertical"] . 'px ' . $options["bs_wib_box_shadow_blur"] . 'px ' . $options["bs_wib_box_shadow_spread"] . 'px ' . $options["bs_wib_box_shadow_color"] . ' !important; 
           -webkit-box-shadow: ' . $options["bs_wib_box_shadow_horizontal"] . 'px ' . $options["bs_wib_box_shadow_vertical"] . 'px ' . $options["bs_wib_box_shadow_blur"] . 'px ' . $options["bs_wib_box_shadow_spread"] . 'px ' . $options["bs_wib_box_shadow_color"] . ' !important; 
           box-shadow: ' . $options["bs_wib_box_shadow_horizontal"] . 'px ' . $options["bs_wib_box_shadow_vertical"] . 'px ' . $options["bs_wib_box_shadow_blur"] . 'px ' . $options["bs_wib_box_shadow_spread"] . 'px ' . $options["bs_wib_box_shadow_color"] . ' !important; 
           } </style>';
    }
}

// Applies filter to add 'wib-img' class to all pages
        add_filter('post_class', 'bs_wib_class');
        // creates 'wib-img' class
        function bs_wib_class($classes) {
        $classes[] = 'wib-img';
        return $classes;
        }

// sanitizes the inputs from the options pages and outputs clean data
// ONLY does one checkbox right now
function bs_wib_validation( $input ) {
    
    // Create our array for storing the validated options
    $output = array();
    
    // Loop through each of the incoming options 
    foreach( $input as $key => $value ) {
    
        // Check to see if the current option has a value. If so, process it.  
        if( isset( $input[$key] ) ) {
        
            // Strip all HTML and PHP tags and properly handle quoted strings
            $output[$key] = strip_tags( stripslashes( $input[$key] ) );
        
        } // end if
    
    } // end foreach
    
    // Return the array processing any additional functions filtered by this action
    return apply_filters( 'bs_wib_validation', $output, $input );
}

?>