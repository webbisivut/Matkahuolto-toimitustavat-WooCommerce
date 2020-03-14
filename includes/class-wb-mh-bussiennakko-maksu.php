<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * WB_Gateway_Bussiennakko_Mh Class
 *
 * @class WB_Gateway_Bussiennakko_Mh
 * @version	1.0.0
 * @since 1.0.0
 * @package	WB_Matkahuolto
 * @author Webbisivut.org
 */
function WB_Init_Mh_Bussiennakkomaksu_Gateway_Class() {
	class WB_Gateway_Bussiennakko_Mh extends WC_Payment_Gateway {

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
			$this->id                 = 'wb_bussiennakko_mh';
			$this->method_title       = __( 'Maksu Matkahuoltoon', 'wb-matkahuolto-toimitustavat' );
			$this->method_description = __( 'Maksu Matkahuollon toimipisteeseen.', 'wb-matkahuolto-toimitustavat' );
			$this->has_fields         = false;

			// Load the settings
			$this->init_form_fields();
			$this->init_settings();

			// Get settings
			$this->title              = esc_attr( $this->get_option( 'title' ) );
			$this->description        = esc_attr( $this->get_option( 'description' ) );
			$this->instructions       = esc_attr( $this->get_option( 'instructions', $this->description ) );
			$this->enable_for_methods = $this->get_option( 'enable_for_methods', array() );

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou', array( $this, 'thankyou_page_mh' ), 1 );

			// Customer Emails
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
		}

		/**
		 * Initialise Gateway Settings Form Fields
		 */
		public function init_form_fields() {
			$shipping_methods = array();

			if ( is_admin() )
				foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
					$shipping_methods[ $method->id ] = $method->id;
				}

			$this->form_fields = array(
				'enabled' => array(
					'title'       => __( 'Ota toimitustapa käyttöön', 'wb-matkahuolto-toimitustavat' ),
					'label'       => __( 'Ota käyttöön maksu Matkahuollon toimipisteeseen', 'wb-matkahuolto-toimitustavat' ),
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no'
				),
				'title' => array(
					'title'       => __( 'Nimi', 'wb-matkahuolto-toimitustavat' ),
					'type'        => 'text',
					'description' => __( 'Näytetään kassalla', 'wb-matkahuolto-toimitustavat' ),
					'default'     => __( 'Maksu Matkahuollon toimipisteeseen', 'wb-matkahuolto-toimitustavat' ),
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => __( 'Kuvaus', 'wb-matkahuolto-toimitustavat' ),
					'type'        => 'textarea',
					'description' => __( 'Maksutavan kuvaus kassalla.', 'wb-matkahuolto-toimitustavat' ),
					'default'     => __( 'Maksu Matkahuollon toimipisteeseen noudettaessa.', 'wb-matkahuolto-toimitustavat' ),
					'desc_tip'    => true,
				),
				'instructions' => array(
					'title'       => __( 'Maksuohjeet', 'wb-matkahuolto-toimitustavat' ),
					'type'        => 'textarea',
					'description' => __( 'Maksuohjeet jotka näytetään kiitos-sivulla.', 'wb-matkahuolto-toimitustavat' ),
					'default'     => __( 'Maksa toimituksen maksu Matkahuollon toimipisteeseen.', 'wb-matkahuolto-toimitustavat' ),
					'desc_tip'    => true,
				),
				'enable_for_methods' => array(
					'title'             => __( 'Ota käyttöön seuraaville toimitustavoille', 'wb-matkahuolto-toimitustavat' ),
					'type'              => 'multiselect',
					'class'             => 'wc-enhanced-select',
					'css'               => 'width: 450px;',
					'default'           => '',
					'description'       => __( 'Valitse mille toimitustavoille haluat maksutavan näkyvän, tai jätä tyhjäksi jos haluat sen näkyvän kaikille toimitustavoille.', 'wb-matkahuolto-toimitustavat' ),
					'options'           => $shipping_methods,
					'desc_tip'          => true,
					'custom_attributes' => array(
						'data-placeholder' => __( 'Valitse toimitustavat', 'wb-matkahuolto-toimitustavat' )
					)
				),
				'be_lisamaksu_on_off' => array(
					'title'       => __( 'Lisäkulu', 'wb-matkahuolto-toimitustavat' ),
					'type'        => 'select',
					'options'		=> array( 'ei' => 'Ei', 'kylla' => 'Kyllä' ),
					'description' => __( 'Otetaanko käyttöön lisäkulu maksutavalle?', 'wb-matkahuolto-toimitustavat' ),
					'default'     => __( 'ei', 'wb-matkahuolto-toimitustavat' )
				),
				'be_lisamaksu_nimi' => array(
					'title'       => __( 'Lisäkulun nimi', 'wb-matkahuolto-toimitustavat' ),
					'type'        => 'text',
					'description' => __( 'Anna lisäkulun nimi, joka näytetään kassalla', 'wb-matkahuolto-toimitustavat' ),
					'default'		=> '',
					'placeholder'	=> __( '' )
				),
				'be_lisamaksu_hinta' => array(
					'title'       => __( 'Lisäkulun hinta', 'wb-matkahuolto-toimitustavat' ),
					'type'        => 'text',
					'description' => __( 'Anna lisäkulun hinta euroina.', 'wb-matkahuolto-toimitustavat' ),
					'default'		=> '',
					'placeholder'	=> __( '' )
				),
				'be_lisamaksu_verotus' => array(
					'title'       => __( 'Verot', 'wb-matkahuolto-toimitustavat' ),
					'type'        => 'select',
					'description' => __( 'Otetaanko käyttöön verot lisäkululle?', 'wb-matkahuolto-toimitustavat' ),
					'options'		=> array( 'ei' => 'Ei', 'kylla' => 'Kyllä' ),
					'default'		=> 'kylla'
				),
		   );
		}

		/**
		 * Check If The Gateway Is Available For Use
		 *
		 * @return bool
		 */
		public function is_available() {
			$order          = null;
			$needs_shipping = false;

			// Test if shipping is needed first
			if ( WC()->cart && WC()->cart->needs_shipping() ) {
				$needs_shipping = true;
			} elseif ( is_page( wc_get_page_id( 'checkout' ) ) && 0 < get_query_var( 'order-pay' ) ) {
				$order_id = absint( get_query_var( 'order-pay' ) );
				$order    = wc_get_order( $order_id );

				// Test if order needs shipping.
				if ( 0 < sizeof( $order->get_items() ) ) {
					foreach ( $order->get_items() as $item ) {
						$_product = $item->get_product();
						if ( $_product && $_product->needs_shipping() ) {
							$needs_shipping = true;
							break;
						}
					}
				}
			}

			$needs_shipping = apply_filters( 'woocommerce_cart_needs_shipping', $needs_shipping );

			// Check methods
			if ( ! empty( $this->enable_for_methods ) && $needs_shipping ) {

				// Only apply if all packages are being shipped via chosen methods
				$chosen_shipping_methods_session = WC()->session->get( 'chosen_shipping_methods' );

				if ( isset( $chosen_shipping_methods_session ) ) {
					$chosen_shipping_methods = array_unique( $chosen_shipping_methods_session );
				} else {
					$chosen_shipping_methods = array();
				}

				$check_method = false;

				if ( is_object( $order ) ) {
					if ( $order->get_shipping_method() ) {
						$check_method = $order->get_shipping_method();
					}
				} elseif ( empty( $chosen_shipping_methods ) || sizeof( $chosen_shipping_methods ) > 1 ) {
					$check_method = false;
				} elseif ( sizeof( $chosen_shipping_methods ) == 1 ) {
					$check_method = $chosen_shipping_methods[0];
				}

				if ( ! $check_method ) {
					return false;
				}

				$found = false;

				foreach ( $this->enable_for_methods as $method_id ) {
					if ( strpos( $check_method, $method_id ) === 0 ) {
						$found = true;
						break;
					}
				}

				if ( ! $found ) {
					return false;
				}
			}

			return parent::is_available();
		}


		/**
		 * Process the payment and return the result
		 *
		 * @param int $order_id
		 * @return array
		 */
		public function process_payment( $order_id ) {

			$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );

			if( $chosen_payment_method == 'wb_bussiennakko_mh') {

				$order = wc_get_order( $order_id );

				// Mark as processing (payment won't be taken until delivery)
				$order->update_status( 'processing', __( 'Maksetaan noudon yhteydessä', 'wb-matkahuolto-toimitustavat' ) );

				// Reduce stock levels
				$order->reduce_order_stock();

				// Remove cart
				WC()->cart->empty_cart();

				// Return thankyou redirect
				return array(
					'result' 	=> 'success',
					'redirect'	=> $this->get_return_url( $order )
				);
			}
		}

		/**
		 * Output for the order received page.
		 */
		public function thankyou_page_mh($order_id) {

			$order = new WC_Order( $order_id );

			if ( $this->instructions && 'wb_bussiennakko_mh' === $order->get_payment_method() ) {
				//echo wpautop( wptexturize( $this->instructions ) ) ;
			}

		}

		/**
		 * Add content to the WC emails.
		 *
		 * @access public
		 * @param WC_Order $order
		 * @param bool $sent_to_admin
		 * @param bool $plain_text
		 */
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

			if ( $this->instructions && ! $sent_to_admin && 'wb_bussiennakko_mh' === $order->get_payment_method() ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}

		}
	}

	/**
	 * Add fee on article specifics
	* @param WC_Cart $cart
	 */
	function add_bussiennakko_mh_fees(){
		$bussiennakko_class = new WB_Gateway_Bussiennakko_Mh();

		if( $bussiennakko_class->settings['be_lisamaksu_on_off'] == 'kylla' ) {
			global $woocommerce;

			$chosen_methods = WC()->session->get( 'chosen_payment_method' );
			$chosen_method = $chosen_methods;

			$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
			$chosen_shipping_method = $chosen_shipping_methods[0];

			$available_methods = $bussiennakko_class->enable_for_methods;

			$toim_hinta = $bussiennakko_class->settings['be_lisamaksu_hinta'];
			$toim_lisa_nimi = $bussiennakko_class->settings['be_lisamaksu_nimi'];
			$toim_verot = $bussiennakko_class->settings['be_lisamaksu_verotus'];

			if( $bussiennakko_class->settings['be_lisamaksu_verotus'] == 'kylla' ) {
				$toim_verot = true;
			} else if ( $bussiennakko_class->settings['be_lisamaksu_verotus'] == 'ei' ) {
				$toim_verot = false;
			}

			$fees = $toim_hinta;

			foreach($available_methods as $method) {
				if ($fees > 0 && $chosen_method == 'wb_bussiennakko_mh' && strpos($chosen_shipping_method, $method) !== false) {
					WC()->cart->add_fee( $toim_lisa_nimi, $fees, $toim_verot, '' );
				}
			}

		}
	}

	add_action('woocommerce_cart_calculate_fees' , 'add_bussiennakko_mh_fees');
	add_action( 'woocommerce_after_cart_item_quantity_update', 'add_bussiennakko_mh_fees' );

}
add_action( 'plugins_loaded', 'WB_Init_Mh_Bussiennakkomaksu_Gateway_Class' );

function add_wb_bussiennakko_mh_gateway_class( $methods ) {
	$methods[] = 'WB_Gateway_Bussiennakko_Mh';
	return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'add_wb_bussiennakko_mh_gateway_class' );
