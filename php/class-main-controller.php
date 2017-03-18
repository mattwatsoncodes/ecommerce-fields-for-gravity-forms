<?php

namespace mkdo\ecommerce_fields_for_gravity_forms;

/**
 * Class Main_Controller
 *
 * The main loader for this plugin
 *
 * @package mkdo\ecommerce_fields_for_gravity_forms
 */
class Main_Controller {

	private $plugin_options;
	private $assets_controller;
	private $admin_notices;
	private $woo_product_field;
	private $application_submission;

	/**
	 * Constructor
	 *
	 * @param Options            $options              Object defining the options page
	 * @param AssetsController   $assets_controller    Object to load the assets
	 */
	public function __construct(
		Plugin_Options $plugin_options,
		Assets_Controller $assets_controller,
		Admin_Notices $admin_notices,
		Woo_Product_Field $woo_product_field,
		Application_Submission $application_submission
	) {
		$this->plugin_options          = $plugin_options;
        $this->assets_controller       = $assets_controller;
		$this->admin_notices           = $admin_notices;
		$this->woo_product_field       = $woo_product_field;
		$this->application_submission  = $application_submission;
	}

	/**
	 * Do Work
	 */
	public function run() {
		load_plugin_textdomain( MKDO_EFFGF_TEXT_DOMAIN, false, MKDO_EFFGF_ROOT . '\languages' );
		$this->plugin_options->run();
		$this->assets_controller->run();
		$this->admin_notices->run();
		$this->woo_product_field->run();
		$this->application_submission->run();
	}
}
