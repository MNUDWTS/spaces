<?php
if (!defined('ABSPATH')) {
    exit;
}
require_once plugin_dir_path(__FILE__) . 'event_utils.php';
if (!class_exists('Spaces_MNU_Event_Meta_Boxes')) {
    class Spaces_MNU_Event_Meta_Boxes
    {
        private $floor;
        private $images;
        private $responsible_users;
        private $access_roles;
        private $selected_categories;
        private $roles;
        private $sorted_roles;
        private $current_user_id;
        private $location;
        private $date;
        private $start_time;
        private $end_time;
        private $public_start_time;
        private $seat_count;
        public function __construct()
        {
            add_action('add_meta_boxes', array($this, 'add_event_meta_boxes'));
            add_action('save_post', array($this, 'save_event_meta_boxes'));
        }
        public function add_event_meta_boxes()
        {
            add_meta_box(
                'event_static_fields',
                __('Event Details', 'spaces_mnu_plugin'),
                array($this, 'render_static_fields_meta_box'),
                'event',
                'normal',
                'high'
            );

            add_meta_box(
                'event_localized_fields',
                __('Event Localized Details', 'spaces_mnu_plugin'),
                array($this, 'render_localized_fields_meta_box'),
                'event',
                'normal',
                'high'
            );
        }
        public function render_static_fields_meta_box($post)
        {
            $variables = Spaces_MNU_Event_Utils::initialize_form_variables($post);
            include 'templates/event-static-fields-meta-box.php';
        }
        public function render_localized_fields_meta_box($post)
        {
            $variables = Spaces_MNU_Event_Utils::initialize_form_variables($post);
            include 'templates/event-localized-fields-meta-box.php';
        }
        public function save_event_meta_boxes($post_id)
        {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
            Spaces_MNU_Event_Utils::save_event_meta_fields($post_id, $_POST);
        }
    }
    new Spaces_MNU_Event_Meta_Boxes();
}
