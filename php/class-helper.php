<?php

namespace mkdo\ecommerce_fields_for_gravity_forms;

/**
 * Class Helper
 *
 * The helper for this plugin
 *
 * @package mkdo\ecommerce_fields_for_gravity_forms
 */
class Helper {

	/**
	 * Constructor
	 */
	public function __construct() {

	}

	public static function get_product_by_sku( $sku ) {
	    global $wpdb;
	    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );
	    if ( $product_id ) {
	        return new \WC_Product( $product_id );
	    }
	    return null;
	}
}
