<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * sz_WB_Mh_Lahipaketti_Shipping_Method Class
 *
 * @class sz_WB_Mh_Lahipaketti_Shipping_Method
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
class cart_items_mh_lp_sz_mh {

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
 * Get pickup points
 *
 * @access public
 * @return string
 */
function mh_noutopisteet_sz_mh_function_mh() {
	global $woocommerce;

	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );

	foreach( $chosen_methods as $shipping_method_used) {
		if(stristr($shipping_method_used, 'sz_wb_mh_lahipaketti_shipping_method')) {
			$getTheID = preg_replace('/[^0-9]/', '', $shipping_method_used);
		}
	}

	if ( class_exists('sz_WB_Mh_Lahipaketti_Shipping_Method') ) {
		$lahipaketti_shipping_method = new sz_WB_Mh_Lahipaketti_Shipping_Method( $instance_id = $getTheID );
		$matkahuolto_tunnus = $lahipaketti_shipping_method->asiakasnumero;
	} else {
		$matkahuolto_tunnus = apply_filters( 'mh_set_customer_id', '1234567' );
	}

	if($matkahuolto_tunnus == '' || $matkahuolto_tunnus == null) {
		$matkahuolto_tunnus = '1234567';
	}

	$noutopiste = esc_attr($_POST['senddata']);

	$noutopisteArray = explode("|", $noutopiste);

	$toimitusOsoite = $noutopisteArray[0];
	$toimitusMatkahuoltonumero = $noutopisteArray[1];
	$toimitusPaikkakunta = $noutopisteArray[2];

	$noutopisteet_maara = '50';

	$xml = new XMLWriter();
	$xml->openMemory();
	$xml->startDocument('1.0');
	$xml->startElement('MHSearchOfficesRequest');

	$xml->writeElement('Login', $matkahuolto_tunnus);
	$xml->writeElement('StreetAddress', $toimitusOsoite);

	$xml->writeElement('ResponseType', "xml");
	$xml->writeElement('MaxResults', $noutopisteet_maara);

	if (preg_match('/^\d{5}$/', $toimitusPaikkakunta)) {
 		$xml->writeElement('PostalCode', $toimitusPaikkakunta);
 	} else if (preg_match('/^[a-zA-ZäöåÄÖÅ]+$/', $toimitusPaikkakunta)) {
 		$xml->writeElement('City', $toimitusPaikkakunta);
 	} else {
		 echo 'Viallinen postinumero tai paikkakunta. Muuta hakua ja yritä uudelleen.';
		 die();
	}

	$xml->endElement();
	define('sendXml', $xml->flush());
	$xml->endDocument();

	 // Lähetetään XML
	$ch = curl_init();

	$rajapintaHaku = $lahipaketti_shipping_method->lp_rajapinta;

