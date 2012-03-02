<?php
/*
Plugin Name: FJL Legislator Search
Plugin URI: http://www.flapjacklabs.com/wordpress-legislator-search-plugin/
Description: Adds a widget that allows users to search for their congressional legislators by zip code.
Version: 0.1.0
Author: Flapjack Labs
Author URI: http://www.flapjacklabs.com
Author Email: jason@flapjacklabs.com
License: See LICENSE.txt
*/

//Sunlight Labs Library
require('lib'.DIRECTORY_SEPARATOR.'sunlight-php'.DIRECTORY_SEPARATOR.'class.sunlightlabs.php');

class LegislatorSearch {
	
	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/

	const name = 'Legislator Search';
	
	const slug = 'legislator-search';
	 
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {
	
	    // Define constants used throughout the plugin
	    $this->init_plugin_constants();
  
		load_plugin_textdomain( PLUGIN_LOCALE, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
		
    	// Load JavaScript and stylesheets
    	$this->register_scripts_and_styles();				
		
	    //Register actions
		add_action('widgets_init', create_function('', 'return register_widget("LegislatorSearchWidget");'));
		
		//Register ajax actions
		if(is_admin())
		{
			add_action('wp_ajax_legislator_search_ajax', array(&$this, 'legislator_search_ajax'));			
			add_action('wp_ajax_nopriv_legislator_search_ajax', array(&$this, 'legislator_search_ajax'));
		}		
		
	} // end constructor
	
	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/		
	
	/**
	 * Searches for congressional legislators by zip code using the Sunlight Labs API. 
	 */
	function legislator_search_ajax() {
		$widget_id = $_GET['widget_id'];
		$api_key   = get_option($widget_id.'-legislator_search_api_key', '');
		$zip       = $_GET['zip'];
		$params    = array(
			'zip' => $zip,
		);
		if(api_key != '')
		{
			$sl = new SunlightLegislator();
			$sl->api_key = $api_key;
			$params['results'] = $sl->legislatorZipCode($zip);			
		}
		$this->render('legislator-search-ajax', $params);
		die;
		//$this->render('ajax_search_legislators_by_zip', array());
	}
  
	/*--------------------------------------------*
	 * Private Functions
	 *---------------------------------------------*/
	
	/**
	 * Renders a view with parameters.
	 * 
	 * @view The view to render.
	 * @params The parameters to make available to the view.
	 */
	private function render($view, $params) {
		extract($params);		
		ob_start();
		require('views'.DIRECTORY_SEPARATOR.$view.'.php');
		$view = ob_get_contents();
		ob_end_clean();		
		echo $view;
	}
   
	/**
	 * Initializes constants used for convenience throughout 
	 * the plugin.
	 */
	private function init_plugin_constants() {
				
		if ( !defined( 'PLUGIN_NAME' ) ) {
		  define( 'PLUGIN_NAME', self::name );
		} // end if
				
		if ( !defined( 'PLUGIN_SLUG' ) ) {
		  define( 'PLUGIN_SLUG', self::slug );
		} // end if		
	
	} // end init_plugin_constants
	
	/**
	 * Registers and enqueues stylesheets for the administration panel and the
	 * public facing site.
	 */
	private function register_scripts_and_styles() {
		if ( is_admin() ) {
			$this->load_file( self::slug . '-admin-script', '/js/admin.js', true );
			$this->load_file( self::slug . '-admin-style', '/css/admin.css' );
		} else { 			
			$this->load_file( self::slug . '-style', '/css/widget.css' );
			$this->load_file( self::slug . '-jquery', '/js/jquery-1.7.1.min.js', true);
			$this->load_file( self::slug . '-script', '/js/widget.js', true );
			wp_localize_script( self::slug . '-script', 'legislator_search', array(
				'ajaxurl' => get_settings('siteurl') . '/wp-admin/admin-ajax.php',
			));
		} // end if/else
	} // end register_scripts_and_styles
	
	/**
	 * Helper function for registering and enqueueing scripts and styles.
	 *
	 * @name	The 	ID to register with WordPress
	 * @file_path		The path to the actual file
	 * @is_script		Optional argument for if the incoming file_path is a JavaScript source file.
	 */
	private function load_file( $name, $file_path, $is_script = false ) {
		
		$url = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery') );
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			} // end if
		} // end if
    
	} // end load_file
  
} // end class

// Instantiate the plugin
new LegislatorSearch();

class FjlWidget extends WP_Widget {
	private $slug;
	
	function FjlWidget($name, $slug, $desc = '')
	{
		$this->slug = $slug;
		parent::WP_Widget(false, $name, array(
			'description' => $desc,
		));
	}
		
	function render($view, $params = array())
	{		
		extract($params);		
		ob_start();
		require('views'.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.$this->slug.DIRECTORY_SEPARATOR.$view.'.php');
		$view = ob_get_contents();
		ob_end_clean();		
		echo $view;		
	}
}

class LegislatorSearchWidget extends FjlWidget {
	const slug = 'legislator-search';
	const name = 'Legislator Search';
	const desc = 'Use this widget to allow users search for their U.S. congressional legislators.';
	
	function LegislatorSearchWidget() {
		parent::FjlWidget(self::name, self::slug, self::desc);		
	}

	function widget($args, $instance) {
		extract($args);						
		update_option($args['widget_id'].'-legislator_search_api_key', $instance['api_key']);
		$params   = array(
			'title'         => apply_filters('widget_title', $instance['title']),
			'before_widget' => $before_widget,
			'after_widget'  => $after_widget,
			'before_title'  => $before_title,
			'after_title'   => $after_title,
			'widget'        => $this,
		);				
		$this->render('display', $params);
	}
			
	function update($new_instance, $old_instance) {
		$instance            = $old_instance;
		$instance['title']   = strip_tags($new_instance['title']);			
		$instance['api_key'] = strip_tags($new_instance['api_key']);				
		return $instance;	
	}
	
	function form($instance) {		
		$this->render('form', array(
			'title'   => $instance['title'],
			'api_key' => $instance['api_key'],
			'widget'  => $this,
		));		
	}
}

?>