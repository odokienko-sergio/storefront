<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */

/**
 * Import products from XML.
 */
function import_products_from_xml() {
	// Construct the path to the XML file using ABSPATH
	$xml_file_path = ABSPATH . 'wp-content/themes/storefront/products.xml';

	// Load the XML file
	$xml = simplexml_load_file($xml_file_path);

	// Loop through each product in the XML file
	foreach ($xml->product as $product_data) {
		// Check if product already exists
		$existing_product = get_page_by_title( $product_data->name, OBJECT, 'product' );

		if ($existing_product === null) {
			// Create new product
			$product = new WC_Product();

			$product->set_name($product_data->name);
			$product->set_description($product_data->description);
			$product->set_price($product_data->price);
			// Add other fields as necessary, based on your XML structure

			$product->save();
		}
	}

	echo "Products imported successfully!";
}

/**
 * Schedule the product import if it hasn't been scheduled yet.
 */
function schedule_product_import() {
	if ( ! wp_next_scheduled( 'import_daily_products' ) ) {
		wp_schedule_event( time(), 'daily', 'import_daily_products' );
	}
}
add_action( 'wp', 'schedule_product_import' );

// Link our import function to the scheduled event
add_action( 'import_daily_products', 'import_products_from_xml' );