	if($rajapintaHaku == 'map24') {
		$url = "http://map.matkahuolto.fi/map24mh/searchoffices";
	} else if($rajapintaHaku == 'test') {
		$url = "https://extservicestest.matkahuolto.fi/noutopistehaku/public/searchoffices";
	} else {
		$url = "https://extservices.matkahuolto.fi/noutopistehaku/public/searchoffices";
	}

	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
	curl_setopt($ch, CURLOPT_POSTFIELDS, sendXml);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));

	$res = curl_exec($ch);

	if( curl_error($ch) == null ) {

	// Luetaan Vastaanotettu XML
	$res = urldecode($res);

	if(preg_match('/oikeutta/', $res)) {
		echo __('Virhe 1001. Asetetulla MH Asiakasnumerolla ei käyttöoikeutta rajapintaan. Muuta asiakasnumeroa Lähipaketti toimitustavan asetuksista tai ota tarvittaessa yhteyttä: <a href="mailto:verkkokauppapalvelut@matkahuolto.fi">verkkokauppapalvelut@matkahuolto.fi</a>', 'wb-matkahuolto-toimitustavat');
		die();
	}

	preg_match_all( "/\<Office\>(.*?)\<\/Office\>/s", $res, $offices );

		echo '<select id="noutopiste-result-sz-mh" name="noutopiste-result-sz-mh" class="noutopiste-result-sz-mh">';
		foreach( $offices[1] as $office ) {
			 preg_match_all( "/\<Id\>(.*?)\<\/Id\>/",
			 $office, $id );

			 preg_match_all( "/\<Name\>(.*?)\<\/Name\>/",
			 $office, $name );

			 preg_match_all( "/\<StreetAddress\>(.*?)\<\/StreetAddress\>/",
			 $office, $StreetAddress );

			 preg_match_all( "/\<PostalCode\>(.*?)\<\/PostalCode\>/",
			 $office, $PostalCode );

			 preg_match_all( "/\<City\>(.*?)\<\/City\>/",
			 $office, $City );

			 $pickupAdd = mb_strtolower(utf8_encode($name[1][0]) .', '. utf8_encode($StreetAddress[1][0]) .', '. $PostalCode[1][0] .' '. utf8_encode($City[1][0]), 'UTF-8');
			 $pickupAdd = mb_convert_case($pickupAdd, MB_CASE_TITLE, "UTF-8");

			 $pickupAddValue = mb_strtolower($id[1][0] .'; '. utf8_encode($name[1][0]) .'; '.utf8_encode($StreetAddress[1][0]).'; '. $PostalCode[1][0] .' '. utf8_encode($City[1][0]), 'UTF-8');
			 $pickupAddValue = mb_convert_case($pickupAddValue, MB_CASE_TITLE, "UTF-8");

			 echo '<option value="' . $pickupAddValue . '">' . $pickupAdd . '</option>';
		 }

		 echo '</select>';

	}

}

add_action( 'wp_ajax_nopriv_noutopisteet_sz_mh', 'mh_noutopisteet_sz_mh_function_mh' );
add_action( 'wp_ajax_noutopisteet_sz_mh', 'mh_noutopisteet_sz_mh_function_mh' );

/**
 * Main Function
 *
 * @access public
 */
