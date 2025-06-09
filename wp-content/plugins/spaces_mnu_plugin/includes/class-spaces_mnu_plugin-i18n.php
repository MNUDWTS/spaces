<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://spaces.mnu.kz
 * @since      1.0.0
 *
 * @package    Spaces_mnu_plugin
 * @subpackage Spaces_mnu_plugin/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Spaces_mnu_plugin
 * @subpackage Spaces_mnu_plugin/includes
 * @author     DWTS <bazarov_berik@mnu.kz>
 */
class Spaces_mnu_plugin_i18n
{
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain(
			'spaces_mnu_plugin',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);
	}
}
