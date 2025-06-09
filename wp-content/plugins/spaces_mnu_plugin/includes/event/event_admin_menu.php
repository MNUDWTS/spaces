<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Spaces_MNU_Event_Admin_Menu')) {
    class Spaces_MNU_Event_Admin_Menu
    {
        public function __construct()
        {
            add_action('admin_menu', array($this, 'add_event_menu'));
        }
        public function add_event_menu()
        {
            add_menu_page(
                __('Events', 'spaces_mnu_plugin'),
                __('Events', 'spaces_mnu_plugin'),
                'edit_events',
                'edit.php?post_type=event',
                '',
                'dashicons-calendar',
                1
            );

            add_submenu_page(
                'edit.php?post_type=event',
                __('Event Types', 'spaces_mnu_plugin'),
                __('Event Types', 'spaces_mnu_plugin'),
                'manage_categories',
                'edit-tags.php?taxonomy=event_type&post_type=event'
            );

            add_submenu_page(
                'edit.php?post_type=event',
                __('Event Categories', 'spaces_mnu_plugin'),
                __('Event Categories', 'spaces_mnu_plugin'),
                'manage_categories',
                'edit-tags.php?taxonomy=event_category&post_type=event'
            );
        }
    }
    new Spaces_MNU_Event_Admin_Menu();
}
