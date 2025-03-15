<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://bonnermedis.de
 * @since      1.0.0
 *
 * @package    Mlp_Aktion
 * @subpackage Mlp_Aktion/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Mlp_Aktion
 * @subpackage Mlp_Aktion/includes
 * @author     Andreas Schneider <anschneider187@gmail.com>
 */
class Mlp_Aktion_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mlp-aktion',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
