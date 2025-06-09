<?php

/**
 * @link              https://spaces.mnu.kz
 * @since             1.0.0
 * @package           Spaces_mnu_plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Spaces MNU Plugin
 * Plugin URI:        https://spaces.mnu.kz
 * Description:       Online platform for optimization and management of internal processes of Maqsut Narikbayev University.
 * Version:           1.0.0
 * Author:            DWTS
 * Author URI:        mailto:dwts@mnu.kz/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       spaces_mnu_plugin
 * Domain Path:       /languages
 */

if (! defined('WPINC')) {
	die;
}

define('SPACES_MNU_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SPACES_MNU_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SPACES_MNU_PLUGIN_VERSION', '1.0.0');

function activate_spaces_mnu_plugin()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-spaces_mnu_plugin-activator.php';
	Spaces_mnu_plugin_Activator::activate();
}

function deactivate_spaces_mnu_plugin()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-spaces_mnu_plugin-deactivator.php';
	Spaces_mnu_plugin_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_spaces_mnu_plugin');
register_deactivation_hook(__FILE__, 'deactivate_spaces_mnu_plugin');

require plugin_dir_path(__FILE__) . 'includes/class-spaces_mnu_plugin.php';

function spaces_mnu_enqueue_styles()
{
	wp_enqueue_style('spaces-mnu-tailwind', plugin_dir_url(__FILE__) . 'assets/css/tailwind.css', array(), SPACES_MNU_PLUGIN_VERSION, 'all');
}

add_action('wp_enqueue_scripts', 'spaces_mnu_enqueue_styles');
add_action('admin_enqueue_scripts', 'spaces_mnu_enqueue_styles');
