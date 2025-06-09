<?php
if (!defined('ABSPATH')) {
    exit;
}



if (!class_exists('Spaces_MNU_Plugin_Resource')) {

    class Spaces_MNU_Plugin_Resource
    {
        public function __construct()
        {
            $this->load_dependencies();
        }
        private function load_dependencies()
        {
            require_once plugin_dir_path(__FILE__) . 'resource_post_type.php';
            require_once plugin_dir_path(__FILE__) . 'resource_utils.php';
            require_once plugin_dir_path(__FILE__) . 'resource_ajax.php';
            require_once plugin_dir_path(__FILE__) . 'resource_assets.php';
            require_once plugin_dir_path(__FILE__) . 'resource_admin_menu.php';
            require_once plugin_dir_path(__FILE__) . 'resource_templates.php';
            require_once plugin_dir_path(__FILE__) . 'resource_meta_boxes.php';
            require_once plugin_dir_path(__FILE__) . 'resource_frontend_forms.php';
            require_once plugin_dir_path(__FILE__) . 'booking_ajax.php';
        }
    }

    new Spaces_MNU_Plugin_Resource();
}