function sz_WB_Mh_Lahipaketti_Shipping_Method_Init() {

	if ( ! class_exists( 'sz_WB_Mh_Lahipaketti_Shipping_Method' ) ) {

		class sz_WB_Mh_Lahipaketti_Shipping_Method extends WC_Shipping_Method {

			/**
			* Constructor for Matkahuolto shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct( $instance_id = 0 ) {
				$this->id = 'sz_wb_mh_lahipaketti_shipping_method'; // Id for your shipping method. Should be uunique.
				$this->instance_id = absint( $instance_id );
				$this->method_title = __( 'Matkahuolto Lähipaketti (Matkahuolto toimitustavat)', 'wb-matkahuolto-toimitustavat' ); // Title shown in admin
				$this->method_description = __( 'Matkahuolto lähipaketti', 'wb-matkahuolto-toimitustavat' ); // Description shown in admin
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

				$this->asiakasnumero		   	   = esc_attr( $this->get_option('asiakasnumero') );
				$this->lp_rajapinta		   	   	   = $this->get_option('lp_rajapinta');
				
				$this->lahipaketti_paino1		   = str_replace(",", ".", esc_attr( $this->get_option('lahipaketti_paino1') ));
				$this->lahipaketti_hinta1		   = str_replace(",", ".", esc_attr( $this->get_option('lahipaketti_hinta1') ));

				$this->lahipaketti_paino2		   = str_replace(",", ".", esc_attr( $this->get_option('lahipaketti_paino2') ));
				$this->lahipaketti_hinta2		   = str_replace(",", ".", esc_attr( $this->get_option('lahipaketti_hinta2') ));

				$this->lahipaketti_ilm_toim	  	   = str_replace(",", ".", esc_attr( $this->get_option('lahipaketti_ilm_toim') ));
				$this->lahipaketti_kas_kulut	   = str_replace(",", ".", esc_attr( $this->get_option('lahipaketti_kas_kulut') ));

				$this->lahipaketti_max_korkeus     = str_replace(",", ".", esc_attr( $this->get_option('lahipaketti_max_korkeus') ));
				$this->lahipaketti_max_pituus      = str_replace(",", ".", esc_attr( $this->get_option('lahipaketti_max_pituus') ));
				$this->lahipaketti_max_leveys      = str_replace(",", ".", esc_attr( $this->get_option('lahipaketti_max_leveys') ));
				$this->lahipaketti_max_paino       = str_replace(",", ".", esc_attr( $this->get_option('lahipaketti_max_paino') ));

				$this->lahp_max_paino_select       = esc_attr( $this->get_option('lahp_max_paino_select'));

				$this->lp_kuponki			   	   = esc_attr( $this->get_option('lp_kuponki') );
				$this->lp_kuponki_kaikki		   = esc_attr( $this->get_option('lp_kuponki_kaikki') );

				$this->tax_status	  	   		   = $this->get_option('tax_status');

				$this->title 			   		   = $this->get_option( 'title' );
				$this->availability 	   		   = $this->get_option( 'availability' );
				$this->countries 		   		   = $this->get_option( 'countries' );

				// Save settings in admin if you have any defined
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			function init_form_fields() {

				$this->instance_form_fields = apply_filters('wb_matkahuolto_lahipaketti_hinnoittelu_sz', array(
					'title'	   	  		   => array(
						'title'            => __('Toimitustavan nimi', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => 'Matkahuolto lähipaketti',
						'description'      => __('Anna toimitustavalle nimi jonka asiakas näkee kassalla.', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('Matkahuolto lähipaketti')
					),
					'asiakasnumero'	   	   => array(
						'title'            => __('Asiakasnumero', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '1234567',
						'description'      => __('Anna Matkahuollon asiakasnumero. Jos asiakasnumeroa ei ole, tämän voi jättää tyhjäksi. <span style="color: #ff0000;">HUOM!! Jos noutopistevalinta ei näy kassalla, ota yhteyttä: <a href="mailto:verkkokauppapalvelut@matkahuolto.fi">verkkokauppapalvelut@matkahuolto.fi</a> ja pyydä että antavat pääsyn rajapinnalle teidän asiakasnumerollenne! Jos teillä ei ole asiakasnumeroa, valitkaa seuraavassa kohdassa "Noutopiste rajapinta - Testipalvelin".</span>', 'wb-matkahuolto-toimitustavat'),
						'default'          => ''
					),
					'lp_rajapinta' => array(
							'title'			=> __( 'Noutopiste rajapinta', 'wb-matkahuolto-toimitustavat' ),
							'type'			=> 'select',
							'description'	=> 'Valitse haetaanko noutopisteet MPaketti rajapinnalta, testirajapinnalta, vaiko Map24 rajapinnalta. Oletus MPaketti.',
							'default'		=> 'mpaketti',
							'options'		=> array(
								'mpaketti'		=> __( 'MPaketti', 'wb-matkahuolto-toimitustavat' ),
								'map24'		=> __( 'Map24', 'wb-matkahuolto-toimitustavat' ),
								'test'		=> __( 'Testipalvelin', 'wb-matkahuolto-toimitustavat' ),
							)
					),
					'lahipaketti_paino1'	   	   => array(
						'title'            => __('Paketti1 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '2',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('2')
					),
					'lahipaketti_hinta1'	   	   => array(
						'title'            => __('Paketti1 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '7.70',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('7.70')
					),
					'lahipaketti_paino2'	   	   => array(
						'title'            => __('Paketti2 Paino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '10',
						'description'      => __('Ilmoita paketin max-paino. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('10')
					),
					'lahipaketti_hinta2'	   	   => array(
						'title'            => __('Paketti2 Hinta', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '9.90',
						'description'      => __('Ilmoita paketin hinta. Voit käyttää referenssinä: <a href="https://matkahuolto.fi/fi/pakettipalvelut/hinnat/" target="_blank">Matkahuollon hinnastoa</a>', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('9.90')
					),
					'lahipaketti_ilm_toim'	   => array(
						'title'            => __('Lähipaketti ilmaisen toimituksen raja', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Anna summa jonka jälkeen ei lisätä toimituskuluja. Jätä tyhjäksi jos et halua käyttää tätä toimintoa.', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('')
					),
					'lahipaketti_kas_kulut'	   => array(
						'title'            => __('Käsittelykulut', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '0',
						'description'      => __('Lisää tähän mahdolliset käsittelykulut.', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('0')
					),
					'lahipaketti_max_korkeus' => array(
						'title'            => __('Maksimikorkeus', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '150',
						'description'      => __('Tuotteen maksimikorkeus, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 150cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('150')
					),
					'lahipaketti_max_pituus' => array(
						'title'            => __('Maksimipituus', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '150',
						'description'      => __('Tuotteen maksimipituus, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 150cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('150')
					),
					'lahipaketti_max_leveys' => array(
						'title'            => __('Maksimileveys', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '150',
						'description'      => __('Tuotteen maksimileveys, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 150cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('150')
					),
					'lahipaketti_max_paino' => array(
						'title'            => __('Maksimipaino', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '10',
						'description'      => __('Tuotteen tai korin maksimipaino, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 10kg. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('10')
					),
					'lahp_max_paino_select' => array(
							'title'			=> __( 'Ota huomioon korin yhteenlaskettu paino?', 'wb-matkahuolto-toimitustavat' ),
							'type'			=> 'select',
							'description'	=> 'Jos käytössä, lasketaan koko korin paino verrattaessa yllä asetettuun maksimipainoon. Jos ei käytössä, huomioidaan vain yksittäiset tuotteet.',
							'default'		=> 'kylla',
							'options'		=> array(
								'kylla'		=> __( 'Kyllä', 'wb-matkahuolto-toimitustavat' ),
								'ei'		=> __( 'Ei', 'wb-matkahuolto-toimitustavat' ),
							)
					),
					'lp_kuponki'	 	   => array(
						'title'            => __('Kuponki', 'wb-matkahuolto-toimitustavat'),
						'type'             => 'text',
						'placeholder'	   => '',
						'description'      => __('Anna tähän kuponkikoodi joka oikeuttaa ilmaiseen toimitukseen. Jos haluat antaa useamman koodin, erota koodit pilkulla.', 'wb-matkahuolto-toimitustavat'),
						'default'          => __('')
					),
					'lp_kuponki_kaikki' => array(
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
				) );
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

				$paino1 = $this->lahipaketti_paino1;
				$paino2 = $this->lahipaketti_paino2;

				$ilm_toim = floatval($this->lahipaketti_ilm_toim);

				// Kuponki
				$has_coupon = false;
				$all_coupons = array();
				$annettu_koodi = $this->lp_kuponki;
				$annettu_koodi_array_or_not = false;

				$salli_kaikki_kupongit = $this->lp_kuponki_kaikki;

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
						$lopullinen_hinta = $this->lahipaketti_hinta1 + $this->lahipaketti_kas_kulut;
					} elseif ( ($paino1 <= $weight) && ($weight <= $paino2) ) {
						$lopullinen_hinta = $this->lahipaketti_hinta2 + $this->lahipaketti_kas_kulut;
					} else {
						$lopullinen_hinta = $this->lahipaketti_hinta2 + $this->lahipaketti_kas_kulut;
					}
				}

				$lopullinen_hinta = apply_filters('wb_matkahuolto_lopullinen_hinta_sz', $lopullinen_hinta);

				$rate = apply_filters('wb_mh_lahipaketti_rate_filter', array(
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

add_action( 'woocommerce_shipping_init', 'sz_WB_Mh_Lahipaketti_Shipping_Method_init' );

function add_sz_WB_Mh_Lahipaketti_Shipping_Method( $methods ) {

	$methods['sz_wb_mh_lahipaketti_shipping_method'] = 'sz_WB_Mh_Lahipaketti_Shipping_Method';
	return $methods;

}

add_filter( 'woocommerce_shipping_methods', 'add_sz_WB_Mh_Lahipaketti_Shipping_Method' );

function hide_show_mh_lp_sz_mh( $rates, $package ) {
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
			if (strpos($shipping_id, 'sz_wb_mh_lahipaketti_shipping_method') !== false) {
				$get_the_id = str_replace('sz_wb_mh_lahipaketti_shipping_method', '', $shipping_id);
				$get_the_id = str_replace(':', '', $get_the_id);
			}
		}

		$kassan_tiedot = new cart_items_mh_lp_sz_mh();

		$length = $kassan_tiedot->pituus($countQty = false);
		$width = $kassan_tiedot->leveys($countQty = false);
		$height = $kassan_tiedot->korkeus($countQty = false);
		$weight = $kassan_tiedot->paino($countQty = true);

		$lahipaketti_shipping_method = new sz_WB_Mh_Lahipaketti_Shipping_Method( $instance_id = $get_the_id );

		if(!empty($lahipaketti_shipping_method->lahp_max_paino_select) && $lahipaketti_shipping_method->lahp_max_paino_select == 'ei') {
			$total_weight = 0;
		} else {
			$total_weight = $woocommerce->cart->cart_contents_weight;
		}

		if(!empty($lahipaketti_shipping_method->lahipaketti_max_paino)) {
			$max_paino = $lahipaketti_shipping_method->lahipaketti_max_paino;
		} else {
			$max_paino = 10;
		}

		if(!empty($lahipaketti_shipping_method->lahipaketti_max_korkeus)) {
			$max_korkeus = $lahipaketti_shipping_method->lahipaketti_max_korkeus;
		} else {
			$max_korkeus = 150;
		}

		if(!empty($lahipaketti_shipping_method->lahipaketti_max_leveys)) {
			$max_leveys = $lahipaketti_shipping_method->lahipaketti_max_leveys;
		} else {
			$max_leveys = 150;
		}

		if(!empty($lahipaketti_shipping_method->lahipaketti_max_pituus)) {
			$max_pituus = $lahipaketti_shipping_method->lahipaketti_max_pituus;
		} else {
			$max_pituus = 150;
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

		if($total_weight == null OR $total_weight == '') {
			$total_weight = 0;
		}

		if ( max($height) > $max_korkeus OR max($length) > $max_pituus OR max($width) > $max_leveys OR max($weight) > $max_paino OR $total_weight > $max_paino ) {
			$new_rates = array();

			foreach ( $rates as $rate_id => $rate ) {
				if ( 'sz_wb_mh_lahipaketti_shipping_method' !== $rate->method_id ) {
					$new_rates[ $rate_id ] = $rate;
				}
			}

			return $new_rates;

		} else {
			return $rates;
		}
	}

}

add_filter( 'woocommerce_package_rates', 'hide_show_mh_lp_sz_mh' , 10, 2 );

/**
* Get the PickupPoints
*
* @since 1.0.0
* @return void
*/
function wb_mh_noutopistehaku_sz_mh( $checkout ) {

	echo '
	<div class="lahipaketti-wrap-sz-mh">
		<div class="js-ajax-php-json2-sz-mh">
			<div class="required-city-sz-mh">'.__('Anna paikkakunta tai postinumero ja paina Hae:','wb-matkahuolto-toimitustavat').'</div>
			<input type="text" id="mh_noutopiste-sz-mh" class="mh_noutopiste-sz-mh" name="mh_noutopiste-sz-mh" value="" placeholder="'. __('Paikkakunta tai postinumero*','wb-matkahuolto-toimitustavat').'" />
			<button class="js-ajax-php-json-sz-mh">'. __('Hae','wb-matkahuolto-toimitustavat') .'</button><span class="loading-img-mh-sz"></span>
		</div>

		<div class="mh-return-sz-mh"></div>
	</div>
	';
}

add_action( 'woocommerce_checkout_order_review','wb_mh_noutopistehaku_sz_mh', 20);

/**
 * Detect Klarna pickup point plugin. For use on Front End only.
 */

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active( 'wb-klarna-pickup/wb-klarna-pickup.php' ) OR is_plugin_active( 'wb-klarna-pickup-master/wb-klarna-pickup.php' ) ) {
	add_action( 'show_pickup_point_action_hook', 'wb_mh_noutopistehaku_sz_mh', 10);
	//add_action( 'klarna-show-pickup-points','wb_mh_noutopistehaku_sz_mh', 20);
}

/**
* Process the checkout
*
* @since 1.0.0
* @return void
*/
function wb_matkahuolto_checkout_field_process_sz_mh() {
	global $woocommerce;

	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
	$chosen_method = $chosen_methods[0];

	if ( $chosen_method == stristr($chosen_method, 'sz_wb_mh_lahipaketti_shipping_method') && empty($_POST['noutopiste-result-sz-mh']) && $chosen_method !== NULL ) {
		wc_add_notice( __( 'Virhe! Matkahuollon noutopiste puuttuu!' ), 'error' );
	}
}

add_action('woocommerce_checkout_process', 'wb_matkahuolto_checkout_field_process_sz_mh');

/**
* Update the Order Meta With Field Value
*
* @since 1.0.0
* @return void
*/
function wb_mh_noutopisteet_update_order_meta_sz_mh( $order_id ) {
	global $woocommerce;

	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
	$chosen_method = $chosen_methods[0];

	if ( ! empty( $_POST['noutopiste-result-sz-mh'] ) && $chosen_method == stristr($chosen_method, 'sz_wb_mh_lahipaketti_shipping_method')) {
		update_post_meta( $order_id, 'noutopiste-result', sanitize_text_field( $_POST['noutopiste-result-sz-mh'] ) );

		$getPickupLocation = sanitize_text_field( $_POST['noutopiste-result-sz-mh'] );
		$getPickupLocation = str_replace(";", ", ", $getPickupLocation);

		update_post_meta( $order_id, 'noutopiste-result-b-mh',  $getPickupLocation);
	}

}

add_action( 'woocommerce_checkout_update_order_meta', 'wb_mh_noutopisteet_update_order_meta_sz_mh' );

/**
* Display field value on the order edit page
*
* @since 1.0.0
* @return void
*/
function wb_mh_noutopisteet_display_admin_order_meta_sz_mh($order){

	$value = get_post_meta( $order->get_order_number(), 'noutopiste-result-b-mh', true );
	if ( ! empty($value) ) {
		echo '<p><strong>'.__('Matkahuollon noutopiste', 'wb-matkahuolto-toimitustavat').':</strong> ' . $value . '</p>';
	}

}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'wb_mh_noutopisteet_display_admin_order_meta_sz_mh', 10, 1 );

/**
* Get package dimensions
*
* @since 1.0.0
* @return void
*/
function mh_lp_weight_sz_mh( $order_id ) {
	global $woocommerce;

	$items = $woocommerce->cart->get_cart();

	$tilavuus_yht = 0;

	foreach ($items as $item) {
		$width = $item['data']->get_width();
		$height = $item['data']->get_height();
		$length = $item['data']->get_length();

		if( isset( $height, $length, $width ) ) {
			$tilavuus = $height * $length * $width / 1000000;
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
		if(stristr($shipping_method_used, 'sz_wb_mh_lahipaketti_shipping_method')) {
			update_post_meta( $order_id, 'cart_weight', $weight );
			update_post_meta( $order_id, 'cart_qty', $count );
			update_post_meta( $order_id, 'cart_subtotal', $cart_subtotal );
			update_post_meta( $order_id, 'cart_volume_total', $tilavuus_yht );
		}
	}

}

add_action('woocommerce_checkout_update_order_meta', 'mh_lp_weight_sz_mh');
