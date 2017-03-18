jQuery( document ).ready( function( $ ) {

	/*********************************
	 * Update Woo Total
	 ********************************/

	function woo_product_update_total() {
		var total = 0;
		$('input.effgf-product_quantity').each( function() {
			var quantity = $(this).val();
			if ( quantity < 0 ) {
				$(this).val( 0 );
				quantity = 0;
			}
			var price    = $(this).closest( '.ginput_container' ).find( 'input.effgf-product_price' ).val();
			total = total + ( quantity * price );
		} );
		$('.woo_total input').val( parseFloat(total).toFixed(2) );
		$('.woo_total input').change();
	}

	$( 'body' ).on( 'keyup mouseup', '.gform_woo_product_quantity input', function(){
		woo_product_update_total();
	});

	$( 'body' ).on( 'keyup mouseup', '.woo_total input', function(){
		woo_product_update_total();
	});

	woo_product_update_total();


	/*********************************
	 * Update Woo Summery Table
	 ********************************/

	function update_summary_table() {
		var total = $('.woo_total input').val();
		if ( $('.license-summary').length > 0 ) {
			var output = '';
			output += '<table>';
			$('.gfield_woo_product').each( function() {
				var quantity = $(this).closest( '.gfield' ).find( 'input.effgf-product_quantity' ).val();
				var price    = $(this).closest( '.gfield' ).find( 'input.effgf-product_price' ).val();
				if ( quantity > 0 ) {
					output += '<tr>';
					output += '<td>' + quantity + ' x ' + $(this).html() + '</td>';
					output += '<td>£' + parseFloat(price).toFixed(2) + '</td>';
					output += '<tr>';
				}
			} );
			output += '<tr>';
			output += '<td><strong>Total</strong></td>';
			output += '<td><strong>£' + parseFloat(total).toFixed(2) + '</strong></td>';
			output += '<tr>';
			output += '</table>';
			$('.license-summary').html( output );
		}

	}
	update_summary_table();

});
