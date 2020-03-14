<?php
/*
 * Plugin Name: WooCommerce Matkahuolto Toimitustavat
 * Version: 1.4.3.4
 * Plugin URI: http://www.webbisivut.org/
 * Description: Matkahuolto Toimitustavat lisäosa WooCommercelle.
 * Author: Webbisivut.org
 * Author URI: http://www.webbisivut.org/
 * Requires at least: 4.3
 * Tested up to: 5.0
 * WC requires at least: 3.0
 * WC tested up to: 3.5.2
 *
 * @package WordPress
 * @author Webbisivut.org
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if(!is_multisite()) {
	if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		
		function wb_matkahuolto_toimitustavat_woo_admin_notice__error() {
			$class = 'notice notice-error';
			$message = __( 'Virhe Matkahuolto Toimitustavat lisäosan käyttöönotossa! WooCommerce ei ole aktivoituna!', 'wb-matkahuolto' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
		}
		add_action( 'admin_notices', 'wb_matkahuolto_toimitustavat_woo_admin_notice__error' );

	} else {
		wb_matkahuolto_toimitustavat_requirements();
	}
} else {
	wb_matkahuolto_toimitustavat_requirements();
}

function wb_matkahuolto_toimitustavat_requirements() {
	// Include plugin class files
	require_once( 'includes/class-wb-mh-matkahuolto-toimitustavat.php' );

	// Shipping methods
	require_once( 'includes/class-wb-mh-bussipaketti-shipment-methods-sz.php' );
	require_once( 'includes/class-wb-mh-jakopaketti-shipment-methods-sz.php' );
	require_once( 'includes/class-wb-mh-kotijakelu-shipment-methods-sz.php' );
	require_once( 'includes/class-wb-mh-pikapaketti-shipment-methods-sz.php' );
	require_once( 'includes/class-wb-mh-bussiennakko-shipment-methods-sz.php' );
	require_once( 'includes/class-wb-mh-lahipaketti-shipment-methods-sz.php' );
	require_once( 'includes/class-wb-mh-ahvenanmaa-shipment-methods-sz.php' );

	// Payment methods
	require_once( 'includes/class-wb-mh-bussiennakko-maksu.php' );

	/**
	 * Returns the main instance of WooCommerce_Matkahuolto_Toimitustavat to prevent the need to use globals.
	 *
	 * @since  1.0.0
	 * @return object WooCommerce_Matkahuolto_Toimitustavat
	 */
	function WooCommerce_Matkahuolto_Toimitustavat () {
		$instance = WooCommerce_Matkahuolto_Toimitustavat::instance( __FILE__, '1.0.0' );
		return $instance;
	}

	WooCommerce_Matkahuolto_Toimitustavat();
}

function formatPriceMatkahuoltoToimitustavat($haystack, $replace, $needle) {
	$pos = strpos($haystack, $needle);

	if ($pos !== false) {
		$newstring = substr_replace($haystack, $replace, $pos, strlen($needle));
	} else {
		$newstring = $haystack;
	}

	return $newstring;
}
?>
