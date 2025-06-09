<?php

if (!defined('ABSPATH')) {
	exit;
}

class Spaces_mnu_plugin
{
	public function __construct()
	{
		$this->load_dependencies();
		$this->init_i18n();
		add_filter('template_include', array($this, 'add_home_template'), 99);
	}

	function add_home_template($template)
	{
		if (is_front_page()) {
			$plugin_template = plugin_dir_path(__FILE__) . 'common/templates/home.php';
			if (file_exists($plugin_template)) {
				return $plugin_template;
			}
		}
		return $template;
	}

	private function load_dependencies()
	{
		require_once plugin_dir_path(__FILE__) . 'common/global-preloader.php';
		require_once plugin_dir_path(__FILE__) . 'common/sort_roles.php';
		require_once plugin_dir_path(__FILE__) . 'common/check_access.php';
		require_once plugin_dir_path(__FILE__) . 'common/send_custom_email.php';
		require_once plugin_dir_path(__FILE__) . 'admin/class-spaces_mnu_department-admin.php';
		require_once plugin_dir_path(__FILE__) . 'admin/class-spaces_mnu_roles-admin.php';
		require_once plugin_dir_path(__FILE__) . 'user/class-spaces_mnu_plugin-user-register.php';
		require_once plugin_dir_path(__FILE__) . 'user/class-spaces_mnu_plugin-user-fields.php';
		require_once plugin_dir_path(__FILE__) . 'user/class-spaces_mnu_plugin-user-public.php';
		require_once plugin_dir_path(__FILE__) . 'resource/class-spaces_mnu_plugin-resource.php';
		require_once plugin_dir_path(__FILE__) . 'event/class-spaces_mnu_plugin-event.php';
		require_once plugin_dir_path(__FILE__) . 'class-spaces_mnu_plugin-i18n.php';
	}

	private function init_i18n()
	{
		add_action('init', function () {
			$plugin_i18n = new Spaces_mnu_plugin_i18n();
			$plugin_i18n->load_plugin_textdomain();
		});
	}

	public function run() {}
}

if (!function_exists('run_spaces_mnu_plugin')) {
	function run_spaces_mnu_plugin()
	{
		$plugin = new Spaces_mnu_plugin();
		$plugin->run();
	}
}

add_action('plugins_loaded', 'run_spaces_mnu_plugin');
