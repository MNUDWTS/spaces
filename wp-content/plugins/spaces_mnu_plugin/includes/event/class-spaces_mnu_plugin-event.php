<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Spaces_MNU_Plugin_Event')) {
    class Spaces_MNU_Plugin_Event
    {
        public function __construct()
        {
            $this->load_dependencies();
        }
        private function load_dependencies()
        {
            require_once plugin_dir_path(__FILE__) . 'event_post_type.php';
            require_once plugin_dir_path(__FILE__) . 'event_utils.php';
            require_once plugin_dir_path(__FILE__) . 'event_ajax.php';
            require_once plugin_dir_path(__FILE__) . 'event_assets.php';
            require_once plugin_dir_path(__FILE__) . 'event_admin_menu.php';
            require_once plugin_dir_path(__FILE__) . 'event_templates.php';
            require_once plugin_dir_path(__FILE__) . 'event_meta_boxes.php';
            require_once plugin_dir_path(__FILE__) . 'event_frontend_forms.php';
            require_once dirname(__FILE__) . '/../resource/booking_ajax.php';
        }
    }
    new Spaces_MNU_Plugin_Event();
}
