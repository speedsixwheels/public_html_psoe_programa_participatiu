<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {
	$child_style_path = get_stylesheet_directory() . '/style.css';
	$child_style_ver  = file_exists( $child_style_path ) ? filemtime( $child_style_path ) : CHILD_THEME_ASTRA_CHILD_VERSION;

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array( 'astra-theme-css' ), $child_style_ver, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 999 );

  
get_template_part('components/functions/index');
get_template_part('components/functions/gravity/index');
get_template_part('components/shortcodes/gravity/index'); 