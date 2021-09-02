<?php
	/**
	Plugin Name: WP 2 Steps Shipping for Woocommerce
	Description: Allows to set 2 steps shipping for Woocommerce
	Author: Svjatoslav Kachmar
	Version: 1.01
	 **/

	add_action( 'woocommerce_checkout_billing', 'tss_before_billing_fields', 5 );

	function tss_before_billing_fields(){
		$checkout = WC()->checkout;

		woocommerce_form_field('delivery_method', array(
			'type' => 'select',
			'label' => 'Shipping from USA',
			'options'       => array(
				'10'  => __( 'Shipping by sea - 10$', 'sdm' ),
				'20' => __( 'Shipping by air - 20$', 'sdm' )
			),
			'class' => array('delivery_method form-row-wide'),
			'clear'     => true
		), $checkout->get_value('delivery_method'));

		WC()->session->set('chosen_shipping_methods', array('flat_rate:9'));
	}

    define('APFSURL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );

	function tss_script_enqueuescripts()
	{
		wp_enqueue_script('tss', APFSURL.'/js/tss-script.js', array('jquery'));
		wp_localize_script( 'tss', 'apfajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
	add_action('wp_enqueue_scripts', 'tss_script_enqueuescripts');

	function tss_ajax_form(){
		global $wpdb;

		foreach ($_COOKIE as $key) {
			if(strlen($key) == 59) {
				$hash = $key;
			}
		}
		$wpdb->insert(
			'tss_table',
			array(
				'settings_value' => $_POST[ 'field_one' ],
				'settings_name' => $hash,
			)
		);
	}
	add_action( 'wp_ajax_tss_ajax_form', 'tss_ajax_form' ); //admin side
	add_action( 'wp_ajax_nopriv_tss_ajax_form', 'tss_ajax_form' ); //for frontend

	add_action( 'woocommerce_after_calculate_totals', 'tss_woocommerce_after_calculate_totals', 30 );
	function tss_woocommerce_after_calculate_totals( $cart ) {
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$product = $cart_item['data'];
			if(!empty($product)){
			}
		}
		
		global $wpdb;
		
		foreach ($_COOKIE as $key) {
			if(strlen($key) == 59) {
				$hash = $key;
			}
		}

		$usa_shipping = $wpdb->get_var(
			"
			SELECT settings_value 
			FROM tss_table
			WHERE settings_name = '$hash'
		    ORDER BY id DESC LIMIT 1
	        "
		);

		if (!isset($usa_shipping) || empty($usa_shipping)) {
			$usa_shipping = 10;
		}
		$cart->total = $cart->total + (int)$usa_shipping;
	}