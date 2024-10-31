<?php
/**
 * Include and setup custom metaboxes and fields. (make sure you copy this file to outside the CMB directory)
 *
 * Be sure to replace all instances of 'puchi_' with your project's prefix.
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 *
 * @category YourThemeOrPlugin
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */
/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */
if ( file_exists( dirname( __FILE__ ) . '/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/init.php';
}
/**
 * Conditionally displays a field when used as a callback in the 'show_on_cb' field parameter
 *
 * @param  CMB2_Field object $field Field object
 *
 * @return bool                     True if metabox should show
 */
function prefix_sanitize_text_callback( $value, $field_args, $field ) {
	/*
	 * Do your custom sanitization. 
	 * strip_tags can allow whitelisted tags
	 * http://php.net/manual/en/function.strip-tags.php
	 */
	$value = strip_tags( $value, '<b></b><span><span><br><br/><small></small><a></a><strong></strong><div></div><iframe></iframe><sup></sup><b></b><form></form><input>' );
	
	return $value;
}
/**
 * Conditionally displays a message if the $post_id is 2
 *
 * @param  array             $field_args Array of field parameters
 * @param  CMB2_Field object $field      Field object
 */
function puchi_before_row_if_2( $field_args, $field ) {
	if ( 2 == $field->object_id ) {
		echo '<p>Testing <b>"before_row"</b> parameter (on $post_id 2)</p>';
	} else {
		echo '<p>Testing <b>"before_row"</b> parameter (<b>NOT</b> on $post_id 2)</p>';
	}
}
function puchi_show_page_meta($cmb) {
	$page = get_page_template_slug( $cmb->object_id() );
	if($page == true || empty($page)){
		if( empty($page) ){
			return true;
		}
	}
	return false;
}
function cmb2_tabs_show_if_front_page($cmb) {
    // Don't show this metabox if it's not the front page template.
    if (get_option('page_on_front') !== $cmb->object_id) {
        return false;
    }
    return true;
}

//SPLIT TEST SHORTCODE META
include dirname(__FILE__) .'/fields/meta-split-shortcode.php';

add_action( 'cmb2_init', 'puchi_register_metabox' );
function puchi_register_metabox() {
	
	//SPLIT TEST META
	include dirname(__FILE__) .'/fields/meta-split.php';
	
}