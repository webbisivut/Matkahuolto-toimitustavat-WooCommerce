<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WB_Mh_Ahvenanmaa_Shipping_Method_sz Class
 *
 * @class WB_Mh_Ahvenanmaa_Shipping_Method_sz
 * @version	1.0.0
 * @since 1.0.0
 * @package	WB_Matkahuolto
 * @author Webbisivut.org
 */

/**
 * Get cart items
 *
 * @access public
 * @return string
 */
class cart_items_mh_ah_sz_mh {

	public function hae_woo() {
		global $woocommerce;
		$items = $woocommerce->cart->get_cart();

		return $items;
	}

	public function korkeus($countQty = false) {
		$kaikki_tuotteet_korkeus = array();

		$items = $this->hae_woo();
		foreach ($items as $item) {
			if($countQty) {
				$qty = $item['quantity'];
			} else {
				$qty = 1;
			}

			$height = floatval($item['data']->get_height()) * $qty;
			array_push($kaikki_tuotteet_korkeus, $height);
		}
		return $kaikki_tuotteet_korkeus;
	}

	public function pituus($countQty = false) {
		$kaikki_tuotteet_pituus = array();

		$items = $this->hae_woo();
		foreach ($items as $item) {
			if($countQty) {
				$qty = $item['quantity'];
			} else {
				$qty = 1;
			}

			$length = floatval($item['data']->get_length()) * $qty;
			array_push($kaikki_tuotteet_pituus, $length);
		}
		return $kaikki_tuotteet_pituus;
	}

	public function leveys($countQty = false) {
		$kaikki_tuotteet_leveys = array();

		$items = $this->hae_woo();
		foreach ($items as $item) {
			if($countQty) {
				$qty = $item['quantity'];
			} else {
				$qty = 1;
			}

			$width = floatval($item['data']->get_width()) * $qty;
			array_push($kaikki_tuotteet_leveys, $width);
		}
		return $kaikki_tuotteet_leveys;
	}

	public function paino($countQty = false) {
		$kaikki_tuotteet_paino = array();

		$items = $this->hae_woo();
		foreach ($items as $item) {
			if($countQty) {
				$qty = $item['quantity'];
			} else {
				$qty = 1;
			}

			$weight = floatval($item['data']->get_weight()) * $qty;
			array_push($kaikki_tuotteet_paino, $weight);
		}
		return $kaikki_tuotteet_paino;
	}

	public function maara() {
		$kaikki_tuotteet_qty = array();

		$items = $this->hae_woo();
		foreach ($items as $item) {
			$qty = $item['quantity'];

			array_push($kaikki_tuotteet_qty, $qty);
		}
		return $kaikki_tuotteet_qty;
	}

	public function toimitusluokka() {
		$kaikki_tuotteet_toimitusluokka = array();

		$items = $this->hae_woo();

		foreach ($items as $item) {
			$toimitusluokka = $item['data']->get_shipping_class();
			array_push($kaikki_tuotteet_toimitusluokka, $toimitusluokka);
		}
		return $kaikki_tuotteet_toimitusluokka;
	}

}

/**
 * Main Function
 *
 * @access public
 */
