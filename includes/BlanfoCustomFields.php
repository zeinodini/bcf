<?php


class BlanfoCustomFields {

	protected $prod_cat;

	public function __construct() {

		$this->prod_cat = 'accessories';

		$this->bcf_loader();

		//add custom fields
		add_action( 'woocommerce_before_add_to_cart_button', [$this ,'bcf_before_add_to_cart_btn'] , 10 , 1 );

		//add fields to product data
		add_filter( 'woocommerce_add_cart_item_data', [$this,'bcf_add_cart_item_data'], 10, 3 );

		//display fields on cart page
		add_filter( 'woocommerce_get_item_data', [$this , 'bcf_product_add_on_display_cart'], 10, 2 );

		//disable qantity field
		add_filter( 'woocommerce_is_sold_individually', [$this,'bcf_remove_quantity_fields'], 10, 2 );

		//add details in checkout page
		add_filter( 'woocommerce_checkout_cart_item_quantity', [$this,'bcf_filter_woocommerce_checkout_cart_item_quantity'], 10, 3 );

		//add and update details in cart page
		add_action( 'woocommerce_before_calculate_totals', [$this , 'bcf_update_item_price_based_on_fields'], 10 , 1 );

//		add_action( 'woocommerce_checkout_create_order_line_item', [$this,'add_custom_data_to_order_item'], 10, 4 );

		add_action( 'woocommerce_checkout_create_order_line_item', [$this,'add_booking_order_line_item'], 10, 4 );

//		add_filter( 'woocommerce_get_price_html', [$this , 'bcf_alter_price_display'], 10, 2 );
	}


	function add_booking_order_line_item( $item, $cart_item_key, $values, $order ) {
		// Get cart item custom data and update order item meta
		if( isset( $values['bcf_custom_fields'] ) ){
			if( ! empty( $values['bcf_custom_fields']['meter']) ) {
				$item->update_meta_data( 'متر', $values['bcf_custom_fields']['meter']['value'] );
			}
			if(! empty( $values['bcf_custom_fields']['centimeter'])){
				$item->update_meta_data( 'سانتیمتر', $values['bcf_custom_fields']['centimeter']['value'] );
			}

		}
	}

	private function bcf_loader() {
		wp_enqueue_script( 'jquery');
		wp_enqueue_script( 'bcf_custom_fields', BCF_URL . 'assets/js/bcf.js', array( 'jquery' ), '1.0.0', false );
	}

	public function bcf_remove_quantity_fields( $return, $product ) {
		$terms = get_the_terms( $product->id, 'product_cat' );
		if($terms[0]->slug == $this->prod_cat ) {
			return true;
		}
	}

	public function bcf_before_add_to_cart_btn($product_id) {
		$terms = get_the_terms( $product_id, 'product_cat' );

		if($terms[0]->slug == $this->prod_cat) {
			$meter_value = isset( $_POST['meter-field'] ) ? sanitize_text_field( $_POST['meter-field'] ) : 1;
			$centimeter_value = isset( $_POST['centimeter-field'] ) ? sanitize_text_field( $_POST['centimeter-field'] ) : 0;
			?>

			<div style="margin-top:20px">
				<p><strong>وارد کردن اندازه پارچه</strong></p>
				<label for="meter-field">متر
				<input id="bcf-meter-field" class="bcf-text-field" type="number" min="0" max="99" name="meter-field" value="<?php echo $meter_value; ?>" placeholder="متر">
				</label>
				<label for="centimeter-field">سانتیمتر
				<input id="bcf-centimeter-field" class="bcf-text-field" type="number" min="0" step="10" max="99" name="centimeter-field" value="<?php echo $centimeter_value; ?>" placeholder="سانتیمتر">
				</label>
                <br>
				<span id="bcf-show-size"></span>
			</div>

			<?php
		}
	}

