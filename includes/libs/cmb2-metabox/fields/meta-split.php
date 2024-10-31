<?php


//META FUNNEL
$split = new_cmb2_box( [
        'id'            => 'puchi_split_content',
        'title'         => __( 'Split Test Content', 'puchi' ),
        'object_types'  => [ 'puchi-split-test'], // Post type
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true, // Show field names on the left
        // 'cmb_styles' => false, // false to disable the CMB stylesheet
        // 'closed'     => true, // true to keep the metabox closed by default
] );
$split_item = $split->add_field( [
        'id'          => 'split_item',
        'type'        => 'group',
        'repeatable'  => true, // use false if you want non-repeatable group
        'options'     => [
                'group_title'   => __( 'Content {#}', 'puchi' ), // since version 1.1.4, {#} gets replaced by row number
                'add_button'    => __( 'Add Another Content', 'puchi' ),
                'remove_button' => __( 'Remove Content', 'puchi' ),
                'sortable'      => false, // beta
                'closed'     => false, // true to have the groups closed by default
        ]
] );

$split->add_group_field( $split_item, [
        'name' => __('Title','puchi'),
        'id'      => 'title',
        'type'    => 'text',
        'default' => '',
        'sanitization_cb' => 'prefix_sanitize_text_callback'
]);

$split->add_group_field( $split_item, [
	'name' => __( 'Weight', 'puchi' ),
	'desc' => __( '%. Numerical weight percentage.', 'puchi' ),
	'default' => '100',
	'id'   => 'chance',
	'type' => 'text_small',
	'attributes' => [
		'type' => 'number',
		'pattern' => '\d*',
	],
	'sanitization_cb' => 'absint',
        'escape_cb'       => 'absint',
] );

$split->add_group_field( $split_item, [
        'name'    => __('Content','puchi'),
        'id'      => 'content',
        'type'    => 'wysiwyg',
        'options' => [
		'wpautop' => false, // use wpautop?
		'media_buttons' => false, // show insert/upload button(s)
		//'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
		'textarea_rows' => 10, // rows="..."
		'tabindex' => '',
		'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the `<style>` tags, can use "scoped".
		'editor_class' => '', // add extra class(es) to the editor textarea
		'teeny' => false, // output the minimal editor config used in Press This
		'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
		'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
		'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
        ]
]);

$split->add_group_field( $split_item, [
	'name'    => __('Tracking Trigger','puchi'),
	'desc'    => __('Track interaction/click from this element at the content.','puchi'),
	'id'      => 'tracker',
	'type'    => 'multicheck',
	'options' => [
		'anchor' => __('Anchor/Link Tag','puchi'),
		'button' => __('Button Tag','puchi'),
		'submit' => __('Input Submit Tag','puchi'),
	]
] );