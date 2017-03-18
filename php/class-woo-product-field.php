<?php
namespace mkdo\ecommerce_fields_for_gravity_forms;
/**
 * Class Woo_Product_Field
 *
 * The Choose Location Field for Gravity Forms
 *
 * @package mkdo\ecommerce_fields_for_gravity_forms
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Woo_Product_Field extends \GF_Field {

	public $type = 'woo_product';

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'gform_editor_js_set_default_values', array( $this, 'gform_editor_js_set_default_values' ) );
		add_action( 'gform_field_standard_settings', array( $this, 'gform_field_standard_settings' ) );
		add_filter( 'gform_tooltips', array( $this, 'gform_tooltips' ) );
		\GF_Fields::register( new \mkdo\ecommerce_fields_for_gravity_forms\Woo_Product_Field() );
	}

	/**
	 * Setup the form defaults
	 *
	 * This is where we need to define the label for the form, and also define any
	 * inputs (if a complex multi-input field).
	 *
	 * This function hooks into JS output in Gravity Forms so is a little odd to write.
	 */
	public function gform_editor_js_set_default_values() {
		?>
		case 'woo_product' :
			field.label = '<?php _e( 'Woo Product', MKDO_EFFGF_TEXT_DOMAIN ); ?>';
			field.inputs = [
				new Input( field.id + 0.1, "<?php _e( 'ID', MKDO_EFFGF_TEXT_DOMAIN ); ?>" ),
				new Input( field.id + 0.2, "<?php _e( 'SKU', MKDO_EFFGF_TEXT_DOMAIN ); ?>" ),
				new Input( field.id + 0.3, "<?php _e( 'Variable', MKDO_EFFGF_TEXT_DOMAIN ); ?>" ),
				new Input( field.id + 0.4, "<?php _e( 'Price', MKDO_EFFGF_TEXT_DOMAIN ); ?>" ),
				new Input( field.id + 0.5, "<?php _e( 'Quantity', MKDO_EFFGF_TEXT_DOMAIN ); ?>" ),
				new Input( field.id + 0.6, "<?php _e( 'Duration', MKDO_EFFGF_TEXT_DOMAIN ); ?>" ),
			];
		break;
		<?php
	}

	/**
	 * Add backend fields to Gravity Forms
	 *
	 * These are all handled with JavaScript, and the JS is handled in the
	 * get_form_editor_inline_script_on_page_render function
	 *
	 * You need to check the position, as if you ommit this the field loops
	 * in all positions in the backend.
	 *
	 * Note that the class on the li ('choose_location_setting' in this case) will
	 * be used in the 'get_form_editor_field_settings' function to allow us to
	 * use these settings
	 *
	 * @param  int      $position    The position of the field in the backend
	 * @return string                HTML output
	 */
	public function gform_field_standard_settings( $position ) {
		if ( 25 == $position  ) {
			$args = array( 'post_type' => 'product', 'posts_per_page' => '10' );
			$products = get_posts( $args );
			?>
			<li class="woo_product_setting field_setting">
				<label for="woo_product_admin_label" class="section_label">
					<?php _e( 'Choose Woo Product', TEXTDOMAIN ); ?>
					<?php gform_tooltip( 'woo_product' ) ?>
				</label>
				<select id="woo_product" onchange="SetFieldProperty('wooProduct', this.value); ToggleWooProduct(this.value);">
					<?php foreach ( $products as $product ) {
						$product = wc_get_product( $product );
						if ( 'simple' !== $product->product_type ) {
							$variations = $product->get_available_variations();
							?>
							<option value="<?php echo $product->get_sku();?>"><?php echo $product->get_title();?></option>
							<?php
							foreach ( $variations as $variation ) {
								?>
								<option value="<?php echo $variation['sku'];?>"><?php echo $product->get_title();?> - <?php echo $variation['variation_description'];?></option>
								<?php
							}
						} else {
							?>
							<option value="<?php echo $product->get_sku();?>"><?php echo $product->get_title();?></option>
							<?php
						}
					} ?>
				</select>
			</li>
			<?php
		}
	}

	/**
	 * Add inline script
	 *
	 * This lets us hook dynamically add our functions and bindings that will
	 * make our backend fields work
	 *
	 * @return String JavaScript output
	 */
	public function get_form_editor_inline_script_on_page_render() {
		$script = "
jQuery(document).bind( 'gform_load_field_settings', function( event, field, form ) {
    jQuery( '#woo_product').val( field.wooProduct == undefined ? '' : field.wooProduct );
});
function ToggleWooProduct( type ) {
    var field = GetSelectedField(),
        isSubmitButton = type == 'submit',
        defaultLabel = jQuery( '#woo_product option:selected' ).text();
    jQuery( '#field_label' ).val( defaultLabel );
    SetFieldProperty( 'label', defaultLabel );
}";
		return $script;
	}


	/**
	 * Form editor settings
	 *
	 * Add one or more backend settings here, make sure you add in your custom
	 * setting, in this case 'choose_location_setting'
	 *
	 * @return Array    An array of settings that the field uses in the backend
	 */
	function get_form_editor_field_settings() {
		return array(
			'woo_product_setting',
			'alternate_input_setting',
		    'conditional_logic_field_setting',
		    'prepopulate_field_setting',
		    'error_message_setting',
		    'label_setting',
		    //'sub_labels_setting',
		    //'label_placement_setting',
		    //'sub_label_placement_setting',
		    'admin_label_setting',
		    //'time_format_setting',
		    'rules_setting',
		    'visibility_setting',
		    //'duplicate_setting',
		    'default_inputs_setting',
		    //'input_placeholders_setting',
		    'description_setting',
		    'css_class_setting',
		);
	}

	/**
	 * Define any tool tips you have setup
	 *
	 * @param  Array   $tooltips    An array of tooltips
	 * @return Array                An array of tooltips
	 */
	public function gform_tooltips( $tooltips ) {
		$tooltips['woo_product'] = __( 'Choose a product from Woo Commerce', TEXTDOMAIN );

		return $tooltips;
	}

	/**
	 * Setup the form title
	 *
	 * @return String     The form title
	 */
	public function get_form_editor_field_title() {
		return esc_attr__( 'Woo Product', MKDO_EFFGF_TEXT_DOMAIN );
	}

	/**
	 * Is conditional logic supported?
	 *
	 * @return Boolean  True if conditoinal logic is supported
	 */
	public function is_conditional_logic_supported() {
		return true;
	}

	/**
	 * Create our form button
	 *
	 * @return Array    Form button details
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'pricing_fields',
			'text'  => $this->get_form_editor_field_title(),
		);
	}

	/**
	 * Validate the form
	 *
	 * This is left empty so that the $value does not get overridden
	 *
	 * @param  String/Array  $value The field value
	 * @param  Object        $form  The form
	 */
	function validate( $value, $form ) {}


	/**
	 * Render the field
	 *
	 * @param  Object        $form     The form object
	 * @param  String/Array  $value    The value of the field
	 * @param  Object        $entry    The entry value
	 * @return String                  HTML of the form
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {

		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$form_id         = absint( $form['id'] );
		$id              = intval( $this->id );
		$field_id        = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
		$class_suffix    = $is_entry_detail ? '_admin' : '';

		/**
		 * Code for sub-lable placements, I have overriden as this will always be hidden unless in the admin
		 */

		// $form_sub_label_placement  = rgar( $form, 'subLabelPlacement' );
		// $field_sub_label_placement = $this->subLabelPlacement;
		// $is_sub_label_above        = $field_sub_label_placement == 'above' || ( empty( $field_sub_label_placement ) && $form_sub_label_placement == 'above' );
		// $sub_label_class_attribute = $field_sub_label_placement == 'hidden_label' ? "class='hidden_sub_label screen-reader-text'" : '';

		$sub_label_class_attribute = is_admin() ? "class=''" : "class='hidden_sub_label screen-reader-text'";
		$disabled_text             = $is_form_editor ? "disabled='disabled'" : '';

		/**
		 * Grab the values from the value (should always be an array)
		 */
		$product_id          = null;
		$product_sku         = null;
		$product_is_variable = null;
		$product_price       = null;
		$product_quantity    = null;
		$product_duration    = null;

		if ( is_array( $value ) ) {
			$product_id          = esc_attr( \RGForms::get( $this->id . '.1', $value ) );
			$product_sku         = esc_attr( \RGForms::get( $this->id . '.2', $value ) );
			$product_is_variable = esc_attr( \RGForms::get( $this->id . '.3', $value ) );
			$product_price       = esc_attr( \RGForms::get( $this->id . '.4', $value ) );
			$product_quantity    = esc_attr( \RGForms::get( $this->id . '.5', $value ) );
			$product_duration    = esc_attr( \RGForms::get( $this->id . '.6', $value ) );
		}

		if ( empty( $product_sku ) ) {
			$product_sku = $this->wooProduct;

			if ( ! empty( $product_sku ) ) {
				$product = Helper::get_product_by_sku( $product_sku );

				if ( empty( $product_id ) ) {
					$product_id = $product->id;
				}

				if ( empty( $product_is_variable ) ) {
					$product_is_variable = ( 'product_variation' === $product->post->post_type ) ? 'true' : 'false';
				}

				if ( empty( $product_price ) ) {
					$product_price = $product->get_price();
				}

				if ( empty( $product_quantity ) ) {
					$product_quantity = '0';
				}
			}
		}
		
		$product_duration = get_post_meta( $product_id, 'attribute_duration', true );

		/**
		 * Set the field type, we want them hidden on the front end for this plugin
		 */
		$field_type = is_admin() ? 'text' : 'hidden';

		/**
		 * Get the input values
		 */
		$product_id_input          = \GFFormsModel::get_input( $this, $this->id . '.1' );
		$product_sku_input         = \GFFormsModel::get_input( $this, $this->id . '.2' );
		$product_is_variable_input = \GFFormsModel::get_input( $this, $this->id . '.3' );
		$product_price_input       = \GFFormsModel::get_input( $this, $this->id . '.4' );
		$product_quantity_input    = \GFFormsModel::get_input( $this, $this->id . '.5' );
		$product_duration_input    = \GFFormsModel::get_input( $this, $this->id . '.6' );

		/**
		 * Get the placeholder attributes (if set)
		 */
		$product_id_placeholder_attribute          = \GFCommon::get_input_placeholder_attribute( $product_id_input );
		$product_sku_placeholder_attribute         = \GFCommon::get_input_placeholder_attribute( $product_sku_input );
		$product_is_variable_placeholder_attribute = \GFCommon::get_input_placeholder_attribute( $product_is_variable_input );
		$product_price_placeholder_attribute       = \GFCommon::get_input_placeholder_attribute( $product_price_input );
		$product_quantity_placeholder_attribute    = \GFCommon::get_input_placeholder_attribute( $product_quantity_input );
		$product_duration_placeholder_attribute    = \GFCommon::get_input_placeholder_attribute( $product_duration_input );

		/**
		 * Get the tab indexes
		 */
		$product_id_tabindex          = $this->get_tabindex();
		$product_sku_tabindex         = $this->get_tabindex();
		$product_is_variable_tabindex = $this->get_tabindex();
		$product_price_tabindex       = $this->get_tabindex();
		$product_quantity_tabindex    = $this->get_tabindex();
		$product_duration_tabindex    = $this->get_tabindex();

		/**
		 * Set the labels (these could be manually set if the backend is configured)
		 */
		$product_id_label          = rgar( $product_id_input, 'customLabel' ) != '' ? $product_id_input['customLabel'] : gf_apply_filters( array( 'product_id', $form_id ), esc_html__( 'ID', MKDO_EFFGF_TEXT_DOMAIN ), $form_id );
		$product_sku_label         = rgar( $product_sku_input, 'customLabel' ) != '' ? $product_sku_input['customLabel'] : gf_apply_filters( array( 'product_sku', $form_id ), esc_html__( 'SKU', MKDO_EFFGF_TEXT_DOMAIN ), $form_id );
		$product_is_variable_label = rgar( $product_is_variable_input, 'customLabel' ) != '' ? $product_is_variable_input['customLabel'] : gf_apply_filters( array( 'product_is_variable', $form_id ), esc_html__( 'Variable', MKDO_EFFGF_TEXT_DOMAIN ), $form_id );
		$product_price_label       = rgar( $product_price_input, 'customLabel' ) != '' ? $product_price_input['customLabel'] : gf_apply_filters( array( 'product_price', $form_id ), esc_html__( 'Price', MKDO_EFFGF_TEXT_DOMAIN ), $form_id );
		$product_quantity_label    = rgar( $product_quantity_input, 'customLabel' ) != '' ? $product_quantity_input['customLabel'] : gf_apply_filters( array( 'product_quantity', $form_id ), esc_html__( 'Quantity', MKDO_EFFGF_TEXT_DOMAIN ), $form_id );
		$product_duration_label    = rgar( $product_duration_input, 'customLabel' ) != '' ? $product_duration_input['customLabel'] : gf_apply_filters( array( 'product_duration', $form_id ), esc_html__( 'Duration', MKDO_EFFGF_TEXT_DOMAIN ), $form_id );

		/**
		 * Create the labels and the fields
		 */
		$label1 = "<label for='{$field_id}_1' {$sub_label_class_attribute}>{$product_id_label}</label>";
		$label2 = "<label for='{$field_id}_2' {$sub_label_class_attribute}>{$product_sku_label}</label>";
		$label3 = "<label for='{$field_id}_3' {$sub_label_class_attribute}>{$product_is_variable_label}</label>";
		$label4 = "<label for='{$field_id}_4' {$sub_label_class_attribute}>{$product_price_label}</label>";
		$label5 = "<label for='{$field_id}_5' {$sub_label_class_attribute}>{$product_quantity_label}</label>";
		$label6 = "<label for='{$field_id}_6' {$sub_label_class_attribute}>{$product_duration_label}</label>";

		$input1 = "<input type='hidden' class='effgf-product_id' name='input_{$id}.1' id='{$field_id}_1' value='{$product_id}' {$product_id_tabindex}  {$disabled_text} {$product_id_placeholder_attribute} />";
		$input2 = "<input type='hidden' class='effgf-product_sku' name='input_{$id}.2' id='{$field_id}_2' value='{$product_sku}' {$product_sku_tabindex}  {$disabled_text} {$product_sku_placeholder_attribute} />";
		$input3 = "<input type='hidden' class='effgf-product_is_variable' name='input_{$id}.3' id='{$field_id}_3' value='{$product_is_variable}' {$product_is_variable_tabindex}  {$disabled_text} {$product_is_variable_placeholder_attribute} />";
		$input4 = "<input type='hidden' class='effgf-product_price' name='input_{$id}.4' id='{$field_id}_4' value='{$product_price}' {$product_price_tabindex}  {$disabled_text} {$product_price_placeholder_attribute} />";
		$input5 = "<input type='number' class='effgf-product_quantity' name='input_{$id}.5' id='{$field_id}_5' value='{$product_quantity}' {$product_quantity_tabindex}  {$disabled_text} {$product_quantity_placeholder_attribute} />";
		$input6 = "<input type='hidden' class='effgf-product_duration' name='input_{$id}.6' id='{$field_id}_6' value='{$product_duration}' {$product_duration_tabindex}  {$disabled_text} {$product_duration_placeholder_attribute} />";

		$price_output = '£' . number_format( (float)$product_price, 2, '.', '' );

		return "
		<div class='ginput_complex{$class_suffix} ginput_container gfield_trigger_change effgf-woo-product-container' id='{$field_id}'>
				{$input1}{$input2}{$input3}{$input4}{$input6}
				<div class='gform_woo_product_price'>
				{$price_output}
				</div>
				<div class='gform_woo_product_quantity'>
				{$label5}
				{$input5}
				</div>
			<div class='gf_clear gf_clear_complex'></div>
        </div>
		";
	}

	/**
	 * Get the field classes
	 * @return String     The field classes
	 */
	public function get_field_label_class() {
		return 'gfield_label gfield_label_before_complex';
	}

	/**
	 * Get input property
	 *
	 * @param  Int     $input_id      The ide of the input
	 * @param  String  $property_name The name of the propperty
	 * @return String                 Verturns the value
	 */
	public function get_input_property( $input_id, $property_name ) {
		$input = \GFFormsModel::get_input( $this, $this->id . '.' . (string) $input_id );

		return rgar( $input, $property_name );
	}

	/**
	 * Sanitize the settings
	 */
	public function sanitize_settings() {
		parent::sanitize_settings();
		if ( is_array( $this->inputs ) ) {
			foreach ( $this->inputs as &$input ) {
				if ( isset( $input['choices'] ) && is_array( $input['choices'] ) ) {
					$input['choices'] = $this->sanitize_settings_choices( $input['choices'] );
				}
			}
		}
	}

	/**
	 * Return the field
	 *
	 * @param  String/Array   $value                  The Value
	 * @param  Bool           $force_frontend_label   Force the frontend label
	 * @param  Object         $form                   The Form
	 * @return String                                 The field output
	 */
	public function get_field_content( $value, $force_frontend_label, $form ) {
	    $form_id         = $form['id'];
	    $admin_buttons   = $this->get_admin_buttons();
	    $is_entry_detail = $this->is_entry_detail();
	    $is_form_editor  = $this->is_form_editor();
	    //$is_admin        = $is_entry_detail || $is_form_editor;
	    $is_admin        = true; //is_admin();
	    $field_label     = $this->get_field_label( $force_frontend_label, $value );
	    $field_id        = $is_admin || $form_id == 0 ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";
	    $field_content   = ! $is_admin ? '{FIELD}' : $field_content = sprintf( "%s<label for='input_%s' class='gfield_label gfield_woo_product'>%s</label>{FIELD}", $admin_buttons, $field_id, esc_html( $field_label ) );

	    return $field_content;
	}

	/**
	 * Show the value on the entries screen
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		if ( is_array( $value ) && ! empty( $value ) ) {

			$field_label         = $this->get_field_label( false, $value );
			$product_id          = trim( $value[ $this->id . '.1' ] );
			$product_sku         = trim( $value[ $this->id . '.2' ] );
			$product_is_variable = trim( $value[ $this->id . '.3' ] );
			$product_price       = trim( $value[ $this->id . '.4' ] );
			$product_quantity    = trim( $value[ $this->id . '.5' ] );

			return $field_label . ' (£' . $product_price . ')' . ' x ' . $product_quantity;
	    } else {
	        return $value;
	    }
	}
}
