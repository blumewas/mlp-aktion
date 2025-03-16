<?php

/**
 * Fired during plugin activation
 *
 * @link       https://bonnermedis.de
 * @since      1.0.0
 *
 * @package    Mlp_Aktion
 * @subpackage Mlp_Aktion/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Mlp_Aktion
 * @subpackage Mlp_Aktion/includes
 * @author     Andreas Schneider <anschneider187@gmail.com>
 */
class Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        if (! class_exists('WooCommerce')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die('Dieses Plugin benötigt WooCommerce. Bitte installiere und aktiviere WooCommerce.', 'Plugin-Fehler', array('back_link' => true));
        }
	}

}
