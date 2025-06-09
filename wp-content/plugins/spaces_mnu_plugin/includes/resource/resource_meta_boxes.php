<?php
if (!defined('ABSPATH')) {
    exit;
}
require_once plugin_dir_path(__FILE__) . 'resource_utils.php';
if (!class_exists('Spaces_MNU_Resource_Meta_Boxes')) {
    class Spaces_MNU_Resource_Meta_Boxes
    {
        private $floor;
        private $images;
        private $responsible_users;
        private $access_roles;
        private $type_terms;
        private $selected_type;
        private $selected_categories;
        private $roles;
        private $sorted_roles;
        private $current_user_id;
        private $availability_enabled;
        private $available_time;
        private $available_days;
        public function __construct()
        {
            add_action('add_meta_boxes', array($this, 'add_resource_meta_boxes'));
            add_action('save_post', array($this, 'save_resource_meta_boxes'));
        }
        public function add_resource_meta_boxes()
        {
            add_meta_box(
                'resource_static_fields',
                __('Resource Details', 'spaces_mnu_plugin'),
                array($this, 'render_static_fields_meta_box'),
                'resource',
                'normal',
                'high'
            );

            add_meta_box(
                'resource_localized_fields',
                __('Resource Localized Details', 'spaces_mnu_plugin'),
                array($this, 'render_localized_fields_meta_box'),
                'resource',
                'normal',
                'high'
            );
        }
        public function render_static_fields_meta_box($post)
        {
            $variables = Spaces_MNU_Resource_Utils::initialize_form_variables($post);
            include 'templates/resource-static-fields-meta-box.php';
        }
        public function render_localized_fields_meta_box($post)
        {
            $variables = Spaces_MNU_Resource_Utils::initialize_form_variables($post);
            include 'templates/resource-localized-fields-meta-box.php';
        }
        public function save_resource_meta_boxes($post_id)
        {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
            Spaces_MNU_Resource_Utils::save_resource_meta_fields($post_id, $_POST);
        }
    }
    new Spaces_MNU_Resource_Meta_Boxes();
}