function WB_Mh_Ahvenanmaa_Shipping_Method_sz_Init() {

	if ( ! class_exists( 'WB_Mh_Ahvenanmaa_Shipping_Method_sz' ) ) {

		class WB_Mh_Ahvenanmaa_Shipping_Method_sz extends WC_Shipping_Method {

			/**
			* Constructor for Matkahuolto shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct( $instance_id = 0 ) {
				$this->id = 'wb_mh_ahvenanmaa_shipping_method_sz'; // Id for your shipping method. Should be uunique.
				$this->instance_id = absint( $instance_id );
				$this->method_title = __( 'Matkahuolto Ahvenanmaa (Matkahuolto toimitustavat)', 'wb-matkahuolto-toimitustavat' ); // Title shown in admin
				$this->method_description = __( 'Matkahuolto ahvenanmaa', 'wb-matkahuolto-toimitustavat' ); // Description shown in admin
				$this->supports = array(
					'shipping-zones',
					'instance-settings',
				);

				$this->init();
			}

			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			function init() {

				// Load the settings API
				$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
				$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

				$this->ahvenanmaa_paino1		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino1') ));
				$this->ahvenanmaa_hinta1		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta1') ));

				$this->ahvenanmaa_paino2		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino2') ));
				$this->ahvenanmaa_hinta2		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta2') ));

				$this->ahvenanmaa_paino3		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino3') ));
				$this->ahvenanmaa_hinta3		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta3') ));

				$this->ahvenanmaa_paino4		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino4') ));
				$this->ahvenanmaa_hinta4		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta4') ));

				$this->ahvenanmaa_paino5		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino5') ));
				$this->ahvenanmaa_hinta5		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta5') ));

				$this->ahvenanmaa_paino6		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino6') ));
				$this->ahvenanmaa_hinta6		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta6') ));

				$this->ahvenanmaa_paino7		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino7') ));
				$this->ahvenanmaa_hinta7		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta7') ));

				$this->ahvenanmaa_paino8		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino8') ));
				$this->ahvenanmaa_hinta8		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta8') ));

				$this->ahvenanmaa_paino9		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino9') ));
				$this->ahvenanmaa_hinta9		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta9') ));

				$this->ahvenanmaa_paino10		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino10') ));
				$this->ahvenanmaa_hinta10		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta10') ));

				$this->ahvenanmaa_paino11		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino11') ));
				$this->ahvenanmaa_hinta11		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta11') ));

				$this->ahvenanmaa_paino12		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino12') ));
				$this->ahvenanmaa_hinta12		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta12') ));

				$this->ahvenanmaa_paino13		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino13') ));
				$this->ahvenanmaa_hinta13		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta13') ));

				$this->ahvenanmaa_paino14		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_paino14') ));
				$this->ahvenanmaa_hinta14		   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_hinta14') ));

				$this->ah_hinta_yli	   			   = str_replace(",", ".", esc_attr( $this->get_option('ah_hinta_yli') ));

				$this->ahvenanmaa_ilm_toim	   	   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_ilm_toim') ));
				$this->ahvenanmaa_kas_kulut	   = str_replace(",", ".", esc_attr( $this->get_option('ahvenanmaa_kas_kulut') ));

				$this->ah_max_korkeus    		   = str_replace(",", ".", esc_attr( $this->get_option('ah_max_korkeus') ));
				$this->ah_max_pituus     		   = str_replace(",", ".", esc_attr( $this->get_option('ah_max_pituus') ));
				$this->ah_max_leveys     		   = str_replace(",", ".", esc_attr( $this->get_option('ah_max_leveys') ));
				$this->ah_max_paino     		   = str_replace(",", ".", esc_attr( $this->get_option('ah_max_paino') ));
				
				$this->ah_max_paino_select     	   = esc_attr( $this->get_option('ah_max_paino_select'));

				$this->ah_kuponki			   	   = esc_attr( $this->get_option('ah_kuponki') );
				$this->ah_kuponki_kaikki		   = esc_attr( $this->get_option('ah_kuponki_kaikki') );

				$this->tax_status	  	  		   = $this->get_option('tax_status');

				$this->title 			   		   = $this->get_option( 'title' );
				$this->availability 	  		   = $this->get_option( 'availability' );
				$this->countries 		   		   = $this->get_option( 'countries' );

				// Save settings in admin if you have any defined
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			function init_form_fields() {

				$this->instance_form_fields = array(
					'title'	   	  		   => array(
						'title'            => __('Toimitustavan nimi', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => 'Matkahuolto Ahvenanmaa',
						'description'      => __('Anna toimitustavalle nimi jonka asiakas näkee kassalla.', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('Matkahuolto ahvenanmaa')
					),
					'ahvenanmaa_paino1'	   	   => array(
						'title'            => __('Paketti1 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '2',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('2')
					),
					'ahvenanmaa_hinta1'	   	   => array(
						'title'            => __('Paketti1 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '10.80',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('10.80')
					),
					'ahvenanmaa_paino2'	   	   => array(
						'title'            => __('Paketti2 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '5',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('5')
					),
					'ahvenanmaa_hinta2'	   	   => array(
						'title'            => __('Paketti2 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '12.80',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('12.80')
					),
					'ahvenanmaa_paino3'	   	   => array(
						'title'            => __('Paketti3 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '10',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('10')
					),
					'ahvenanmaa_hinta3'	   	   => array(
						'title'            => __('Paketti3 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '16.00',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('16.00')
					),
					'ahvenanmaa_paino4'	   	   => array(
						'title'            => __('Paketti4 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '15',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('15')
					),
					'ahvenanmaa_hinta4'	   	   => array(
						'title'            => __('Paketti4 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '16.60',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('16.60')
					),
					'ahvenanmaa_paino5'	   	   => array(
						'title'            => __('Paketti5 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '20',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('20')
					),
					'ahvenanmaa_hinta5'	   	   => array(
						'title'            => __('Paketti5 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '19.20',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('19.20')
					),
					'ahvenanmaa_paino6'	   	   => array(
						'title'            => __('Paketti6 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '25',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('25')
					),
					'ahvenanmaa_hinta6'	   	   => array(
						'title'            => __('Paketti6 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '20.60',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('20.60')
					),
					'ahvenanmaa_paino7'	   	   => array(
						'title'            => __('Paketti7 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '30',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('30')
					),
					'ahvenanmaa_hinta7'	   	   => array(
						'title'            => __('Paketti7 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '23.00',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('23.00')
					),
					'ahvenanmaa_paino8'	   	   => array(
						'title'            => __('Paketti8 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '40',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('40')
					),
					'ahvenanmaa_hinta8'	   	   => array(
						'title'            => __('Paketti8 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '29.50',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('29.50')
					),
					'ahvenanmaa_paino9'	   	   => array(
						'title'            => __('Paketti9 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '50',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('50')
					),
					'ahvenanmaa_hinta9'	   	   => array(
						'title'            => __('Paketti9 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '31.30',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('31.30')
					),
					'ahvenanmaa_paino10'	   	   => array(
						'title'            => __('Paketti10 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '60',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('60')
					),
					'ahvenanmaa_hinta10'	   	   => array(
						'title'            => __('Paketti10 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '42.50',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('42.50')
					),
					'ahvenanmaa_paino11'	   	   => array(
						'title'            => __('Paketti11 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '70',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('70')
					),
					'ahvenanmaa_hinta11'	   	   => array(
						'title'            => __('Paketti11 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '47.60',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('47.60')
					),
					'ahvenanmaa_paino12'	   	   => array(
						'title'            => __('Paketti12 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '80',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('80')
					),
					'ahvenanmaa_hinta12'	   	   => array(
						'title'            => __('Paketti12 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '51.40',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('51.40')
					),
					'ahvenanmaa_paino13'	   	   => array(
						'title'            => __('Paketti13 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '90',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('90')
					),
					'ahvenanmaa_hinta13'	   	   => array(
						'title'            => __('Paketti13 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '56.30',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('90.50')
					),
					'ahvenanmaa_paino14'	   	   => array(
						'title'            => __('Paketti14 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('100')
					),
					'ahvenanmaa_hinta14'	   	   => array(
						'title'            => __('Paketti14 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '63.90',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('63.90')
					),
					'ah_hinta_yli' => array(
						'title'            => __('Maksimipainon ylittävä hinnoittelu', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '12.60',
						'description'      => __('Jos käytössä, suurimman painorajan ylittäviin paketteihin lisätään kulu aina 20 kilon välein.', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('')
					),
					'ahvenanmaa_ilm_toim'	   => array(
						'title'            => __('Ahvenanmaa ilmaisen toimituksen raja', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Anna summa jonka jälkeen ei lisätä toimituskuluja. Jätä tyhjäksi jos et halua käyttää tätä toimintoa.', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('')
					),
					'ahvenanmaa_kas_kulut'	   => array(
						'title'            => __('Käsittelykulut', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '0',
						'description'      => __('Lisää tähän mahdolliset käsittelykulut.', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('0')
					),
					'ah_max_korkeus' => array(
						'title'            => __('Maksimikorkeus', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '166',
						'description'      => __('Tuotteen maksimikorkeus, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 166cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('166')
					),
					'ah_max_pituus' => array(
						'title'            => __('Maksimipituus', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '166',
						'description'      => __('Tuotteen maksimipituus, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 166cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('166')
					),
					'ah_max_leveys' => array(
						'title'            => __('Maksimileveys', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '166',
						'description'      => __('Tuotteen maksimileveys, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 166cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('166')
					),
					'ah_max_paino' 		   => array(
						'title'            => __('Maksimipaino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '55',
						'description'      => __('Tuotteen tai korin maksimipaino, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 55kg. Jos et halua käyttää maksimipainoa, anna tähän mahdollisimman suuri luku, esim. 9999. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('55')
					),
					'ah_max_paino_select' => array(
							'title'			=> __( 'Ota huomioon korin yhteenlaskettu paino?', 'wb-matkahuolto-toimitustavat' ),
							'type'			=> 'select',
							'description'	=> 'Jos käytössä, lasketaan koko korin paino verrattaessa yllä asetettuun maksimipainoon. Jos ei käytössä, huomioidaan vain yksittäiset tuotteet.',
							'default'		=> 'kylla',
							'options'		=> array(
								'kylla'		=> __( 'Kyllä', 'wb-matkahuolto-toimitustavat' ),
								'ei'		=> __( 'Ei', 'wb-matkahuolto-toimitustavat' ),
							)
					),
					'ah_kuponki'	 	   => array(
						'title'            => __('Kuponki', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '',
						'description'      => __('Anna tähän kuponkikoodi joka oikeuttaa ilmaiseen toimitukseen. Jos haluat antaa useamman koodin, erottele koodit pilkulla.', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('')
					),
					'ah_kuponki_kaikki' => array(
							'title'			=> __( 'Salli kaikki kupongit', 'wb-matkahuolto-toimitustavat' ),
							'type'			=> 'select',
							'description'	=> 'Jos valittuna, kuponkikoodeja ei tarvitse erikseen lisätä, vaan ilmainen toimitus sallitaan miltä tahansa kupongilta, jolle se on määritelty kohdassa WooCommerce - Kupongit.',
							'default'		=> 'ei',
							'options'		=> array(
								'kylla'		=> __( 'Kyllä', 'wb-matkahuolto-toimitustavat' ),
								'ei'		=> __( 'Ei', 'wb-matkahuolto-toimitustavat' ),
							)
					),
					'availability' => array(
							'title'			=> __( 'Saatavuus', 'wb-matkahuolto-toimitustavat' ),
							'type'			=> 'select',
							'class'         => 'wc-enhanced-select',
							'description'	=> '',
							'default'		=> 'all',
							'options'		=> array(
								'including'		=> __( 'Toimitetaan valittuihin maihin', 'wb-matkahuolto-toimitustavat' ),
								'excluding'		=> __( 'Ei toimiteta valittuihin maihin', 'wb-matkahuolto-toimitustavat' ),
								'all'		    => __( 'Toimitetaan kaikkiin maihin joihin myydään', 'wb-matkahuolto-toimitustavat' ),
							)
					),
					'countries' => array(
							'title'			=> __( 'Valitut maat', 'wb-matkahuolto-toimitustavat' ),
							'type'			=> 'multiselect',
							'class'			=> 'wc-enhanced-select',
							'css'			=> 'width: 450px;',
							'default'		=> '',
							'options'		=> WC()->countries->get_shipping_countries(),
							'custom_attributes' => array(
								'data-placeholder' => __( 'Valitse maat', 'wb-matkahuolto-toimitustavat' )
							)
					),
					'tax_status' => array(
							'title' 		=> __( 'Verotettava', 'wb-matkahuolto-toimitustavat' ),
							'type' 			=> 'select',
							'description' 	=> '',
							'default' 		=> 'taxable',
							'options'		=> array(
								'taxable' 	=> __( 'Verotettava', 'woocommerce' ),
								'none' 		=> __( 'Ei verotettava', 'woocommerce' ),
							),
					)
				);
			}

			/**
			 * is_available function.
			 *
			 * @access public
			 * @param mixed $package
			 * @return bool
			 */
			public function is_available( $package ) {

				if ( "no" === $this->enabled ) {
					return false;
				}

				if ( 'including' === $this->availability ) {

					if ( is_array( $this->countries ) && ! in_array( $package['destination']['country'], $this->countries ) ) {
						return false;
					}

				} elseif ( 'excluding' === $this->availability ) {

					if ( is_array( $this->countries ) && ( in_array( $package['destination']['country'], $this->countries ) || ! $package['destination']['country'] ) ) {
						return false;
					}

				} elseif ( 'all' === $this->availability ) {
					$ship_to_countries = array_keys( WC()->countries->get_shipping_countries() );
				}

				if ( isset($ship_to_countries) && is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries ) ) {
					return false;
				}

				return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
			}

			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping( $package = array() ) {

				$woocommerce = function_exists('WC') ? WC() : $GLOBALS['woocommerce'];
				$weight      = $woocommerce->cart->cart_contents_weight;
				$items = $woocommerce->cart->get_cart();

				$cart_price = preg_replace( '#[^\d.,]#', '', $woocommerce->cart->get_cart_total() );

				if($cart_price > 999) {
					$woocommerce_price_thousand_sep = esc_attr(get_option('woocommerce_price_thousand_sep'));
					$woocommerce_price_decimal_sep = esc_attr(get_option('woocommerce_price_decimal_sep'));

					if($woocommerce_price_thousand_sep == ',') {
						$replace = '';
						$needle = ',';

						$cart_price = formatPriceMatkahuoltoPro($cart_price, $replace, $needle);

						if($woocommerce_price_decimal_sep == ',') {
							$cart_price = str_replace(',', '.', $cart_price);
						}
					} else if($woocommerce_price_thousand_sep == '.') {
						$replace = '';
						$needle = '.';

						$cart_price = formatPriceMatkahuoltoPro($cart_price, $replace, $needle);

						if($woocommerce_price_decimal_sep == ',') {
							$cart_price = str_replace(',', '.', $cart_price);
						}
					} else if($woocommerce_price_thousand_sep == '' OR $woocommerce_price_thousand_sep == ' ') {
						if($woocommerce_price_decimal_sep == ',') {
							$cart_price = str_replace(',', '.', $cart_price);
						}
					}
				}
				
				$cart_price = floatval($cart_price);
				$cart_price = number_format($cart_price, 2, '.', '');

				$cart_tax = $woocommerce->cart->get_taxes();
				$cart_tax = array_sum($cart_tax);
				$cart_tax = floatval($cart_tax);

				$cart_total_price = $cart_price + $cart_tax;

				$paino1 = $this->ahvenanmaa_paino1;
				$paino2 = $this->ahvenanmaa_paino2;
				$paino3 = $this->ahvenanmaa_paino3;
				$paino4 = $this->ahvenanmaa_paino4;
				$paino5 = $this->ahvenanmaa_paino5;
				$paino6 = $this->ahvenanmaa_paino6;
				$paino7 = $this->ahvenanmaa_paino7;
				$paino8 = $this->ahvenanmaa_paino8;
				$paino9 = $this->ahvenanmaa_paino9;
				$paino10 = $this->ahvenanmaa_paino10;
				$paino11 = $this->ahvenanmaa_paino11;
				$paino12 = $this->ahvenanmaa_paino12;
				$paino13 = $this->ahvenanmaa_paino13;
				$paino14 = $this->ahvenanmaa_paino14;

				$ahvenanmaa_hinta_yli = $this->ah_hinta_yli;

				$ilm_toim = floatval($this->ahvenanmaa_ilm_toim);

				// Kuponki
				$has_coupon = false;
				$all_coupons = array();
				$annettu_koodi = $this->ah_kuponki;
				$annettu_koodi_array_or_not = false;

				$salli_kaikki_kupongit = $this->ah_kuponki_kaikki;

				if ( $coupons = WC()->cart->get_coupons() ) {
					if($salli_kaikki_kupongit == 'kylla') {
						foreach ( $coupons as $coupon ) {
							if ( $coupon->get_free_shipping() ) {
								$has_coupon = true;
							}
						}
					} else {
						// Tarkistetaan onko useampia koodeja
						if (strpos($annettu_koodi, ',') != false) {
							// On useampia koodeja. Tehdään array
						 $annettu_koodi_array_or_not = true;
						} else {
							// Ei useampia koodeja
						 $annettu_koodi_array_or_not = false;
						}

						foreach ( $coupons as $code => $coupon ) { 
							array_push($all_coupons, $code);
						}

						if($annettu_koodi_array_or_not) {
							$annetut_koodit = explode(",", $annettu_koodi);

							foreach( $annetut_koodit as $koodi) {
								if(isset($coupon) && $coupon->is_valid() && in_array($koodi, $all_coupons)) {
									$has_coupon = true;
								}
							}
						} else {
							if(isset($coupon) && $coupon->is_valid() && in_array($annettu_koodi, $all_coupons)) {
								$has_coupon = true;
							}
						}

					}
				}

				// Lasketaan toimituksen hinta
				if ( ($ilm_toim !='') && ($ilm_toim <= floatval($cart_price)) OR $has_coupon ) {
					$lopullinen_hinta = '0';
				} else {
					if ( (0 <= $weight) && ($weight <= $paino1) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta1 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino1 <= $weight) && ($weight <= $paino2) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta2 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino2 <= $weight) && ($weight <= $paino3) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta3 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino3 <= $weight) && ($weight <= $paino4) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta4 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino4 <= $weight) && ($weight <= $paino5) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta5 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino5 <= $weight) && ($weight <= $paino6) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta6 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino6 <= $weight) && ($weight <= $paino7) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta7 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino7 <= $weight) && ($weight <= $paino8) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta8 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino8 <= $weight) && ($weight <= $paino9) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta9 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino9 <= $weight) && ($weight <= $paino10) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta10 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino10 <= $weight) && ($weight <= $paino11) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta11 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino11 <= $weight) && ($weight <= $paino12) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta12 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino12 <= $weight) && ($weight <= $paino13) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta13 + $this->ahvenanmaa_kas_kulut;
					} elseif ( ($paino13 <= $weight) && ($weight <= $paino14) ) {
						$lopullinen_hinta = $this->ahvenanmaa_hinta14 + $this->ahvenanmaa_kas_kulut;
					} elseif ( $weight > $paino14 && $ahvenanmaa_hinta_yli != '') {
						$ylipaino = ($weight - $paino14);
						$ylipaino_kerroin = ceil(($weight - $paino14) / 20 + 1);

						if($ylipaino < 20) {
							$ylihinta = $ahvenanmaa_hinta_yli;
						} else {
							$ylihinta = ($ylipaino_kerroin) * $ahvenanmaa_hinta_yli;
						}

						$lopullinen_hinta = $this->ahvenanmaa_hinta14 + $this->ahvenanmaa_kas_kulut + $ylihinta;
					} else {
						$lopullinen_hinta = $this->ahvenanmaa_hinta14 + $this->ahvenanmaa_kas_kulut;
					}
				}

				$rate = apply_filters('wb_mh_ahvenanmaa_rate_filter', array(
					'id' => $this->id . $this->instance_id,
					'label' => $this->title,
					'cost' => $lopullinen_hinta,
					'package' => $package,
					'taxes'     => '',
					'calc_tax' => 'per_order'
				) );

				// Register the rate
				$this->add_rate( $rate );
			}
		}
	}
}

