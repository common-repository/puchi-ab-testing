<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://enigmatheme.club
 * @since      1.0.0
 *
 * @package    Puchi
 * @subpackage Puchi/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Puchi
 * @subpackage Puchi/public
 * @author     Bayu Idham Fathurachman <bayu_idham@yahoo.com>
 */
class Puchi_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->controller = \Puchi\Controller::get_instance();
		
		add_shortcode('puchi', [$this,'split_test_content']);
	}
	
	public function set_visitor_cookie(){
		if(!isset($_COOKIE['puchi_data'])){
			//$ip = '165.227.237.58'; 
			$ip = $this->get_user_ip_address();
			$data = $this->process_user_geo_data([], $ip);
			$user_data_cookie = [
				'ip_address' => (isset($data['ip_address'])) ? $data['ip_address'] : 'none',
				'city' => (isset($data['city'])) ? $data['city'] : 'none',
				'country' => (isset($data['country'])) ? $data['country'] : 'none',
				'country_code' => (isset($data['country_code'])) ? $data['country_code'] : 'none'
			];
			setcookie('puchi_data',  wp_json_encode($user_data_cookie), time()+86400, COOKIEPATH, COOKIE_DOMAIN );
		}
	}
	
	private function process_user_geo_data($data, $ip){
		$data['ip_address'] = $ip;
		$user_data = $this->get_user_data_by_ip($ip);
		if($user_data){
			$data['city'] = ($user_data['geoplugin_city'] != '' ) ? $user_data['geoplugin_city'] : 'none' ;
			$data['country'] = ($user_data['geoplugin_countryName'] != '' ) ? $user_data['geoplugin_countryName'] : 'none' ;
			$data['country_code'] = ($user_data['geoplugin_countryCode'] != '' ) ? $user_data['geoplugin_countryCode'] : 'none' ;
		}
		
		return $data;
	}
	
	public function split_test_content($atts){
		$a = shortcode_atts( [ 'id' => ''], $atts );
		
		if($a['id'] != ''){
			return $this->process_split_test_content($a['id']);
		}
	}
	
	private function is_white_ip($ip){
		$setting = get_option('puchi_settings', []);
		if(isset($setting['white_ip']) && $setting['white_ip'] != ''){
			$white_ip =  array_filter(explode(',',preg_replace('/\s+/', '',  $setting['white_ip'] )));
			if(in_array($ip, $white_ip)){
				return true;
			}
		}
		return false;
	}
	
	private function process_split_test_content($id){
		$results = '';
		$content = $this->calculate_split_test_weight(get_post_meta($id, 'split_item', true));
		if(is_array($content)):
			ob_start();
			//$ip = '165.227.237.58'; 
			$ip = $this->get_user_ip_address();
			if($ip != '' && !$this->is_white_ip($ip)):
				$data = [
					'page_id' => get_the_ID(),
					'split_id' => (int)$id,
					'content_title' => $content['title'],
					'tracker' => $content['tracker'],
					'weight' => (int)$content['weight'],
					'country' => 'none',
					'city' => 'none',
					'country_code' => 'none',
					'ip_address' => 'none'
				];
				if($ip != ''){
					if(isset($_COOKIE['puchi_data']) && $_COOKIE['puchi_data'] != ''){
						$puchi_data = json_decode(stripslashes($_COOKIE['puchi_data']), true);
						$data['ip_address'] = (isset($puchi_data['ip_address'])) ? $puchi_data['ip_address'] : 'none';
						$data['city'] = (isset($puchi_data['city'])) ? $puchi_data['city'] : 'none';
						$data['country'] = (isset($puchi_data['country'])) ? $puchi_data['country'] : 'none';
						$data['country_code'] = (isset($puchi_data['country_code'])) ? $puchi_data['country_code'] : 'none';
					}else{
						$data = $this->process_user_geo_data($data, $ip);
					}
				}
				$this->controller->add_split_data($data);
			?>
				<span class="puchi-content" data-puchi='<?php  echo base64_encode(wp_json_encode($data));?>'>
					<?php echo do_shortcode($content['content']);?>
				</span><!-- end of puchi content -->
			<?php
			else:
				echo do_shortcode($content['content']);
			endif;
			$results = ob_get_contents();
			ob_end_clean();
		endif;
		return $results;
	}
	
	private function calculate_split_test_weight($content){
		if (is_array($content) && !empty($content)) {
			$rand = mt_rand(0, 100);
			foreach ($content as $c) {
				$rand -= (int)$c['chance'];
				if ($rand <= 0) {
					return [
						'title' => isset($c['title']) ? $c['title'] : '',
						'content' => isset($c['content']) ? $c['content'] : '',
						'tracker' => isset($c['tracker']) ? $c['tracker'] : '',
						'weight' => isset($c['chance']) ? $c['chance'] : ''
					];
				}
			}
		}
		return false;
	}
	
	private function get_user_ip_address(){
		$ip = '';
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
		}
		if( strpos( $_SERVER['REMOTE_ADDR'], ',') !== false ) {
			$one_ip = explode(",",$_SERVER['REMOTE_ADDR']);
			$ip = $one_ip[0];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	private function get_user_data_by_ip($ip){
		$data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip={$ip}"), true);
		return $data;
	}
	
	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/puchi-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		$min = (pch_compress()) ? '.min' : '';
		wp_enqueue_script( 'puchi_base64', plugin_dir_url( __FILE__ ) . 'js/base64.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/puchi-public'.$min.'.js', array( 'jquery' ), $this->version, true );
		$jsobj = [
			'api_url'   => site_url('/wp-json/puchi/v1/'),
			'nonce' => wp_create_nonce( 'wp_rest' )
		];
		wp_localize_script( $this->plugin_name, 'puchi_data', $jsobj);

	}

}
