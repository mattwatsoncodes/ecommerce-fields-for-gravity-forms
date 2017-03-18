<?php
namespace mkdo\ecommerce_fields_for_gravity_forms;
/**
 * Class Admin_Notices
 *
 * Notifies the user if the admin needs attention
 *
 * @package mkdo\ecommerce_fields_for_gravity_forms
 */
class Admin_Notices {

	private $options_prefix;
	private $plugin_settings_url;

	/**
	 * Constructor
	 */
	function __construct( Plugin_Options $plugin_options ) {
		$this->options_prefix      = $plugin_options->get_options_prefix();
		$this->plugin_settings_url = $plugin_options->get_plugin_settings_url();
	}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Do Admin Notifications
	 */
	public function admin_notices() {

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

		$enqueued_front_end_assets = get_option(
			$prefix . 'enqueue_front_end_assets',
			array(
				'google_maps_api_js',
				'plugin_css',
				'plugin_js',
			)
		);

		$enqueued_back_end_assets = get_option(
			$prefix . 'enqueue_back_end_assets',
			array(
				'google_maps_api_js',
				'plugin_admin_css',
				'plugin_admin_js',
			)
		);

		// If no assets are enqueued
		// prevent errors by declaring the variable as an array
		if ( ! is_array( $enqueued_front_end_assets ) ) {
			$enqueued_front_end_assets = array();
		}

		if ( ! is_array( $enqueued_back_end_assets ) ) {
			$enqueued_back_end_assets = array();
		}


		if ( ! class_exists( 'GFFormsModel', false ) ) {
			$gravity_forms_url = 'http://www.gravityforms.com/';
			?>
			<div class="notice notice-warning is-dismissible">
			<p>
			<?php _e( sprintf( 'The %sLicence Type Field for Gravity Forms%s plugin requires that you %sinstall and activate the Gravity Forms plugin%s.', '<strong>', '</strong>', '<a href="' . $gravity_forms_url . '" target="_blank">', '</a>' ) , MKDO_EFFGF_TEXT_DOMAIN ); ?>
			</p>
			</div>
			<?php
		}
	}
}