add_action( 'woocommerce_shipping_init', 'WB_Mh_Ahvenanmaa_Shipping_Method_sz_init' );

function add_WB_Mh_Ahvenanmaa_Shipping_Method_sz( $methods ) {

	$methods['wb_mh_ahvenanmaa_shipping_method_sz'] = 'WB_Mh_Ahvenanmaa_Shipping_Method_sz';
	return $methods;

}

add_filter( 'woocommerce_shipping_methods', 'add_WB_Mh_Ahvenanmaa_Shipping_Method_sz' );

function hide_show_mh_ah_sz_mh( $rates, $package ) {
	$woocommerce = function_exists('WC') ? WC() : $GLOBALS['woocommerce'];
	$items = $woocommerce->cart->get_cart();

	$shippingIds = array();

	if(isset($rates)) {
		$get_the_id = null;
		
		// Get all shipping methods in use
		foreach ( $rates as $rate_id => $rate ) {
			array_push($shippingIds, $rate->id);
		}

		// Get the instance id
		foreach ($shippingIds as $shipping_id) {
			if (strpos($shipping_id, 'wb_mh_ahvenanmaa_shipping_method_sz') !== false) {
				$get_the_id = str_replace('wb_mh_ahvenanmaa_shipping_method_sz', '', $shipping_id);
				$get_the_id = str_replace(':', '', $get_the_id);
			}
		}

		$kassan_tiedot = new cart_items_mh_ah_sz_mh();

		$length = $kassan_tiedot->pituus($countQty = false);
		$width = $kassan_tiedot->leveys($countQty = false);
		$height = $kassan_tiedot->korkeus($countQty = false);
		$weight = $kassan_tiedot->paino($countQty = true);

		$ah_shipping_method = new WB_Mh_Ahvenanmaa_Shipping_Method_sz( $instance_id = $get_the_id );

		if(!empty($ah_shipping_method->ah_max_paino_select) && $ah_shipping_method->ah_max_paino_select == 'ei') {
			$cart_weight = 0;
		} else {
			$cart_weight = $woocommerce->cart->cart_contents_weight;
		}		

		if(!empty($ah_shipping_method->ah_max_korkeus)) {
			$max_korkeus = $ah_shipping_method->ah_max_korkeus;
		} else {
			$max_korkeus = 166;
		}

		if(!empty($ah_shipping_method->ah_max_leveys)) {
			$max_leveys = $ah_shipping_method->ah_max_leveys;
		} else {
			$max_leveys = 166;
		}

		if(!empty($ah_shipping_method->ah_max_pituus)) {
			$max_pituus = $ah_shipping_method->ah_max_pituus;
		} else {
			$max_pituus = 166;
		}

		if(!empty($ah_shipping_method->ah_max_paino)) {
			$max_paino = $ah_shipping_method->ah_max_paino;
		} else {
			$max_paino = 55;
		}

		if($height == null OR $height == '') {
			$height = array(0);
		}

		if($length == null OR $length == '') {
			$length = array(0);
		}

		if($width == null OR $width == '') {
			$width = array(0);
		}

		if($weight == null OR $weight == '') {
			$weight = array(0);
		}

		if($cart_weight == null OR $cart_weight == '') {
			$cart_weight = 0;
		}

		if ( max($height) > $max_korkeus OR max($length) > $max_pituus OR max($width) > $max_leveys OR max($weight) > $max_paino OR $cart_weight > $max_paino ) {
			$new_rates = array();

			foreach ( $rates as $rate_id => $rate ) {
				if ( 'wb_mh_ahvenanmaa_shipping_method_sz' !== $rate->method_id ) {
					$new_rates[ $rate_id ] = $rate;
				}
			}

			return $new_rates;

		} else {
			return $rates;
		}
	}

}

