<?php
namespace mkdo\ecommerce_fields_for_gravity_forms;
/**
 * Class Application_Submission
 *
 * Does the application submission
 *
 * @package mkdo\ecommerce_fields_for_gravity_forms
 */
class Application_Submission {

	/**
	 * Constructor
	 */
	function __construct() {
	}

	/**
	 * Do Work
	 */
	public function run() {
		if ( ! is_main_site( get_current_blog_id() ) ) {
			add_action( 'gform_after_submission', array( $this, 'after_submission' ), 10, 2 );
		}
	}

	/**
	 * Form Submission
	 */
	public function after_submission( $entry, $form ) {

		global $woocommerce;

		$woocommerce->cart->empty_cart();
		$meta  = \GFFormsModel::get_form_meta( $entry['form_id'] );

		foreach ( $meta['fields'] as $field ) {
			if ( 'woo_product' === $field['type'] ) {
				$id = $field['id'];
				$product_quantity = $entry[ $id . '.5' ];
				$product_id = $entry[ $id . '.1' ];
				if ( $product_quantity > 0 ) {
					$woocommerce->cart->add_to_cart( $product_id, $product_quantity );
				}
			}
		}

		$prefix = '_objective_licensing_organisation_';
		$user_id = get_current_user_id();

		switch_to_blog( BLOG_ID_CURRENT_SITE );
		$organisation_id                   = get_user_meta( get_current_user_id(), 'objective_licensing_user_associated_organisation', true );
		$title                             = esc_html( get_the_title( $organisation_id ) );
		$registered_name                   = esc_html( get_post_meta( $organisation_id, $prefix . 'registered_name', true ) );
		$company_registeration_number      = esc_html( get_post_meta( $organisation_id, $prefix . 'company_registeration_number', true ) );
		$postcode                          = esc_html( get_post_meta( $organisation_id, $prefix . 'postcode', true ) );
		$address                           = get_post_meta( $organisation_id, $prefix . 'address', true );
		$first_name                        = esc_html( get_post_meta( $organisation_id, $prefix . 'user_first_name', true ) );
		$last_name                         = esc_html( get_post_meta( $organisation_id, $prefix . 'user_last_name', true ) );
		$telephone                         = esc_html( get_post_meta( $organisation_id, $prefix . 'telephone', true ) );
		$telephone_24                      = esc_html( get_post_meta( $organisation_id, $prefix . 'telephone_24', true ) );
		$email                             = sanitize_email( get_post_meta( $organisation_id, $prefix . 'email', true ) );
		$waste_licence_registration_number = esc_html( get_post_meta( $organisation_id, $prefix . 'waste_licence_registration_number', true ) );
		$city                              = esc_html( get_post_meta( $organisation_id, $prefix . 'city', true ) );
		$province                          = esc_html( get_post_meta( $organisation_id, $prefix . 'province', true ) );
		restore_current_blog();

		$address = implode( ', ', array_map( 'sanitize_text_field', explode( "\n", $address ) ) );
		$address = str_replace( $city . ',', '', $address );
		$address = str_replace( $province . ',', '', $address );
		$address = str_replace( $postcode, '', $address );

		update_user_meta( $user_id, 'billing_first_name', $first_name );
		update_user_meta( $user_id, 'shipping_first_name', $first_name );
		update_user_meta( $user_id, 'billing_last_name', $last_name );
		update_user_meta( $user_id, 'shipping_last_name', $last_name );
		update_user_meta( $user_id, 'billing_company', $registered_name );
		update_user_meta( $user_id, 'shipping_company', $registered_name );
		update_user_meta( $user_id, 'billing_address_1', $address );
		update_user_meta( $user_id, 'shipping_address_1', implode( ', ', array_map( 'sanitize_text_field', explode( "\n", $address ) ) ) );
		update_user_meta( $user_id, 'billing_city', esc_html( $city ) );
		update_user_meta( $user_id, 'shipping_city', esc_html( $city ) );
		update_user_meta( $user_id, 'billing_state', esc_html( $province ) );
		update_user_meta( $user_id, 'shipping_state', esc_html( $province ) );
		update_user_meta( $user_id, 'billing_postcode', $postcode );
		update_user_meta( $user_id, 'shipping_postcode', $postcode );
		update_user_meta( $user_id, 'billing_email', $email );
		update_user_meta( $user_id, 'billing_phone', $telephone );
	}
}
