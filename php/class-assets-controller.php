<?php
namespace mkdo\ecommerce_fields_for_gravity_forms;
/**
 * Class Assets_Controller
 *
 * Sets up the JS and CSS needed for this plugin
 *
 * @package mkdo\ecommerce_fields_for_gravity_forms
 */
class Assets_Controller {

	private $options_prefix;

	/**
	 * Constructor
	 */
	function __construct( Plugin_Options $plugin_options ) {
		$this->options_prefix = $plugin_options->get_options_prefix();
	}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}


	/**
	 * Enqueue Scripts
	 */
	public function wp_enqueue_scripts() {

		$prefix        = $this->options_prefix;
		$default_value = '';

		if ( is_multisite() && get_current_blog_id() != SITE_ID_CURRENT_SITE ) {
			switch_to_blog( SITE_ID_CURRENT_SITE );
			$default_value = get_option(
				$prefix . 'google_maps_api_key',
				$default_value
			);
			restore_current_blog();
		}

		$google_api_key = get_option(
			$prefix . 'google_maps_api_key',
			$default_value
		);

		$default_value = '';

		if ( is_multisite() && get_current_blog_id() != SITE_ID_CURRENT_SITE ) {
			switch_to_blog( SITE_ID_CURRENT_SITE );
			$default_value = get_option(
				$prefix . 'pca_api_key',
				$default_value
			);
			restore_current_blog();
		}

		$pca_api_key = get_option(
			$prefix . 'pca_api_key',
			$default_value
		);

		$enqueued_assets = get_option(
			$prefix . 'enqueue_front_end_assets',
			array(
				'google_maps_api_js',
				'plugin_css',
				'plugin_js',
			)
		);

		// If no assets are enqueued
		// prevent errors by declaring the variable as an array
		if ( ! is_array( $enqueued_assets ) ) {
			$enqueued_assets = array();
		}

		// CSS
		if ( in_array( 'plugin_css', $enqueued_assets ) ) {
			$plugin_css_url = plugins_url( 'css/plugin.css', MKDO_EFFGF_ROOT );
			wp_enqueue_style( MKDO_EFFGF_TEXT_DOMAIN, $plugin_css_url, array(), MKDO_EFFGF_VERSION, false );
		}

		// JS
		if ( in_array( 'plugin_js', $enqueued_assets ) ) {
			$plugin_js_url  = plugins_url( 'js/plugin.js', MKDO_EFFGF_ROOT );
			wp_enqueue_script( MKDO_EFFGF_TEXT_DOMAIN, $plugin_js_url, array( 'jquery' ), MKDO_EFFGF_VERSION, true );
		}

		if ( ! empty( $google_api_key ) ) {
			wp_localize_script( MKDO_EFFGF_TEXT_DOMAIN, 'google_api_key', $google_api_key );
		}

		if ( ! empty( $pca_api_key ) ) {
			wp_localize_script( MKDO_EFFGF_TEXT_DOMAIN, 'pca_api_key', $pca_api_key );
		}

		// Google Maps API
		if ( in_array( 'google_maps_api_js', $enqueued_assets ) ) {
			$google_maps_api_url = '//maps.google.com/maps/api/js?v=3&language=en&libraries=places&key=' . $google_api_key;
			wp_enqueue_script( 'google-maps', $google_maps_api_url, array( 'jquery' ), '', true );
		}
	}

	/**
	 * Enqueue Admin Scripts
	 */
	public function admin_enqueue_scripts() {

		$prefix = $this->options_prefix;

		$google_api_key = get_option(
			$prefix . 'google_maps_api_key',
			''
		);

		$enqueued_assets = get_option(
			$prefix . 'enqueue_back_end_assets',
			array(
				'google_maps_api_js',
				'plugin_admin_css',
				'plugin_admin_js',
			)
		);

		// If no assets are enqueued
		// prevent errors by declaring the variable as an array
		if ( ! is_array( $enqueued_assets ) ) {
			$enqueued_assets = array();
		}

		// CSS
		if ( in_array( 'plugin_admin_css', $enqueued_assets ) ) {
			$plugin_css_url = plugins_url( 'css/plugin-admin.css', MKDO_EFFGF_ROOT );
			wp_enqueue_style( MKDO_EFFGF_TEXT_DOMAIN . '-admin', $plugin_css_url, array(), MKDO_EFFGF_VERSION, false );
		}

		// JS
		if ( in_array( 'plugin_admin_js', $enqueued_assets ) ) {
			$plugin_js_url  = plugins_url( 'js/plugin-admin.js', MKDO_EFFGF_ROOT );
			wp_enqueue_script( MKDO_EFFGF_TEXT_DOMAIN . '-admin', $plugin_js_url, array( 'jquery' ), MKDO_EFFGF_VERSION, true );
		}

		// Google Maps API
		if ( in_array( 'google_maps_api_js', $enqueued_assets ) ) {
			$google_maps_api_url = '//maps.google.com/maps/api/js?v=3&language=en&libraries=places&key=' . $google_api_key;
			wp_enqueue_script( 'google-maps', $google_maps_api_url, array( 'jquery' ), '', true );
		}
	}
}
