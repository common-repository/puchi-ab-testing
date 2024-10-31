<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://enigmatheme.club
 * @since      1.0.0
 *
 * @package    Puchi
 * @subpackage Puchi/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Puchi
 * @subpackage Puchi/admin
 * @author     Bayu Idham Fathurachman <bayu_idham@yahoo.com>
 */
class Puchi_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	
	/**
	 * Controller API for the plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var    class    $controller    Use to accress API
	 */
	
	private $controller;
	
	/**
	 * Compress assets for the plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var    class    $compress_assets for compressing assets
	 */
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->controller = \Puchi\Controller::get_instance();
		
	}
	
	public function add_admin_menu(){
		add_menu_page(
		    __('Puchi', 'puchi'),
		    __('Puchi', 'puchi'),
		    'manage_options',
		    'puchi_dashboard',
		    [$this, 'show_admin_menu'],
		    plugin_dir_url(dirname(__FILE__)) .'admin/img/puchi-icon.png', 40);
		
		add_submenu_page(
			'puchi_dashboard',
			__('Statistic', 'puchi'),
			__('Statistic', 'puchi'),
			'manage_options',
			'puchi_statistic',
			[$this, 'statistic_view']);
		
		add_submenu_page(
			'puchi_dashboard',
			__('Settings', 'puchi'),
			__('Settings', 'puchi'),
			'manage_options',
			'puchi_setting',
			[$this, 'setting_view']);

	}
	
	public function show_admin_menu(){}
	
	public function statistic_view(){
		require_once(plugin_dir_path(__FILE__) . 'partials/statistic.php');
	}
	
	public function setting_view(){
		require_once(plugin_dir_path(__FILE__) . 'partials/setting.php');
	}
	
	public function create_post_type(){
		$labels = array(
			'name' => _x('Split Test', 'post type general name', 'puchi'),
			'singular_name' => _x('Split Test', 'post type singular name', 'puchi'),
			'menu_name' => _x('Split Test', 'admin menu', 'puchi'),
			'name_admin_bar' => _x('Split Test', 'add new on admin bar', 'puchi'),
			'add_new' => _x('Add New', 'puchi', 'puchi'),
			'add_new_item' => __('Add New Split Test', 'puchi'),
			'new_item' => __('New Split Test', 'puchi'),
			'edit_item' => __('Edit Split Test', 'puchi'),
			'view_item' => __('View Split Test', 'puchi'),
			'all_items' => __('Split Test', 'puchi'),
			'search_items' => __('Search Split Test', 'puchi'),
			'parent_item_colon' => __('Parent Split Test:', 'puchi'),
			'not_found' => __('No Split Test found.', 'puchi'),
			'not_found_in_trash' => __('No Split Test found in Trash.', 'puchi')
		);
	    
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => 'puchi_dashboard',
			'menu_icon' => 'dashicons-building',
			'query_var' => true,
			'rewrite' => array('slug' => 'puchi-split-test'),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => 8,
			'supports' => array('title')
		);
	    
		register_post_type('puchi-split-test', $args);
		flush_rewrite_rules();
	}
	
	public function split_test_shortcode_column_head($defaults){
		$defaults['shortcode'] = 'Shortcode';
		return $defaults;
	}
	
	public function split_test_shortcode_column_content($column_name, $post_id){
		if($column_name == 'shortcode'){
			$title = get_the_title($post_id);
			$shortcode = '[puchi id="'.$post_id.'" title="'.$title.'"]';
			echo "<input style='max-width:400px' class='widefat' type='text' value='$shortcode' />";
		}
	}
	
	public function before_delete_page($post_id){
		return $this->controller->delete_statistic_data($post_id);
	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Puchi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Puchi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$min = (pch_compress()) ? '.min' : '';
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/puchi-admin'.$min.'.css', array(), $this->version, 'all' );
		$screen = get_current_screen();
		if( 'puchi_page_puchi_statistic' == $screen->id || 'puchi_page_puchi_setting' == $screen->id){
			wp_enqueue_style( 'puchi_icon', plugin_dir_url( __FILE__ ) . 'css/icon'.$min.'.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'puchi_confirm', plugin_dir_url( __FILE__ ) . 'css/confirm.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'puchi_chart', plugin_dir_url( __FILE__ ) . 'css/Chart.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'puchi_datepicker', plugin_dir_url( __FILE__ ) . 'css/datepicker'.$min.'.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'puchi_statistic', plugin_dir_url( __FILE__ ) . 'css/statistic'.$min.'.css', array(), $this->version, 'all' );
		}
		
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Puchi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Puchi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$screen = get_current_screen();
		$min = (pch_compress()) ? '.min' : '';
		if( 'puchi_page_puchi_statistic' == $screen->id || 'puchi_page_puchi_setting' == $screen->id){
			
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-datepicker');
			
			wp_enqueue_script( 'puchi_tabel' , plugin_dir_url( __FILE__ ) . 'js/data-table.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'puchi_confirm', plugin_dir_url( __FILE__ ) . 'js/confirm.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'puchi_scrollbar', plugin_dir_url( __FILE__ ) . 'js/scrollbar.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'puchi_chart', plugin_dir_url( __FILE__ ) . 'js/Chart.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'puchi_statistic', plugin_dir_url( __FILE__ ) . 'js/statistic'.$min.'.js', array( 'jquery' ), $this->version, true );
		}
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/puchi-admin'.$min.'.js', array( 'jquery' ), $this->version, true );
		$jsobj = [
			'api_url'   => site_url('/wp-json/puchi/v1/'),
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'lang' => [
				'confirm' => __('Confirm', 'puchi'),
				's1' =>  __('Are you sure you want to delete', 'puchi'),
				's2' =>  __('statistic data?', 'puchi')
			]
		];
		wp_localize_script( $this->plugin_name, 'puchi_data', $jsobj);
	}
}
