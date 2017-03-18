<?php

/**
 * @link              https://github.com/mkdo/ecommerce-fields-for-gravity-forms
 * @package           mkdo\ecommerce_fields_for_gravity_forms
 *
 * Plugin Name:       Ecommerce Fields for Gravity Forms
 * Plugin URI:        https://github.com/mkdo/ecommerce-fields-for-gravity-forms
 * Description:       Location Fields designed to work with Gravity Forms
 * Version:           1.0.0
 * Author:            Make Do <hello@makedo.net>
 * Author URI:        http://www.makedo.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ecommerce-fields-for-gravity-forms
 * Domain Path:       /languages
 */

// Use Namespaces
use mkdo\ecommerce_fields_for_gravity_forms\Helper;
use mkdo\ecommerce_fields_for_gravity_forms\Main_Controller;
use mkdo\ecommerce_fields_for_gravity_forms\Plugin_Options;
use mkdo\ecommerce_fields_for_gravity_forms\Assets_Controller;
use mkdo\ecommerce_fields_for_gravity_forms\Admin_Notices;
use mkdo\ecommerce_fields_for_gravity_forms\Woo_Product_Field;
use mkdo\ecommerce_fields_for_gravity_forms\Application_Submission;

add_action( 'plugins_loaded', function() {

	// Constants
	define( 'MKDO_EFFGF_ROOT', __FILE__ );
	define( 'MKDO_EFFGF_VERSION', '1.0.1' );
	define( 'MKDO_EFFGF_TEXT_DOMAIN', 'ecommerce-fields-for-gravity-forms' );

	// Load Classes
	require_once 'php/class-helper.php';
	require_once 'php/class-main-controller.php';
	require_once 'php/class-plugin-options.php';
	require_once 'php/class-assets-controller.php';
	require_once 'php/class-admin-notices.php';
	require_once 'php/class-woo-product-field.php';
	require_once 'php/class-application-submission.php';

	// Initialize Classes
	$plugin_options         = new Helper();
	$plugin_options         = new Plugin_Options();
	$assets_controller      = new Assets_Controller( $plugin_options );
	$admin_notices          = new Admin_Notices( $plugin_options );
	$woo_product_field      = new Woo_Product_Field();
	$application_submission = new Application_Submission();
	$main_controller        = new Main_Controller(
		$plugin_options,
		$assets_controller,
		$admin_notices,
		$woo_product_field,
		$application_submission
	);

	// Run the Plugin
	$main_controller->run();

} );