add_filter( 'woocommerce_package_rates', 'hide_show_mh_ah_sz_mh' , 10, 2 );

/**
* Get package dimensions
*
* @since 1.0.0
* @return void
*/
function mh_ah_weight_sz_mh( $order_id ) {
	global $woocommerce;

	$items = $woocommerce->cart->get_cart();

	$tilavuus_yht = 0;

	foreach ($items as $item) {
		$width = $item['data']->get_width();
		$height = $item['data']->get_height();
		$length = $item['data']->get_length();

		if( isset( $height, $length, $width ) ) {
			$tilavuus = $height * $length * $width;
		} else {
			$tilavuus = '';
		}

		$tilavuus_yht += $tilavuus;
	}

	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );

	$count = $woocommerce->cart->cart_contents_count;
	$weight = $woocommerce->cart->cart_contents_weight;
	$cart_subtotal = floatval( preg_replace( '#[^\d.]#', '', $woocommerce->cart->get_cart_subtotal() ) );

	$tilavuus_yht *= $count;

	foreach( $chosen_methods as $shipping_method_used) {
		if(stristr($shipping_method_used, 'wb_mh_ahvenanmaa_shipping_method_sz')) {
			update_post_meta( $order_id, 'cart_weight', $weight );
			update_post_meta( $order_id, 'cart_qty', $count );
			update_post_meta( $order_id, 'cart_subtotal', $cart_subtotal );
			update_post_meta( $order_id, 'cart_volume_total', $tilavuus_yht );
		}
	}

}

add_action('woocommerce_checkout_update_order_meta', 'mh_ah_weight_sz_mh');