	public function bcf_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {

		$product = wc_get_product( $product_id );
		$price = $product->get_price();

		if( isset( $_REQUEST['meter-field'] ) ) {

			$cart_item_data['bcf_custom_fields']['meter'] = array(
				'label' => 'متر',
				'value' => sanitize_text_field( $_REQUEST['meter-field'] ),
			);
		}
		if( isset( $_REQUEST['centimeter-field'] ) ) {
			$cart_item_data['bcf_custom_fields']['centimeter'] = array(
				'label' => 'سانتیمتر',
				'value' => sanitize_text_field( $_REQUEST['centimeter-field'] ),
			);
		}
		$current_product = wc_get_product($product_id);
		$current_price = $current_product->get_price();
		$meter_field = $cart_item_data['bcf_custom_fields']['meter']['value'];
		$centimeter_field = $cart_item_data['bcf_custom_fields']['centimeter']['value'];
		$new_price = ($current_price * $meter_field) + ($current_price * ($centimeter_field / 100));

		$cart_item_data['bcf_custom_fields']['new_price'] = $new_price;


		return $cart_item_data;
	}

	public function bcf_product_add_on_display_cart( $data, $cart_item ) {
		$custom_items = array();
		if( !empty( $cart_data ) )
			$custom_items = $data;


		if( isset( $cart_item['bcf_custom_fields'] ) ) {

			foreach( $cart_item['bcf_custom_fields'] as $key => $custom_data ){
				if( $key != 'key' and $key != 'new_price'  ){
					$custom_items[] = array(
						'name' => $custom_data['label'],
						'value' => $custom_data['value'],
					);
				}
			}
		}
		return $custom_items;

	}

	public function bcf_filter_woocommerce_checkout_cart_item_quantity( $item_qty, $cart_item, $cart_item_key ) {

		$product_id = $cart_item['product_id'];
		$terms = get_the_terms( $product_id, 'product_cat' );
			if($terms[0]->slug == $this->prod_cat) {
				$item_qty = '<strong class="product-quantity">' . $cart_item['bcf_custom_fields']['meter']['label'] . ' ' . $cart_item['bcf_custom_fields']['meter']['value'] . ' و ' . $cart_item['bcf_custom_fields']['centimeter']['label'] . ' ' . $cart_item['bcf_custom_fields']['centimeter']['value'] . '</strong>';
			}

		return $item_qty;
	}

	public function bcf_update_item_price_based_on_fields( $cart ) {

		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
			return;

		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
			return;

		foreach( $cart->get_cart() as $cart_item_key => $cart_item ) {
			$product_id = $cart_item['product_id'];
			$terms = get_the_terms( $product_id, 'product_cat' );

			if( $terms[0]->slug == $this->prod_cat ) {

				$new_price = $cart_item['bcf_custom_fields']['new_price'];
				$cart_item['data']->set_price( $new_price );

			}
		}
	}

	public function bcf_alter_price_display( $price_html, $product ) {
		$product_id = $product->get_id();
		$terms = get_the_terms( $product_id, 'product_cat' );
		if($terms[0]->slug == $this->prod_cat ) {
//			$current_product = wc_get_product($product_id);
			$current_price = wc_get_price_to_display( $product );
			$custom_field = get_post_meta( $product->id, 'bcf_custom_fields', true );
			print_r(WC()->cart->get_cart());
//			$meter_field = $custom_field['meter']['value'];
//			$centimeter_field = $custom_field['centimeter']['value'];
//			$new_price = ($current_price * $meter_field) + ($current_price * ($centimeter_field / 100));
//			$price_html = wc_price( $new_price);
//			$orig_price = wc_get_price_to_display( $product );
//          $price_html = wc_price( $orig_price * 0.80 );
		}
		return $price_html;
	}

	public function add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {
		$product = wc_get_product( $item->get_product_id() ); // The WC_Product Object (and the parent variable product object for product variations)
		print_r($product);

//		$value   = $product->get_meta('validity_field');

		if( ! empty( $value ) ) {
			$item->update_meta_data( __( 'Waznosc konta', 'waznosc' ), $value );
		}
	}

}