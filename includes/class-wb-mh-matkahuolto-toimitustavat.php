<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WooCommerce_Matkahuolto_Toimitustavat {

	/**
	 * The single instance of WooCommerce_Matkahuolto_Toimitustavat.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'woocommerce_matkahuolto_toimitustavat';

		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wb_enqueue_scripts_bussiennakko_js' ), 20 );
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'matkahuolto_toim_tavat_woo_actions'), 10, 1);
		add_action( 'load-edit.php', array( $this, 'matkahuolto_toim_tavat_custom_action' ), 4 );

		// Load backend CSS
		add_action( 'admin_enqueue_scripts', array($this, 'wb_enqueue_styles_matkahuolto_toim_backend' ), 20 );

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // Edn __construct ()

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );

		wp_localize_script( $this->_token . '-frontend', 'mh_sz_Ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

		if( is_checkout() OR is_cart() ) {
			wp_register_script( $this->_token . '-frontend-matkahuolto-js', esc_url( $this->assets_url ) . 'js/wb.matkahuolto.frontend.default.js', array( 'jquery' ), $this->_version );
			wp_enqueue_script( $this->_token . '-frontend-matkahuolto-js' );
		}
	}

	/**
	 * Load Backend CSS
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	function wb_enqueue_styles_matkahuolto_toim_backend () {
		wp_enqueue_style('matkahuolto-toim-backend-css', plugins_url().'/wb-matkahuolto-toimitustavat/assets/css/backend.css', true );
	}

	/**
	 * Load bussiennakkomaksu Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
		public function wb_enqueue_scripts_bussiennakko_js () {
			$bussiennakko_class = new WB_Gateway_Bussiennakko_Mh();
			if( $bussiennakko_class->settings['be_lisamaksu_on_off'] == 'kylla' && is_checkout() OR is_cart()) {
				wp_register_script( $this->_token . '-frontend-be-maksu-js', esc_url( $this->assets_url ) . 'js/bussiennakko.js', array( 'jquery' ), $this->_version );
				wp_enqueue_script( $this->_token . '-frontend-be-maksu-js' );
			}
		}

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'wb-matkahuolto-toimitustavat', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'wb-matkahuolto-toimitustavat';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Matkahuolto Toimitustavat WooCommerce Actions
	 *
	 * @since 1.1.0
	 * @access public
	 * @return void
	 */
	 public function matkahuolto_toim_tavat_woo_actions($array) {
		
		global $post;
		global $post_type;

		if($post_type == 'shop_order') {
			$order = new WC_Order($post->ID);
			$valid_methods = $this->validMethods();

			$shipping_methods = $order->get_shipping_methods();

			foreach ($shipping_methods as $shipping_method) {
				$shipping_id = $shipping_method['method_id'];
			}

			if(isset($shipping_id)) {
				foreach($valid_methods as $method) {
					if(strpos($shipping_id, $method) !== false) {
						$array['tulostakirje_matkahuolto_toim'] = array(
							'url'       => admin_url("edit.php?post=$post->ID&post_type=shop_order&action=tulostakirje_matkahuolto_toim"),
							'name'      => __( 'Hae osoitetiedot', 'wb-matkahuolto-toimitustavat' ),
							'action'    => "tulostakirje_matkahuolto_toim"
						);
					}
				}
			}

			return $array;
		}

	}

	/**
	 * Custom Action
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function matkahuolto_toim_tavat_custom_action() {
		
		// Käynnistetään Custom Order Status
		global $typenow;
		$post_type = $typenow;

		if($post_type == 'shop_order') {
			$wp_list_table = _get_list_table('WP_Posts_List_Table');
			$action = $wp_list_table->current_action();

			$allowed_actions = array("tulostakirje_matkahuolto_toim");

			if(!in_array($action, $allowed_actions)) return;

			if ($action == 'tulostakirje_matkahuolto_toim') {
				$post_ids = array_map( 'absint', (array) $_REQUEST['post'] );

				foreach ( $post_ids as $post_id ) {

					$order = wc_get_order( $post_id );

					$etunimi = ucfirst( $order->get_shipping_first_name() );
					$sukunimi = ucfirst( $order->get_shipping_last_name() );
					$osoite1 = ucfirst( $order->get_shipping_address_1() );
					$osoite2 = ucfirst( $order->get_shipping_address_2() );
					$kaupunki = mb_strtoupper( $order->get_shipping_city(), 'UTF-8' );
					$postinumero = $order->get_shipping_postcode();
					$yritys = ucfirst( $order->get_shipping_company() );

					echo $etunimi . ' ' . $sukunimi . ' ' . $yritys . '<br>';
					echo $osoite1 . '<br>';
					echo $postinumero . ' ' . $kaupunki . '<br>';

				}

			exit;
			}

		}
	}

	/**
	* Valid Matkahuolto toimitustavat shipping methods
	*
	* @since 1.1.0
	* @return array
	*/
	public function validMethods() {
		$valid_methods = array(
			'wb_mh_ahvenanmaa_shipping_method_sz',
			'wb_mh_bussiennakko_shipping_method_sz',
			'wb_mh_bussipaketti_shipping_method_sz',
			'wb_mh_jakopaketti_shipping_method_sz',
			'sz_wb_mh_lahipaketti_shipping_method',
			'wb_mh_pikapaketti_shipping_method_sz'
		);

		return $valid_methods;
	}

	/**
	 * Main WooCommerce_Matkahuolto_Toimitustavat Instance
	 *
	 * Ensures only one instance of WooCommerce_Matkahuolto_Toimitustavat is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WooCommerce_Matkahuolto_Toimitustavat()
	 * @return Main WooCommerce_Matkahuolto_Toimitustavat instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	}

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	}

}
